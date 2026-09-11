<?php
// PASTIKAN SESSION DIMULAI DI BARIS PALING ATAS
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Hubungkan ke database
include 'config/koneksi.php';
date_default_timezone_set('Asia/Makassar');

// Kunci Akses dari Dashboard Duitku Sandbox
$merchantCode = 'DS35199'; // Ganti dengan Merchant Code lu
$apiKey       = '8567dfa3bc50ff93104ed5222b406b1d';       // Ganti dengan API Key lu

// DAFTAR KELAS AKTIF BERDASARKAN BROSUR (MB NON GEMBUNG)
$kelas_mb = [
    5  => ["nama" => "Sesi 5 - MB. Non Gembung Tilas", "harga" => 55000],
    11 => ["nama" => "Sesi 11 - MB. Non Gembung Marwah", "harga" => 125000],
    15 => ["nama" => "Sesi 15 - MB. Non Gembung Sejati", "harga" => 560000],
    20 => ["nama" => "Sesi 20 - MB. Non Gembung Sultan", "harga" => 220000],
    24 => ["nama" => "Sesi 24 - MB. Non Gembung Napak", "harga" => 85000],
];

// Cek apakah user adalah admin
$is_admin = isset($_SESSION['admin_username']) && !empty($_SESSION['admin_username']);

// ---------------------------------------------------------------------------
// BAGIAN 1: PROSES PEMESANAN (CASH / QRIS)
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_booking'])) {
    header('Content-Type: application/json');
    
    $nama_peserta  = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nama_burung   = mysqli_real_escape_string($koneksi, $_POST['burung']);
    $alamat        = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kelas_lomba   = mysqli_real_escape_string($koneksi, $_POST['kelas_lomba']); 
    $nomor_pilih   = mysqli_real_escape_string($koneksi, $_POST['nomor']);
    $id_kelas_pilih= (int)$_POST['id_kelas'];
    
    // Tangkap metode pembayaran & deskripsi (Default ke QRIS jika tidak ada)
    $metode_input = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : 'QRIS Duitku';
    $deskripsi = isset($_POST['deskripsi']) ? mysqli_real_escape_string($koneksi, $_POST['deskripsi']) : '';

    // Cek Keamanan: Pastikan hanya admin yang bisa pakai Cash/Transfer
    if (!$is_admin && $metode_input == 'Cash/Transfer') {
        $metode_input = 'QRIS Duitku'; // Paksa ke QRIS jika user biasa mencoba manipulasi form
    }

    if (empty($nomor_pilih) || $nomor_pilih == "Belum ada yang dipilih") {
        echo json_encode(['status' => 'error', 'message' => 'Anda belum memilih nomor gantangan!']);
        exit;
    }

    $harga_satuan = $kelas_mb[$id_kelas_pilih]['harga'] ?? 55000;
    $jumlah_gantangan = count(explode(',', $nomor_pilih));
    $total_harga = $jumlah_gantangan * $harga_satuan;

    // Cek double booking gantangan (abaikan yang statusnya batal)
    $query_cek = mysqli_query($koneksi, "SELECT nomor_gantangan FROM transaksi WHERE kelas_lomba = '$kelas_lomba' AND status_pembayaran != 'batal'");
    $gantangan_terisi = [];
    while ($row = mysqli_fetch_assoc($query_cek)) {
        $pecah = explode(',', $row['nomor_gantangan']);
        foreach ($pecah as $no) {
            if (trim($no) != "") { $gantangan_terisi[] = trim($no); }
        }
    }

    $nomor_pilih_arr = explode(',', $nomor_pilih);
    foreach ($nomor_pilih_arr as $no_baru) {
        if (in_array(trim($no_baru), $gantangan_terisi)) {
            echo json_encode(['status' => 'error', 'message' => 'Nomor gantangan ' . trim($no_baru) . ' baru saja terisi!']);
            exit;
        }
    }

    // Penentuan Status & Deskripsi berdasarkan Metode
    $status_awal = ($metode_input === 'Cash/Transfer') ? 'Lunas' : 'pending';
    $waktu_pesan = date('Y-m-d H:i:s');
    $waktu_expired = ($metode_input === 'Cash/Transfer') ? $waktu_pesan : date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($waktu_pesan)));
    
    if (empty($deskripsi)) {
        $deskripsi_awal = ($metode_input === 'Cash/Transfer') ? 'Lunas di Kasir' : 'Pending QRIS';
    } else {
        $deskripsi_awal = $deskripsi;
    }

    // Simpan ke database
    $query_insert = "INSERT INTO transaksi 
                     (nama_peserta, nama_burung, alamat, kelas_lomba, metode_pembayaran, status_pembayaran, nomor_gantangan, jumlah_gantangan, total_harga, waktu_expired, deskripsi) 
                     VALUES 
                     ('$nama_peserta', '$nama_burung', '$alamat', '$kelas_lomba', '$metode_input', '$status_awal', '$nomor_pilih', '$jumlah_gantangan', '$total_harga', '$waktu_expired', '$deskripsi_awal')";

    if (!mysqli_query($koneksi, $query_insert)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal simpan database: ' . mysqli_error($koneksi)]);
        exit;
    }

    $id_transaksi_lokal = mysqli_insert_id($koneksi);

    // JIKA ADMIN PILIH CASH, STOP DI SINI (JANGAN TEMBAK DUITKU)
    if ($metode_input === 'Cash/Transfer') {
        echo json_encode([
            'status' => 'success_cash',
            'message' => 'Transaksi Cash/Transfer berhasil dicatat sebagai LUNAS!'
        ]);
        exit;
    }

    // JIKA QRIS, LANJUTKAN TEMBAK API DUITKU
    $merchantOrderId    = 'MBNG-' . $id_transaksi_lokal . '-' . time();
    $paymentAmount = $total_harga;
    $email         = 'kicaumania@arenasejati.com';
    $phone         = '081234567890';
    $productDetails= 'Tiket ' . $kelas_lomba . ' No: ' . $nomor_pilih;
    
    $signature     = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

    $params = [
        'merchantCode'    => $merchantCode,
        'paymentAmount'   => $paymentAmount,
        'merchantOrderId' => $merchantOrderId,
        'productDetails'  => $productDetails,
        'email'           => $email,
        'phoneNumber'     => $phone,
        'paymentMethod'   => 'SP', // Kode QRIS Duitku
        'callbackUrl'     => 'http://127.0.0.1/kasirBurung/callback_duitku.php',
        'returnUrl'       => 'http://127.0.0.1/kasirBurung/mbnongembung.php',
        'expiryPeriod'    => 10,
        'signature'       => $signature
    ];

    $params_json = json_encode($params);
    $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params_json)
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['status' => 'error', 'message' => 'Curl Error: ' . $err]);
        exit;
    }

    $result = json_decode($response, true);

    if (isset($result['statusCode']) && $result['statusCode'] === '00') {
        $qrString = $result['qrString'];
        $reference= $result['reference'];
        mysqli_query($koneksi, "UPDATE transaksi SET deskripsi = 'Ref: $reference' WHERE id = $id_transaksi_lokal");

        echo json_encode([
            'status' => 'success',
            'id_transaksi' => $id_transaksi_lokal,
            'qr_string' => $qrString
        ]);
    } else {
        $msg = $result['statusMessage'] ?? 'Gagal generate QRIS dari Duitku';
        echo json_encode(['status' => 'error', 'message' => $msg]);
    }
    exit;
}

// ---------------------------------------------------------------------------
// BAGIAN 1B: PROSES HAPUS REGISTRASI
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_delete'])) {
    header('Content-Type: application/json');
    $id = (int)$_POST['id'];
    
    $query = "DELETE FROM transaksi WHERE id = $id";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'message' => 'Data registrasi berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . mysqli_error($koneksi)]);
    }
    exit;
}

// ---------------------------------------------------------------------------
// BAGIAN 2: AJAX ENDPOINT (GET STATUS KELAS & REGISTRASI)
// ---------------------------------------------------------------------------
if (isset($_GET['get_status_kelas'])) {
    header('Content-Type: application/json');
    $id_req = (int)$_GET['get_status_kelas'];
    
    if (!array_key_exists($id_req, $kelas_mb)) {
        echo json_encode(['error' => 'Kelas tidak ditemukan']);
        exit;
    }
    
    $nama_kelas_req = $kelas_mb[$id_req]['nama'];
    
    // Hanya ambil transaksi yang tidak batal
    $query_terisi = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE kelas_lomba = '$nama_kelas_req' AND status_pembayaran != 'batal'");
    $terisi_lunas = [];
    $registrasi = [];

    while ($row = mysqli_fetch_assoc($query_terisi)) {
        $pecah = explode(',', $row['nomor_gantangan']);
        foreach ($pecah as $no) {
            if (trim($no) != "") {
                $terisi_lunas[] = (int)trim($no);
            }
        }
        
        $registrasi[] = [
            'id' => $row['id'],
            'nama_peserta' => $row['nama_peserta'],
            'nama_burung' => $row['nama_burung'],
            'alamat' => $row['alamat'],
            'metode_pembayaran' => $row['metode_pembayaran'],
            'status_pembayaran' => $row['status_pembayaran'],
            'nomor_gantangan' => $row['nomor_gantangan']
        ];
    }
    
    $registrasi = array_reverse($registrasi);
    
    echo json_encode([
        'harga' => $kelas_mb[$id_req]['harga'],
        'gantangan_lunas' => $terisi_lunas,
        'data_registrasi' => $registrasi,
        'is_admin' => $is_admin
    ]);
    exit;
}

// Ambil data default awal (Sesi pertama MB Non Gembung: Sesi 5)
$id_awal = 5;
$nama_kelas_awal = $kelas_mb[$id_awal]['nama'];
$harga_awal = $kelas_mb[$id_awal]['harga'];

$query_terisi_awal = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE kelas_lomba = '$nama_kelas_awal' AND status_pembayaran != 'batal' ORDER BY id DESC");
$gantangan_lunas_db = [];
$registrasi_awal = [];

while ($row = mysqli_fetch_assoc($query_terisi_awal)) {
    $pecah = explode(',', $row['nomor_gantangan']);
    foreach ($pecah as $no) {
        if (trim($no) != "") {
            $gantangan_lunas_db[] = (int)trim($no);
        }
    }
    $registrasi_awal[] = $row;
}

// Panggil Header untuk memunculkan Navbar & CSS utama
include 'header.php';
?>

<!-- Tambahkan sedikit margin-top agar tidak tertutup navbar fixed -->
<div class="main-wrapper" style="margin-top: 100px;">
    <div class="left-column">

        <div class="seat-section">
            <a class="back-link" href="paket.php">← Kembali ke Utama</a>
            <h2>Area Pemilihan Gantangan - MB Non Gembung</h2>

            <div class="select-kelas-box">
                <label for="id_kelas_pilih">Silakan Pilih Sesi / Kelas Lomba:</label>
                <select id="id_kelas_pilih" onchange="muatUlangKomponenKelas(this.value)">
                    <?php foreach($kelas_mb as $id => $item): ?>
                        <option value="<?php echo $id; ?>" <?php echo ($id === $id_awal) ? 'selected' : ''; ?>>
                            <?php echo $item['nama']; ?> (Tiket: Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="legend">
                <div class="legend-item"><div class="box available"></div> Tersedia</div>
                <div class="legend-item"><div class="box selected"></div> Dipilih </div>
                <div class="legend-item"><div class="box occupied"></div> Terisi / Lunas </div>
            </div>

            <div class="seat-map" id="seat-map"></div>
        </div>

        <!-- ========================================== -->
        <!-- TARUH PENGECEKAN ADMIN DI SINI             -->
        <!-- ========================================== -->
        <?php if ($is_admin): ?>
        <div class="registration-section" id="registration-section" style="display: <?php echo (count($registrasi_awal) > 0) ? 'block' : 'none'; ?>;">
            <h2>Data Registrasi Peserta (Kelas Ini)</h2>
            <div id="registration-list">
                <?php foreach ($registrasi_awal as $reg): ?>
                    <div class="reg-card">
                        <div class="reg-info">
                            <h4><?php echo htmlspecialchars($reg['nama_peserta']); ?></h4>
                            <p><strong>Burung:</strong> <?php echo htmlspecialchars($reg['nama_burung']); ?></p>
                            <p><strong>Alamat:</strong> <?php echo htmlspecialchars($reg['alamat']); ?></p>
                            <p><strong>Status:</strong> 
                                <?php if (strtolower($reg['status_pembayaran']) === 'lunas'): ?>
                                    <span style="color: #0d9488; font-weight: bold;">Lunas (<?php echo htmlspecialchars($reg['metode_pembayaran']); ?>)</span>
                                <?php else: ?>
                                    <span style="color: #d97706; font-weight: bold;">Pending Pembayaran</span>
                                <?php endif; ?>
                            </p>
                            
                            <div style="margin-top:8px">
                                <button onclick="handleDelete(this)" data-id="<?php echo $reg['id']; ?>" class="btn-primary" style="display:inline-block;padding:6px 10px;font-size:12px;background:#e11d48;border-color:#e11d48; width:auto;">Hapus</button>
                            </div>
                        </div>
                        <div class="reg-seat">
                            <span>No. Gantangan</span>
                            <strong><?php echo $reg['nomor_gantangan']; ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <!-- ========================================== -->

    </div>

    <aside class="side-booking">
        <div class="floating-card">
            <div class="card-price">
                <span class="amount" id="display-total">Rp0</span>
                <span class="unit" id="display-unit">Total Harga (Rp <?php echo number_format($harga_awal, 0, ',', '.'); ?> / gantangan)</span>
            </div>
            
            <form id="form-transaksi">
                <input type="hidden" name="action_booking" value="1">
                <input type="hidden" name="kelas_lomba" id="hidden-nama-kelas" value="<?php echo $nama_kelas_awal; ?>">
                <input type="hidden" name="id_kelas" id="hidden-id-kelas" value="<?php echo $id_awal; ?>">

                <div class="form-group">
                    <label>Nama Peserta</label>
                    <input type="text" name="nama" id="input-nama" placeholder="Masukkan nama" required>
                </div>
                <div class="form-group">
                    <label>Nama Burung</label>
                    <input type="text" name="burung" id="input-burung" placeholder="Masukkan nama burung" required>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" name="alamat" id="input-alamat" placeholder="Masukkan alamat" required>
                </div>

                <div class="form-group">
                    <label>Jumlah Gantangan</label>
                    <input type="number" name="jumlah" id="input-jumlah" value="0" readonly>
                </div>
                <div class="form-group">
                    <label>Nomor Gantangan Terpilih</label>
                    <input type="text" name="nomor" id="input-nomor" placeholder="Belum ada yang dipilih" readonly>
                </div>
                
                <!-- PILIHAN METODE PEMBAYARAN KHUSUS ADMIN -->
                <?php if($is_admin): ?>
                    <div class="form-group">
                        <label style="color: #34d399;"><i class="fas fa-user-shield"></i> Metode Pembayaran (Admin)</label>
                        <select name="metode_pembayaran" id="input-metode" style="width: 100%; padding: 10px; border-radius: 8px; background: rgba(0,0,0,0.2); border: 1px solid #4b5563; color: white;">
                            <option value="Cash/Transfer">Cash / Transfer </option>
                            <option value="QRIS Duitku">QRIS Duitku </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi </label>
                        <input type="text" name="deskripsi" id="input-deskripsi" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn-primary" id="btn-submit-pesan">Proses Pesanan</button>
                <?php else: ?>
                    <!-- Form untuk user biasa (Hanya QRIS) -->
                    <input type="hidden" name="metode_pembayaran" value="QRIS Duitku">
                    <button type="submit" class="btn-primary" id="btn-submit-pesan">Pesan & Bayar QRIS</button>
                <?php endif; ?>
                
            </form>
        </div>
    </aside>
</div>

<script>
    let HARGA_PER_KURSI = <?php echo $harga_awal; ?>;
    const TOTAL_GANTANGAN = 24; 
    
    let kursiLunas = <?php echo json_encode($gantangan_lunas_db); ?>; 
    let kursiTerpilihArr = [];

    const seatMapContainer = document.getElementById('seat-map');
    const displayTotal = document.getElementById('display-total');
    const displayUnit = document.getElementById('display-unit');
    const inputJumlah = document.getElementById('input-jumlah');
    const inputNomor = document.getElementById('input-nomor');
    const hiddenNamaKelas = document.getElementById('hidden-nama-kelas');
    const hiddenIdKelas = document.getElementById('hidden-id-kelas');
    const registrationSection = document.getElementById('registration-section');
    const listContainer = document.getElementById('registration-list');

    const CURRENT_FILE_URL = window.location.pathname;

    const daftarNamaKelas = <?php 
        $js_array = [];
        foreach($kelas_mb as $id => $item) { $js_array[$id] = $item['nama']; }
        echo json_encode($js_array);
    ?>;

    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function renderGantanganMap() {
        seatMapContainer.innerHTML = ""; 
        for (let i = 1; i <= TOTAL_GANTANGAN; i++) {
            const divKursi = document.createElement('div');
            divKursi.innerText = i;

            if (kursiLunas.includes(i)) {
                divKursi.className = 'seat occupied';
            } else {
                divKursi.className = 'seat available';
                if (kursiTerpilihArr.includes(i)) { divKursi.classList.add('selected'); }
                
                divKursi.addEventListener('click', () => {
                    divKursi.classList.toggle('selected');
                    if (divKursi.classList.contains('selected')) {
                        kursiTerpilihArr.push(i);
                    } else {
                        kursiTerpilihArr = kursiTerpilihArr.filter(id => id !== i);
                    }
                    updateTotal();
                });
            }
            seatMapContainer.appendChild(divKursi);
        }
    }

    function updateTotal() {
        const totalHarga = kursiTerpilihArr.length * HARGA_PER_KURSI;
        displayTotal.innerText = formatRupiah(totalHarga);
        inputJumlah.value = kursiTerpilihArr.length;
        inputNomor.value = kursiTerpilihArr.sort((a, b) => a - b).join(', ');
    }

    function muatUlangKomponenKelas(idKelas) {
        fetch(`${CURRENT_FILE_URL}?get_status_kelas=${idKelas}`)
        .then(res => res.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }

            HARGA_PER_KURSI = parseInt(data.harga);
            kursiLunas = data.gantangan_lunas;
            kursiTerpilihArr = []; 
            
            hiddenIdKelas.value = idKelas;
            hiddenNamaKelas.value = daftarNamaKelas[idKelas];

            displayUnit.innerText = `Total Harga (${formatRupiah(HARGA_PER_KURSI)} / gantangan)`;
            updateTotal();
            renderGantanganMap();

            // Hanya perbarui list registrasi jika user adalah admin
            if (data.is_admin && listContainer) {
                listContainer.innerHTML = "";
                if (data.data_registrasi.length > 0) {
                    if(registrationSection) registrationSection.style.display = 'block';
                    data.data_registrasi.forEach(reg => {
                        let statusLabel = reg.status_pembayaran.toLowerCase() === 'lunas' 
                            ? `<span style="color: #0d9488; font-weight: bold;">Lunas (${reg.metode_pembayaran})</span>` 
                            : '<span style="color: #d97706; font-weight: bold;">Pending Pembayaran</span>';
                        
                        let btnHapus = `<div style="margin-top:8px;"><button onclick="handleDelete(this)" data-id="${reg.id}" class="btn-primary" style="display:inline-block;padding:6px 10px;font-size:12px;background:#e11d48;border-color:#e11d48; width:auto; margin-top:0;">Hapus</button></div>`;

                        const itemCard = document.createElement('div');
                        itemCard.className = 'reg-card';
                        itemCard.innerHTML = `
                            <div class="reg-info">
                                <h4>${escapeHTML(reg.nama_peserta)}</h4>
                                <p><strong>Burung:</strong> ${escapeHTML(reg.nama_burung)}</p>
                                <p><strong>Alamat:</strong> ${escapeHTML(reg.alamat)}</p>
                                <p><strong>Status:</strong> ${statusLabel}</p>
                                ${btnHapus}
                            </div>
                            <div class="reg-seat">
                                <span>No. Gantangan</span>
                                <strong>${reg.nomor_gantangan}</strong>
                            </div>`;
                        listContainer.appendChild(itemCard);
                    });
                } else {
                    if(registrationSection) registrationSection.style.display = 'none';
                }
            }
        })
        .catch(err => console.error(err));
    }

    function escapeHTML(str) {
        if(!str) return '';
        return str.toString().replace(/[&<>'"]/g, tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag));
    }

    // Integrasi Form submit langsung meminta QRIS Duitku ATAU Input Cash
    document.getElementById('form-transaksi').addEventListener('submit', function(e) {
        e.preventDefault();
        if (kursiTerpilihArr.length === 0) { alert('Silakan pilih minimal 1 gantangan!'); return; }

        const btnSubmit = document.getElementById('btn-submit-pesan');
        const teksAsli = btnSubmit.innerText;
        btnSubmit.disabled = true;
        btnSubmit.innerText = 'Memproses...';

        const formData = new FormData(this);
        fetch(CURRENT_FILE_URL, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            btnSubmit.disabled = false;
            btnSubmit.innerText = teksAsli;

            if (data.status === 'success_cash') {
                // Berhasil untuk metode Cash / Transfer (Admin)
                alert(data.message);
                window.location.reload();
            } else if (data.status === 'success') {
                // Berhasil generate QRIS
                window.location.href = `qris.php?id=${data.id_transaksi}&qr=${encodeURIComponent(data.qr_string)}`;
            } else {
                alert('Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error(error);
            btnSubmit.disabled = false;
            btnSubmit.innerText = teksAsli;
        });
    });

    function handleDelete(btn) {
        if (!confirm('Yakin ingin menghapus data ini?')) return;
        const id = btn.dataset.id;
        const fd = new FormData();
        fd.append('action_delete', '1');
        fd.append('id', id);
        
        fetch(CURRENT_FILE_URL, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            alert(res.message);
            if (res.status === 'success') { muatUlangKomponenKelas(hiddenIdKelas.value); }
        })
        .catch(err => { console.error(err); alert('Terjadi kesalahan'); });
    }

    renderGantanganMap();
</script>

<?php include 'footer.php'; ?>
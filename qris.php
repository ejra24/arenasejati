<?php
include 'config/koneksi.php';
date_default_timezone_set('Asia/Makassar'); 

// ---------------------------------------------------------
// PROSES AJAX UNTUK BATALKAN TRANSAKSI (WAKTU HABIS / MANUAL)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_cancel'])) {
    header('Content-Type: application/json');
    $id_batal = (int)$_POST['id'];
    
    $query_batal = "UPDATE transaksi SET status_pembayaran = 'batal' WHERE id = $id_batal AND status_pembayaran = 'pending'";
    if (mysqli_query($koneksi, $query_batal)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// ---------------------------------------------------------
// TAMPILAN HALAMAN QRIS ASLI (DUITKU SANDBOX)
// ---------------------------------------------------------
if (!isset($_GET['id'])) {
    echo "<script>alert('ID Transaksi tidak valid!'); window.location.href='paket.php';</script>";
    exit;
}

$id_transaksi = (int)$_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id = $id_transaksi");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data transaksi tidak ditemukan!'); window.location.href='paket.php';</script>";
    exit;
}

if ($data['status_pembayaran'] === 'Lunas') {
    echo "<script>alert('Pembayaran ini sudah LUNAS!'); window.location.href='paket.php';</script>";
    exit;
} else if ($data['status_pembayaran'] === 'batal') {
    echo "<script>alert('Waktu pembayaran telah habis atau dibatalkan.'); window.location.href='paket.php';</script>";
    exit;
}

$waktu_expired_timestamp = strtotime($data['waktu_expired']) * 1000; 

// Ambil string QR dari deskripsi atau simpanan sementara (atau kita ambil ulang via API jika perlu)
// Karena kita butuh qrString dari Duitku, kita bisa simpan sementara di session atau ambil dari kolom database jika nanti ditambahkan. 
// Untuk amannya, kita sediakan fallback URL QR dari string Duitku yang dilempar lewat URL/Session atau kita generate ulang.
$qrString = isset($_GET['qr']) ? $_GET['qr'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS Duitku - Arena Sejati</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .qris-wrapper { display: flex; justify-content: center; align-items: center; min-height: 80vh; }
        .qris-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center; color: #333; max-width: 450px; width: 100%; }
        .qris-card h2 { border: none; margin-bottom: 5px; padding-bottom: 0; }
        .qris-card p { color: #666; font-size: 14px; margin-bottom: 20px; }
        .timer-box { background: #fef3c7; color: #d97706; padding: 10px 20px; border-radius: 8px; font-size: 24px; font-weight: bold; margin: 20px 0; border: 2px solid #fde68a; }
        .timer-box.danger { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        .qris-image { width: 250px; height: 250px; margin: 0 auto; border: 1px solid #eee; padding: 10px; border-radius: 12px; margin-bottom: 20px; }
        .total-pay { font-size: 28px; font-weight: 900; color: #111827; margin-bottom: 20px; }
        .btn-check { background: #059669; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; font-size: 16px; cursor: pointer; margin-bottom: 10px; }
        .btn-check:hover { background: #047857; }
        .btn-cancel { background: #ef4444; color: white; border: none; padding: 12px 20px; border-radius: 8px; width: 100%; font-weight: bold; font-size: 16px; cursor: pointer; }
        .btn-cancel:hover { background: #dc2626; }
    </style>
</head>
<body>

<div class="qris-wrapper">
    <div class="qris-card">
        <h2>Scan QRIS (Duitku Sandbox)</h2>
        <p>Kelas: <strong><?php echo htmlspecialchars($data['kelas_lomba']); ?></strong><br>
           Atas Nama: <strong><?php echo htmlspecialchars($data['nama_peserta']); ?></strong></p>
        
        <div class="timer-box" id="countdown-timer">10:00</div>

        <!-- Gambar QR Code Asli Dari Duitku -->
        <div id="qris-container">
            <img class="qris-image" id="qr-img" src="" alt="Loading QRIS...">
        </div>

        <div class="total-pay">Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></div>
        
        <p style="font-size: 12px; color: #999;">Gunakan aplikasi simulasi pembayaran atau m-Banking Anda untuk scan QRIS Sandbox ini.</p>

        <button class="btn-check" onclick="window.location.reload()">Cek Status Pembayaran</button>
        <button class="btn-cancel" id="btn-batalkan">Batalkan Pesanan</button>
    </div>
</div>

<script>
    // Tangkap data qrString dari parameter URL yang dikirim JavaScript sebelumnya
    const urlParams = new URLSearchParams(window.location.search);
    const rawQrString = urlParams.get('qr');

    if (rawQrString) {
        // Generate gambar QR dari string asli Duitku menggunakan api qrserver
        document.getElementById('qr-img').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(rawQrString)}`;
    } else {
        document.getElementById('qris-container').innerHTML = '<p style="color:red;">Gagal memuat QR Code. Silakan ulangi pemesanan.</p>';
    }

    const countDownDate = <?php echo $waktu_expired_timestamp; ?>;
    const timerElement = document.getElementById('countdown-timer');
    const idTransaksi = <?php echo $id_transaksi; ?>;

    function batalkanPesanan(auto = false) {
        if (!auto && !confirm('Yakin ingin membatalkan pesanan ini?')) return;

        const fd = new FormData();
        fd.append('action_cancel', '1');
        fd.append('id', idTransaksi);

        fetch(window.location.pathname + window.location.search, { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if(auto) alert('Waktu pembayaran telah habis. Pesanan otomatis dibatalkan.');
            window.location.href = 'cendet.php'; // Kembali ke halaman cendet
        })
        .catch(err => console.error(err));
    }

    document.getElementById('btn-batalkan').addEventListener('click', () => batalkanPesanan(false));

    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const m = minutes < 10 ? "0" + minutes : minutes;
        const s = seconds < 10 ? "0" + seconds : seconds;

        timerElement.innerHTML = m + ":" + s;

        if (distance < 120000) timerElement.classList.add('danger');

        if (distance < 0) {
            clearInterval(x);
            timerElement.innerHTML = "WAKTU HABIS";
            batalkanPesanan(true);
        }
    }, 1000);
</script>

</body>
</html>
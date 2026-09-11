<?php
// Pastikan koneksi database sudah terhubung
include 'config/koneksi.php';
date_default_timezone_set('Asia/Makassar');

// Kunci Akses dari Dashboard Duitku Sandbox
$merchantCode = 'DS35199'; // Ganti dengan Merchant Code lu
$apiKey       = '8567dfa3bc50ff93104ed5222b406b1d';       // Ganti dengan API Key lu

// Cek apakah data dikirim dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $nama_peserta     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nama_burung      = mysqli_real_escape_string($koneksi, $_POST['burung']);
    $alamat           = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kelas_lomba      = mysqli_real_escape_string($koneksi, $_POST['kelas_lomba']);
    $nomor_pilih      = mysqli_real_escape_string($koneksi, $_POST['nomor']);
    $jumlah_gantangan = (int)$_POST['jumlah'];
    $deskripsi        = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    if (empty($nomor_pilih)) {
        echo json_encode(['status' => 'error', 'message' => 'Pilih nomor gantangan dulu!']);
        exit;
    }

    // Tentukan harga per gantangan berdasarkan kelas
    // (Bisa disesuaikan dengan array kelas lu sebelumnya)
    $harga_satuan = 55000; 
    if (strpos($kelas_lomba, 'Marwah') !== false) { $harga_satuan = 125000; }
    else if (strpos($kelas_lomba, 'Napak') !== false) { $harga_satuan = 85000; }
    
    $total_harga = $jumlah_gantangan * $harga_satuan;

    // 1. Simpan dulu ke database lokal dengan status 'pending' & waktu expired 10 menit
    $waktu_pesan = date('Y-m-d H:i:s');
    $waktu_expired = date('Y-m-d H:i:s', strtotime('+10 minutes', strtotime($waktu_pesan)));

    $query_insert = "INSERT INTO transaksi 
                     (nama_peserta, nama_burung, alamat, kelas_lomba, metode_pembayaran, status_pembayaran, nomor_gantangan, jumlah_gantangan, total_harga, waktu_expired, deskripsi) 
                     VALUES 
                     ('$nama_peserta', '$nama_burung', '$alamat', '$kelas_lomba', 'QRIS Duitku', 'pending', '$nomor_pilih', '$jumlah_gantangan', '$total_harga', '$waktu_expired', '$deskripsi')";

    if (!mysqli_query($koneksi, $query_insert)) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal simpan database: ' . mysqli_error($koneksi)]);
        exit;
    }

    $id_transaksi_lokal = mysqli_insert_id($koneksi);
    $merchantOrderId    = 'ARENA-' . $id_transaksi_lokal . '-' . time(); // ID Unik pesanan

    // 2. Siapkan data untuk dikirim ke API Duitku (Meminta QRIS)
    $paymentAmount = $total_harga;
    $email         = 'kicaumania@arenasejati.com';
    $phone         = '081234567890';
    $productDetails= 'Tiket Lomba ' . $kelas_lomba . ' No: ' . $nomor_pilih;
    
    // Signature keamanan wajib dari Duitku: md5(merchantCode + merchantOrderId + paymentAmount + apiKey)
    $signature     = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

    $params = [
        'merchantCode'    => $merchantCode,
        'paymentAmount'   => $paymentAmount,
        'merchantOrderId' => $merchantOrderId,
        'productDetails'  => $productDetails,
        'email'           => $email,
        'phoneNumber'     => $phone,
        'paymentMethod'   => 'SP', // Kode untuk QRIS di Duitku
        'callbackUrl'     => 'http://127.0.0.1/kasirBurung/callback_duitku.php', // File penerima status lunas otomatis
        'returnUrl'       => 'http://127.0.0.1/kasirBurung/sukses.php',
        'expiryPeriod'    => 10, // Berlaku 10 menit
        'signature'       => $signature
    ];

    $params_json = json_encode($params);

    // URL API Duitku Sandbox untuk Inquiry Request (Membuat Transaksi)
    $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($params_json)
    ]);
    
    // Matikan SSL verify khusus di localhost (agar tidak error curl 60)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['status' => 'error', 'message' => 'Curl Error: ' . $err]);
        exit;
    }

    $result = json_decode($response, true);

    // Cek apakah permintaan QRIS ke Duitku berhasil
    if (isset($result['statusCode']) && $result['statusCode'] === '00') {
        $qrString = $result['qrString']; // Teks QRIS / Link QR Code dari Duitku
        $reference= $result['reference']; // Kode referensi transaksi Duitku

        // Update data transaksi lokal dengan reference dari Duitku
        mysqli_query($koneksi, "UPDATE transaksi SET deskripsi = CONCAT(deskripsi, ' | Ref: $reference') WHERE id = $id_transaksi_lokal");

        // Lempar respon sukses beserta string QR-nya ke Javascript
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
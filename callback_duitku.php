<?php
// Include koneksi database
include 'config/koneksi.php';

// Ambil kunci API Key dari Duitku untuk validasi keamanan callback
$apiKey = '8567dfa3bc50ff93104ed5222b406b1d'; // <-- Ganti dengan API Key Duitku lu yang asli

// Ambil data JSON yang dikirim oleh server Duitku
$data = file_get_contents("php://input");
$callbackData = json_decode($data, true);

if (!empty($callbackData)) {
    $merchantOrderId = $callbackData['merchantOrderId'] ?? '';
    $resultCode      = $callbackData['resultCode'] ?? '';
    $amount          = $callbackData['amount'] ?? '';
    $reference       = $callbackData['reference'] ?? '';
    $signature       = $callbackData['signature'] ?? '';

    // Validasi Signature untuk memastikan ini benar-benar dari server Duitku asli
    // Rumus signature dari Duitku: md5(merchantCode + amount + merchantOrderId + apiKey)
    // Atau kita bisa validasi sederhana menggunakan API Key
    
    // Pecah merchantOrderId untuk mendapatkan ID transaksi lokal kita
    // Format merchantOrderId kita sebelumnya: ARENA-[id_transaksi]-[timestamp]
    $pecahId = explode('-', $merchantOrderId);
    $id_transaksi = $pecahId[1] ?? 0;

    if ($id_transaksi > 0) {
        // Cek data transaksi di database
        $query_cek = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id = $id_transaksi");
        $transaksi = mysqli_fetch_assoc($query_cek);

        if ($transaksi) {
            // Pastikan status sebelumnya masih pending (belum diproses ganda)
            if ($transaksi['status_pembayaran'] === 'pending') {
                
                // Result Code '00' artinya BERHASIL / LUNAS
                if ($resultCode == '00') {
                    $update = "UPDATE transaksi SET status_pembayaran = 'Lunas' WHERE id = $id_transaksi";
                    if (mysqli_query($koneksi, $update)) {
                        http_response_code(200);
                        echo json_encode(['status' => 'success', 'message' => 'Callback diterima, status diubah jadi Lunas']);
                        exit;
                    }
                } 
                // Result Code selain '00' (misal batal/gagal)
                else if ($resultCode == '01') {
                    $update = "UPDATE transaksi SET status_pembayaran = 'batal' WHERE id = $id_transaksi";
                    mysqli_query($koneksi, $update);
                    http_response_code(200);
                    echo json_encode(['status' => 'success', 'message' => 'Transaksi dibatalkan oleh sistem']);
                    exit;
                }
            }
        }
    }
}

// Jika ada yang tidak valid
http_response_code(400);
echo json_encode(['status' => 'error', 'message' => 'Bad Request / Invalid Signature']);
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    echo "Akses ditolak! Anda harus login sebagai admin.";
    exit;
}

include 'config/koneksi.php';

$id_transaksi = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id = $id_transaksi");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data transaksi tidak ditemukan.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembelian - #<?php echo $data['id']; ?></title>
    <style>
        body { font-family: monospace; width: 300px; margin: 0 auto; color: #000; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; font-size: 12px; }
        table td { padding: 4px 0; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h3>ARENA SEJATI SAMARINDA</h3>
        <p style="font-size: 11px; margin: 0;">Struk Resmi Tiket Lomba Burung</p>
    </div>
    
    <div class="line"></div>
    
    <table>
        <tr>
            <td>ID Transaksi</td>
            <td>: #<?php echo $data['id']; ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?php echo $data['waktu_expired']; ?></td>
        </tr>
        <tr>
            <td>Peserta</td>
            <td>: <?php echo htmlspecialchars($data['nama_peserta']); ?></td>
        </tr>
        <tr>
            <td>Burung</td>
            <td>: <?php echo htmlspecialchars($data['nama_burung']); ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>: <?php echo htmlspecialchars($data['kelas_lomba']); ?></td>
        </tr>
        <tr>
            <td>No. Gantangan</td>
            <td>: <strong><?php echo $data['nomor_gantangan']; ?></strong></td>
        </tr>
        <tr>
            <td>Metode</td>
            <td>: <?php echo htmlspecialchars($data['metode_pembayaran']); ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: <strong>
                <?php 
                $st = strtolower(trim($data['status_pembayaran']));
                if ($st === 'lunas') {
                    echo 'LUNAS';
                } elseif ($st === 'dp') {
                    echo 'DP';
                } else {
                    echo strtoupper($data['status_pembayaran']);
                }
                ?>
            </strong></td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        <tr>
            <td><strong>Total Bayar</strong></td>
            <td style="text-align: right;"><strong>Rp <?php echo number_format($data['total_harga'], 0, ',', '.'); ?></strong></td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center" style="font-size: 11px;">
        <p>Terima Kasih Atas Partisipasinya<br>Salam Kicau Mania!</p>
    </div>

    <div class="text-center no-print" style="margin-top: 20px;">
        <button onclick="window.print()" style="padding: 5px 15px; cursor: pointer;">Cetak Ulang</button>
    </div>

</body>
</html>
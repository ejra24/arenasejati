<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    exit("Akses ditolak");
}

include 'config/koneksi.php';
$query = mysqli_query($koneksi, "SELECT * FROM transaksi ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Transaksi - Arena Sejati</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        h2, p { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #0d9488; color: white; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <h2>ARENA SEJATI SAMARINDA</h2>
    <p>Laporan Rekapitulasi Seluruh Transaksi Pendaftaran Lomba</p>
    <p style="font-size: 10px; color: #666;">Dicetak pada: <?php echo date('d-m-Y H:i:s'); ?></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peserta</th>
                <th>Burung</th>
                <th>Alamat</th>
                <th>Kelas Lomba</th>
                <th>No. Gantangan</th>
                <th>Metode</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($row = mysqli_fetch_assoc($query)): 
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nama_peserta']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_burung']); ?></td>
                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td><?php echo htmlspecialchars($row['kelas_lomba']); ?></td>
                <td><?php echo htmlspecialchars($row['nomor_gantangan']); ?></td>
                <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                <td><?php echo strtoupper($row['status_pembayaran']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0d9488; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak / Simpan PDF</button>
    </div>

</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])) {
    exit("Akses ditolak");
}

include 'config/koneksi.php';

// Header agar browser mendownload file sebagai Excel (.xls)
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment5; filename=Laporan_Transaksi_ArenaSejati_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$query = mysqli_query($koneksi, "SELECT * FROM transaksi ORDER BY id DESC");
?>
<table border="1">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>No</th>
            <th>Nama Peserta</th>
            <th>Nama Burung</th>
            <th>Alamat</th>
            <th>Kelas Lomba</th>
            <th>Nomor Gantangan</th>
            <th>Metode Pembayaran</th>
            <th>Total Harga</th>
            <th>Status Pembayaran</th>
            <th>Waktu Transaksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while($row = mysqli_fetch_assoc($query)): 
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nama_peserta']; ?></td>
            <td><?php echo $row['nama_burung']; ?></td>
            <td><?php echo $row['alamat']; ?></td>
            <td><?php echo $row['kelas_lomba']; ?></td>
            <td><?php echo $row['nomor_gantangan']; ?></td>
            <td><?php echo $row['metode_pembayaran']; ?></td>
            <td><?php echo $row['total_harga']; ?></td>
            <td><?php echo strtoupper($row['status_pembayaran']); ?></td>
            <td><?php echo $row['waktu_expired']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
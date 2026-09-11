<?php
session_start();      // Memulai session yang sedang aktif
session_unset();      // Menghapus semua variabel session
session_destroy();    // Menghancurkan session secara permanen

// Kembalikan pengguna ke halaman utama (paket.php) setelah keluar
header("Location: paket.php");
exit;
?>
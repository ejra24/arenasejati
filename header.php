<?php
// Pastikan session dimulai dengan aman di baris paling awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arena Sejati</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    .navbar {
        background: rgba(41, 27, 17, 0.9);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        padding: 0.8rem 0; 
        position: fixed;
        top: 0; 
        width: 100%; 
        z-index: 2000;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .nav-wrapper { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        width: 100%;
        padding: 0 20px;
        box-sizing: border-box;
    }

    /* Tiga Kolom Seimbang */
    .nav-left, .nav-right { 
        flex: 1; 
        display: flex; 
        align-items: center; 
    }
    .nav-left { justify-content: flex-start; }
    .nav-right { justify-content: flex-end; gap: 12px; }
    
    .nav-center { 
        flex: 1; 
        display: flex; 
        justify-content: center; 
    }

    .logo h1 { 
        color: #ffffff; 
        font-size: 1.4rem; 
        font-weight: 800; 
        letter-spacing: 1px;
        margin: 0;
        text-transform: uppercase;
        white-space: nowrap; /* Mencegah patah baris */
    }

    /* =========================================
       DROPDOWN MENU HAMBURGER
       ========================================= */
    .dropdown {
        position: relative;
        display: inline-block;
        padding-bottom: 12px; 
        margin-bottom: -12px; 
    }
    
    .hamburger-btn {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 5px 8px;
        transition: 0.3s;
        border-radius: 6px;
    }
    
    .hamburger-btn:hover {
        color: #f59e0b;
        background: rgba(255,255,255,0.05);
    }

    .dropdown-content {
        display: none;
        position: absolute;
        left: 0;
        top: 100%; 
        background-color: #291b11;
        min-width: 210px;
        box-shadow: 0px 10px 25px rgba(0,0,0,0.6);
        z-index: 100;
        border-radius: 8px;
        margin-top: 2px; 
        border: 1px solid rgba(255,255,255,0.15);
        overflow: hidden;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content a {
        color: white;
        padding: 12px 18px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        transition: 0.2s;
    }
    
    .dropdown-content a:hover {
        background-color: #d97706;
        color: white;
    }

    /* =========================================
       STYLING UNTUK POV KANAN
       ========================================= */
    .admin-badge {
        color: #34d399; 
        font-size: 0.75rem; 
        font-weight: bold; 
        border: 1px solid #34d399; 
        padding: 5px 10px; 
        border-radius: 6px;
        background: rgba(52, 211, 153, 0.1);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    
    .btn-logout-mini {
        color: #ef4444;
        font-size: 0.85rem;
        text-decoration: none;
        padding: 5px 8px;
        border-radius: 6px;
        border: 1px solid rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.05);
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
    }
    .btn-logout-mini:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    .btn-peraturan {
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.05);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
        white-space: nowrap;
    }
    
    .btn-peraturan:hover {
        background: #d97706;
        border-color: #d97706;
        color: white;
    }

    /* =========================================
       TAMPILAN MOBILE (RESPONSIVE)
       ========================================= */
    @media (max-width: 768px) {
        .logo h1 {
            font-size: 1.1rem; 
        }
        .btn-peraturan {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
    }

    @media (max-width: 480px) {
        .nav-wrapper {
            padding: 0 10px; 
        }
        .logo h1 {
            font-size: 1rem; 
        }
        .nav-right {
            gap: 6px; /* Jarak antar tombol dikurangi sedikit */
        }
        
        /* SEMBUNYIKAN TEKS DI HP (Sisa Ikon Saja) */
        .btn-peraturan .teks-tombol,
        .admin-badge .teks-admin {
            display: none; 
        }
        
        /* Rapikan padding jadi bentuk kotak */
        .btn-peraturan,
        .admin-badge {
            padding: 6px 10px; 
        }
    }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container nav-wrapper">
        
        <!-- BAGIAN KIRI -->
        <div class="nav-left">
            <div class="dropdown">
                <button class="hamburger-btn">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="dropdown-content">
                    <a href="paket.php#bagian-lomba"><i class="fas fa-list" style="width: 20px;"></i> Kategori</a>
                    <a href="#" onclick="document.getElementById('modalRiwayat').style.display='flex'; return false;">
                        <i class="fas fa-history" style="width: 20px;"></i> Riwayat Transaksi
                    </a>
                    
                    <?php if(isset($_SESSION['admin_username']) && !empty($_SESSION['admin_username'])): ?>
                        <!-- KHUSUS ADMIN -->
                        <a href="paket.php#peraturan-lomba"><i class="fas fa-scroll" style="width: 20px;"></i> Peraturan Lomba</a>
                    <?php endif; ?>
                    
                    <?php if(!isset($_SESSION['admin_username']) || empty($_SESSION['admin_username'])): ?>
                        <!-- Strip rahasia -->
                        <a href="login.php" style="color: rgba(255,255,255,0.25); justify-content: center; font-weight: bold; letter-spacing: 2px;">-</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- BAGIAN TENGAH -->
        <div class="nav-center">
            <div class="logo">
                <a href="paket.php" style="text-decoration: none;">
                    <h1>Arena Sejati</h1>
                </a>
            </div>
        </div>
        
        <!-- BAGIAN KANAN -->
        <div class="nav-right">
            <?php if(isset($_SESSION['admin_username']) && !empty($_SESSION['admin_username'])): ?>
                <!-- POV ADMIN -->
                <span class="admin-badge">
                    <i class="fas fa-user-shield"></i> 
                    <span class="teks-admin">Admin</span> <!-- Teks dibungkus agar bisa disembunyikan di HP -->
                </span>
                <a href="logout.php" class="btn-logout-mini" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <!-- POV PEMBELI -->
                <a href="paket.php#peraturan-lomba" class="btn-peraturan">
                    <i class="fas fa-scroll"></i> 
                    <span class="teks-tombol">Peraturan Lomba</span>
                </a>
            <?php endif; ?>
        </div>

    </div>
</nav>

</body>
</html>
<?php
// Hubungkan ke database
include 'config/koneksi.php';
$query_riwayat = mysqli_query($koneksi, "SELECT * FROM transaksi ORDER BY id DESC");

// Panggil Header
include 'header.php';
?>

<!-- KODE CSS KHUSUS HALAMAN UTAMA -->
<style>
/* 1. MENGATUR HERO SECTION */
.hero-section { 
    height: auto !important; 
    padding: 130px 0 50px 0 !important; 
    display: flex !important; 
    align-items: center; 
    justify-content: center;
}
.hero-container { 
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* 2. CSS SPONSOR BERGERAK */
.sponsor-section { 
    width: 100%; 
    padding: 25px 0; 
    background: rgba(0, 0, 0, 0.2); 
    margin-top: 10px; 
    overflow: hidden; 
    position: relative; 
    border-top: 1px solid rgba(255, 255, 255, 0.1); 
    border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
}
.sponsor-title { 
    text-align: center; 
    font-size: 0.9rem; 
    text-transform: uppercase; 
    letter-spacing: 2px; 
    color: #9ca3af; 
    margin-bottom: 20px; 
    font-weight: 600; 
}
.marquee-container { 
    display: flex; 
    width: 100%; 
    overflow: hidden; 
    position: relative; 
}
.marquee-container::before, .marquee-container::after { 
    content: ''; 
    position: absolute; 
    top: 0; 
    width: 80px; 
    height: 100%; 
    z-index: 2; 
    pointer-events: none; 
}
.marquee-container::before { 
    left: 0; 
    background: linear-gradient(to right, #291b11, transparent); 
}
.marquee-container::after { 
    right: 0; 
    background: linear-gradient(to left, #291b11, transparent); 
}
.marquee-track { 
    display: flex; 
    flex-direction: row; 
    gap: 30px; 
    align-items: center; 
    width: max-content; 
    animation: scrollMarquee 25s linear infinite; 
}
.marquee-container:hover .marquee-track { 
    animation-play-state: paused; 
}
.sponsor-logo { 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    min-width: 150px; 
    height: 65px; 
    background: rgba(255, 255, 255, 0.05); 
    border: 1px solid rgba(255, 255, 255, 0.1); 
    border-radius: 10px; 
    padding: 10px 15px; 
    flex-shrink: 0; 
    transition: all 0.3s ease; 
}

/* 3. WARNA & UKURAN LOGO SPONSOR */
.sponsor-logo img { 
    width: 110px; 
    height: 40px; 
    object-fit: contain; 
    filter: none !important; 
    opacity: 1 !important; 
    transition: all 0.3s ease; 
}
.sponsor-logo:hover { 
    background: rgba(255, 255, 255, 0.1); 
    border-color: rgba(245, 158, 11, 0.5); 
    transform: translateY(-3px); 
}
.sponsor-logo:hover img { 
    transform: scale(1.15); 
}
@keyframes scrollMarquee { 
    0% { transform: translateX(0); } 
    100% { transform: translateX(-50%); } 
}

/* 4. PEMBATAS CONTAINER AGAR TIDAK KEBANYAKAN MELEBAR */
.container-utama {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Styling Tambahan untuk Peraturan di Dashboard */
    .dashboard-rules-section {
        max-width: 1200px;
        margin: 60px auto 40px auto;
        padding: 0 20px;
        color: #f3f4f6;
    }
    .rules-section-title {
        text-align: center;
        font-size: 1.8rem;
        color: #f59e0b;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .pakem-box-dash {
        background: rgba(41, 27, 17, 0.9);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .pakem-title-dash {
        text-align: center;
        font-size: 1.3rem;
        color: #f59e0b;
        margin-bottom: 20px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .pakem-grid-dash {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
    }
    .pakem-item-dash {
        background: #1e130c;
        border: 1px solid rgba(255,255,255,0.08);
        padding: 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .pakem-number-dash {
        background: #d97706;
        color: white;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pakem-text-dash h4 {
        margin: 0;
        font-size: 0.95rem;
        color: #fff;
    }
    .bird-rules-grid-dash {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .bird-card-dash {
        background: rgba(41, 27, 17, 0.85);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.4);
    }
    .bird-card-dash h3 {
        color: #f59e0b;
        border-bottom: 1px solid rgba(245, 158, 11, 0.3);
        padding-bottom: 10px;
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }
    .bird-card-dash ol {
        margin: 0;
        padding-left: 20px;
        font-size: 0.9rem;
        line-height: 1.6;
        color: #e5e7eb;
    }
    .bird-card-dash li {
        margin-bottom: 8px;
    }
    .nb-box-dash {
        background: rgba(217, 119, 6, 0.15);
        border-left: 4px solid #d97706;
        padding: 15px 20px;
        border-radius: 0 8px 8px 0;
        font-size: 0.95rem;
        margin-top: 20px;
        color: #fef3c7;
    }
    .nb-box-dash strong {
        color: #f59e0b;
    }

</style>

<!-- HERO SECTION -->
<section class="hero-section">
    <div class="hero-container">
        <div class="hero-badge"><i class="fas fa-trophy"></i> Official Arena Lomba Burung</div>
        <h1 class="hero-title">Arena Sejati</h1>
        <p class="hero-desc">Situs resmi pendaftaran gantangan, cek jadwal sesi lomba, dan pantau riwayat transaksi secara *realtime*.</p>
        <a href="#bagian-lomba" class="hero-btn">Mulai Pilih Kategori <i class="fas fa-arrow-down"></i></a>
    </div>
</section>

<!-- SPONSOR BERGERAK -->
<div class="sponsor-section">
    <div class="sponsor-title">Didukung Oleh Brand & Partner Resmi</div>
    <div class="marquee-container">
        <div class="marquee-track">
            <!-- SET 1 -->
            <div class="sponsor-logo"><img src="uploads/logo_media_sejati.png" alt="Sponsor 1"></div>
            <div class="sponsor-logo"><img src="uploads/CV.MOZA.png" alt="Sponsor 2"></div>
            <div class="sponsor-logo"><img src="uploads/logo_ASN.png" alt="Sponsor 3"></div>
            <div class="sponsor-logo"><img src="uploads/logo_BOS-6.png" alt="Sponsor 4"></div>
            <div class="sponsor-logo"><img src="uploads/logo_kilau3.png" alt="Sponsor 5"></div>
            <div class="sponsor-logo"><img src="uploads/logo_SEJATI8.png" alt="Sponsor 6"></div>
            <div class="sponsor-logo"><img src="uploads/Logo_Sejati_Cafe_n_Resto_Gold-3.png" alt="Sponsor 7"></div>
            <div class="sponsor-logo"><img src="uploads/logo_sejati_store2.png" alt="Sponsor 8"></div>
            <div class="sponsor-logo"><img src="uploads/logo_ssn.png" alt="Sponsor 9"></div>
            <div class="sponsor-logo"><img src="uploads/logo_tanah_merah3.png" alt="Sponsor 10"></div>

            <!-- SET 2 (DUPLIKAT UNTUK LOOPING) -->
            <div class="sponsor-logo"><img src="uploads/logo_media_sejati.png" alt="Sponsor 1"></div>
            <div class="sponsor-logo"><img src="uploads/CV.MOZA.png" alt="Sponsor 2"></div>
            <div class="sponsor-logo"><img src="uploads/logo_ASN.png" alt="Sponsor 3"></div>
            <div class="sponsor-logo"><img src="uploads/logo_BOS-6.png" alt="Sponsor 4"></div>
            <div class="sponsor-logo"><img src="uploads/logo_kilau3.png" alt="Sponsor 5"></div>
            <div class="sponsor-logo"><img src="uploads/logo_SEJATI8.png" alt="Sponsor 6"></div>
            <div class="sponsor-logo"><img src="uploads/Logo_Sejati_Cafe_n_Resto_Gold-3.png" alt="Sponsor 7"></div>
            <div class="sponsor-logo"><img src="uploads/logo_sejati_store2.png" alt="Sponsor 8"></div>
            <div class="sponsor-logo"><img src="uploads/logo_ssn.png" alt="Sponsor 9"></div>
            <div class="sponsor-logo"><img src="uploads/logo_tanah_merah3.png" alt="Sponsor 10"></div>
        </div>
    </div>
</div>

<!-- KONTEN UTAMA & PENCARIAN -->
<div id="bagian-lomba">
    <div class="container-utama">
        <div class="search-section" style="margin-top: 40px;">
            <h2>Pencarian Pintar Sesi Lomba</h2>
            <p>Masukkan nomor sesi (1-24) dari brosur Napak Tilas Marwah Murai Borneo untuk mengetahui kategori burung, harga tiket, dan langsung pesan gantangan.</p>
            <div class="search-box">
                <input type="number" id="search-sesi" oninput="cariSesiBrosur(this.value)" placeholder="Ketik nomor urutan jadwal lomba... (Contoh: 9)">
            </div>
            <div class="search-result" id="hasil-pencarian"></div>
        </div>
    </div>

    <section id="paket" class="paket">
        <div class="container-utama">
            <div class="section-header">
                <h2 class="section-title">Pilih Kategori Burung</h2>
            </div>

            <div class="paket-grid">
                
                <div class="paket-card">
                    <div class="paket-image">
                        <img src="https://i.pinimg.com/736x/01/1f/d7/011fd776013418aa25bd63ab41d6f3ae.jpg" alt="Murai Borneo">
                        <div class="paket-price-tag">Rp55k - Rp560k</div>
                    </div>
                    <div class="paket-content">
                        <h3>1. Murai Borneo</h3>
                        <a href="murai.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

                <div class="paket-card">
                    <div class="paket-image">
                        <img src="https://i.pinimg.com/736x/af/10/e6/af10e61d244449c14084d34d8df59f3c.jpg" alt="Mb Non Gembung">
                        <div class="paket-price-tag">Rp55k - Rp560k</div>
                    </div>
                    <div class="paket-content">
                        <h3>2. MB Non Gembung</h3>
                        <a href="mbnongembung.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

                <div class="paket-card">
                    <div class="paket-image">
                        <img src="https://i.pinimg.com/1200x/a8/d2/1a/a8d21ac506ff3c463d8863a94eb0f478.jpg" alt="Cucak Ijo">
                        <div class="paket-price-tag">Rp55k - Rp220k</div>
                    </div>
                    <div class="paket-content">
                        <h3>3. Cucak Ijo</h3>
                        <a href="cucakijo.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

                <div class="paket-card">
                    <div class="paket-image">
                        <img src="https://i.pinimg.com/736x/fd/4f/68/fd4f68b34f3f850069e6dc65ffb44f41.jpg" alt="Kacer">
                        <div class="paket-price-tag">Rp55k - Rp125k</div>
                    </div>
                    <div class="paket-content">
                        <h3>4. Kacer</h3>
                        <a href="kacer.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

                <div class="paket-card">
                    <div class="paket-image">
                        <img src="https://i.pinimg.com/736x/4b/1b/d7/4b1bd777906cc2e834be2912eb6fecab.jpg" alt="Cendet">
                        <div class="paket-price-tag">Rp55k - Rp125k</div>
                    </div>
                    <div class="paket-content">
                        <h3>5. Cendet</h3>
                        <a href="cendet.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

                <div class="paket-card">
                    <div class="paket-image">
                        <img src="uploads/burungkonin.png" alt="Konin">
                        <div class="paket-price-tag">Rp55k - Rp124k</div>
                    </div>
                    <div class="paket-content">
                        <h3>6. Konin</h3>
                        <a href="konin.php" class="paket-link">Lihat Kelas Lomba <span>→</span></a>
                    </div>
                </div>

            </div>
        </div>

        <!-- BAGIAN PERATURAN & PENILAIAN LOMBA DI DASHBOARD -->
<div class="dashboard-rules-section" id="peraturan-lomba">
    <div class="rules-section-title"><i class="fas fa-scroll"></i> Peraturan & Penilaian Lomba</div>

    <!-- PAKEM PENILAIAN -->
    <div class="pakem-box-dash">
        <div class="pakem-title-dash"><i class="fas fa-award"></i> Pakem Penilaian Utama</div>
        <div class="pakem-grid-dash">
            <div class="pakem-item-dash">
                <div class="pakem-number-dash">1</div>
                <div class="pakem-text-dash"><h4>Irama Lagu / Variasi</h4></div>
            </div>
            <div class="pakem-item-dash">
                <div class="pakem-number-dash">2</div>
                <div class="pakem-text-dash"><h4>Durasi Kerja</h4></div>
            </div>
            <div class="pakem-item-dash">
                <div class="pakem-number-dash">3</div>
                <div class="pakem-text-dash"><h4>Volume</h4></div>
            </div>
            <div class="pakem-item-dash">
                <div class="pakem-number-dash">4</div>
                <div class="pakem-text-dash"><h4>Gaya dan Fisik</h4></div>
            </div>
        </div>
    </div>

    <!-- ATURAN PELANGGARAN PER KATEGORI -->
    <h3 style="color: #fff; font-size: 1.3rem; margin-bottom: 20px; border-left: 4px solid #d97706; padding-left: 10px;">Aturan Pelanggaran & Diskualifikasi</h3>
    
    <div class="bird-rules-grid-dash">
        
        <!-- 1. Murai Borneo -->
        <div class="bird-card-dash">
            <h3>1. Murai Borneo</h3>
            <ol>
                <li>Wajib Full Gembung</li>
                <li>Turun <strong>DISKUALIFIKASI</strong></li>
                <li>Ngelowo / Batman <strong>DISKUALIFIKASI</strong></li>
                <li>Nampar 1x P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Ring bunyi dalam hitungan 3 detik <strong>DISKUALIFIKASI</strong></li>
                <li>Didis / mandi angin dalam hitungan 3 detik P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
            </ol>
        </div>

        <!-- 2. MB Non Gembung -->
        <div class="bird-card-dash">
            <h3>2. MB Non Gembung</h3>
            <ol>
                <li>Turun <strong>DISKUALIFIKASI</strong></li>
                <li>Ngelowo / Batman <strong>DISKUALIFIKASI</strong></li>
                <li>Ring bunyi dalam hitungan 3 detik <strong>DISKUALIFIKASI</strong></li>
                <li>Didis / mandi angin dalam hitungan 3 detik P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
            </ol>
        </div>

        <!-- 3. Cucak Ijo -->
        <div class="bird-card-dash">
            <h3>3. Cucak Ijo</h3>
            <ol>
                <li>Wajib Jegrik</li>
                <li>Turun <strong>DISKUALIFIKASI</strong></li>
                <li>Nampar 1x P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Didis / mandi angin dalam hitungan 3 detik P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Didis ekor <strong>DISKUALIFIKASI</strong></li>
            </ol>
        </div>

        <!-- 4. Kacer -->
        <div class="bird-card-dash">
            <h3>4. Kacer</h3>
            <ol>
                <li>Turun <strong>DISKUALIFIKASI</strong></li>
                <li>Bagong / OB <strong>DISKUALIFIKASI</strong></li>
                <li>Nampar 1x P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Didis / mandi angin dalam hitungan 3 detik P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
            </ol>
        </div>

        <!-- 5. Cendet -->
        <div class="bird-card-dash">
            <h3>5. Cendet</h3>
            <ol>
                <li>Turun <strong>DISKUALIFIKASI</strong></li>
                <li>Nampar 1x P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Didis / mandi angin dalam hitungan 3 detik P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Buka sayap / miyek <strong>DISKUALIFIKASI</strong></li>
                <li>Buka sayap sambil membawakan lagu, <em>masih masuk penilaian</em></li>
            </ol>
        </div>

        <!-- 6. Konin -->
        <div class="bird-card-dash">
            <h3>6. Konin</h3>
            <ol>
                <li>Ngetip / loncat / ngering 1x (P1) <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Nyisir sayap P1 <br><span style="color: #f87171; font-size: 0.85rem;">(Jika terjadi 2x Diskualifikasi)</span></li>
                <li>Turun / nebok <strong>DISKUALIFIKASI</strong></li>
                <li>Angkat kaki <strong>DISKUALIFIKASI</strong></li>
                <li>Kaki nempel jeruji sambil bunyi <strong>DISKUALIFIKASI</strong></li>
                <li>Didis ekor <strong>DISKUALIFIKASI</strong></li>
            </ol>
        </div>

    </div>

    <!-- CATATAN PENTING -->
    <div class="nb-box-dash">
        <strong>NB :</strong> Apabila mendapatkan bendera pelanggaran P1, masih layak koncer B.
    </div>
</div>
    </section>
</div>

<?php 
// Panggil Footer
include 'footer.php'; 
?>
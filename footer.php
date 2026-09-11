<footer style="background-color: #1a110a; color: white; padding: 3rem 0 2rem; text-align: center; margin-top: 3rem; border-top: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="container">
        <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: #fff;">Arena Sejati</h3>
        <p style="color: #d1d5db; margin-bottom: 1.5rem; font-size: 0.95rem;">Pusat Informasi dan Pendaftaran Gantangan Lomba Burung Berkicau Terpercaya.</p>
        
        <div style="display: flex; justify-content: center; gap: 1.5rem; margin-bottom: 2rem;">
            <a href="https://wa.me/6287778311868" target="_blank" title="WhatsApp" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; font-size: 1.2rem; border: 1px solid rgba(255, 255, 255, 0.15); transition: 0.3s;"><i class="fab fa-whatsapp"></i></a>
            <a href="https://instagram.com/arenasejati" target="_blank" title="Instagram" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; font-size: 1.2rem; border: 1px solid rgba(255, 255, 255, 0.15); transition: 0.3s;"><i class="fab fa-instagram"></i></a>
            <a href="https://tiktok.com/@arenasejati" target="_blank" title="TikTok" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; font-size: 1.2rem; border: 1px solid rgba(255, 255, 255, 0.15); transition: 0.3s;"><i class="fab fa-tiktok"></i></a>
            <a href="https://facebook.com/arenasejati" target="_blank" title="Facebook" style="display: inline-flex; align-items: center; justify-content: center; width: 45px; height: 45px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); color: white; font-size: 1.2rem; border: 1px solid rgba(255, 255, 255, 0.15); transition: 0.3s;"><i class="fab fa-facebook-f"></i></a>
        </div>

        <div style="padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); color: #9ca3af; font-size: 0.85rem;">
            &copy; <?= date('Y'); ?> Arena Sejati. All rights reserved.
        </div>
    </div>
</footer>

<!-- MODAL RIWAYAT TRANSAKSI -->
<div id="modalRiwayat" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="background-color: #fff; width: 95%; max-width: 1200px; max-height: 90vh; border-radius: 1rem; display: flex; flex-direction: column; color: #1f2937;">
        
        <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 1.5rem; margin: 0;">Riwayat Semua Transaksi</h2>
            </div>
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <?php 
                $is_admin = isset($_SESSION['admin_username']) && !empty($_SESSION['admin_username']);
                if ($is_admin): 
                ?>
                    <!-- TOMBOL EXPORT ADMIN -->
                    <div>
                        <a href="export_excel.php" target="_blank" style="background: #10b981; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; margin-right: 5px;">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="export_pdf.php" target="_blank" style="background: #ef4444; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                <?php endif; ?>
                <span onclick="tutupModal()" style="font-size: 2rem; color: #9ca3af; cursor: pointer; line-height: 1;">&times;</span>
            </div>
        </div>

        <div style="padding: 1.5rem 2rem; overflow-y: auto;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; white-space: nowrap;">
                    <thead>
                        <tr>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">No</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Nama Peserta</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Nama Burung</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Alamat</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Sesi Kelas</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Kategori / Kelas</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">No. Gantangan</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Metode Bayar</th>
                            <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; background-color: #f8fafc; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; position: sticky; top: 0;">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($query_riwayat) && $query_riwayat):
                        $no = 1;
                        while($row = mysqli_fetch_assoc($query_riwayat)): 
                            $parts = explode('.', $row['kelas_lomba'], 2);
                            $sesi = isset($parts[0]) ? trim($parts[0]) : '-';
                            $kategori = isset($parts[1]) ? trim($parts[1]) : $row['kelas_lomba'];
                            
                            // Ambil status asli dari database dan jadikan huruf kecil untuk pengecekan
                            $status_asli = isset($row['status_pembayaran']) ? trim($row['status_pembayaran']) : 'pending';
                            $status_lower = strtolower($status_asli);
                        ?>
                        <tr>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><?= $no++; ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151; font-weight: 500;"><?= htmlspecialchars($row['nama_peserta'] ?? '') ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><?= htmlspecialchars($row['nama_burung'] ?? '') ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><?= htmlspecialchars($row['alamat'] ?? '') ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><span style="font-weight: bold; color: #1f2937;">Sesi <?= htmlspecialchars($sesi) ?></span></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><?= htmlspecialchars($kategori) ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><span style="background-color: #f3f4f6; padding: 2px 8px; border-radius: 6px; font-weight: 800; color: #d97706; border: 1px solid #d1d5db;"><?= htmlspecialchars($row['nomor_gantangan'] ?? '') ?></span></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;"><?= htmlspecialchars($row['metode_pembayaran'] ?? '') ?></td>
                            <td style="padding: 12px 15px; border-bottom: 1px solid #e5e7eb; color: #374151;">
                                <!-- Pengecekan status yang akurat sesuai database -->
                                <?php if($status_lower === 'lunas'): ?>
                                    <span style="padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; color: white; background-color: #10b981;">LUNAS</span>
                                <?php elseif($status_lower === 'dp'): ?>
                                    <span style="padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; color: white; background-color: #3b82f6;">DP</span>
                                <?php elseif($status_lower === 'pending'): ?>
                                    <span style="padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; color: white; background-color: #f59e0b;">PENDING</span>
                                <?php else: ?>
                                    <span style="padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; color: white; background-color: #ef4444;"><?= strtoupper($status_asli); ?></span>
                                <?php endif; ?>

                               <!-- TOMBOL PRINT KHUSUS ADMIN & HANYA JIKA STATUS LUNAS / DP -->
                                <?php 
                                $status_lower = strtolower($row['status_pembayaran'] ?? '');
                                if ($is_admin && isset($row['id']) && ($status_lower === 'lunas' || $status_lower === 'dp')): 
                                ?>
                                    <div style="margin-top: 5px;">
                                        <button onclick="cetakTransaksi(<?php echo $row['id']; ?>)" style="background: #0d9488; color: white; padding: 3px 8px; border-radius: 4px; border: none; cursor: pointer; font-size: 11px;">
                                            <i class="fas fa-print"></i> Print Struk
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($query_riwayat) == 0): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: #6b7280;">Belum ada riwayat transaksi.</td>
                        </tr>
                        <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const modalRiwayat = document.getElementById('modalRiwayat');
    function bukaModal() { modalRiwayat.style.display = 'flex'; }
    function tutupModal() { modalRiwayat.style.display = 'none'; }
    window.onclick = function(event) { if (event.target === modalRiwayat) tutupModal(); }

    // Fungsi membuka halaman cetak struk satuan
    function cetakTransaksi(idTransaksi) {
        window.open(`print_invoice.php?id=${idTransaksi}`, '_blank', 'width=600,height=600');
    }

    const databaseBrosurSamarinda = [
        { sesi: 1, kategori: "Murai Borneo (Kelas Tilas)", harga: "Rp 55.000", link: "murai.php" },
        { sesi: 2, kategori: "Kacer (Kelas Tilas)", harga: "Rp 55.000", link: "kacer.php" },
        { sesi: 3, kategori: "Cucak Ijo (Kelas Tilas)", harga: "Rp 55.000", link: "cucakijo.php" },
        { sesi: 4, kategori: "Cendet (Kelas Tilas)", harga: "Rp 55.000", link: "cendet.php" },
        { sesi: 5, kategori: "MB Non Gembung (Kelas Tilas)", harga: "Rp 55.000", link: "mbnongembung.php" },
        { sesi: 6, kategori: "Konin (Kelas Tilas)", harga: "Rp 55.000", link: "konin.php" },
        { sesi: 7, kategori: "Murai Borneo (Kelas Marwah)", harga: "Rp 125.000", link: "murai.php" },
        { sesi: 8, kategori: "Kacer (Kelas Marwah)", harga: "Rp 125.000", link: "kacer.php" },
        { sesi: 9, kategori: "Cucak Ijo A (Kelas Napak)", harga: "Rp 85.000", link: "cucakijo.php" },
        { sesi: 10, kategori: "Cendet (Kelas Marwah)", harga: "Rp 125.000", link: "cendet.php" },
        { sesi: 11, kategori: "MB Non Gembung (Kelas Marwah)", harga: "Rp 125.000", link: "mbnongembung.php" },
        { sesi: 12, kategori: "Konin (Kelas Marwah)", harga: "Rp 125.000", link: "konin.php" },
        { sesi: 13, kategori: "Murai Borneo (Kelas Sejati)", harga: "Rp 560.000", link: "murai.php" },
        { sesi: 14, kategori: "Cucak Ijo (Kelas Sultan)", harga: "Rp 220.000", link: "cucakijo.php" },
        { sesi: 15, kategori: "MB Non Gembung (Kelas Sejati)", harga: "Rp 560.000", link: "mbnongembung.php" },
        { sesi: 16, kategori: "Murai Borneo (Kelas Sultan)", harga: "Rp 220.000", link: "murai.php" },
        { sesi: 17, kategori: "Kacer (Kelas Napak)", harga: "Rp 85.000", link: "kacer.php" },
        { sesi: 18, kategori: "Cucak Ijo (Kelas Marwah)", harga: "Rp 125.000", link: "cucakijo.php" },
        { sesi: 19, kategori: "Cendet (Kelas Napak)", harga: "Rp 85.000", link: "cendet.php" },
        { sesi: 20, kategori: "MB Non Gembung (Kelas Sultan)", harga: "Rp 220.000", link: "mbnongembung.php" },
        { sesi: 21, kategori: "Konin (Kelas Napak)", harga: "Rp 85.000", link: "konin.php" },
        { sesi: 22, kategori: "Murai Borneo (Kelas Napak)", harga: "Rp 85.000", link: "murai.php" },
        { sesi: 23, kategori: "Cucak Ijo B (Kelas Napak)", harga: "Rp 85.000", link: "cucakijo.php" },
        { sesi: 24, kategori: "MB Non Gembung (Kelas Napak)", harga: "Rp 85.000", link: "mbnongembung.php" }
    ];

    function cariSesiBrosur(nomorSesi) {
        const boxHasil = document.getElementById('hasil-pencarian');
        if (!nomorSesi || nomorSesi.trim() === "") { boxHasil.style.display = "none"; return; }
        const ketemu = databaseBrosurSamarinda.find(item => item.sesi == nomorSesi);
        boxHasil.style.display = "block";
        if (ketemu) {
            boxHasil.innerHTML = `• Sesi / Urutan Lomba: <strong>Sesi ${ketemu.sesi}</strong><br>• Kategori Burung: <strong style="color: #fff; font-size: 1.1rem;">${ketemu.kategori}</strong><br>• Harga Tiket Masuk: <strong style="color: #fca5a5;">${ketemu.harga}</strong> (Semua Kelas Sangkar Bebas)<br>👉 <a href="${ketemu.link}" style="color: #fcd34d; font-weight: bold; text-decoration: underline;">Klik di Sini Untuk Langsung Booking Gantangan Sesi Ini →</a>`;
        } else {
            boxHasil.innerHTML = `<span style="color: #fca5a5; font-weight: 500;">❌ Sesi ${nomorSesi} tidak terdaftar. Jadwal urutan lomba di brosur hanya tersedia dari Sesi 1 sampai Sesi 25.</span>`;
        }
    }
</script>
</body>
</html>
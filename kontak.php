<?php
$activePage = 'kontak';
$pageTitle  = 'Kontak Kami';
require_once 'config/koneksi.php';
include 'includes/header.php';

$pesan_sukses = '';
$pesan_error  = '';

// Proses simpan pesan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim_pesan'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $email        = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pesan        = mysqli_real_escape_string($conn, trim($_POST['pesan']));

    if (!empty($nama_lengkap) && !empty($email) && !empty($pesan)) {
        $query_simpan = "INSERT INTO pesan_kontak (nama_lengkap, email, pesan) VALUES ('$nama_lengkap', '$email', '$pesan')";
        if (mysqli_query($conn, $query_simpan)) {
            $pesan_sukses = "Terima kasih! Pesan Anda telah berhasil terkirim. Tim kami akan segera menghubungi Anda.";
        } else {
            $pesan_error = "Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.";
        }
    } else {
        $pesan_error = "Harap lengkapi semua kolom yang wajib diisi (*).";
    }
}
?>

<!-- HEADER BANNER HALAMAN KONTAK -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <span class="text-success fw-bold small d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> LAYANAN PELANGGAN AKTIF
        </span>
        <h3 class="fw-bold text-dark fs-4 mb-2">Kontak Kami</h3>
        <p class="text-muted small mb-0" style="max-width: 720px;">
            Kami siap membantu Anda mendapatkan informasi mengenai produk bahan bangunan, ketersediaan stok, harga, maupun pemesanan. Silakan hubungi TB Mitra melalui kontak di bawah ini.
        </p>
    </div>
</section>

<!-- KONTEN UTAMA KONTAK -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- KOLOM KIRI: INFO KONTAK, PETA, & JAM OPERASIONAL -->
            <div class="col-lg-7">
                <div class="mb-4">
                    <h4 class="fw-bold text-dark fs-5 mb-2">Hubungi Kami</h4>
                    <p class="text-muted small mb-0">
                        Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan seputar produk, harga, atau pemesanan. Tim TB Mitra siap membantu memberikan informasi yang Anda butuhkan.
                    </p>
                </div>

                <!-- 3 Kartu Kontak Utama -->
                <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
                    <!-- Kartu Telepon / WhatsApp -->
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white">
                            <div class="text-dark fs-5 mb-2"><i class="bi bi-telephone-fill"></i></div>
                            <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">TELEPON / WHATSAPP</span>
                            <div class="fw-bold text-dark small mb-1">0895-3275-11919</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                Senin - Minggu<br>08.00 - 20.00 WIB
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Email Resmi -->
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white">
                            <div class="text-dark fs-5 mb-2"><i class="bi bi-envelope-fill"></i></div>
                            <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">EMAIL RESMI</span>
                            <div class="fw-bold text-dark small mb-1 text-break">mitra@tokobangunan.id</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                Respon cepat dalam 24 jam kerja
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Lokasi Toko -->
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white">
                            <div class="text-dark fs-5 mb-2"><i class="bi bi-geo-alt-fill"></i></div>
                            <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">LOKASI TOKO</span>
                            <div class="fw-bold text-dark small mb-1">Jl. Melati No. 123</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                Jakarta Pusat<br>Pusat Material Utama
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Baris Peta Lokasi & Jam Operasional -->
                <div class="row g-3">
                    <!-- Kartu Peta -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark small">Lokasi Kami</span>
                                <a href="https://maps.google.com/?q=Depok" target="_blank" class="text-decoration-none text-primary fw-semibold" style="font-size: 0.75rem;">
                                    Lihat peta lebih besar <i class="bi bi-arrow-up-right"></i>
                                </a>
                            </div>
                            <div class="rounded-3 overflow-hidden mb-2" style="height: 170px;">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.241610439638!2d106.791568!3d-6.362773!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69eec1f1b1b1b1%3A0x1b1b1b1b1b1b1b1b!2sJl.%20Pangkalan%20Jati%202%2C%20Depok!5e0!3m2!1sid!2sid!4v1700000000000" 
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                                </iframe>
                            </div>
                            <div class="d-flex align-items-start gap-2 text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle text-dark mt-0.5"></i>
                                <span>Tersedia parkir luas & bongkar muatan armada truk material.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Jam Operasional -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center gap-2 fw-bold text-dark small mb-3">
                                    <i class="bi bi-clock"></i> Jam Operasional
                                </div>
                                <span class="text-muted fw-bold d-block mb-1" style="font-size: 0.68rem; letter-spacing: 0.4px;">HARI KERJA & AKHIR PEKAN</span>
                                <div class="small text-dark mb-1">Senin – Minggu</div>
                                <div class="fw-bold text-dark small mb-3">08.00 – 20.00 WIB</div>

                                <div class="p-2 bg-light rounded-2 small text-muted mb-3" style="font-size: 0.76rem;">
                                    <strong class="text-dark d-block mb-1">Pengiriman Armada:</strong>
                                    Truk ritase berangkat jam 09.00 & 14.00 WIB setiap harinya.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.75rem;">
                                <span class="text-muted">Buka setiap hari kalender</span>
                                <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Buka Sekarang</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM KIRIM PESAN -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark fs-5 mb-2">Kirim Pesan</h5>
                    <p class="text-muted small mb-4">
                        Isi form di bawah ini untuk menghubungi kami. Tim kami akan segera menindaklanjuti pesan Anda.
                    </p>

                    <?php if ($pesan_sukses): ?>
                        <div class="alert alert-success small py-2 mb-3"><?= $pesan_sukses ?></div>
                    <?php endif; ?>

                    <?php if ($pesan_error): ?>
                        <div class="alert alert-danger small py-2 mb-3"><?= $pesan_error ?></div>
                    <?php endif; ?>

                    <form action="kontak.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control shadow-none" placeholder="Masukkan nama Anda" required style="font-size: 0.88rem;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control shadow-none" placeholder="Masukkan email Anda" required style="font-size: 0.88rem;">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Pesan <span class="text-danger">*</span></label>
                            <textarea name="pesan" rows="4" class="form-control shadow-none" placeholder="Tulis pesan Anda di sini..." required style="font-size: 0.88rem;"></textarea>
                        </div>

                        <button type="submit" name="kirim_pesan" class="btn btn-orange w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            Kirim Pesan <i class="bi bi-send"></i>
                        </button>
                    </form>

                    <p class="text-muted text-center mt-3 mb-0" style="font-size: 0.72rem;">
                        Data Anda terlindungi sepenuhnya oleh Kebijakan Privasi Toko Bangunan MITRA.
                    </p>
                </div>
            </div>
        </div>

        <!-- BANNER CALL TO ACTION BAWAH -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mt-5 bg-white">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-light p-3 text-dark fs-4">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Tim ahli dan teknisi kami siap membantu Anda kapan saja!</h6>
                        <span class="text-muted small">Konsultasi kebutuhan material bangunan gratis via WhatsApp.</span>
                    </div>
                </div>
                <div>
                    <a href="https://wa.me/62895327511919" target="_blank" class="btn btn-outline-dark fw-semibold px-4 py-2 small d-inline-flex align-items-center gap-2">
                        Hubungi Sekarang <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
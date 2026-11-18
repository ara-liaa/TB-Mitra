<?php
$activePage = 'beranda';
$pageTitle  = 'Beranda';
require_once 'config/koneksi.php';
include 'includes/header.php';

// Ambil data Kategori dari database
$query_kategori = mysqli_query($conn, "SELECT * FROM kategori LIMIT 8");

// Ambil data Produk Pilihan dari database
$query_produk = mysqli_query($conn, "SELECT * FROM produk WHERE is_pilihan = 1 LIMIT 6");

// Ambil data Testimoni dari database
$query_testimoni = mysqli_query($conn, "SELECT * FROM testimoni LIMIT 3");
?>

<!-- HERO BANNER SECTION -->
<section class="py-4">
    <div class="container">
        <div class="position-relative rounded-4 overflow-hidden shadow-sm" style="min-height: 420px; background: linear-gradient(rgba(8, 28, 56, 0.75), rgba(8, 28, 56, 0.75)), url('assets/img/banners/hero-bg.jpg') center/cover no-repeat;">
            <div class="container h-100 d-flex flex-column justify-content-center py-5 px-4 px-md-5 text-white" style="min-height: 420px;">
                <span class="badge bg-warning text-dark fw-bold mb-3 align-self-start px-3 py-2 rounded-pill" style="font-size: 0.75rem;">TOKO BANGUNAN MITRA</span>
                <h1 class="fw-extrabold display-5 mb-3" style="max-width: 650px; font-weight: 800; line-height: 1.2;">
                    Solusi Lengkap untuk Kebutuhan Bangunan Anda
                </h1>
                <p class="lead mb-4 text-light opacity-90 fs-6" style="max-width: 550px;">
                    Harga Grosir & Eceran untuk Proyek & Renovasi Rumah Anda.
                </p>
                <div>
                    <a href="katalog.php" class="btn btn-orange px-4 py-2 fs-6 d-inline-flex align-items-center gap-2">
                        Belanja Sekarang <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <!-- Slider Dots Indicator -->
            <div class="position-absolute bottom-0 start-50 translate-middle-x pb-3 d-flex gap-2">
                <span class="rounded-pill bg-light opacity-100" style="width: 24px; height: 6px;"></span>
                <span class="rounded-pill bg-light opacity-50" style="width: 8px; height: 6px;"></span>
            </div>
        </div>
    </div>
</section>

<!-- KATEGORI PRODUK SECTION -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-bar fs-5 mb-0">Kategori Produk</h3>
            <a href="katalog.php" class="text-decoration-none small fw-bold text-muted hover-orange">Lihat Semua <i class="bi bi-chevron-right small"></i></a>
        </div>

        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-8 g-3">
            <?php while ($kat = mysqli_fetch_assoc($query_kategori)): ?>
                <div class="col">
                    <a href="katalog.php?kategori=<?= $kat['id_kategori'] ?>" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm text-center py-3 px-2 rounded-3 hover-card bg-white">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-2">
                                <div class="icon-box mb-2 text-primary fs-3">
                                    <i class="bi <?= $kat['ikon'] ?>"></i>
                                </div>
                                <span class="small fw-semibold text-dark text-truncate w-100" style="font-size: 0.82rem;"><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- PRODUK PILIHAN SECTION -->
<section class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-bar fs-5 mb-0">Produk Pilihan</h3>
            <a href="katalog.php" class="text-decoration-none small fw-bold text-muted hover-orange">Lihat Semua <i class="bi bi-chevron-right small"></i></a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-3">
            <?php while ($prod = mysqli_fetch_assoc($query_produk)): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white product-card">
                        <div class="p-3 text-center bg-light rounded-top-3" style="height: 140px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-box-seam text-secondary opacity-50 display-6"></i>
                            <!-- Jika nanti ada gambar asli: <img src="assets/img/produk/<?= $prod['gambar'] ?>" class="img-fluid h-100 object-fit-contain" alt="..."> -->
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title text-dark fs-6 fw-semibold text-truncate mb-1" title="<?= htmlspecialchars($prod['nama_produk']) ?>">
                                <?= htmlspecialchars($prod['nama_produk']) ?>
                            </h6>
                            <div class="text-orange fw-bold mb-3" style="font-size: 0.9rem; color: #F97316;">
                                Rp <?= number_format($prod['harga'], 0, ',', '.') ?>
                            </div>
                            <form action="proses/proses_keranjang.php" method="POST" class="mt-auto">
                                <input type="hidden" name="id_produk" value="<?= $prod['id_produk'] ?>">
                                <button type="submit" name="tambah" class="btn btn-orange w-100 py-1.5 fs-7 d-flex align-items-center justify-content-center gap-1" style="font-size: 0.78rem;">
                                    <i class="bi bi-cart-plus"></i> + Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- KEUNGGULAN TOKO & ILUSTRASI TRUK -->
<section class="py-5 my-3 bg-white border-top border-bottom">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="row row-cols-2 row-cols-md-4 g-4 text-center">
                    <div class="col">
                        <div class="mb-2 text-primary fs-3"><i class="bi bi-shield-check"></i></div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Produk Berkualitas</h6>
                    </div>
                    <div class="col">
                        <div class="mb-2 text-primary fs-3"><i class="bi bi-tag"></i></div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Harga Terjangkau</h6>
                    </div>
                    <div class="col">
                        <div class="mb-2 text-primary fs-3"><i class="bi bi-headset"></i></div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Pelayanan Ramah</h6>
                    </div>
                    <div class="col">
                        <div class="mb-2 text-primary fs-3"><i class="bi bi-grid-1x2"></i></div>
                        <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">Pilihan Produk Lengkap</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <!-- Ilustrasi Truk Pengiriman Kuning -->
                <div class="p-3 bg-light rounded-4 d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 100%; max-width: 280px; height: 110px;">
                    <div class="d-flex align-items-center gap-3 text-warning">
                        <i class="bi bi-truck display-5 text-warning"></i>
                        <div class="text-start">
                            <span class="d-block fw-bold text-dark small">Pengiriman Cepat</span>
                            <span class="text-muted" style="font-size: 0.75rem;">Armada Truk Siap Antar</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TESTIMONI PELANGGAN SECTION -->
<section class="py-4 mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="section-bar fs-5 mb-0">Testimoni Pelanggan</h3>
            <a href="#" class="text-decoration-none small fw-bold text-muted hover-orange">Lihat Semua <i class="bi bi-chevron-right small"></i></a>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php while ($testi = mysqli_fetch_assoc($query_testimoni)): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary fw-bold" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;"><?= htmlspecialchars($testi['nama_pelanggan']) ?></h6>
                                    <div class="text-warning small">
                                        <?php for($i = 0; $i < $testi['rating']; $i++): ?>
                                            <i class="bi bi-star-fill"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small mb-0" style="font-size: 0.85rem; line-height: 1.5;">
                                "<?= htmlspecialchars($testi['komentar']) ?>"
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</>

<?php include 'includes/footer.php'; ?>
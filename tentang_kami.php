<?php
$activePage = 'tentang';
$pageTitle  = 'Tentang Kami';
require_once 'config/koneksi.php';
include 'includes/header.php';
?>

<!-- BREADCRUMB / JUDUL HALAMAN -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <span class="text-muted small d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">PROFIL PERUSAHAAN MITRA</span>
        <h3 class="fw-bold mb-0 text-dark fs-5">Tentang Kami</h3>
        <p class="text-muted small mb-0">Membangun Bersama, Untuk Masa Depan yang Lebih Baik</p>
    </div>
</section>

<!-- KONTEN UTAMA TENTANG KAMI -->
<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-center mb-5">
            <!-- Kolom Kiri: Foto Toko / Gudang -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white p-3">
                    <div class="bg-light rounded-3 p-4 text-center" style="min-height: 280px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i class="bi bi-shop display-3 text-secondary opacity-50 mb-3"></i>
                        <span class="text-muted small">Ilustrasi / Foto Toko Bangunan Mitra</span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Deskripsi Perusahaan & Statistik -->
            <div class="col-lg-6">
                <span class="text-orange fw-bold small text-uppercase" style="color: #F97316; letter-spacing: 0.5px;">Toko Bangunan Terkemuka</span>
                <h2 class="fw-bold text-dark fs-3 mb-3">Toko Bangunan Mitra</h2>
                <p class="text-muted small lh-lg mb-4">
                    Toko Bangunan Mitra adalah toko bahan bangunan terpercaya yang hadir untuk memenuhi kebutuhan material konstruksi Anda, mulai dari bahan dasar hingga perlengkapan bangunan lainnya. Dengan kualitas produk terbaik, harga bersaing, dan pelayanan ramah, kami berkomitmen untuk menjadi mitra terbaik dalam setiap proyek pembangunan Anda, baik perbaikan rumah skala mikro hingga konstruksi skala besar.
                </p>

                <!-- Statistik Angka -->
                <div class="row row-cols-3 g-3 pt-2 border-top">
                    <div class="col">
                        <h4 class="fw-extrabold text-dark fs-4 mb-0">4</h4>
                        <span class="text-muted" style="font-size: 0.78rem;">Tahun Melayani</span>
                    </div>
                    <div class="col">
                        <h4 class="fw-extrabold text-dark fs-4 mb-0">1.000</h4>
                        <span class="text-muted" style="font-size: 0.78rem;">Produk Lengkap</span>
                    </div>
                    <div class="col">
                        <h4 class="fw-extrabold text-dark fs-4 mb-0">99.8%</h4>
                        <span class="text-muted" style="font-size: 0.78rem;">Kepuasan Mitra</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KEUNGGULAN 4 POIN -->
        <div class="row row-cols-1 row-cols-md-4 g-4 mb-5 text-center">
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="mb-3 text-primary fs-3"><i class="bi bi-shield-check"></i></div>
                    <h6 class="fw-bold mb-2 fs-6">Produk Berkualitas</h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">Menyediakan berbagai bahan bangunan dengan kualitas yang baik untuk kebutuhan Anda.</p>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="mb-3 text-primary fs-3"><i class="bi bi-tag"></i></div>
                    <h6 class="fw-bold mb-2 fs-6">Harga Terjangkau</h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">Menawarkan produk dengan harga yang bersaing dan sesuai kebutuhan.</p>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="mb-3 text-primary fs-3"><i class="bi bi-headset"></i></div>
                    <h6 class="fw-bold mb-2 fs-6">Pelayanan Ramah</h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">Siap membantu pelanggan mendapatkan produk yang sesuai dengan kebutuhannya.</p>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="mb-3 text-primary fs-3"><i class="bi bi-grid-1x2"></i></div>
                    <h6 class="fw-bold mb-2 fs-6">Pilihan Produk Lengkap</h6>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">Tersedia berbagai kebutuhan bahan bangunan dalam satu tempat.</p>
                </div>
            </div>
        </div>

        <!-- VISI & MISI + MENGAPA MEMILIH KAMI -->
        <div class="row g-4 mb-5">
            <!-- Visi & Misi -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h4 class="fw-bold text-dark fs-5 mb-4">Visi & Misi</h4>
                    
                    <div class="mb-3">
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2" style="font-size: 0.95rem;">
                            <i class="bi bi-eye text-primary"></i> Visi
                        </h6>
                        <p class="text-muted small fst-italic mb-3">
                            "Menjadi toko bahan bangunan yang terpercaya dalam menyediakan produk harga yang terjangkau serta pelayanan terbaik untuk memenuhi kebutuhan pembangunan masyarakat."
                        </p>
                    </div>

                    <div>
                        <h6 class="fw-bold text-dark d-flex align-items-center gap-2 mb-2" style="font-size: 0.95rem;">
                            <i class="bi bi-bullseye text-primary"></i> Misi
                        </h6>
                        <ol class="text-muted small ps-3 mb-0 d-flex flex-column gap-2">
                            <li>Menyediakan bahan bangunan yang berkualitas.</li>
                            <li>Menawarkan produk dengan harga yang terjangkau.</li>
                            <li>Memberikan pelayanan yang ramah dan terbaik kepada pelanggan.</li>
                            <li>Memenuhi kebutuhan bahan bangunan pelanggan dengan pilihan produk yang beragam.</li>
                            <li>Menjaga kepercayaan dan kepuasan pelanggan.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Mengapa Memilih Kami -->
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h4 class="fw-bold text-dark fs-5 mb-4">Mengapa Memilih Kami?</h4>
                    <p class="text-muted small mb-3">
                        Kami memahami bahwa setiap kebutuhan pembangunan memerlukan perencanaan yang tepat. Oleh karena itu, TB Mitra memberikan kemudahan bagi pelanggan dalam mencari dan mendapatkan bahan bangunan.
                    </p>

                    <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong class="d-block text-dark small">Belanja Lebih Praktis</strong>
                                <span class="text-muted small" style="font-size: 0.8rem;">Lihat berbagai produk tanpa harus datang langsung ke toko.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong class="d-block text-dark small">Informasi Produk Jelas</strong>
                                <span class="text-muted small" style="font-size: 0.8rem;">Memudahkan pelanggan mengetahui detail dan harga produk.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong class="d-block text-dark small">Pemesanan Mudah</strong>
                                <span class="text-muted small" style="font-size: 0.8rem;">Proses pemesanan dapat dilakukan melalui website.</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="bi bi-check-circle-fill text-success mt-1"></i>
                            <div>
                                <strong class="d-block text-dark small">Hemat Waktu</strong>
                                <span class="text-muted small" style="font-size: 0.8rem;">Cari kebutuhan bahan bangunan dengan lebih cepat.</span>
                            </div>
                        </li>
                    </ul>

                    <div class="alert alert-light border small text-dark mb-0 py-2">
                        <i class="bi bi-shield-exclamation text-warning me-1"></i> Garansi Uang Kembali Jika Barang Rusak / Tidak Sesuai
                    </div>
                </div>
            </div>
        </div>

        <!-- BANNER CALL TO ACTION BAWAH -->
        <div class="rounded-4 p-4 text-white text-center position-relative overflow-hidden" style="background-color: #081C38;">
            <div class="position-relative z-1 py-3">
                <span class="d-block text-warning small fw-bold mb-1" style="font-size: 0.75rem;">SOLUSI KEBUTUHAN BANGUNAN ANDA</span>
                <h4 class="fw-bold mb-3">Kualitas Tepat, Harga Bersahabat.</h4>
                <p class="text-light opacity-75 small mb-4 mx-auto" style="max-width: 500px;">
                    Dapatkan penawaran harga terbaik di tempat kami.
                </p>
                <a href="katalog.php" class="btn btn-orange px-4 py-2 fs-7 fw-semibold">
                    Belanja Sekarang <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
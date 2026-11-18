<?php
$activePage = 'katalog';
$pageTitle  = 'Katalog Produk';
require_once 'config/koneksi.php';
include 'includes/header.php';

// Ambil parameter filter & pencarian dari URL
$id_kategori = isset($_GET['kategori']) ? intval($_GET['kategori']) : 0;
$keyword     = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$sort        = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';

// Query dasar untuk produk
$sql = "SELECT * FROM produk WHERE 1=1";

if ($id_kategori > 0) {
    $sql .= " AND id_kategori = $id_kategori";
}

if (!empty($keyword)) {
    $sql .= " AND nama_produk LIKE '%$keyword%'";
}

// Logika Sorting
if ($sort === 'termurah') {
    $sql .= " ORDER BY harga ASC";
} elseif ($sort === 'termahal') {
    $sql .= " ORDER BY harga DESC";
} elseif ($sort === 'nama_asc') {
    $sql .= " ORDER BY nama_produk ASC";
} else {
    $sql .= " ORDER BY id_produk DESC"; // Terbaru
}

$query_produk = mysqli_query($conn, $sql);

// Ambil data untuk Sidebar Kategori
$query_sidebar_kat = mysqli_query($conn, "SELECT * FROM kategori");
?>

<div class="container py-4">
    <div class="row g-4">
        <!-- SIDEBAR KATEGORI -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <h5 class="fw-bold mb-3 fs-6 pb-2 border-bottom">Kategori</h5>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    <li>
                        <a href="katalog.php" class="text-decoration-none small d-block py-1 px-2 rounded <?= ($id_kategori === 0 && empty($keyword)) ? 'bg-light fw-bold text-primary' : 'text-dark hover-orange' ?>">
                            Semua Produk
                        </a>
                    </li>
                    <?php while ($kat = mysqli_fetch_assoc($query_sidebar_kat)): ?>
                        <li>
                            <a href="katalog.php?kategori=<?= $kat['id_kategori'] ?>" class="text-decoration-none small d-block py-1 px-2 rounded <?= ($id_kategori === $kat['id_kategori']) ? 'bg-light fw-bold text-primary' : 'text-dark hover-orange' ?>">
                                <?= htmlspecialchars($kat['nama_kategori']) ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- KONTEN UTAMA KATALOG -->
        <div class="col-lg-9">
            <!-- Header Katalog & Search/Sort Bar -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                <div class="row align-items-center gy-3">
                    <div class="col-md-4">
                        <h4 class="fw-bold mb-0 fs-5 text-dark">Katalog Produk</h4>
                    </div>
                    <div class="col-md-8">
                        <form action="katalog.php" method="GET" class="d-flex gap-2">
                            <?php if ($id_kategori > 0): ?>
                                <input type="hidden" name="kategori" value="<?= $id_kategori ?>">
                            <?php endif; ?>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                                <input type="text" name="cari" class="form-control border-start-0 ps-0 shadow-none fs-7" placeholder="Cari produk..." value="<?= htmlspecialchars($keyword) ?>" style="font-size: 0.88rem;">
                            </div>
                            <select name="sort" class="form-select shadow-none fs-7" style="max-width: 170px; font-size: 0.88rem;" onchange="this.form.submit()">
                                <option value="terbaru" <?= ($sort === 'terbaru') ? 'selected' : '' ?>>Urutkan: Terbaru</option>
                                <option value="termurah" <?= ($sort === 'termurah') ? 'selected' : '' ?>>Harga Terendah</option>
                                <option value="termahal" <?= ($sort === 'termahal') ? 'selected' : '' ?>>Harga Tertinggi</option>
                                <option value="nama_asc" <?= ($sort === 'nama_asc') ? 'selected' : '' ?>>Nama A-Z</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- GRID PRODUK -->
            <?php if (mysqli_num_rows($query_produk) > 0): ?>
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <?php while ($prod = mysqli_fetch_assoc($query_produk)): ?>
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm rounded-3 bg-white product-card">
                                <div class="p-4 text-center bg-light rounded-top-3" style="height: 160px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-box-seam text-secondary opacity-50 display-5"></i>
                                </div>
                                <div class="card-body d-flex flex-column p-3">
                                    <h6 class="card-title text-dark fs-6 fw-semibold text-truncate mb-1" title="<?= htmlspecialchars($prod['nama_produk']) ?>">
                                        <?= htmlspecialchars($prod['nama_produk']) ?>
                                    </h6>
                                    <div class="text-orange fw-bold mb-3" style="font-size: 0.95rem; color: #F97316;">
                                        Rp <?= number_format($prod['harga'], 0, ',', '.') ?>
                                    </div>
                                    <form action="proses/proses_keranjang.php" method="POST" class="mt-auto">
                                        <input type="hidden" name="id_produk" value="<?= $prod['id_produk'] ?>">
                                        <button type="submit" name="tambah" class="btn btn-orange w-100 py-2 fs-7 d-flex align-items-center justify-content-center gap-1 fw-semibold" style="font-size: 0.82rem;">
                                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-3 p-5 text-center bg-white">
                    <div class="text-muted mb-2"><i class="bi bi-search display-4"></i></div>
                    <h5 class="fw-bold text-dark fs-6">Produk Tidak Ditemukan</h5>
                    <p class="text-muted small mb-3">Maaf, produk yang Anda cari atau filter tidak tersedia.</p>
                    <div>
                        <a href="katalog.php" class="btn btn-sm btn-orange px-4">Reset Pencarian</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
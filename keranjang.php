<?php
$activePage = 'keranjang';
$pageTitle  = 'Keranjang Belanja';
require_once 'config/koneksi.php';
include 'includes/header.php';

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$total_belanja = 0;
?>

<div class="container py-4">
    <!-- Breadcrumb / Header Halaman -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">RINGKASAN PESANAN</span>
                <h4 class="fw-bold text-dark fs-5 mb-0">Keranjang Belanja Anda</h4>
            </div>
            <?php if (!empty($keranjang)): ?>
                <a href="proses/proses_keranjang.php?kosongkan=true" class="text-danger small text-decoration-none fw-semibold">
                    <i class="bi bi-trash"></i> Kosongkan Keranjang
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($keranjang)): ?>
        <!-- JIKA KERANJANG KOSONG -->
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
            <div class="text-muted mb-3 display-4"><i class="bi bi-cart-x"></i></div>
            <h5 class="fw-bold text-dark fs-5 mb-2">Keranjang Belanja Masih Kosong</h5>
            <p class="text-muted small mb-4">Belum ada produk yang ditambahkan ke keranjang belanja Anda.</p>
            <div>
                <a href="katalog.php" class="btn btn-orange px-4 py-2 fw-semibold">
                    <i class="bi bi-arrow-left"></i> Lihat Katalog Produk
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- JIKA KERANJANG ADA ISINYA -->
        <form action="proses/proses_keranjang.php" method="POST">
            <div class="row g-4">
                <!-- Tabel Daftar Item -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-3 bg-white overflow-hidden">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light text-muted small" style="font-size: 0.78rem;">
                                    <tr>
                                        <th class="py-3 ps-3">Produk</th>
                                        <th class="py-3">Harga</th>
                                        <th class="py-3" style="width: 120px;">Jumlah</th>
                                        <th class="py-3">Subtotal</th>
                                        <th class="py-3 text-center" style="width: 60px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    foreach ($keranjang as $id_produk => $qty):
                                        $q_prod = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id_produk");
                                        if ($p = mysqli_fetch_assoc($q_prod)):
                                            $subtotal = $p['harga'] * $qty;
                                            $total_belanja += $subtotal;
                                    ?>
                                        <tr>
                                            <td class="ps-3 py-3">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-secondary flex-shrink-0" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-box-seam fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-semibold text-dark mb-0 small" style="font-size: 0.88rem;"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                                                        <span class="text-muted" style="font-size: 0.75rem;">Stok tersedia: <?= $p['stok'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small text-muted">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" name="qty[<?= $id_produk ?>]" value="<?= $qty ?>" min="1" max="<?= $p['stok'] ?>" class="form-control form-control-sm text-center shadow-none fw-bold" style="font-size: 0.85rem;">
                                            </td>
                                            <td class="fw-bold text-orange small" style="color: #F97316;">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="proses/proses_keranjang.php?hapus=<?= $id_produk ?>" class="text-danger fs-6" title="Hapus Item"><i class="bi bi-trash3"></i></a>
                                            </td>
                                        </tr>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white border-0 p-3 d-flex justify-content-between align-items-center">
                            <a href="katalog.php" class="text-decoration-none small text-muted fw-semibold">
                                <i class="bi bi-arrow-left"></i> Lanjutkan Belanja
                            </a>
                            <button type="submit" name="update_cart" class="btn btn-outline-dark btn-sm px-3 fw-semibold">
                                Perbarui Keranjang
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kartu Total Belanja & Checkout -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                        <h5 class="fw-bold text-dark fs-6 mb-3 pb-2 border-bottom">Ringkasan Belanja</h5>
                        
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Total Item</span>
                            <span class="fw-semibold text-dark"><?= array_sum($keranjang) ?> Barang</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 small text-muted">
                            <span>Estimasi Pengiriman</span>
                            <span class="fw-semibold text-success">Dihitung saat checkout</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-4 pt-2 border-top">
                            <span class="fw-bold text-dark">Total Harga</span>
                            <span class="fw-extrabold text-orange fs-5" style="color: #F97316;">Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                        </div>

                        <a href="#" onclick="alert('Fitur Checkout akan segera terintegrasi dengan WhatsApp / sistem pembayaran!'); return false;" class="btn btn-orange w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                            Lanjut ke Checkout <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
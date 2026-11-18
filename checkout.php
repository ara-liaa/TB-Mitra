<?php
$activePage = 'checkout';
$pageTitle  = 'Checkout Pesanan';
require_once 'config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
if (empty($keranjang)) {
    header("Location: keranjang.php");
    exit;
}

$subtotal = 0;
foreach ($keranjang as $id_p => $qty) {
    $q = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk = $id_p");
    if ($p = mysqli_fetch_assoc($q)) {
        $subtotal += $p['harga'] * $qty;
    }
}
$ongkir = 15000;
$total_bayar = $subtotal + $ongkir;

include 'includes/header.php';
?>

<div class="container py-4">
    <!-- STEP INDICATOR (1. Alamat, 2. Pembayaran, 3. Konfirmasi) -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white text-center">
        <div class="row align-items-center justify-content-center position-relative">
            <div class="col-4 d-flex flex-column align-items-center">
                <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px; font-size: 0.85rem;">1</div>
                <span class="fw-bold text-dark small" style="font-size: 0.82rem;">Alamat</span>
            </div>
            <div class="col-4 d-flex flex-column align-items-center">
                <div class="rounded-circle bg-light text-muted fw-bold d-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px; font-size: 0.85rem;">2</div>
                <span class="text-muted small" style="font-size: 0.82rem;">Pembayaran</span>
            </div>
            <div class="col-4 d-flex flex-column align-items-center">
                <div class="rounded-circle bg-light text-muted fw-bold d-flex align-items-center justify-content-center mb-2" style="width: 32px; height: 32px; font-size: 0.85rem;">3</div>
                <span class="text-muted small" style="font-size: 0.82rem;">Konfirmasi</span>
            </div>
        </div>
    </div>

    <form action="proses/proses_checkout.php" method="POST">
        <div class="row g-4">
            <!-- KOLOM KIRI: ALAMAT & PEMBAYARAN -->
            <div class="col-lg-7">
                <!-- Alamat Pengiriman -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark fs-6 mb-0">Alamat Pengiriman</h5>
                        <button type="button" class="btn btn-outline-dark btn-sm px-3 py-1" style="font-size: 0.78rem;">+ Alamat Baru</button>
                    </div>

                    <div class="p-3 border rounded-3 bg-light position-relative">
                        <div class="form-check">
                            <input class="form-check-input shadow-none" type="radio" name="alamat" id="alamatUtama" checked>
                            <label class="form-check-label w-100" for="alamatUtama">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <strong class="text-dark small">Rumah (Utama)</strong>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size: 0.65rem;">Utama</span>
                                </div>
                                <p class="text-muted small mb-1" style="font-size: 0.82rem;">Jl. Melati No. 12, RT 04/RW 02, Jakarta Selatan</p>
                                <span class="text-muted" style="font-size: 0.75rem;">0812-3456-7890</span>
                            </label>
                        </div>
                        <a href="#" class="position-absolute top-0 end-0 mt-3 me-3 text-decoration-none text-primary fw-semibold small" style="font-size: 0.78rem;">Ubah</a>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark fs-6 mb-3">Metode Pembayaran</h5>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 border rounded-3">
                            <div class="form-check d-flex align-items-center justify-content-between">
                                <div>
                                    <input class="form-check-input shadow-none me-2" type="radio" name="metode_pembayaran" id="bankTransfer" value="Transfer Bank" checked>
                                    <label class="form-check-label fw-semibold text-dark small" for="bankTransfer">
                                        Transfer Bank (BCA, BNI, Mandiri)
                                    </label>
                                </div>
                                <i class="bi bi-bank fs-5 text-muted"></i>
                            </div>
                        </div>

                        <div class="p-3 border rounded-3">
                            <div class="form-check d-flex align-items-center justify-content-between">
                                <div>
                                    <input class="form-check-input shadow-none me-2" type="radio" name="metode_pembayaran" id="cod" value="COD">
                                    <label class="form-check-label fw-semibold text-dark small" for="cod">
                                        COD (Bayar di Tempat)
                                    </label>
                                </div>
                                <i class="bi bi-cash-coin fs-5 text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN PESANAN -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h5 class="fw-bold text-dark fs-6 mb-3 pb-2 border-bottom">Ringkasan Pesanan</h5>
                    
                    <div class="d-flex flex-column gap-3 mb-4">
                        <?php 
                        foreach ($keranjang as $id_p => $qty):
                            $q_p = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk = $id_p");
                            if ($prod = mysqli_fetch_assoc($q_p)):
                        ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-light rounded p-2 text-secondary"><i class="bi bi-box-seam"></i></div>
                                    <div>
                                        <h6 class="fw-semibold text-dark mb-0 small" style="font-size: 0.85rem;"><?= htmlspecialchars($prod['nama_produk']) ?></h6>
                                        <span class="text-muted" style="font-size: 0.72rem;"><?= $qty ?> barang</span>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark small" style="font-size: 0.85rem;">Rp <?= number_format($prod['harga'] * $qty, 0, ',', '.') ?></span>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal</span>
                        <span class="fw-semibold text-dark">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-muted pb-3 border-bottom">
                        <span>Ongkir</span>
                        <span class="fw-semibold text-dark">Rp <?= number_format($ongkir, 0, ',', '.') ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold text-dark">Total</span>
                        <span class="fw-extrabold text-orange fs-5" style="color: #F97316;">Rp <?= number_format($total_bayar, 0, ',', '.') ?></span>
                    </div>

                    <button type="submit" name="buat_pesanan" class="btn btn-orange w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                        Pesan Sekarang <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
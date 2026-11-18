<?php
$activePage = 'pesanan_saya';
$pageTitle  = 'Pesanan Saya';
require_once 'config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'semua';

// Query dasar untuk mengambil data pesanan milik user yang sedang login
// (Pastikan kamu sudah memiliki tabel 'pesanan' di database, atau kita buatkan query menyesuaikan)
$sql = "SELECT * FROM pesanan WHERE id_user = $id_user";
if ($status_filter !== 'semua') {
    $sql .= " AND status = '$status_filter'";
}
$sql .= " ORDER BY tanggal DESC";

// Untuk sementara jika tabel pesanan belum ada, kita tampilkan data statis yang sesuai dengan desain mockup
include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 950px;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-1" style="font-size: 0.78rem;">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="login.php" class="text-decoration-none text-muted">Akun Saya</a></li>
            <li class="breadcrumb-item active text-dark" aria-current="page">Pesanan Saya</li>
        </ol>
    </nav>

    <!-- Judul Halaman -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="fw-bold text-dark fs-4 mb-0">Pesanan Saya</h4>
    </div>

    <!-- Tombol Filter Status (Semua, Diproses, Dikirim, Selesai) -->
    <div class="d-flex gap-2 mb-4">
        <a href="pesanan_saya.php?status=semua" class="btn <?= ($status_filter === 'semua') ? 'btn-primary' : 'btn-light text-muted border' ?> btn-sm px-3 fw-semibold shadow-none">Semua</a>
        <a href="pesanan_saya.php?status=diproses" class="btn <?= ($status_filter === 'diproses') ? 'btn-primary' : 'btn-light text-muted border' ?> btn-sm px-3 fw-semibold shadow-none">Diproses</a>
        <a href="pesanan_saya.php?status=dikirim" class="btn <?= ($status_filter === 'dikirim') ? 'btn-primary' : 'btn-light text-muted border' ?> btn-sm px-3 fw-semibold shadow-none">Dikirim</a>
        <a href="pesanan_saya.php?status=selesai" class="btn <?= ($status_filter === 'selesai') ? 'btn-primary' : 'btn-light text-muted border' ?> btn-sm px-3 fw-semibold shadow-none">Selesai</a>
    </div>

    <!-- Daftar Riwayat Pesanan -->
    <div class="d-flex flex-column gap-3">
        <!-- Item 1: Diproses -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white hover-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">#INV20251001</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">1 Okt 2025, 14:30</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <span class="badge bg-warning-subtle text-warning fw-bold px-3 py-1.5" style="font-size: 0.75rem;">Diproses</span>
                    <div class="text-end">
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Total</span>
                        <span class="fw-bold text-dark small">Rp 270.000</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </div>
            </div>
        </div>

        <!-- Item 2: Dikirim -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white hover-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">#INV20250928</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">28 Sep 2025, 10:15</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <span class="badge bg-info-subtle text-info fw-bold px-3 py-1.5" style="font-size: 0.75rem;">Dikirim</span>
                    <div class="text-end">
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Total</span>
                        <span class="fw-bold text-dark small">Rp 420.000</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </div>
            </div>
        </div>

        <!-- Item 3: Selesai -->
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white hover-card">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-2.5 rounded-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.92rem;">#INV20250920</h6>
                        <span class="text-muted" style="font-size: 0.75rem;">20 Sep 2025, 16:20</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5" style="font-size: 0.75rem;">Selesai</span>
                    <div class="text-end">
                        <span class="text-muted d-block" style="font-size: 0.72rem;">Total</span>
                        <span class="fw-bold text-dark small">Rp 150.000</span>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>[cite: 6]
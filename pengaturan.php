<?php
$activePage = 'pengaturan';
$pageTitle  = 'Pengaturan Profil';
require_once 'config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$sukses = '';
$error  = '';

// Proses Simpan Perubahan Profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_profil'])) {
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $telepon  = mysqli_real_escape_string($conn, trim($_POST['telepon']));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $kota     = mysqli_real_escape_string($conn, trim($_POST['kota']));
    $kodepos  = mysqli_real_escape_string($conn, trim($_POST['kodepos']));
    
    $id_user  = $_SESSION['id_user'];

    // Update data user di database
    $query = "UPDATE users SET nama = '$nama' WHERE id_user = $id_user";
    if (mysqli_query($conn, $query)) {
        $_SESSION['nama'] = $nama; // Update session nama
        $sukses = "Perubahan profil berhasil disimpan!";
    } else {
        $error = "Terjadi kesalahan saat menyimpan perubahan.";
    }
}

include 'includes/header.php';
?>

<div class="container py-4" style="max-width: 950px;">
    <!-- Breadcrumb & Judul Halaman -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.78rem;">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="login.php" class="text-decoration-none text-muted">Akun Saya</a></li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Pengaturan Profil</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark fs-4 mb-1">Pengaturan Profil</h3>
            <p class="text-muted small mb-0">Kelola data profil, alamat pengiriman bahan bangunan, dan keamanan akun Anda.</p>
        </div>
        <div>
            <a href="login.php?logout=true" class="btn btn-outline-danger btn-sm px-3 fw-semibold d-inline-flex align-items-center gap-1">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>

    <?php if ($sukses): ?>
        <div class="alert alert-success small py-2 mb-3"><?= $sukses ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger small py-2 mb-3"><?= $error ?></div>
    <?php endif; ?>

    <form action="pengaturan.php" method="POST">
        <!-- 1. FOTO PROFIL PELANGGAN -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h6 class="fw-bold text-dark fs-6 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-bounding-box text-orange" style="color: #F97316;"></i> Foto Profil Pelanggan
            </h6>
            <div class="d-flex align-items-center gap-4 flex-wrap">
                <div class="rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center border" style="width: 75px; height: 75px;">
                    <i class="bi bi-person-fill text-secondary display-6"></i>
                </div>
                <div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-dark btn-sm px-3 fw-semibold" style="font-size: 0.8rem;">
                            <i class="bi bi-camera me-1"></i> Pilih Foto
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-semibold" style="font-size: 0.8rem;">
                            Hapus
                        </button>
                    </div>
                    <span class="text-muted d-block" style="font-size: 0.74rem;">Gunakan format JPG atau PNG. Ukuran maksimal 2MB.</span>
                </div>
            </div>
        </div>

        <!-- 2. DATA DIRI & KONTAK -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h6 class="fw-bold text-dark fs-6 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-orange" style="color: #F97316;"></i> Data Diri & Kontak
            </h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark small">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control shadow-none" value="<?= htmlspecialchars($_SESSION['nama']) ?>" required style="font-size: 0.88rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Alamat Email</label>
                    <input type="email" name="email" class="form-control shadow-none bg-light" value="<?= htmlspecialchars($_SESSION['email']) ?>" readonly style="font-size: 0.88rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Nomor WhatsApp / Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted small">+62</span>
                        <input type="text" name="telepon" class="form-control shadow-none" placeholder="812-3456-7890" value="812-3456-7890" style="font-size: 0.88rem;">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ALAMAT PENGIRIMAN (PENGANTARAN BAHAN BANGUNAN) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h6 class="fw-bold text-dark fs-6 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-geo-alt text-orange" style="color: #F97316;"></i> Alamat Pengiriman (Pengantaran Bahan Bangunan)
            </h6>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark small">Alamat Lengkap & Patokan Proyek/Rumah</label>
                    <input type="text" name="alamat" class="form-control shadow-none" value="Jl. Pangkalan Jati 2 No. 18, RT 03 / RW 05, Dekat Masjid Al-Ikhlas" style="font-size: 0.88rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Kota / Kabupaten</label>
                    <input type="text" name="kota" class="form-control shadow-none" value="Kota Depok" style="font-size: 0.88rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark small">Kode Pos</label>
                    <input type="text" name="kodepos" class="form-control shadow-none" value="16513" style="font-size: 0.88rem;">
                </div>
            </div>
        </div>

        <!-- 4. UBAH KATA SANDI -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h6 class="fw-bold text-dark fs-6 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-lock text-orange" style="color: #F97316;"></i> Ubah Kata Sandi
            </h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark small">Kata Sandi Saat Ini</label>
                    <input type="password" name="password_lama" class="form-control shadow-none" placeholder="••••••••" style="font-size: 0.88rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark small">Kata Sandi Baru</label>
                    <input type="password" name="password_baru" class="form-control shadow-none" placeholder="Minimal 8 karakter" style="font-size: 0.88rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-dark small">Konfirmasi Sandi Baru</label>
                    <input type="password" name="konfirmasi_password" class="form-control shadow-none" placeholder="Ulangi sandi baru" style="font-size: 0.88rem;">
                </div>
            </div>
        </div>

        <!-- TOMBOL AKSI BAWAH -->
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="login.php" class="btn btn-light px-4 py-2 fw-semibold text-dark border small">Batal</a>
            <button type="submit" name="simpan_profil" class="btn btn-orange px-4 py-2 fw-semibold small">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>[cite: 8]
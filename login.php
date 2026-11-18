<?php
$activePage = 'login';
$pageTitle  = 'Akun Saya';
require_once 'config/koneksi.php';

$error_login   = '';
$error_reg     = '';
$sukses_reg    = '';

// Proses Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
        if ($query && mysqli_num_rows($query) > 0) {
            $user = mysqli_fetch_assoc($query);
            // Verifikasi password (mendukung password_hash atau teks biasa untuk kemudahan testing)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['email']   = $user['email'];
                $_SESSION['role']    = $user['role'];
                
                header("Location: login.php");
                exit;
            } else {
                $error_login = "Password yang Anda masukkan salah.";
            }
        } else {
            $error_login = "Email tidak terdaftar.";
        }
    } else {
        $error_login = "Harap isi email dan password.";
    }
}

// Proses Register
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    if (!empty($nama) && !empty($email) && !empty($_POST['password'])) {
        $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $error_reg = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
        } else {
            $query_ins = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', 'pelanggan')";
            if (mysqli_query($conn, $query_ins)) {
                $sukses_reg = "Registrasi berhasil! Silakan login menggunakan akun Anda.";
            } else {
                $error_reg = "Terjadi kesalahan sistem. Coba beberapa saat lagi.";
            }
        }
    } else {
        $error_reg = "Semua kolom registrasi wajib diisi.";
    }
}

// Proses Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

include 'includes/header.php';
?>

<div class="container py-5">
    <?php if (isset($_SESSION['id_user'])): ?>
        <!-- JIKA USER SUDAH LOGIN (HALAMAN PROFIL) -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                    <div class="mb-3 text-primary display-5">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?>!</h4>
                    <p class="text-muted small mb-4"><?= htmlspecialchars($_SESSION['email']) ?> (Role: <span class="text-uppercase fw-semibold"><?= $_SESSION['role'] ?></span>)</p>

                    <div class="d-grid gap-2 col-8 mx-auto mb-3">
                        <a href="katalog.php" class="btn btn-orange py-2 fw-semibold">Mulai Belanja</a>
                        <a href="login.php?logout=true" class="btn btn-outline-danger py-2 fw-semibold">Keluar (Logout)</a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- JIKA USER BELUM LOGIN (FORM LOGIN & REGISTER) -->
        <div class="row justify-content-center g-4">
            <!-- Form Login -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h4 class="fw-bold text-dark fs-5 mb-2">Masuk ke Akun</h4>
                    <p class="text-muted small mb-4">Silakan masukkan email dan password Anda.</p>

                    <?php if ($error_login): ?>
                        <div class="alert alert-danger small py-2 mb-3"><?= $error_login ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Email</label>
                            <input type="email" name="email" class="form-control shadow-none" placeholder="nama@email.com" required style="font-size: 0.88rem;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Password</label>
                            <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required style="font-size: 0.88rem;">
                        </div>
                        <button type="submit" name="login" class="btn btn-orange w-100 py-2 fw-semibold">
                            Masuk <i class="bi bi-box-arrow-in-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Form Register / Daftar Akun Baru -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h4 class="fw-bold text-dark fs-5 mb-2">Daftar Akun Baru</h4>
                    <p class="text-muted small mb-4">Belum punya akun? Buat akun baru untuk mulai memesan.</p>

                    <?php if ($sukses_reg): ?>
                        <div class="alert alert-success small py-2 mb-3"><?= $sukses_reg ?></div>
                    <?php endif; ?>

                    <?php if ($error_reg): ?>
                        <div class="alert alert-danger small py-2 mb-3"><?= $error_reg ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control shadow-none" placeholder="Nama lengkap Anda" required style="font-size: 0.88rem;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-dark">Email</label>
                            <input type="email" name="email" class="form-control shadow-none" placeholder="nama@email.com" required style="font-size: 0.88rem;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-dark">Password</label>
                            <input type="password" name="password" class="form-control shadow-none" placeholder="Minimal 6 karakter" required style="font-size: 0.88rem;">
                        </div>
                        <button type="submit" name="register" class="btn btn-outline-dark w-100 py-2 fw-semibold">
                            Daftar Sekarang <i class="bi bi-person-plus"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
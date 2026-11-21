<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$storeDisplayName = defined('STORE_NAME') ? STORE_NAME : 'Toko Bangunan Mitra';
$currentScript    = basename((string) ($_SERVER['PHP_SELF'] ?? ''));

$activeNavKey = $activeAdminNav ?? match ($currentScript) {
    'dashboard.php'       => 'dashboard',
    'produk.php'          => 'produk',
    'kategori.php'        => 'kategori',
    'pesanan.php'         => 'pesanan',
    'pelanggan.php'       => 'pelanggan',
    'laporan.php'         => 'laporan',
    'pengaturan-toko.php' => 'pengaturan',
    default               => 'dashboard',
};

$adminLowStockCount = 0;
if (function_exists('get_db')) {
    try {
        $adminLowStockCount = (int) get_db()->query(
            'SELECT COUNT(*) FROM products WHERE status = "aktif" AND stock <= min_stock'
        )->fetchColumn();
    } catch (Throwable $e) {
        $adminLowStockCount = 0;
    }
}

$adminNavItems = [
    'dashboard'  => ['href' => 'dashboard.php',       'icon' => 'bi-speedometer2',   'label' => 'Dashboard'],
    'produk'     => ['href' => 'produk.php',          'icon' => 'bi-box-seam',       'label' => 'Produk'],
    'kategori'   => ['href' => 'kategori.php',        'icon' => 'bi-tags',           'label' => 'Kategori'],
    'pesanan'    => ['href' => 'pesanan.php',         'icon' => 'bi-receipt',        'label' => 'Pesanan'],
    'pelanggan'  => ['href' => 'pelanggan.php',       'icon' => 'bi-people',         'label' => 'Pelanggan'],
    'laporan'    => ['href' => 'laporan.php',         'icon' => 'bi-bar-chart-line', 'label' => 'Laporan'],
    'pengaturan' => ['href' => 'pengaturan-toko.php', 'icon' => 'bi-gear',           'label' => 'Info Toko'],
];
?>
<header class="sticky-top bg-white border-bottom shadow-2xs admin-header no-print">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand d-flex align-items-center gap-2 me-lg-3 text-nowrap" href="dashboard.php">
                <span class="bg-primary text-white fw-bold rounded-2 px-2 py-1">
                    <i class="bi bi-shield-lock"></i>
                </span>
                <span class="lh-1">
                    <span class="fw-bold text-primary d-block fs-6">Admin Panel</span>
                    <span class="text-secondary fw-semibold d-none d-xl-inline" style="font-size: 0.7rem;">
                        <?= htmlspecialchars($storeDisplayName) ?>
                    </span>
                </span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#adminNavbarCollapse" aria-controls="adminNavbarCollapse"
                    aria-expanded="false" aria-label="Buka navigasi admin">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbarCollapse">
                <ul class="navbar-nav admin-nav flex-lg-row align-items-lg-center mx-lg-auto my-2 my-lg-0 gap-1 small fw-semibold">
                    <?php foreach ($adminNavItems as $key => $item): ?>
                        <?php $isActive = ($activeNavKey === $key); ?>
                        <li class="nav-item">
                            <a class="nav-link px-2 px-xl-3 py-2 rounded-3 text-nowrap <?= $isActive ? 'active bg-primary-subtle text-primary' : 'text-secondary' ?>"
                               href="<?= htmlspecialchars($item['href']) ?>"
                               <?= $isActive ? 'aria-current="page"' : '' ?>>
                                <i class="bi <?= htmlspecialchars($item['icon']) ?> me-1"></i>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-lg-0 flex-shrink-0">
                    <?php if ($adminLowStockCount > 0): ?>
                        <a href="produk.php?filter_stock=low"
                           class="btn btn-warning-subtle border border-warning-subtle text-dark btn-sm rounded-pill px-2 px-xl-3 fw-semibold text-nowrap">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>
                            Stok (<?= (int) $adminLowStockCount ?>)
                        </a>
                    <?php endif; ?>

                    <a href="../index.php" class="btn btn-outline-secondary btn-sm text-nowrap" title="Lihat Website Toko">
                        <i class="bi bi-globe me-1"></i> Toko
                    </a>

                    <a href="../logout.php" class="btn btn-outline-danger btn-sm fw-semibold text-nowrap">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

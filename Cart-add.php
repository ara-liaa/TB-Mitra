<?php
require_once __DIR__ . '/../config/auth.php';

$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

$respond = static function (int $statusCode, array $payload, ?string $fallbackRedirect = null) use ($isAjax): void {
    if ($isAjax) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    if (!empty($payload['message'])) {
        set_flash(!empty($payload['ok']) ? 'success' : 'warning', (string) $payload['message']);
    }
    header('Location: ' . ($fallbackRedirect ?? '../katalog.php'));
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $respond(405, ['ok' => false, 'message' => 'Metode request tidak diizinkan.'], '../katalog.php');
}

$productId = (int) ($_POST['product_id'] ?? 0);
$qty       = max(1, (int) ($_POST['qty'] ?? 1));

// Jika Pengunjung (belum login), arahkan ke halaman Login/Register
if (!is_logged_in()) {
    if ($productId > 0) {
        $_SESSION['pending_cart_item'] = [
            'product_id' => $productId,
            'qty'        => $qty,
        ];
    }
    $loginRedirect = 'login.php?redirect=' . urlencode('katalog.php?add=' . $productId);
    $respond(401, [
        'ok'       => false,
        'message'  => 'Silakan masuk atau daftar terlebih dahulu.',
        'redirect' => $loginRedirect,
    ], '../' . $loginRedirect);
}

// Hanya role pelanggan yang dapat menambah ke keranjang
if (current_user_role() !== 'pelanggan') {
    $respond(403, [
        'ok'      => false,
        'message' => 'Fitur keranjang belanja hanya tersedia untuk akun Pelanggan.',
    ], '../index.php');
}

if ($productId <= 0) {
    $respond(400, ['ok' => false, 'message' => 'Produk tidak valid.'], '../katalog.php');
}

$pdo    = get_db();
$userId = (int) $_SESSION['user_id'];

$stmtProd = $pdo->prepare('SELECT id, name, stock, status FROM products WHERE id = :id LIMIT 1');
$stmtProd->execute([':id' => $productId]);
$product = $stmtProd->fetch();

if (!$product || $product['status'] !== 'aktif') {
    $respond(404, ['ok' => false, 'message' => 'Produk tidak ditemukan atau sudah tidak aktif.'], '../katalog.php');
}

$availableStock = (int) $product['stock'];
if ($availableStock <= 0) {
    $respond(409, ['ok' => false, 'message' => 'Stok produk "' . $product['name'] . '" sedang habis.'], '../katalog.php');
}

// Cek jumlah yang sudah ada di keranjang pelanggan
$stmtExisting = $pdo->prepare('SELECT qty FROM cart_items WHERE user_id = :uid AND product_id = :pid LIMIT 1');
$stmtExisting->execute([':uid' => $userId, ':pid' => $productId]);
$existingQty = (int) ($stmtExisting->fetchColumn() ?: 0);

$targetQty = min($existingQty + $qty, $availableStock);

$stmtUpsert = $pdo->prepare(
    'INSERT INTO cart_items (user_id, product_id, qty)
     VALUES (:uid, :pid, :qty)
     ON DUPLICATE KEY UPDATE qty = :upd_qty'
);
$stmtUpsert->execute([
    ':uid'     => $userId,
    ':pid'     => $productId,
    ':qty'     => $targetQty,
    ':upd_qty' => $targetQty,
]);

refresh_cart_session($pdo, $userId);
$totalCartItems = get_cart_count();

$respond(200, [
    'ok'         => true,
    'message'    => '"' . $product['name'] . '" ditambahkan ke keranjang.',
    'cart_count' => $totalCartItems,
], '../pelanggan/keranjang.php');

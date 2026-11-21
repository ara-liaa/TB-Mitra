<?php
require_once __DIR__ . '/../config/auth.php';

$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

$respond = static function (int $statusCode, array $payload, string $fallbackUrl = '../pelanggan/keranjang.php') use ($isAjax): void {
    if ($isAjax) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    if (!empty($payload['message'])) {
        set_flash(!empty($payload['ok']) ? 'success' : 'warning', (string) $payload['message']);
    }
    header('Location: ' . ($payload['redirect'] ?? $fallbackUrl));
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $respond(405, ['ok' => false, 'message' => 'Metode request tidak diizinkan.']);
}

if (!is_logged_in()) {
    $respond(401, [
        'ok'       => false,
        'message'  => 'Silakan masuk terlebih dahulu.',
        'redirect' => '../login.php?redirect=' . urlencode('pelanggan/keranjang.php'),
    ]);
}

if (current_user_role() !== 'pelanggan') {
    $respond(403, ['ok' => false, 'message' => 'Akses ditolak untuk role ini.'], '../index.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $respond(419, ['ok' => false, 'message' => 'Token keamanan tidak valid. Silakan muat ulang halaman.']);
}

$pdo       = get_db();
$userId    = (int) $_SESSION['user_id'];
$action    = (string) ($_POST['action'] ?? '');
$productId = (int) ($_POST['product_id'] ?? 0);

if ($productId <= 0 || !in_array($action, ['increase', 'decrease', 'set', 'remove'], true)) {
    $respond(400, ['ok' => false, 'message' => 'Permintaan pembaruan keranjang tidak valid.']);
}

$stmtItem = $pdo->prepare(
    'SELECT ci.id, ci.qty, p.name, p.price, p.wholesale_price, p.wholesale_min_qty, p.stock, p.status
     FROM cart_items ci
     INNER JOIN products p ON p.id = ci.product_id
     WHERE ci.user_id = :uid AND ci.product_id = :pid
     LIMIT 1'
);
$stmtItem->execute([':uid' => $userId, ':pid' => $productId]);
$row = $stmtItem->fetch();

if (!$row) {
    $respond(404, ['ok' => false, 'message' => 'Item tidak ditemukan di keranjang.']);
}

$removed = false;
$message = 'Keranjang diperbarui.';

if ($action === 'remove') {
    $pdo->prepare('DELETE FROM cart_items WHERE user_id = :uid AND product_id = :pid')
        ->execute([':uid' => $userId, ':pid' => $productId]);
    $removed = true;
    $message = '"' . $row['name'] . '" dihapus dari keranjang.';
} else {
    $maxStock = (int) $row['stock'];
    if ($row['status'] !== 'aktif' || $maxStock <= 0) {
        $pdo->prepare('DELETE FROM cart_items WHERE user_id = :uid AND product_id = :pid')
            ->execute([':uid' => $userId, ':pid' => $productId]);
        $removed = true;
        $message = 'Stok "' . $row['name'] . '" sedang habis.';
    } else {
        $currentQty = (int) $row['qty'];
        $targetQty  = match ($action) {
            'increase' => $currentQty + 1,
            'decrease' => max(1, $currentQty - 1),
            'set'      => max(1, (int) ($_POST['qty'] ?? $currentQty)),
        };

        if ($targetQty > $maxStock) {
            $targetQty = $maxStock;
            $message   = 'Jumlah disesuaikan ke sisa stok tersedia (' . $maxStock . ').';
        }

        $pdo->prepare('UPDATE cart_items SET qty = :qty WHERE user_id = :uid AND product_id = :pid')
            ->execute([':qty' => $targetQty, ':uid' => $userId, ':pid' => $productId]);
    }
}

// Hitung ulang total keranjang pelanggan
$stmtAll = $pdo->prepare(
    'SELECT ci.qty, p.price, p.wholesale_price, p.wholesale_min_qty
     FROM cart_items ci
     INNER JOIN products p ON p.id = ci.product_id
     WHERE ci.user_id = :uid'
);
$stmtAll->execute([':uid' => $userId]);
$allRows = $stmtAll->fetchAll();

$totalCount  = 0;
$totalAmount = 0.0;
foreach ($allRows as $r) {
    $q = (int) $r['qty'];
    $uPrice = (float) $r['price'];
    if (
        !empty($r['wholesale_price']) &&
        !empty($r['wholesale_min_qty']) &&
        $q >= (int) $r['wholesale_min_qty']
    ) {
        $uPrice = (float) $r['wholesale_price'];
    }
    $totalCount  += $q;
    $totalAmount += ($uPrice * $q);
}

refresh_cart_session($pdo, $userId);

$itemPayload = null;
if (!$removed) {
    $stmtUpdated = $pdo->prepare(
        'SELECT ci.qty, p.price, p.wholesale_price, p.wholesale_min_qty, p.stock
         FROM cart_items ci
         INNER JOIN products p ON p.id = ci.product_id
         WHERE ci.user_id = :uid AND ci.product_id = :pid
         LIMIT 1'
    );
    $stmtUpdated->execute([':uid' => $userId, ':pid' => $productId]);
    $upd = $stmtUpdated->fetch();

    if ($upd) {
        $q = (int) $upd['qty'];
        $effPrice = (float) $upd['price'];
        $isWholesale = false;
        if (
            !empty($upd['wholesale_price']) &&
            !empty($upd['wholesale_min_qty']) &&
            $q >= (int) $upd['wholesale_min_qty']
        ) {
            $effPrice = (float) $upd['wholesale_price'];
            $isWholesale = true;
        }
        $itemPayload = [
            'qty'             => $q,
            'stock'           => (int) $upd['stock'],
            'effective_price' => $effPrice,
            'is_wholesale'    => $isWholesale,
            'subtotal'        => $effPrice * $q,
        ];
    }
}

$respond(200, [
    'ok'           => true,
    'message'      => $message,
    'removed'      => $removed,
    'item'         => $itemPayload,
    'cart_count'   => $totalCount,
    'total_amount' => $totalAmount,
]);


<?php
require_once __DIR__ . '/../config/auth.php';

$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
$role   = current_user_role() ?? '';

$defaultRedirect = ($role === 'admin')
    ? '../admin/pesanan.php'
    : '../pelanggan/pesanan.php';

$respond = static function (int $statusCode, array $payload, string $fallbackUrl) use ($isAjax): void {
    if ($isAjax) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    if (!empty($payload['message'])) {
        set_flash(!empty($payload['ok']) ? 'success' : 'danger', (string) $payload['message']);
    }
    header('Location: ' . ($payload['redirect'] ?? $fallbackUrl));
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $respond(405, ['ok' => false, 'message' => 'Metode request tidak diizinkan.'], $defaultRedirect);
}

if (!is_logged_in()) {
    $loginUrl = '../login.php?redirect=' . urlencode('pelanggan/pesanan.php');
    $respond(401, [
        'ok'       => false,
        'message'  => 'Silakan masuk terlebih dahulu.',
        'redirect' => $loginUrl,
    ], $loginUrl);
}

if (!in_array($role, ['pelanggan', 'admin'], true)) {
    $respond(403, ['ok' => false, 'message' => 'Akses ditolak.'], '../index.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $respond(419, [
        'ok'      => false,
        'message' => 'Token keamanan tidak valid. Silakan muat ulang halaman.',
    ], $defaultRedirect);
}

$orderId = (int) ($_POST['order_id'] ?? 0);
$reason  = trim((string) ($_POST['cancel_reason'] ?? ''));

if ($orderId <= 0) {
    $respond(400, ['ok' => false, 'message' => 'ID pesanan tidak valid.'], $defaultRedirect);
}

$pdo    = get_db();
$userId = (int) $_SESSION['user_id'];

try {
    $pdo->beginTransaction();

    if ($role === 'admin') {
        $stmtOrd = $pdo->prepare(
            'SELECT id, invoice_code, user_id, order_status
             FROM orders
             WHERE id = :oid
             FOR UPDATE'
        );
        $stmtOrd->execute([':oid' => $orderId]);
    } else {
        $stmtOrd = $pdo->prepare(
            'SELECT id, invoice_code, user_id, order_status
             FROM orders
             WHERE id = :oid AND user_id = :uid
             FOR UPDATE'
        );
        $stmtOrd->execute([':oid' => $orderId, ':uid' => $userId]);
    }

    $order = $stmtOrd->fetch();

    if (!$order) {
        throw new RuntimeException('Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.');
    }

    $currentStatus = (string) $order['order_status'];

    if ($currentStatus === 'dibatalkan') {
        throw new RuntimeException('Pesanan #' . $order['invoice_code'] . ' sudah dibatalkan sebelumnya.');
    }

    // Ketentuan pembatalan:
    // - Pelanggan: hanya saat status masih menunggu_konfirmasi atau diproses (belum dikirim/selesai)
    // - Admin: tidak dapat membatalkan pesanan yang sudah berstatus selesai
    if ($role === 'pelanggan') {
        $allowedForCustomer = ['menunggu_konfirmasi', 'diproses'];
        if (!in_array($currentStatus, $allowedForCustomer, true)) {
            throw new RuntimeException(
                'Pesanan #' . $order['invoice_code'] . ' tidak dapat dibatalkan karena statusnya sudah "'
                . str_replace('_', ' ', $currentStatus) . '".'
            );
        }
    } else {
        if ($currentStatus === 'selesai') {
            throw new RuntimeException(
                'Pesanan #' . $order['invoice_code'] . ' sudah selesai dan tidak dapat dibatalkan.'
            );
        }
    }

    // Kembalikan stok seluruh item pada pesanan ke tabel products
    $stmtItems = $pdo->prepare('SELECT product_id, qty FROM order_items WHERE order_id = :oid');
    $stmtItems->execute([':oid' => $orderId]);
    $items = $stmtItems->fetchAll();

    $stmtRestoreStock = $pdo->prepare(
        'UPDATE products SET stock = stock + :qty WHERE id = :pid'
    );
    foreach ($items as $item) {
        $stmtRestoreStock->execute([
            ':qty' => max(0, (int) $item['qty']),
            ':pid' => (int) $item['product_id'],
        ]);
    }

    $finalReason = $reason !== ''
        ? mb_substr($reason, 0, 250)
        : ($role === 'admin' ? 'Dibatalkan oleh admin toko' : 'Dibatalkan oleh pelanggan');

    $stmtUpdateOrder = $pdo->prepare(
        'UPDATE orders
         SET order_status = "dibatalkan",
             cancel_reason = :reason,
             cancelled_by = :cancelled_by
         WHERE id = :oid'
    );
    $stmtUpdateOrder->execute([
        ':reason'       => $finalReason,
        ':cancelled_by' => $role,
        ':oid'          => $orderId,
    ]);

    $pdo->commit();

    $redirectTarget = ($role === 'admin')
        ? '../admin/pesanan.php?detail=' . $orderId
        : '../pelanggan/pesanan.php';

    $respond(200, [
        'ok'           => true,
        'message'      => 'Pesanan #' . $order['invoice_code'] . ' berhasil dibatalkan dan stok produk telah dikembalikan.',
        'order_id'     => $orderId,
        'invoice_code' => (string) $order['invoice_code'],
        'redirect'     => $redirectTarget,
    ], $redirectTarget);
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $respond(409, ['ok' => false, 'message' => $e->getMessage()], $defaultRedirect);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Cancel order error: ' . $e->getMessage());
    $respond(500, [
        'ok'      => false,
        'message' => 'Terjadi kesalahan sistem saat membatalkan pesanan.',
    ], $defaultRedirect);
}

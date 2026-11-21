<?php
require_once __DIR__ . '/../config/auth.php';

$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

$respond = static function (int $statusCode, array$payload, string $fallbackUrl = '../pelanggan/checkout.php') use ($isAjax): void {
    if ($isAjax) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    if (!empty($payload['message'])) {
        set_flash(!empty($payload['ok']) ? 'success' : 'danger', (string)$payload['message']);
    }
    header('Location: ' . ($payload['redirect'] ?? $fallbackUrl));
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {$respond(405, ['ok' => false, 'message' => 'Metode request tidak diizinkan.'], '../pelanggan/checkout.php');
}

if (!is_logged_in()) {
    $loginUrl = '../login.php?redirect=' . urlencode('pelanggan/checkout.php');
    $respond(401, [
        'ok'       => false,
        'message'  => 'Silakan masuk terlebih dahulu untuk melanjutkan checkout.',
        'redirect' => $loginUrl,
    ], $loginUrl);
}

if (current_user_role() !== 'pelanggan') {
    $respond(403, [
        'ok'      => false,
        'message' => 'Hanya akun Pelanggan yang dapat membuat pesanan.',
    ], '../index.php');
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $respond(419, [
        'ok'      => false,
        'message' => 'Token keamanan tidak valid. Silakan muat ulang halaman checkout.',
    ], '../pelanggan/checkout.php');
}

$pdo    = get_db();
$userId = (int)$_SESSION['user_id'];

$addressId     = (int) ($_POST['address_id'] ?? 0);
$paymentMethod = (string) ($_POST['payment_method'] ?? '');
$notes         = trim((string) ($_POST['notes'] ?? ''));

$allowedPayments = ['cash', 'qris', 'transfer_bca'];
if (!in_array($paymentMethod, $allowedPayments, true)) {$respond(400, [
        'ok'      => false,
        'message' => 'Pilih metode pembayaran yang valid (Cash, QRIS, atau Transfer BCA).',
    ], '../pelanggan/checkout.php');
}

// Ambil ongkir standar dari store_settings jika tersedia
$shippingCost = 15000.0;
try {
    $storeRow =$pdo->query('SELECT default_shipping_cost FROM store_settings ORDER BY id ASC LIMIT 1')->fetch();
    if ($storeRow && isset($storeRow['default_shipping_cost'])) {
        $shippingCost = max(0.0, (float)$storeRow['default_shipping_cost']);
    }
} catch (Throwable $e) {$shippingCost = 15000.0;
}

try {
    $pdo->beginTransaction();

    // Validasi alamat pengiriman milik pelanggan
    if ($addressId > 0) {$stmtAddr = $pdo->prepare( 'SELECT * FROM customer_addresses WHERE id = :aid AND user_id = :uid LIMIT 1' );$stmtAddr->execute([':aid' => $addressId, ':uid' =>$userId]);
        $address =$stmtAddr->fetch();
    } else {
        $stmtAddr =$pdo->prepare(
            'SELECT * FROM customer_addresses WHERE user_id = :uid ORDER BY is_default DESC, id DESC LIMIT 1'
        );
        $stmtAddr->execute([':uid' =>$userId]);
        $address =$stmtAddr->fetch();
    }

    if (!$address) {
        throw new RuntimeException('Alamat pengiriman belum dipilih atau belum tersedia. Silakan lengkapi alamat Anda.');
    }

    // Ambil item keranjang dan kunci baris produk terkait untuk mencegah race condition stok
    $stmtCart =$pdo->prepare(
        'SELECT
            ci.id AS cart_id,
            ci.product_id,
            ci.qty,
            p.sku,
            p.name,
            p.price,
            p.wholesale_price,
            p.wholesale_min_qty,
            p.stock,
            p.status
         FROM cart_items ci
         INNER JOIN products p ON p.id = ci.product_id
         WHERE ci.user_id = :uid
         ORDER BY ci.id ASC
         FOR UPDATE'
    );
    $stmtCart->execute([':uid' =>$userId]);
    $cartRows =$stmtCart->fetchAll();

    if (empty($cartRows)) {
        throw new RuntimeException('Keranjang belanja Anda kosong.');
    }

    $subtotal  = 0.0;
    $lineItems = [];

    foreach ($cartRows as$item) {
        $qty       = (int)$item['qty'];
        $stock     = (int)$item['stock'];
        $prodName  = (string)$item['name'];

        if ($item['status'] !== 'aktif') {
            throw new RuntimeException('Produk "' . $prodName . '" sedang tidak aktif.');
        }
        if ($qty <= 0 || $stock <$qty) {
            throw new RuntimeException('Stok produk "' . $prodName . '" tidak mencukupi (tersedia: ' . $stock . ').');
        }

        $unitPrice = (float)$item['price'];
        if (
            !empty($item['wholesale_price']) &&
            !empty($item['wholesale_min_qty']) &&
            $qty >= (int)$item['wholesale_min_qty']
        ) {
            $unitPrice = (float)$item['wholesale_price'];
        }

        $lineSubtotal = $unitPrice * $qty;
        $subtotal    +=$lineSubtotal;

        $lineItems[] = [
            'product_id'   => (int) $item['product_id'],
            'product_sku'  => (string) $item['sku'],
            'product_name' => $prodName,
            'qty'          => $qty,
            'unit_price'   => $unitPrice,
            'subtotal'     => $lineSubtotal,
        ];
    }

    $totalAmount   = $subtotal +$shippingCost;
    $invoiceCode   = 'TBM-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
    $paymentStatus = ($paymentMethod === 'cash') ? 'belum_bayar'
      

<?php
require_once __DIR__ . '/../config/auth.php';
require_role('admin');

$pdo = get_db();

// Tangani aksi POST (ubah status aktif/nonaktif akun pelanggan)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('danger', 'Token keamanan tidak valid. Silakan coba lagi.');
        header('Location: pelanggan.php');
        exit;
    }

    $action     = (string) ($_POST['action'] ?? '');
    $customerId = (int) ($_POST['customer_id'] ?? 0);

    if ($action === 'toggle_status' &&$customerId > 0) {
        $newStatus = ($_POST['status'] ?? 'aktif') === 'nonaktif' ? 'nonaktif' : 'aktif';

        $stmtUpd =$pdo->prepare(
            'UPDATE users
             SET status = :status
             WHERE id = :id AND role = "pelanggan"'
        );
        $stmtUpd->execute([
            ':status' => $newStatus,
            ':id'     => $customerId,
        ]);

        if ($stmtUpd->rowCount() > 0) {
            set_flash(
                'success',
                'Status akun pelanggan berhasil diubah menjadi ' . ($newStatus === 'aktif' ? 'Aktif' : 'Nonaktif') . '.'
            );
        } else {
            set_flash('warning', 'Data pelanggan tidak ditemukan atau tidak mengalami perubahan.');
        }

        header('Location: pelanggan.php?detail=' . $customerId);
        exit;
    }
}

// Parameter pencarian, filter, dan pengurutan
$searchQuery  = trim((string) ($_GET['q'] ?? ''));
$statusFilter = (string) ($_GET['status'] ?? '');
$sortBy       = (string) ($_GET['sort'] ?? 'terbaru');

$where  = ['u.role = "pelanggan"'];
$params = [];

if ($searchQuery !== '') {$where[] = '(u.name LIKE :q1 OR u.email LIKE :q2 OR u.phone LIKE :q3)';
    $like = '\%' .$searchQuery . '%';
    $params[':q1'] =$like;
    $params[':q2'] =$like;
    $params[':q3'] =$like;
}

if (in_array($statusFilter, ['aktif', 'nonaktif'], true)) {$where[] = 'u.status = :status';
    $params[':status'] =$statusFilter;
}

$orderSql = match ($sortBy) {
    'belanja_tertinggi' => 'ORDER BY total_spent DESC, u.created_at DESC',
    'pesanan_terbanyak' => 'ORDER BY total_orders DESC, u.created_at DESC',
    'nama_az'           => 'ORDER BY u.name ASC',
    default             => 'ORDER BY u.created_at DESC',
};

$sqlCustomers = 'SELECT
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.status,
                    u.created_at,
                    COUNT(o.id) AS total_orders,
                    COALESCE(SUM(CASE WHEN o.order_status != "dibatalkan" THEN o.total_amount ELSE 0 END), 0) AS total_spent,
                    MAX(o.created_at) AS last_order_at
                 FROM users u
                 LEFT JOIN orders o ON o.user_id = u.id
                 WHERE ' . implode(' AND ', $where) . '
                 GROUP BY u.id, u.name, u.email, u.phone, u.status, u.created_at
                 ' . $orderSql;

$stmtCust = $pdo->prepare($sqlCustomers);
$stmtCust->execute($params);
$customers =$stmtCust->fetchAll();

// Statistik Ringkas Pelanggan
$totalCustomers = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role = "pelanggan"')->fetchColumn();
$activeCustomers = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role = "pelanggan" AND status = "aktif"')->fetchColumn();
$buyingCustomers = (int)$pdo->query(
    'SELECT COUNT(DISTINCT user_id) FROM orders WHERE order_status != "dibatalkan"'
)->fetchColumn();

// Tentukan pelanggan yang ditampilkan pada panel Detail Pelanggan di sebelah kanan
$detailId = isset($_GET['detail']) && ctype_digit((string)$_GET['detail'])
    ? (int) $_GET['detail']
    : (!empty($customers) ? (int)$customers[0]['id'] : 0);

$selectedCustomer = null;
foreach ($customers as$c) {
    if ((int) $c['id'] ===$detailId) {
        $selectedCustomer =$c;
        break;
    }
}
if ($selectedCustomer === null && !empty($customers)) {
    $selectedCustomer =$customers[0];
}

// Ambil alamat tersimpan dan 5 pesanan terakhir milik pelanggan yang dipilih
$customerAddresses = [];$customerOrders    = [];
if ($selectedCustomer) {
    $cid = (int)$selectedCustomer['id'];

    $stmtAddr = $pdo->prepare( 'SELECT * FROM customer_addresses WHERE user_id = :uid ORDER BY is_default DESC, id DESC' );$stmtAddr->execute([':uid' => $cid]);$customer
      

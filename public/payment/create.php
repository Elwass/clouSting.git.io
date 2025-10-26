<?php
require_once __DIR__ . '/../../config/config.php';

use Midtrans\Snap;
use Midtrans\MidtransException;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['customer_id'])) {
    header('Location: /customer/login.php');
    exit;
}

$orderIdParam = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderIdParam <= 0) {
    header('Location: /customer/dashboard.php');
    exit;
}

$customerId = (int)$_SESSION['customer_id'];
$orderQuery = mysqli_query($conn, "SELECT p.*, u.nama, u.email, pk.nama_paket, pk.harga FROM pesanan p JOIN users u ON p.user_id = u.id JOIN paket_hosting pk ON p.paket_id = pk.id WHERE p.id = $orderIdParam AND p.user_id = $customerId LIMIT 1");
if (!$orderQuery || mysqli_num_rows($orderQuery) === 0) {
    header('Location: /customer/dashboard.php');
    exit;
}

$order = mysqli_fetch_assoc($orderQuery);
if (!in_array($order['status'], ['pending', 'failed'], true)) {
    header('Location: /customer/dashboard.php?payment=success');
    exit;
}

$grossAmount = (int)$order['harga'];
try {
    $randomSuffix = bin2hex(random_bytes(5));
} catch (Exception $e) {
    $randomSuffix = time();
}
$midtransOrderId = 'CLOUDHOST-' . $order['id'] . '-' . strtoupper($randomSuffix);

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$callbacks = [
    'finish' => $baseUrl . '/customer/dashboard.php?payment=success&order_id=' . $order['id'],
    'error' => $baseUrl . '/customer/dashboard.php?payment=failed&order_id=' . $order['id'],
    'pending' => $baseUrl . '/customer/dashboard.php?payment=pending&order_id=' . $order['id'],
];

$params = [
    'transaction_details' => [
        'order_id' => $midtransOrderId,
        'gross_amount' => $grossAmount,
    ],
    'item_details' => [
        [
            'id' => 'PAKET-' . $order['paket_id'],
            'price' => $grossAmount,
            'quantity' => 1,
            'name' => $order['nama_paket'],
        ],
    ],
    'customer_details' => [
        'first_name' => $order['nama'],
        'email' => $order['email'],
    ],
    'credit_card' => [
        'secure' => true,
    ],
    'callbacks' => $callbacks,
];

if (!mysqli_begin_transaction($conn)) {
    $_SESSION['payment_error'] = 'Gagal memulai transaksi pembayaran.';
    header('Location: /customer/dashboard.php?payment=failed');
    exit;
}
try {
    $insertQuery = "INSERT INTO transaksi (pesanan_id, order_id, gross_amount, transaction_status) VALUES (?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $insertQuery);
    if ($stmt === false) {
        throw new Exception('Gagal menyiapkan data transaksi.');
    }
    $grossAmountValue = (float)$grossAmount;
    mysqli_stmt_bind_param($stmt, 'isd', $order['id'], $midtransOrderId, $grossAmountValue);
    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        throw new Exception('Gagal menyimpan data transaksi.');
    }
    mysqli_stmt_close($stmt);

    if (!mysqli_query($conn, "UPDATE pesanan SET status='pending' WHERE id=" . (int)$order['id'])) {
        throw new Exception('Gagal memperbarui status pesanan.');
    }

    $snapTransaction = Snap::createTransaction($params);

    if (empty($snapTransaction['redirect_url'])) {
        throw new Exception('Midtrans tidak mengembalikan URL pembayaran.');
    }

    mysqli_commit($conn);
    header('Location: ' . $snapTransaction['redirect_url']);
    exit;
} catch (MidtransException $e) {
    mysqli_rollback($conn);
    $_SESSION['payment_error'] = 'Gagal membuat transaksi: ' . $e->getMessage();
    header('Location: /customer/dashboard.php?payment=failed');
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['payment_error'] = 'Gagal memproses transaksi: ' . $e->getMessage();
    header('Location: /customer/dashboard.php?payment=failed');
    exit;
}

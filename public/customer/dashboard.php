<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['customer_id'])) {
    header('Location: /customer/login.php');
    exit;
}

$customerId = $_SESSION['customer_id'];
$customerName = $_SESSION['customer_name'];

$activeOrderQuery = mysqli_query($conn, "SELECT p.*, pk.nama_paket, pk.harga AS harga_reguler, pd.nama_paket AS nama_diskon, pd.harga_diskon FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id LEFT JOIN paket_diskon pd ON p.paket_diskon_id = pd.id WHERE p.user_id = $customerId ORDER BY p.tanggal_pesanan DESC LIMIT 1");
$activeOrder = mysqli_fetch_assoc($activeOrderQuery);
$latestTransaction = null;
if ($activeOrder) {
    $orderId = (int)$activeOrder['id'];
    $transactionQuery = mysqli_query($conn, "SELECT * FROM transaksi WHERE pesanan_id = $orderId ORDER BY transaction_time DESC LIMIT 1");
    if ($transactionQuery) {
        $latestTransaction = mysqli_fetch_assoc($transactionQuery);
    }
}

$totalOrdersQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE user_id = $customerId");
$totalOrders = mysqli_fetch_assoc($totalOrdersQuery)['total'];

?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="dashboard-wrapper">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold"><?php echo htmlspecialchars(sprintf(translate('customer_dashboard_greeting', 'Halo, %s!'), $customerName)); ?></h2>
                <p class="text-muted mb-0"><?php echo htmlspecialchars(translate('customer_dashboard_subtitle', 'Kelola layanan hosting Anda dengan mudah.')); ?></p>
            </div>
            <div>
                <a href="/customer/pesanan_baru.php" class="btn btn-primary me-2"><i class="fas fa-plus me-2"></i><?php echo htmlspecialchars(translate('customer_dashboard_order_button', 'Pesan Hosting Baru')); ?></a>
                <a href="/customer/logout.php" class="btn btn-outline-danger"><?php echo htmlspecialchars(translate('customer_dashboard_logout', 'Logout')); ?></a>
            </div>
        </div>
        <?php if (!empty($_SESSION['payment_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['payment_error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['payment_error']); ?>
        <?php endif; ?>
        <?php if (isset($_GET['payment'])): ?>
            <?php
            $paymentType = $_GET['payment'];
            $alertConfig = [
                'success' => ['class' => 'success', 'message' => translate('customer_dashboard_alert_success', 'Pembayaran berhasil! Pesanan Anda segera diproses.')],
                'failed' => ['class' => 'danger', 'message' => translate('customer_dashboard_alert_failed', 'Pembayaran gagal. Silakan coba lagi atau gunakan metode lainnya.')],
                'pending' => ['class' => 'warning', 'message' => translate('customer_dashboard_alert_pending', 'Pembayaran Anda masih diproses. Kami akan memperbarui status segera.')],
            ];
            if (isset($alertConfig[$paymentType])) {
                $alert = $alertConfig[$paymentType];
                echo '<div class="alert alert-' . htmlspecialchars($alert['class']) . ' alert-dismissible fade show" role="alert">' . htmlspecialchars($alert['message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            ?>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted"><?php echo htmlspecialchars(translate('customer_dashboard_total_orders', 'Total Pesanan')); ?></h6>
                        <h3 class="fw-bold"><?php echo (int)$totalOrders; ?></h3>
                        <p class="small text-muted"><?php echo htmlspecialchars(translate('customer_dashboard_total_orders_hint', 'Riwayat pesanan hosting Anda.')); ?></p>
                        <a href="/customer/riwayat_pesanan.php" class="btn btn-sm btn-outline-primary"><?php echo htmlspecialchars(translate('customer_dashboard_view_history', 'Lihat Riwayat')); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted"><?php echo htmlspecialchars(translate('customer_dashboard_latest_package', 'Paket Aktif / Terbaru')); ?></h6>
                        <?php if ($activeOrder): ?>
                            <?php
                            $statusMap = [
                                'pending' => ['badge' => 'warning text-dark', 'label' => 'Pending'],
                                'paid' => ['badge' => 'primary', 'label' => 'Sudah Dibayar'],
                                'failed' => ['badge' => 'danger', 'label' => 'Gagal'],
                                'aktif' => ['badge' => 'success', 'label' => 'Aktif'],
                                'selesai' => ['badge' => 'secondary', 'label' => 'Selesai'],
                            ];
                            $statusInfo = $statusMap[$activeOrder['status']] ?? ['badge' => 'secondary', 'label' => ucfirst($activeOrder['status'])];
                            ?>
                            <?php
                            $displayName = !empty($activeOrder['nama_diskon']) ? $activeOrder['nama_diskon'] : $activeOrder['nama_paket'];
                            $isDiscount = !empty($activeOrder['paket_diskon_id']);
                            $totalHarga = isset($activeOrder['total_harga']) ? (float)$activeOrder['total_harga'] : (float)$activeOrder['harga_reguler'];
                            $regularHarga = isset($activeOrder['harga_reguler']) ? (float)$activeOrder['harga_reguler'] : $totalHarga;
                            ?>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h4 class="fw-semibold mb-0"><?php echo htmlspecialchars($displayName); ?></h4>
                                <span class="badge bg-<?php echo $isDiscount ? 'warning text-dark' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars(translate($isDiscount ? 'customer_order_discount_badge' : 'customer_order_regular_badge', $isDiscount ? 'Diskon' : 'Reguler')); ?>
                                </span>
                            </div>
                            <p class="mb-1"><strong><?php echo htmlspecialchars(translate('customer_dashboard_status_label', 'Status')); ?>:</strong> <span class="badge bg-<?php echo $statusInfo['badge']; ?>"><?php echo htmlspecialchars($statusInfo['label']); ?></span></p>
                            <p class="mb-1"><strong><?php echo htmlspecialchars(translate('customer_dashboard_domain_label', 'Domain')); ?>:</strong> <?php echo htmlspecialchars($activeOrder['domain']); ?></p>
                            <p class="mb-1"><strong><?php echo htmlspecialchars(translate('customer_dashboard_payment_method_label', 'Metode Pembayaran')); ?>:</strong> <?php echo htmlspecialchars($activeOrder['metode_pembayaran']); ?></p>
                            <p class="mb-1"><strong><?php echo htmlspecialchars(translate('customer_history_table_price', 'Harga')); ?>:</strong> Rp <?php echo number_format($totalHarga, 0, ',', '.'); ?><?php if ($isDiscount && $regularHarga > 0 && $regularHarga !== $totalHarga): ?><span class="text-muted small ms-2 text-decoration-line-through">Rp <?php echo number_format($regularHarga, 0, ',', '.'); ?></span><?php endif; ?></p>
                            <p class="mb-0"><strong><?php echo htmlspecialchars(translate('customer_dashboard_order_date_label', 'Tanggal Pesanan')); ?>:</strong> <?php echo date('d M Y', strtotime($activeOrder['tanggal_pesanan'])); ?></p>
                            <?php if ($latestTransaction): ?>
                                <?php
                                $trxLabels = [
                                    'pending' => translate('transaction_status_pending', 'Menunggu Pembayaran'),
                                    'capture' => translate('transaction_status_capture', 'Pembayaran Ditangkap'),
                                    'settlement' => translate('transaction_status_settlement', 'Pembayaran Berhasil'),
                                    'deny' => translate('transaction_status_deny', 'Pembayaran Ditolak'),
                                    'cancel' => translate('transaction_status_cancel', 'Pembayaran Dibatalkan'),
                                    'expire' => translate('transaction_status_expire', 'Pembayaran Kedaluwarsa'),
                                    'failure' => translate('transaction_status_failure', 'Pembayaran Gagal'),
                                ];
                                $transactionLabel = $trxLabels[strtolower($latestTransaction['transaction_status'] ?? '')] ?? $latestTransaction['transaction_status'];
                                ?>
                                <p class="mt-3 mb-1"><strong><?php echo htmlspecialchars(translate('customer_dashboard_payment_status_label', 'Status Pembayaran')); ?>:</strong> <?php echo htmlspecialchars($transactionLabel ?? '-'); ?></p>
                                <?php if (!empty($latestTransaction['order_id'])): ?>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars(translate('customer_dashboard_payment_id_label', 'ID Transaksi')); ?>:</strong> <?php echo htmlspecialchars($latestTransaction['order_id']); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (in_array($activeOrder['status'], ['pending', 'failed'], true)): ?>
                                <a href="/payment/create.php?order_id=<?php echo $activeOrder['id']; ?>" class="btn btn-primary mt-3"><?php echo htmlspecialchars(translate('customer_dashboard_pay_now', 'Bayar Sekarang')); ?></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars(translate('customer_dashboard_no_orders', 'Anda belum memiliki pesanan aktif. Mulai dengan memesan paket hosting.')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3"><?php echo htmlspecialchars(translate('customer_dashboard_tips_title', 'Tips Mengoptimalkan Hosting')); ?></h5>
                <ul class="mb-0">
                    <li><?php echo htmlspecialchars(translate('customer_dashboard_tip_1', 'Aktifkan SSL untuk menjaga keamanan data pelanggan Anda.')); ?></li>
                    <li><?php echo htmlspecialchars(translate('customer_dashboard_tip_2', 'Gunakan fitur backup harian untuk menghindari kehilangan data.')); ?></li>
                    <li><?php echo htmlspecialchars(translate('customer_dashboard_tip_3', 'Hubungi tim support kami untuk migrasi website gratis.')); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

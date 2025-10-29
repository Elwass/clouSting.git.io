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

$activeOrderQuery = mysqli_query($conn, "SELECT p.*, pk.nama_paket, pk.harga FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id WHERE p.user_id = $customerId ORDER BY p.tanggal_pesanan DESC LIMIT 1");
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Halo, <?php echo htmlspecialchars($customerName); ?>!</h2>
                <p class="text-muted">Kelola layanan hosting Anda dengan mudah.</p>
            </div>
            <div>
                <a href="/customer/pesanan_baru.php" class="btn btn-primary me-2"><i class="fas fa-plus me-2"></i>Pesan Hosting Baru</a>
                <a href="/customer/logout.php" class="btn btn-outline-danger">Logout</a>
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
            $paymentAlert = '';
            $paymentType = $_GET['payment'];
            if ($paymentType === 'success') {
                $paymentAlert = '<div class="alert alert-success alert-dismissible fade show" role="alert">Pembayaran berhasil! Pesanan Anda segera diproses.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } elseif ($paymentType === 'failed') {
                $paymentAlert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Pembayaran gagal. Silakan coba lagi atau gunakan metode lainnya.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            } elseif ($paymentType === 'pending') {
                $paymentAlert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">Pembayaran Anda masih diproses. Kami akan memperbarui status segera.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            }
            echo $paymentAlert;
            ?>
        <?php endif; ?>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Pesanan</h6>
                        <h3 class="fw-bold"><?php echo (int)$totalOrders; ?></h3>
                        <p class="small text-muted">Riwayat pesanan hosting Anda.</p>
                        <a href="/customer/riwayat_pesanan.php" class="btn btn-sm btn-outline-primary">Lihat Riwayat</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Paket Aktif / Terbaru</h6>
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
                            <h4 class="fw-semibold"><?php echo htmlspecialchars($activeOrder['nama_paket']); ?></h4>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?php echo $statusInfo['badge']; ?>"><?php echo $statusInfo['label']; ?></span></p>
                            <p class="mb-1"><strong>Domain:</strong> <?php echo htmlspecialchars($activeOrder['domain']); ?></p>
                            <p class="mb-1"><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($activeOrder['metode_pembayaran']); ?></p>
                            <p class="mb-0"><strong>Tanggal Pesanan:</strong> <?php echo date('d M Y', strtotime($activeOrder['tanggal_pesanan'])); ?></p>
                            <?php if ($latestTransaction): ?>
                                <?php
                                $trxLabels = [
                                    'pending' => 'Menunggu Pembayaran',
                                    'capture' => 'Pembayaran Ditangkap',
                                    'settlement' => 'Pembayaran Berhasil',
                                    'deny' => 'Pembayaran Ditolak',
                                    'cancel' => 'Pembayaran Dibatalkan',
                                    'expire' => 'Pembayaran Kedaluwarsa',
                                    'failure' => 'Pembayaran Gagal',
                                ];
                                $transactionLabel = $trxLabels[strtolower($latestTransaction['transaction_status'] ?? '')] ?? $latestTransaction['transaction_status'];
                                ?>
                                <p class="mt-3 mb-1"><strong>Status Pembayaran:</strong> <?php echo htmlspecialchars($transactionLabel ?? '-'); ?></p>
                                <?php if (!empty($latestTransaction['order_id'])): ?>
                                    <p class="mb-1"><strong>ID Transaksi:</strong> <?php echo htmlspecialchars($latestTransaction['order_id']); ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (in_array($activeOrder['status'], ['pending', 'failed'], true)): ?>
                                <a href="/payment/create.php?order_id=<?php echo $activeOrder['id']; ?>" class="btn btn-primary mt-3">Bayar Sekarang</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">Anda belum memiliki pesanan aktif. Mulai dengan memesan paket hosting.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Tips Mengoptimalkan Hosting</h5>
                <ul class="mb-0">
                    <li>Aktifkan SSL untuk menjaga keamanan data pelanggan Anda.</li>
                    <li>Gunakan fitur backup harian untuk menghindari kehilangan data.</li>
                    <li>Hubungi tim support kami untuk migrasi website gratis.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

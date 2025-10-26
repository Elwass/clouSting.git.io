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

$activeOrderQuery = mysqli_query($conn, "SELECT p.*, pk.nama_paket, pk.harga FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id WHERE p.user_id = $customerId AND p.status IN ('menunggu', 'aktif') ORDER BY p.tanggal_pesanan DESC LIMIT 1");
$activeOrder = mysqli_fetch_assoc($activeOrderQuery);

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
                            <h4 class="fw-semibold"><?php echo htmlspecialchars($activeOrder['nama_paket']); ?></h4>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?php echo $activeOrder['status'] === 'aktif' ? 'success' : ($activeOrder['status'] === 'menunggu' ? 'warning text-dark' : 'secondary'); ?>"><?php echo ucfirst($activeOrder['status']); ?></span></p>
                            <p class="mb-1"><strong>Domain:</strong> <?php echo htmlspecialchars($activeOrder['domain']); ?></p>
                            <p class="mb-1"><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($activeOrder['metode_pembayaran']); ?></p>
                            <p class="mb-0"><strong>Tanggal Pesanan:</strong> <?php echo date('d M Y', strtotime($activeOrder['tanggal_pesanan'])); ?></p>
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

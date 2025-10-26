<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$totalCustomer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan"))['total'];
$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(pk.harga) as total FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id WHERE p.status IN ('aktif', 'selesai')"));
$totalRevenueValue = $totalRevenue['total'] ?? 0;
$latestOrders = mysqli_query($conn, "SELECT p.*, u.nama, pk.nama_paket FROM pesanan p JOIN users u ON p.user_id = u.id JOIN paket_hosting pk ON p.paket_id = pk.id ORDER BY p.tanggal_pesanan DESC LIMIT 5");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Dashboard Admin</h2>
            <p class="text-muted">Ringkasan aktivitas CloudHost.</p>
            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Total Pelanggan</h6>
                            <h3 class="fw-bold"><?php echo (int)$totalCustomer; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Total Pesanan</h6>
                            <h3 class="fw-bold"><?php echo (int)$totalOrders; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Pendapatan Aktif/Selesai</h6>
                            <h3 class="fw-bold">Rp <?php echo number_format($totalRevenueValue, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Pesanan Terbaru</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Paket</th>
                                    <th>Domain</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($latestOrders) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($latestOrders)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_paket']); ?></td>
                                            <td><?php echo htmlspecialchars($row['domain']); ?></td>
                                            <td><span class="badge bg-<?php echo $row['status'] === 'aktif' ? 'success' : ($row['status'] === 'menunggu' ? 'warning text-dark' : 'secondary'); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                            <td><?php echo date('d M Y', strtotime($row['tanggal_pesanan'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3">Belum ada pesanan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

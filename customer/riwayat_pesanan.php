<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['customer_id'])) {
    header('Location: /customer/login.php');
    exit;
}
$customerId = $_SESSION['customer_id'];

$query = mysqli_query($conn, "SELECT p.*, pk.nama_paket, pk.harga FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id WHERE p.user_id = $customerId ORDER BY p.tanggal_pesanan DESC");
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Riwayat Pesanan</h2>
            <p class="text-muted mb-0">Pantau status semua pesanan hosting Anda.</p>
        </div>
        <a href="/customer/dashboard.php" class="btn btn-outline-secondary">Kembali</a>
    </div>
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Paket</th>
                            <th>Domain</th>
                            <th>Harga</th>
                            <th>Metode Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal Pesanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_paket']); ?></td>
                                    <td><?php echo htmlspecialchars($row['domain']); ?></td>
                                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                    <td><span class="badge bg-<?php echo $row['status'] === 'aktif' ? 'success' : ($row['status'] === 'menunggu' ? 'warning text-dark' : 'secondary'); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($row['tanggal_pesanan'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada pesanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

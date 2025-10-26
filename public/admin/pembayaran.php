<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$transactions = mysqli_query($conn, "SELECT t.*, p.domain, p.status AS pesanan_status, pk.nama_paket, pk.harga, u.nama, u.email FROM transaksi t JOIN pesanan p ON t.pesanan_id = p.id JOIN users u ON p.user_id = u.id JOIN paket_hosting pk ON p.paket_id = pk.id ORDER BY t.transaction_time DESC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Riwayat Pembayaran</h2>
            <p class="text-muted">Pantau status transaksi Midtrans untuk setiap pesanan.</p>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Pelanggan</th>
                                    <th>Paket</th>
                                    <th>Nominal</th>
                                    <th>Metode</th>
                                    <th>Status Transaksi</th>
                                    <th>Status Pesanan</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($transactions && mysqli_num_rows($transactions) > 0): $no = 1; ?>
                                    <?php while ($trx = mysqli_fetch_assoc($transactions)): ?>
                                        <?php
                                        $trxStatus = strtolower($trx['transaction_status'] ?? '');
                                        $badgeMap = [
                                            'settlement' => 'success',
                                            'capture' => 'success',
                                            'pending' => 'warning text-dark',
                                            'deny' => 'danger',
                                            'cancel' => 'danger',
                                            'expire' => 'danger',
                                            'failure' => 'danger',
                                        ];
                                        $badge = $badgeMap[$trxStatus] ?? 'secondary';

                                        $orderStatusMap = [
                                            'pending' => 'warning text-dark',
                                            'paid' => 'primary',
                                            'failed' => 'danger',
                                            'aktif' => 'success',
                                            'selesai' => 'secondary',
                                        ];
                                        $orderBadge = $orderStatusMap[$trx['pesanan_status']] ?? 'secondary';
                                        ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo htmlspecialchars($trx['order_id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($trx['nama']); ?></strong><br>
                                                <span class="text-muted small"><?php echo htmlspecialchars($trx['email']); ?></span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($trx['nama_paket']); ?><br>
                                                <span class="small text-muted"><?php echo htmlspecialchars($trx['domain']); ?></span>
                                            </td>
                                            <?php $nominal = isset($trx['gross_amount']) ? (float)$trx['gross_amount'] : 0; ?>
                                            <td>Rp <?php echo number_format($nominal, 0, ',', '.'); ?></td>
                                            <td><?php echo htmlspecialchars($trx['payment_type'] ?? '-'); ?></td>
                                            <td><span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($trx['transaction_status'] ?? '-'); ?></span></td>
                                            <td><span class="badge bg-<?php echo $orderBadge; ?>"><?php echo htmlspecialchars($trx['pesanan_status']); ?></span></td>
                                            <td><?php echo date('d M Y H:i', strtotime($trx['transaction_time'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">Belum ada data transaksi.</td>
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

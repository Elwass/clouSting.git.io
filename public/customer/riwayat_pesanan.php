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

$query = mysqli_query($conn, "SELECT p.*, pk.nama_paket, pk.harga AS harga_reguler, pd.nama_paket AS nama_diskon FROM pesanan p JOIN paket_hosting pk ON p.paket_id = pk.id LEFT JOIN paket_diskon pd ON p.paket_diskon_id = pd.id WHERE p.user_id = $customerId ORDER BY p.tanggal_pesanan DESC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><?php echo htmlspecialchars(translate('customer_history_title', 'Riwayat Pesanan')); ?></h2>
            <p class="text-muted mb-0"><?php echo htmlspecialchars(translate('customer_history_subtitle', 'Pantau status semua pesanan hosting Anda.')); ?></p>
        </div>
        <a href="/customer/dashboard.php" class="btn btn-outline-secondary"><?php echo htmlspecialchars(translate('customer_history_back', 'Kembali')); ?></a>
    </div>
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_package', 'Paket')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_domain', 'Domain')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_price', 'Harga')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_payment', 'Metode Pembayaran')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_status', 'Status')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_date', 'Tanggal Pesanan')); ?></th>
                            <th><?php echo htmlspecialchars(translate('customer_history_table_project', 'File Proyek')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <?php
                                    $historyName = !empty($row['nama_diskon']) ? $row['nama_diskon'] : $row['nama_paket'];
                                    $historyIsDiscount = !empty($row['paket_diskon_id']);
                                    $historyTotal = isset($row['total_harga']) ? (float)$row['total_harga'] : (float)$row['harga_reguler'];
                                    $historyRegular = isset($row['harga_reguler']) ? (float)$row['harga_reguler'] : $historyTotal;
                                    ?>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span><?php echo htmlspecialchars($historyName); ?></span>
                                            <span class="badge bg-<?php echo $historyIsDiscount ? 'warning text-dark' : 'secondary'; ?>">
                                                <?php echo htmlspecialchars(translate($historyIsDiscount ? 'customer_order_discount_badge' : 'customer_order_regular_badge', $historyIsDiscount ? 'Diskon' : 'Reguler')); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['domain']); ?></td>
                                    <td>Rp <?php echo number_format($historyTotal, 0, ',', '.'); ?><?php if ($historyIsDiscount && $historyRegular > 0 && $historyRegular !== $historyTotal): ?><span class="text-muted small ms-2 text-decoration-line-through">Rp <?php echo number_format($historyRegular, 0, ',', '.'); ?></span><?php endif; ?></td>
                                    <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                    <?php
                                    $statusMap = [
                                        'pending' => ['badge' => 'warning text-dark', 'label' => translate('status_pending', 'Pending')],
                                        'paid' => ['badge' => 'primary', 'label' => translate('status_paid', 'Sudah Dibayar')],
                                        'failed' => ['badge' => 'danger', 'label' => translate('status_failed', 'Gagal')],
                                        'aktif' => ['badge' => 'success', 'label' => translate('status_aktif', 'Aktif')],
                                        'selesai' => ['badge' => 'secondary', 'label' => translate('status_selesai', 'Selesai')],
                                    ];
                                    $statusInfo = $statusMap[$row['status']] ?? ['badge' => 'secondary', 'label' => ucfirst($row['status'])];
                                    ?>
                                    <td><span class="badge bg-<?php echo $statusInfo['badge']; ?>"><?php echo $statusInfo['label']; ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($row['tanggal_pesanan'])); ?></td>
                                    <td>
                                        <?php if (!empty($row['project_file'])): ?>
                                            <a href="/<?php echo htmlspecialchars($row['project_file']); ?>" class="btn btn-sm btn-outline-primary" target="_blank"><?php echo htmlspecialchars(translate('customer_history_download', 'Unduh')); ?></a>
                                        <?php else: ?>
                                            <span class="text-muted small"><?php echo htmlspecialchars(translate('customer_history_no_file', 'Tidak ada')); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted"><?php echo htmlspecialchars(translate('customer_history_empty', 'Belum ada pesanan.')); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

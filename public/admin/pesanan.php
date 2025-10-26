<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = mysqli_real_escape_string($conn, trim($_POST['status'] ?? 'pending'));
    $statusAllow = ['pending', 'paid', 'failed', 'aktif', 'selesai'];
    if ($id > 0 && in_array($status, $statusAllow, true)) {
        mysqli_query($conn, "UPDATE pesanan SET status='$status' WHERE id=$id");
    }
    header('Location: pesanan.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    $fileQuery = mysqli_query($conn, "SELECT project_file FROM pesanan WHERE id=$hapusId");
    if ($fileQuery && mysqli_num_rows($fileQuery) > 0) {
        $fileData = mysqli_fetch_assoc($fileQuery);
        if (!empty($fileData['project_file'])) {
            $filePath = dirname(__DIR__) . '/' . $fileData['project_file'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }
    mysqli_query($conn, "DELETE FROM pesanan WHERE id=$hapusId");
    header('Location: pesanan.php');
    exit;
}

$orders = mysqli_query($conn, "SELECT p.*, u.nama, u.email, pk.nama_paket, pk.harga FROM pesanan p JOIN users u ON p.user_id = u.id JOIN paket_hosting pk ON p.paket_id = pk.id ORDER BY p.tanggal_pesanan DESC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Kelola Pesanan</h2>
            <p class="text-muted">Pantau dan ubah status pesanan pelanggan.</p>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Paket</th>
                                    <th>Domain</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>File Proyek</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($order = mysqli_fetch_assoc($orders)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['nama']); ?></strong><br>
                                            <span class="text-muted small"><?php echo htmlspecialchars($order['email']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['nama_paket']); ?></td>
                                        <td><?php echo htmlspecialchars($order['domain']); ?></td>
                                        <td><?php echo htmlspecialchars($order['metode_pembayaran']); ?></td>
                                        <td>Rp <?php echo number_format($order['harga'], 0, ',', '.'); ?></td>
                                        <td>
                                            <?php
                                            $statusMap = [
                                                'pending' => ['badge' => 'warning text-dark', 'label' => 'Pending'],
                                                'paid' => ['badge' => 'primary', 'label' => 'Sudah Dibayar'],
                                                'failed' => ['badge' => 'danger', 'label' => 'Gagal'],
                                                'aktif' => ['badge' => 'success', 'label' => 'Aktif'],
                                                'selesai' => ['badge' => 'secondary', 'label' => 'Selesai'],
                                            ];
                                            $statusInfo = $statusMap[$order['status']] ?? ['badge' => 'secondary', 'label' => ucfirst($order['status'])];
                                            ?>
                                            <div class="d-flex flex-column gap-2">
                                                <span class="badge bg-<?php echo $statusInfo['badge']; ?>"><?php echo $statusInfo['label']; ?></span>
                                                <form method="post" class="d-flex align-items-center gap-2">
                                                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm">
                                                        <?php foreach ([
                                                            'pending' => 'Pending',
                                                            'paid' => 'Sudah Dibayar',
                                                            'failed' => 'Gagal',
                                                            'aktif' => 'Aktif',
                                                            'selesai' => 'Selesai'
                                                        ] as $key => $label): ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $order['status'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td><?php echo date('d M Y H:i', strtotime($order['tanggal_pesanan'])); ?></td>
                                        <td>
                                            <?php if (!empty($order['project_file'])): ?>
                                                <a href="/<?php echo htmlspecialchars($order['project_file']); ?>" class="btn btn-sm btn-outline-secondary" target="_blank">Unduh</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?hapus=<?php echo $order['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pesanan ini?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

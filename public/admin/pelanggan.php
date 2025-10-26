<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

if (isset($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$hapusId AND role='customer'");
    header('Location: pelanggan.php');
    exit;
}

$customers = mysqli_query($conn, "SELECT u.*, COUNT(p.id) as total_pesanan FROM users u LEFT JOIN pesanan p ON p.user_id = u.id WHERE u.role='customer' GROUP BY u.id ORDER BY u.nama ASC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Data Pelanggan</h2>
            <p class="text-muted">Kelola informasi pelanggan CloudHost.</p>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Total Pesanan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($customer = mysqli_fetch_assoc($customers)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($customer['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo date('d M Y', strtotime($customer['created_at'])); ?></td>
                                        <td><?php echo (int)$customer['total_pesanan']; ?></td>
                                        <td>
                                            <a href="?hapus=<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pelanggan ini?');"><i class="fas fa-trash"></i></a>
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

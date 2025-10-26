<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_paket'] ?? ''));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));
    $harga = (int)($_POST['harga'] ?? 0);
    $fitur = mysqli_real_escape_string($conn, trim($_POST['fitur'] ?? ''));
    $id = (int)($_POST['id'] ?? 0);

    if ($nama === '' || $harga <= 0) {
        $errors[] = 'Nama paket dan harga wajib diisi.';
    }

    if (empty($errors)) {
        if ($id > 0) {
            $update = mysqli_query($conn, "UPDATE paket_hosting SET nama_paket='$nama', deskripsi='$deskripsi', harga=$harga, fitur='$fitur' WHERE id=$id");
            if ($update) {
                $success = 'Paket berhasil diperbarui.';
            } else {
                $errors[] = 'Gagal memperbarui paket.';
            }
        } else {
            $insert = mysqli_query($conn, "INSERT INTO paket_hosting (nama_paket, deskripsi, harga, fitur) VALUES ('$nama', '$deskripsi', $harga, '$fitur')");
            if ($insert) {
                $success = 'Paket baru berhasil ditambahkan.';
            } else {
                $errors[] = 'Gagal menambahkan paket.';
            }
        }
    }
}

if (isset($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM paket_hosting WHERE id=$hapusId");
    header('Location: paket.php');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editQuery = mysqli_query($conn, "SELECT * FROM paket_hosting WHERE id=$editId");
    $editData = mysqli_fetch_assoc($editQuery);
}

$paketList = mysqli_query($conn, "SELECT * FROM paket_hosting ORDER BY harga ASC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Kelola Paket Hosting</h2>
            <p class="text-muted">Tambah, ubah, atau hapus paket hosting CloudHost.</p>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3"><?php echo $editData ? 'Edit Paket' : 'Tambah Paket Baru'; ?></h5>
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Paket</label>
                                <input type="text" class="form-control" name="nama_paket" value="<?php echo htmlspecialchars($editData['nama_paket'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" name="harga" value="<?php echo htmlspecialchars($editData['harga'] ?? ''); ?>" min="10000" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi Singkat</label>
                                <input type="text" class="form-control" name="deskripsi" value="<?php echo htmlspecialchars($editData['deskripsi'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Fitur (pisahkan dengan baris baru)</label>
                                <textarea class="form-control" name="fitur" rows="4" placeholder="Unlimited Bandwidth&#10;Gratis SSL&#10;Backup Harian"><?php echo htmlspecialchars($editData['fitur'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Simpan Perubahan' : 'Tambah Paket'; ?></button>
                                <?php if ($editData): ?>
                                    <a href="/admin/paket.php" class="btn btn-outline-secondary ms-2">Batal</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Daftar Paket</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Paket</th>
                                    <th>Harga</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($paket = mysqli_fetch_assoc($paketList)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($paket['nama_paket']); ?></td>
                                        <td>Rp <?php echo number_format($paket['harga'], 0, ',', '.'); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($paket['deskripsi'])); ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $paket['id']; ?>" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                                            <a href="?hapus=<?php echo $paket['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus paket ini?');"><i class="fas fa-trash"></i></a>
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

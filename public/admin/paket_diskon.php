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

$paketHostingList = [];
$paketHostingResult = mysqli_query($conn, "SELECT id, nama_paket FROM paket_hosting ORDER BY nama_paket ASC");
if ($paketHostingResult) {
    while ($row = mysqli_fetch_assoc($paketHostingResult)) {
        $paketHostingList[] = $row;
    }
}

function sanitize_decimal($value)
{
    $filtered = preg_replace('/[^0-9.,]/', '', trim((string)$value));
    if ($filtered === '') {
        return 0.0;
    }

    $lastComma = strrpos($filtered, ',');
    $lastDot = strrpos($filtered, '.');

    if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
        // Format seperti 1.234,56 -> koma sebagai desimal.
        $normalized = str_replace('.', '', $filtered);
        $normalized = str_replace(',', '.', $normalized);
    } elseif ($lastDot !== false) {
        $fractionLength = strlen($filtered) - $lastDot - 1;
        if ($fractionLength === 0) {
            // Tidak ada angka setelah titik, anggap pemisah ribuan.
            $normalized = str_replace('.', '', $filtered);
            $normalized = str_replace(',', '', $normalized);
        } elseif ($fractionLength > 0 && $fractionLength <= 2) {
            // Titik sebagai desimal, koma sebagai ribuan.
            $normalized = str_replace(',', '', $filtered);
        } else {
            // Titik sebagai ribuan, hapus seluruh koma juga.
            $normalized = str_replace(['.', ','], ['', ''], $filtered);
        }
    } else {
        $normalized = str_replace(',', '', $filtered);
    }

    if (substr_count($normalized, '.') > 1) {
        $parts = explode('.', $normalized);
        $decimal = array_pop($parts);
        $normalized = implode('', $parts) . '.' . $decimal;
    }

    return is_numeric($normalized) ? (float)$normalized : 0.0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama_paket'] ?? ''));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));
    $paketHostingId = (int)($_POST['paket_hosting_id'] ?? 0);
    $hargaNormal = sanitize_decimal($_POST['harga_normal'] ?? 0);
    $hargaDiskon = sanitize_decimal($_POST['harga_diskon'] ?? 0);
    $tanggalMulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggalSelesai = trim($_POST['tanggal_selesai'] ?? '');
    $status = $_POST['status'] ?? 'aktif';
    $status = $status === 'draft' ? 'draft' : 'aktif';

    if ($nama === '') {
        $errors[] = 'Nama paket diskon wajib diisi.';
    }
    if ($paketHostingId <= 0) {
        $errors[] = 'Pilih paket hosting utama untuk diskon ini.';
    }
    if ($hargaNormal <= 0 || $hargaDiskon <= 0) {
        $errors[] = 'Harga normal dan harga diskon harus lebih besar dari 0.';
    } elseif ($hargaDiskon >= $hargaNormal) {
        $errors[] = 'Harga diskon harus lebih rendah dari harga normal.';
    }

    $mulaiSql = $tanggalMulai !== '' ? "'" . mysqli_real_escape_string($conn, $tanggalMulai) . "'" : 'NULL';
    $selesaiSql = $tanggalSelesai !== '' ? "'" . mysqli_real_escape_string($conn, $tanggalSelesai) . "'" : 'NULL';
    $hargaNormalSql = number_format($hargaNormal, 2, '.', '');
    $hargaDiskonSql = number_format($hargaDiskon, 2, '.', '');

    if (empty($errors)) {
        if ($id > 0) {
            $query = "UPDATE paket_diskon SET paket_hosting_id=$paketHostingId, nama_paket='$nama', deskripsi='$deskripsi', harga_normal=$hargaNormalSql, harga_diskon=$hargaDiskonSql, tanggal_mulai=$mulaiSql, tanggal_selesai=$selesaiSql, status='$status' WHERE id=$id";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $success = 'Paket diskon berhasil diperbarui.';
            } else {
                $errors[] = 'Gagal memperbarui paket diskon.';
            }
        } else {
            $query = "INSERT INTO paket_diskon (paket_hosting_id, nama_paket, deskripsi, harga_normal, harga_diskon, tanggal_mulai, tanggal_selesai, status) VALUES ($paketHostingId, '$nama', '$deskripsi', $hargaNormalSql, $hargaDiskonSql, $mulaiSql, $selesaiSql, '$status')";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $success = 'Paket diskon baru berhasil ditambahkan.';
            } else {
                $errors[] = 'Gagal menambahkan paket diskon.';
            }
        }
    }
}

if (isset($_GET['hapus'])) {
    $hapusId = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM paket_diskon WHERE id=$hapusId");
    header('Location: paket_diskon.php');
    exit;
}

$editData = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editQuery = mysqli_query($conn, "SELECT * FROM paket_diskon WHERE id=$editId");
    $editData = mysqli_fetch_assoc($editQuery);
}

$paketDiskonList = mysqli_query($conn, "SELECT * FROM paket_diskon ORDER BY created_at DESC");
?>
<?php require_once __DIR__ . '/../../partials/header.php'; ?>
<div class="d-flex">
    <?php require_once __DIR__ . '/../../partials/sidebar.php'; ?>
    <div class="flex-grow-1 bg-light min-vh-100">
        <div class="p-4">
            <h2 class="fw-bold">Kelola Paket Diskon</h2>
            <p class="text-muted">Atur paket promo yang muncul pada halaman promo pelanggan.</p>
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
                    <h5 class="fw-semibold mb-3"><?php echo $editData ? 'Edit Paket Diskon' : 'Tambah Paket Diskon'; ?></h5>
                    <form method="post">
                        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Nama Paket</label>
                                <input type="text" class="form-control" name="nama_paket" value="<?php echo htmlspecialchars($editData['nama_paket'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Paket Hosting Utama</label>
                                <select class="form-select" name="paket_hosting_id" required>
                                    <option value="">Pilih paket</option>
                                    <?php foreach ($paketHostingList as $paketHost): ?>
                                        <option value="<?php echo $paketHost['id']; ?>" <?php echo (isset($editData['paket_hosting_id']) && (int)$editData['paket_hosting_id'] === (int)$paketHost['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($paketHost['nama_paket']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga Normal (Rp)</label>
                                <input type="number" class="form-control" name="harga_normal" min="0" step="1000" value="<?php echo isset($editData['harga_normal']) ? (float)$editData['harga_normal'] : ''; ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga Diskon (Rp)</label>
                                <input type="number" class="form-control" name="harga_diskon" min="0" step="1000" value="<?php echo isset($editData['harga_diskon']) ? (float)$editData['harga_diskon'] : ''; ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3" placeholder="Tulis highlight promo dan benefit."><?php echo htmlspecialchars($editData['deskripsi'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" value="<?php echo htmlspecialchars($editData['tanggal_mulai'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tanggal_selesai" value="<?php echo htmlspecialchars($editData['tanggal_selesai'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="aktif" <?php echo ($editData['status'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="draft" <?php echo ($editData['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><?php echo $editData ? 'Simpan Perubahan' : 'Tambah Paket Diskon'; ?></button>
                                <?php if ($editData): ?>
                                    <a href="/admin/paket_diskon.php" class="btn btn-outline-secondary ms-2">Batal</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-semibold mb-3">Daftar Paket Diskon</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Paket</th>
                                    <th>Harga Normal</th>
                                    <th>Harga Diskon</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; if ($paketDiskonList && mysqli_num_rows($paketDiskonList) > 0): ?>
                                    <?php while ($paket = mysqli_fetch_assoc($paketDiskonList)): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($paket['nama_paket']); ?></div>
                                                <?php if (!empty($paket['deskripsi'])): ?>
                                                    <?php
                                                    $plainDesc = strip_tags($paket['deskripsi']);
                                                    if (function_exists('mb_strimwidth')) {
                                                        $shortDesc = mb_strimwidth($plainDesc, 0, 80, '...');
                                                    } else {
                                                        $shortDesc = strlen($plainDesc) > 80 ? substr($plainDesc, 0, 77) . '...' : $plainDesc;
                                                    }
                                                    ?>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($shortDesc); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>Rp <?php echo number_format($paket['harga_normal'], 0, ',', '.'); ?></td>
                                            <td class="text-success fw-semibold">Rp <?php echo number_format($paket['harga_diskon'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php
                                                $period = [];
                                                if (!empty($paket['tanggal_mulai'])) {
                                                    $period[] = date('d M Y', strtotime($paket['tanggal_mulai']));
                                                }
                                                if (!empty($paket['tanggal_selesai'])) {
                                                    $period[] = date('d M Y', strtotime($paket['tanggal_selesai']));
                                                }
                                                echo $period ? htmlspecialchars(implode(' - ', $period)) : '-';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($paket['status'] === 'aktif'): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="?edit=<?php echo $paket['id']; ?>" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i></a>
                                                <a href="?hapus=<?php echo $paket['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus paket diskon ini?');"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3">Belum ada paket diskon.</td>
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

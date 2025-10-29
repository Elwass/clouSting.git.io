<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['customer_id'])) {
    header('Location: /customer/login.php');
    exit;
}

$errors = [];
$success = '';

$paketResult = mysqli_query($conn, "SELECT * FROM paket_hosting ORDER BY harga ASC");
$paketList = [];
$paketMap = [];
while ($row = mysqli_fetch_assoc($paketResult)) {
    $paketList[] = $row;
    $paketMap[(int)$row['id']] = $row;
}

$discountResult = mysqli_query($conn, "SELECT pd.*, pk.nama_paket AS base_paket, pk.id AS base_id, pk.fitur AS base_fitur FROM paket_diskon pd JOIN paket_hosting pk ON pd.paket_hosting_id = pk.id WHERE pd.status='aktif' ORDER BY pd.harga_diskon ASC, pd.nama_paket ASC");
$diskonList = [];
$diskonMap = [];
if ($discountResult) {
    while ($row = mysqli_fetch_assoc($discountResult)) {
        $diskonList[] = $row;
        $diskonMap[(int)$row['id']] = $row;
    }
}

$selectedChoice = $_POST['package_choice'] ?? '';

ob_start();
require_once __DIR__ . '/../../partials/header.php';
$headerOutput = ob_get_clean();

$paymentOptions = [
    'Transfer Bank' => translate('customer_order_payment_option_transfer', 'Transfer Bank'),
    'Kartu Kredit' => translate('customer_order_payment_option_credit', 'Kartu Kredit'),
    'E-Wallet' => translate('customer_order_payment_option_ewallet', 'E-Wallet'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageChoice = trim($_POST['package_choice'] ?? '');
    $domain = mysqli_real_escape_string($conn, trim($_POST['domain'] ?? ''));
    $metode = mysqli_real_escape_string($conn, trim($_POST['metode_pembayaran'] ?? ''));
    $projectPath = '';
    $paketId = 0;
    $paketDiskonId = 0;
    $totalHarga = 0.0;

    if ($packageChoice === '') {
        $errors[] = translate('customer_order_error_selection', 'Pilih paket hosting terlebih dahulu.');
    } else {
        if (strpos($packageChoice, 'regular:') === 0) {
            $selectedId = (int)substr($packageChoice, strlen('regular:'));
            if (!isset($paketMap[$selectedId])) {
                $errors[] = translate('customer_order_error_selection', 'Pilih paket hosting terlebih dahulu.');
            } else {
                $paketId = $selectedId;
                $totalHarga = (float)$paketMap[$selectedId]['harga'];
            }
        } elseif (strpos($packageChoice, 'discount:') === 0) {
            $selectedId = (int)substr($packageChoice, strlen('discount:'));
            if (!isset($diskonMap[$selectedId])) {
                $errors[] = translate('customer_order_error_selection', 'Pilih paket hosting terlebih dahulu.');
            } else {
                $paketDiskonId = $selectedId;
                $paketId = (int)$diskonMap[$selectedId]['paket_hosting_id'];
                $totalHarga = (float)$diskonMap[$selectedId]['harga_diskon'];
            }
        } else {
            $errors[] = translate('customer_order_error_selection', 'Pilih paket hosting terlebih dahulu.');
        }
    }

    if ($paketId === 0 || $domain === '' || $metode === '') {
        $errors[] = translate('customer_order_error_required', 'Semua field wajib diisi.');
    }

    if (!isset($_FILES['project_zip']) || $_FILES['project_zip']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = translate('customer_order_error_upload_missing', 'Harap unggah folder project dalam format ZIP.');
    } else {
        $file = $_FILES['project_zip'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = translate('customer_order_error_upload_fail', 'Gagal mengunggah file project. Silakan coba lagi.');
        } else {
            $maxFileSize = 10 * 1024 * 1024; // 10 MB
            if ($file['size'] > $maxFileSize) {
                $errors[] = translate('customer_order_error_filesize', 'Ukuran file melebihi 10 MB. Harap kompres ulang project Anda.');
            } else {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($extension !== 'zip') {
                    $errors[] = translate('customer_order_error_filetype', 'Format file tidak valid. Hanya file ZIP yang diperbolehkan.');
                } else {
                    try {
                        $filename = 'project_' . $_SESSION['customer_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.zip';
                    } catch (Exception $e) {
                        $errors[] = translate('customer_order_error_process', 'Terjadi kesalahan saat memproses file project.');
                        $filename = '';
                    }

                    if ($filename !== '') {
                        $targetPath = rtrim(PROJECT_UPLOAD_DIR, '/') . '/' . $filename;
                        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                            $errors[] = translate('customer_order_error_save', 'Gagal menyimpan file project pada server.');
                        } else {
                            $projectPath = trim(PROJECT_UPLOAD_URI, '/') . '/' . $filename;
                        }
                    }
                }
            }
        }
    }

    if (!empty($errors) && $projectPath !== '') {
        $savedPath = rtrim(PROJECT_UPLOAD_DIR, '/') . '/' . basename($projectPath);
        if (is_file($savedPath)) {
            unlink($savedPath);
        }
        $projectPath = '';
    }

    if (empty($errors)) {
        $customerId = $_SESSION['customer_id'];
        $projectPathDb = mysqli_real_escape_string($conn, $projectPath);
        $paketDiskonSql = $paketDiskonId > 0 ? $paketDiskonId : 'NULL';
        $totalHargaSql = number_format($totalHarga, 2, '.', '');
        $insertQuery = "INSERT INTO pesanan (user_id, paket_id, paket_diskon_id, domain, metode_pembayaran, project_file, status, total_harga, tanggal_pesanan) VALUES ($customerId, $paketId, $paketDiskonSql, '$domain', '$metode', '$projectPathDb', 'pending', $totalHargaSql, NOW())";
        $insert = mysqli_query($conn, $insertQuery);
        if ($insert) {
            $success = translate('customer_order_success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran melalui tombol "Bayar Sekarang" di dashboard Anda.');
            $_POST = [];
            $selectedChoice = '';
        } else {
            if ($projectPath !== '') {
                $savedPath = rtrim(PROJECT_UPLOAD_DIR, '/') . '/' . basename($projectPath);
                if (is_file($savedPath)) {
                    unlink($savedPath);
                }
            }
            $errors[] = translate('customer_order_error_insert', 'Terjadi kesalahan saat menyimpan pesanan.');
        }
    }
}
?>
<?php echo $headerOutput; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h3 class="mb-4 text-center"><?php echo htmlspecialchars(translate('customer_order_heading', 'Pesan Paket Hosting Baru')); ?></h3>
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
                    <form method="post" enctype="multipart/form-data" class="order-form">
                        <div class="mb-4">
                            <p class="text-muted"><?php echo htmlspecialchars(translate('customer_order_select_plan', 'Pilih tipe paket yang ingin Anda pesan.')); ?></p>
                            <div class="order-package-group mb-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars(translate('customer_order_regular_title', 'Paket Reguler')); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars(translate('customer_order_regular_hint', 'Paket standar CloudHost dengan harga normal.')); ?></small>
                                    </div>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars(translate('customer_order_regular_badge', 'Reguler')); ?></span>
                                </div>
                                <?php if (!empty($paketList)): ?>
                                    <div class="row g-3 mt-2">
                                        <?php foreach ($paketList as $paket): ?>
                                            <?php
                                            $value = 'regular:' . $paket['id'];
                                            $isSelected = $selectedChoice === $value;
                                            $fiturList = array_filter(array_map('trim', explode("\n", $paket['fitur'] ?? '')));
                                            ?>
                                            <div class="col-md-6">
                                                <label class="package-option <?php echo $isSelected ? 'selected' : ''; ?>">
                                                    <input type="radio" name="package_choice" value="<?php echo htmlspecialchars($value); ?>" <?php echo $isSelected ? 'checked' : ''; ?>>
                                                    <div class="package-option-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars($paket['nama_paket']); ?></h5>
                                                            <span class="badge bg-light text-primary border border-primary-subtle">Rp <?php echo number_format($paket['harga'], 0, ',', '.'); ?></span>
                                                        </div>
                                                        <?php if (!empty($paket['deskripsi'])): ?>
                                                            <p class="text-muted small mb-2"><?php echo htmlspecialchars($paket['deskripsi']); ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($fiturList)): ?>
                                                            <ul class="list-unstyled small mb-0">
                                                                <?php foreach ($fiturList as $fitur): ?>
                                                                    <li class="d-flex align-items-start gap-2 mb-1"><i class="fas fa-check text-success"></i><span><?php echo htmlspecialchars($fitur); ?></span></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mt-3 mb-0"><?php echo htmlspecialchars(translate('customer_order_regular_empty', 'Belum ada paket reguler yang tersedia.')); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="order-package-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars(translate('customer_order_discount_title', 'Paket Diskon')); ?></h5>
                                        <small class="text-muted"><?php echo htmlspecialchars(translate('customer_order_discount_hint', 'Pilih paket promo aktif untuk mendapatkan harga spesial.')); ?></small>
                                    </div>
                                    <span class="badge bg-warning text-dark"><?php echo htmlspecialchars(translate('customer_order_discount_badge', 'Diskon')); ?></span>
                                </div>
                                <?php if (!empty($diskonList)): ?>
                                    <div class="row g-3 mt-2">
                                        <?php foreach ($diskonList as $diskon): ?>
                                            <?php
                                            $value = 'discount:' . $diskon['id'];
                                            $isSelected = $selectedChoice === $value;
                                            $baseFeatures = array_filter(array_map('trim', explode("\n", $diskon['base_fitur'] ?? '')));
                                            ?>
                                            <div class="col-md-6">
                                                <label class="package-option discount <?php echo $isSelected ? 'selected' : ''; ?>">
                                                    <input type="radio" name="package_choice" value="<?php echo htmlspecialchars($value); ?>" <?php echo $isSelected ? 'checked' : ''; ?>>
                                                    <div class="package-option-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <h5 class="fw-semibold mb-0"><?php echo htmlspecialchars($diskon['nama_paket']); ?></h5>
                                                                <small class="text-muted"><?php echo htmlspecialchars($diskon['base_paket']); ?></small>
                                                            </div>
                                                            <div class="text-end">
                                                                <div class="text-muted small text-decoration-line-through">Rp <?php echo number_format($diskon['harga_normal'], 0, ',', '.'); ?></div>
                                                                <div class="fw-bold text-success">Rp <?php echo number_format($diskon['harga_diskon'], 0, ',', '.'); ?></div>
                                                            </div>
                                                        </div>
                                                        <?php if (!empty($diskon['deskripsi'])): ?>
                                                            <p class="text-muted small mb-2"><?php echo nl2br(htmlspecialchars($diskon['deskripsi'])); ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($baseFeatures)): ?>
                                                            <ul class="list-unstyled small mb-0">
                                                                <?php foreach ($baseFeatures as $fitur): ?>
                                                                    <li class="d-flex align-items-start gap-2 mb-1"><i class="fas fa-star text-warning"></i><span><?php echo htmlspecialchars($fitur); ?></span></li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        <?php endif; ?>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning mt-3 mb-0"><?php echo htmlspecialchars(translate('customer_order_discount_empty', 'Belum ada paket diskon aktif.')); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_order_domain_label', 'Nama Domain')); ?></label>
                            <input type="text" class="form-control" name="domain" placeholder="<?php echo htmlspecialchars(translate('customer_order_domain_placeholder', 'contoh: bisnisanda.com')); ?>" value="<?php echo htmlspecialchars($_POST['domain'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_order_payment_label', 'Metode Pembayaran')); ?></label>
                            <select class="form-select" name="metode_pembayaran" required>
                                <option value=""><?php echo htmlspecialchars(translate('customer_order_payment_placeholder', 'Pilih metode')); ?></option>
                                <?php foreach ($paymentOptions as $value => $label): ?>
                                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES); ?>" <?php echo (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === $value) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_order_file_label', 'File Project (ZIP)')); ?></label>
                            <input type="file" class="form-control" name="project_zip" accept=".zip" required>
                            <div class="form-text"><?php echo htmlspecialchars(translate('customer_order_file_hint', 'Unggah folder project Anda dalam format .zip (maksimal 10 MB).')); ?></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?php echo htmlspecialchars(translate('customer_order_submit', 'Kirim Pesanan')); ?></button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="/customer/dashboard.php" class="text-decoration-none"><?php echo htmlspecialchars(translate('customer_order_back', 'Kembali ke Dashboard')); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.package-option input[type="radio"]').forEach(function (input) {
            input.addEventListener('change', function () {
                document.querySelectorAll('.package-option').forEach(function (label) {
                    var radio = label.querySelector('input[type="radio"]');
                    if (radio) {
                        label.classList.toggle('selected', radio.checked);
                    }
                });
            });
        });
    });
</script>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

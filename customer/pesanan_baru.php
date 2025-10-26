<?php
require_once __DIR__ . '/../config/config.php';
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
while ($row = mysqli_fetch_assoc($paketResult)) {
    $paketList[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paketId = (int)($_POST['paket_id'] ?? 0);
    $domain = mysqli_real_escape_string($conn, trim($_POST['domain'] ?? ''));
    $metode = mysqli_real_escape_string($conn, trim($_POST['metode_pembayaran'] ?? ''));

    if ($paketId === 0 || $domain === '' || $metode === '') {
        $errors[] = 'Semua field wajib diisi.';
    }

    if (empty($errors)) {
        $customerId = $_SESSION['customer_id'];
        $insert = mysqli_query($conn, "INSERT INTO pesanan (user_id, paket_id, domain, metode_pembayaran, status, tanggal_pesanan) VALUES ($customerId, $paketId, '$domain', '$metode', 'menunggu', NOW())");
        if ($insert) {
            $success = 'Pesanan berhasil dibuat. Tim kami akan segera memprosesnya.';
        } else {
            $errors[] = 'Terjadi kesalahan saat menyimpan pesanan.';
        }
    }
}
?>
<?php require_once __DIR__ . '/../partials/header.php'; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h3 class="mb-4 text-center">Pesan Paket Hosting Baru</h3>
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
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Paket Hosting</label>
                            <select class="form-select" name="paket_id" required>
                                <option value="">Pilih paket</option>
                                <?php foreach ($paketList as $paket): ?>
                                    <option value="<?php echo $paket['id']; ?>" <?php echo (isset($_POST['paket_id']) && $_POST['paket_id'] == $paket['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($paket['nama_paket']); ?> - Rp <?php echo number_format($paket['harga'], 0, ',', '.'); ?>/bln</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Domain</label>
                            <input type="text" class="form-control" name="domain" placeholder="contoh: bisnisanda.com" value="<?php echo htmlspecialchars($_POST['domain'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-select" name="metode_pembayaran" required>
                                <option value="">Pilih metode</option>
                                <?php
                                $metodes = ['Transfer Bank', 'Kartu Kredit', 'E-Wallet'];
                                foreach ($metodes as $metode):
                                    $selected = (isset($_POST['metode_pembayaran']) && $_POST['metode_pembayaran'] === $metode) ? 'selected' : '';
                                    echo "<option value='" . htmlspecialchars($metode, ENT_QUOTES) . "' $selected>$metode</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Pesanan</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="/customer/dashboard.php" class="text-decoration-none">Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

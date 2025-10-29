<?php
require_once __DIR__ . '/../../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

ob_start();
require_once __DIR__ . '/../../partials/header.php';
$headerOutput = ob_get_clean();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($conn, trim($_POST['nama'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = mysqli_real_escape_string($conn, trim($_POST['password'] ?? ''));
    $konfirmasi = mysqli_real_escape_string($conn, trim($_POST['konfirmasi_password'] ?? ''));

    if ($nama === '' || $email === '' || $password === '') {
        $errors[] = translate('customer_register_error_required', 'Semua field wajib diisi.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = translate('customer_register_error_email', 'Format email tidak valid.');
    }
    if ($password !== $konfirmasi) {
        $errors[] = translate('customer_register_error_password_confirm', 'Konfirmasi password tidak sesuai.');
    }

    if (empty($errors)) {
        $cekEmail = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (mysqli_num_rows($cekEmail) > 0) {
            $errors[] = translate('customer_register_error_email_exists', 'Email sudah terdaftar. Silakan login.');
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $insert = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hash', 'customer')");
            if ($insert) {
                $_SESSION['success_message'] = translate('customer_register_success', 'Registrasi berhasil. Silakan login.');
                header('Location: /customer/login.php');
                exit;
            } else {
                $errors[] = translate('customer_register_error_generic', 'Terjadi kesalahan saat registrasi.');
            }
        }
    }
}
?>
<?php echo $headerOutput; ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4"><?php echo htmlspecialchars(translate('customer_register_title', 'Registrasi Customer CloudHost')); ?></h3>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_register_name_label', 'Nama Lengkap')); ?></label>
                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_register_email_label', 'Email')); ?></label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_register_password_label', 'Password')); ?></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><?php echo htmlspecialchars(translate('customer_register_confirm_password_label', 'Konfirmasi Password')); ?></label>
                            <input type="password" class="form-control" name="konfirmasi_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100"><?php echo htmlspecialchars(translate('customer_register_submit', 'Daftar Sekarang')); ?></button>
                    </form>
                    <p class="text-center mt-3"><?php echo htmlspecialchars(translate('customer_register_login_prompt', 'Sudah punya akun?')); ?> <a href="/customer/login.php"><?php echo htmlspecialchars(translate('customer_register_login_link', 'Login di sini')); ?></a></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../partials/footer.php'; ?>

<div class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white" style="width: 250px; min-height: 100vh;">
    <a href="/admin/dashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4 fw-semibold">CloudHost Admin</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item"><a href="/admin/dashboard.php" class="nav-link text-white"><i class="fas fa-chart-line me-2"></i>Dashboard</a></li>
        <li><a href="/admin/paket.php" class="nav-link text-white"><i class="fas fa-box-open me-2"></i>Paket Hosting</a></li>
        <li><a href="/admin/pelanggan.php" class="nav-link text-white"><i class="fas fa-users me-2"></i>Pelanggan</a></li>
        <li><a href="/admin/pesanan.php" class="nav-link text-white"><i class="fas fa-shopping-cart me-2"></i>Pesanan</a></li>
        <li><a href="/admin/pembayaran.php" class="nav-link text-white"><i class="fas fa-receipt me-2"></i>Pembayaran</a></li>
    </ul>
    <hr>
    <div>
        <span class="small">Masuk sebagai: <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></span>
        <a class="btn btn-outline-light btn-sm ms-2" href="/admin/logout.php">Logout</a>
    </div>
</div>

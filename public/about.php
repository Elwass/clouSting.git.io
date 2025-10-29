<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="section-title"><?php echo htmlspecialchars(translate('about_page_title', 'Tentang CloudHost')); ?></h1>
                <p><?php echo htmlspecialchars(translate('about_page_intro_1', 'CloudHost berfokus pada penyediaan layanan cloud dan hosting yang stabil, aman, dan mudah digunakan. Kami membantu UMKM hingga perusahaan besar untuk membangun kehadiran online yang tangguh.')); ?></p>
                <p><?php echo htmlspecialchars(translate('about_page_intro_2', 'Didukung oleh infrastruktur modern, otomatisasi deployment, serta tim support berpengalaman, CloudHost terus berinovasi untuk memberikan pengalaman terbaik kepada pelanggan.')); ?></p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i> <?php echo htmlspecialchars(translate('about_page_bullet_1', 'Infrastruktur Tier-3 Data Center')); ?></li>
                    <li><i class="fas fa-check text-success me-2"></i> <?php echo htmlspecialchars(translate('about_page_bullet_2', 'Tim DevOps & Support Berpengalaman')); ?></li>
                    <li><i class="fas fa-check text-success me-2"></i> <?php echo htmlspecialchars(translate('about_page_bullet_3', 'SLA Uptime 99.9%')); ?></li>
                </ul>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded-4 shadow" alt="Tim CloudHost">
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

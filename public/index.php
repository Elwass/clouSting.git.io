<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>
<section class="hero-section text-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 text-lg-start text-center">
                <h1 class="display-5 fw-bold">Solusi Cloud & Hosting Tercepat untuk Bisnis Modern</h1>
                <p class="lead mt-4 mb-4">CloudHost menghadirkan performa tinggi, keamanan berlapis, dan uptime 99.9% agar website bisnis Anda selalu online.</p>
                <a href="/customer/register.php" class="btn btn-primary btn-lg">Mulai Sekarang</a>
            </div>
            <div class="col-lg-5 mt-5 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded-4 shadow-lg" alt="Cloud infrastructure">
            </div>
        </div>
    </div>
</section>
<section class="py-5" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Mengapa Memilih CloudHost?</h2>
            <p class="text-muted">Kami menghadirkan layanan terbaik dengan teknologi terkini dan dukungan pelanggan 24/7.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-tachometer-alt fa-2x"></i></div>
                    <h5 class="fw-semibold">Performa Tinggi</h5>
                    <p>Server kami dirancang untuk kecepatan maksimum dengan resource yang scalable.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-shield-alt fa-2x"></i></div>
                    <h5 class="fw-semibold">Keamanan Premium</h5>
                    <p>Sertifikat SSL gratis, proteksi DDoS, dan backup harian menjaga data Anda tetap aman.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-headset fa-2x"></i></div>
                    <h5 class="fw-semibold">Support 24/7</h5>
                    <p>Tim support siap membantu kapanpun melalui live chat, email, dan telepon.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5 bg-white" id="pricing">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Paket Hosting</h2>
            <p class="text-muted">Pilih paket sesuai kebutuhan website Anda.</p>
        </div>
        <div class="row g-4">
            <?php
            require_once __DIR__ . '/../config/config.php';
            $packages = mysqli_query($conn, "SELECT * FROM paket_hosting ORDER BY harga ASC");
            while ($paket = mysqli_fetch_assoc($packages)):
            ?>
            <div class="col-md-4">
                <div class="price-card <?php echo $paket['nama_paket'] === 'Business' ? 'featured' : ''; ?> text-center h-100">
                    <h4 class="fw-bold text-primary"><?php echo htmlspecialchars($paket['nama_paket']); ?></h4>
                    <p class="display-6 fw-bold mb-3">Rp <?php echo number_format($paket['harga'], 0, ',', '.'); ?>/bln</p>
                    <p class="text-muted"><?php echo htmlspecialchars($paket['deskripsi']); ?></p>
                    <ul class="list-unstyled text-start mt-3 mb-4">
                        <?php foreach (explode("\n", $paket['fitur']) as $fitur): ?>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($fitur); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="/customer/login.php" class="btn btn-primary">Pesan Sekarang</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<section class="py-5 bg-light" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="section-title">Tentang CloudHost</h2>
                <p>CloudHost merupakan penyedia layanan cloud & hosting yang didirikan oleh para profesional IT dengan pengalaman lebih dari 10 tahun. Kami berkomitmen memberikan solusi infrastruktur yang handal, aman, dan mudah digunakan.</p>
                <p>Dengan data center di Jakarta dan Singapore, CloudHost memberikan performa optimal bagi pelanggan di seluruh Asia Tenggara.</p>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <img src="https://images.unsplash.com/photo-1526498460520-4c246339dccb?auto=format&fit=crop&w=900&q=80" class="img-fluid rounded-4 shadow" alt="About CloudHost">
            </div>
        </div>
    </div>
</section>
<section class="py-5" id="cta">
    <div class="container text-center">
        <div class="bg-primary text-white p-5 rounded-4 shadow-lg">
            <h3 class="fw-bold">Siap meningkatkan performa website Anda?</h3>
            <p class="mb-4">Daftar sekarang dan nikmati diskon 30% untuk bulan pertama.</p>
            <a href="/customer/register.php" class="btn btn-light btn-lg text-primary fw-semibold">Daftar Gratis</a>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

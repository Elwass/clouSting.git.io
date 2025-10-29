<?php
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/navbar.php';
?>
<section class="hero-section text-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 text-lg-start text-center">
                <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($t['hero_title']); ?></h1>
                <p class="lead mt-4 mb-4"><?php echo htmlspecialchars($t['hero_subtitle']); ?></p>
                <a href="/customer/register.php" class="btn btn-primary btn-lg"><?php echo htmlspecialchars($t['hero_cta']); ?></a>
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
            <h2 class="section-title"><?php echo htmlspecialchars($t['services_title']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($t['services_subtitle']); ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-tachometer-alt fa-2x"></i></div>
                    <h5 class="fw-semibold"><?php echo htmlspecialchars($t['feature_1_title']); ?></h5>
                    <p><?php echo htmlspecialchars($t['feature_1_desc']); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-shield-alt fa-2x"></i></div>
                    <h5 class="fw-semibold"><?php echo htmlspecialchars($t['feature_2_title']); ?></h5>
                    <p><?php echo htmlspecialchars($t['feature_2_desc']); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-feature p-4 h-100">
                    <div class="icon mb-3 text-primary"><i class="fas fa-headset fa-2x"></i></div>
                    <h5 class="fw-semibold"><?php echo htmlspecialchars($t['feature_3_title']); ?></h5>
                    <p><?php echo htmlspecialchars($t['feature_3_desc']); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5 bg-white" id="pricing">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title"><?php echo htmlspecialchars($t['pricing_title']); ?></h2>
            <p class="text-muted"><?php echo htmlspecialchars($t['pricing_subtitle']); ?></p>
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
                    <a href="/customer/login.php" class="btn btn-primary"><?php echo htmlspecialchars($t['pricing_cta']); ?></a>
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
                <h2 class="section-title"><?php echo htmlspecialchars($t['about_title']); ?></h2>
                <p><?php echo htmlspecialchars($t['about_paragraph_1']); ?></p>
                <p><?php echo htmlspecialchars($t['about_paragraph_2']); ?></p>
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
            <h3 class="fw-bold"><?php echo htmlspecialchars($t['cta_title']); ?></h3>
            <p class="mb-4"><?php echo htmlspecialchars($t['cta_paragraph']); ?></p>
            <a href="/customer/register.php" class="btn btn-light btn-lg text-primary fw-semibold"><?php echo htmlspecialchars($t['cta_button']); ?></a>
        </div>
    </div>
</section>
<div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content discount-modal">
            <button type="button" class="btn-close ms-auto me-2 mt-2" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="row g-0 align-items-center">
                <div class="col-md-5 d-none d-md-block">
                    <div class="discount-modal-figure h-100 rounded-start">
                        <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=80" alt="Sales assistant" class="img-fluid h-100 w-100 object-fit-cover rounded-start">
                    </div>
                </div>
                <div class="col-md-7 p-4">
                    <span class="badge bg-primary-subtle text-primary text-uppercase mb-3">CloudHost Sales</span>
                    <h4 id="discountModalLabel" class="fw-bold mb-3"><?php echo htmlspecialchars($t['modal_title']); ?></h4>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($t['modal_description']); ?></p>
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3 mb-4">
                        <a class="btn btn-primary btn-lg" href="tel:+6281290077322">
                            <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($t['modal_cta']); ?>
                        </a>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle"><i class="fas fa-user-headset"></i></div>
                            <div>
                                <div class="fw-semibold"><?php echo htmlspecialchars($t['modal_contact']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($t['modal_note']); ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                        <i class="fas fa-gift"></i>
                        <span><?php echo htmlspecialchars($t['modal_bonus']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('discountModal');
        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }
        const hasShown = sessionStorage.getItem('cloudhost_discount_modal');
        if (hasShown) {
            return;
        }
        const discountModal = new bootstrap.Modal(modalElement);
        setTimeout(() => {
            discountModal.show();
            sessionStorage.setItem('cloudhost_discount_modal', '1');
        }, 1200);
    });
</script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>

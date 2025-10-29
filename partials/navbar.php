<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="/index.php">CloudHost</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item mx-2"><a class="nav-link" href="/index.php#services"><?php echo htmlspecialchars($t['nav_services']); ?></a></li>
                <li class="nav-item mx-2"><a class="nav-link" href="/index.php#pricing"><?php echo htmlspecialchars($t['nav_packages']); ?></a></li>
                <li class="nav-item mx-2"><a class="nav-link" href="/about.php"><?php echo htmlspecialchars($t['nav_about']); ?></a></li>
                <li class="nav-item mx-2"><a class="nav-link" href="/contact.php"><?php echo htmlspecialchars($t['nav_contact']); ?></a></li>
                <li class="nav-item mx-2">
                    <div class="dropdown language-switcher">
                        <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe-americas"></i>
                            <span><?php echo htmlspecialchars($languageOptions[$currentLang]); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <?php foreach ($languageOptions as $code => $label): ?>
                                <li>
                                    <?php
                                    $langUrl = $currentPath . '?' . $languageQueryPrefix . 'lang=' . urlencode($code);
                                    ?>
                                    <a class="dropdown-item <?php echo $code === $currentLang ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($langUrl); ?>">
                                        <?php echo htmlspecialchars($label); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mx-2"><a class="btn btn-outline-primary" href="/customer/login.php"><?php echo htmlspecialchars($t['nav_customer_login']); ?></a></li>
                <li class="nav-item mx-2"><a class="btn btn-primary" href="/admin/login.php"><?php echo htmlspecialchars($t['nav_admin_login']); ?></a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="promo-banner text-white" data-deadline="<?php echo htmlspecialchars($promoDeadlineIso); ?>">
    <div class="container d-flex flex-column flex-lg-row justify-content-lg-between align-items-lg-center gap-3 py-2">
        <div class="promo-text fw-semibold text-center text-lg-start">
            <span class="me-1"><?php echo htmlspecialchars($t['promo_headline']); ?></span>
            <span class="opacity-75"><?php echo htmlspecialchars($t['promo_subheadline']); ?></span>
        </div>
        <div class="d-flex flex-column flex-lg-row align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="countdown-label text-uppercase small fw-semibold"><?php echo htmlspecialchars($t['promo_countdown_label']); ?></span>
                <span id="promo-countdown" class="countdown-badge" data-deadline="<?php echo htmlspecialchars($promoDeadlineIso); ?>">--</span>
            </div>
            <?php
            $discountUrl = '/paket-diskon.php';
            if ($currentLang !== 'id') {
                $discountUrl .= '?lang=' . urlencode($currentLang);
            }
            ?>
            <a class="btn btn-warning fw-semibold text-dark" href="<?php echo htmlspecialchars($discountUrl); ?>"><?php echo htmlspecialchars($t['promo_button']); ?></a>
        </div>
    </div>
</div>

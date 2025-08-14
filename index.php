<?php
require_once 'includes/functions.php';

$page_title = 'GlobalBorsa - Türkiye\'nin En Güvenilir Kripto Borsası';

// Get some sample market data for display  
$markets = getMarketData('us_stocks', 6);
?>

<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="Türkiye'nin en güvenilir kripto borsası. 7/24 Türkçe destek, güvenli altyapı, düşük komisyonlar.">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="assets/css/landing-new.css" rel="stylesheet">
    <link href="assets/css/landing-index.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation - Direct Integration -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm" style="z-index: 10000 !important;">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php" style="font-size: 1.5rem; z-index: 10001 !important; position: relative;">
                <i class="fas fa-chart-line me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="z-index: 10001 !important;">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-medium <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-primary text-white rounded' : 'text-dark'; ?>" href="index.php" style="z-index: 10001 !important; position: relative; padding: 0.5rem 1rem;">
                            <i class="fas fa-home me-1"></i><?php echo getCurrentLang() == 'tr' ? 'Ana Sayfa' : 'Home'; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="markets.php" style="z-index: 10001 !important; position: relative; padding: 0.5rem 1rem;">
                            <i class="fas fa-chart-bar me-1"></i><?php echo t('markets'); ?>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="trading.php" style="z-index: 10001 !important; position: relative; padding: 0.5rem 1rem;">
                            <i class="fas fa-exchange-alt me-1"></i><?php echo t('trading'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="wallet.php" style="z-index: 10001 !important; position: relative; padding: 0.5rem 1rem;">
                            <i class="fas fa-wallet me-1"></i><?php echo t('wallet'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium text-dark" href="profile.php" style="z-index: 10001 !important; position: relative; padding: 0.5rem 1rem;">
                            <i class="fas fa-user me-1"></i><?php echo t('profile'); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Language Switcher -->
                    <div class="me-3" style="z-index: 10001 !important; position: relative;">
                        <a href="?lang=tr" class="btn btn-sm <?php echo getCurrentLang() == 'tr' ? 'btn-primary' : 'btn-outline-secondary'; ?> me-1">TR</a>
                        <a href="?lang=en" class="btn btn-sm <?php echo getCurrentLang() == 'en' ? 'btn-primary' : 'btn-outline-secondary'; ?>">EN</a>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <!-- User Balance -->
                        <div class="me-3">
                            <small class="text-muted"><?php echo t('balance'); ?>:</small>
                            <strong class="text-success"><?php echo getFormattedHeaderBalance($_SESSION['user_id']); ?></strong>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="z-index: 10001 !important; position: relative;">
                                <i class="fas fa-user me-1"></i><?php echo $_SESSION['username']; ?>
                            </button>
                            <ul class="dropdown-menu" style="z-index: 10002 !important;">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i><?php echo t('profile'); ?></a></li>
                                <li><a class="dropdown-item" href="wallet.php"><i class="fas fa-wallet me-2"></i><?php echo t('wallet'); ?></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i><?php echo t('logout'); ?></a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-primary me-2" style="z-index: 10001 !important; position: relative;"><?php echo t('login'); ?></a>
                        <a href="register.php" class="btn btn-primary" style="z-index: 10001 !important; position: relative;"><?php echo t('register'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Slider -->
    <section class="hero-slider" id="hero">
        <div class="slider-container">
            <!-- Slide 1 -->
            <div class="slide active">
                <div class="slide-background"></div>
                <div class="slide-content">
                    <div class="container">
                        <p class="hero-subtitle"><?php echo getCurrentLang() == 'tr' ? 'Binlerce yatırımcı bize güveniyor' : 'Thousands of investors trust us'; ?></p>
                        <h1 class="hero-title">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Türkiye\'nin en güvenilir <span class="highlight">kripto borsası</span> olmamız tesadüf değil' : 
                                'Being Turkey\'s most trusted <span class="highlight">crypto exchange</span> is no coincidence'; ?>
                        </h1>
                        <p class="hero-description">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Yatırımcılara rahatça kâr edebilecekleri seçkin bir yatırım ortamı sağlıyoruz.' : 
                                'We provide an exclusive investment environment where investors can easily profit.'; ?>
                        </p>
                        <a href="register.php" class="btn-cta">
                            <?php echo getCurrentLang() == 'tr' ? '1.000 TL\'ye varan %100 bonus alın' : 'Get up to 1,000 TL 100% bonus'; ?>
                        </a>
                        <p class="hero-disclaimer">
                            *<?php echo getCurrentLang() == 'tr' ? 'Sınırlı süreli teklif' : 'Limited time offer'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="slide">
                <div class="slide-background slide-bg-2"></div>
                <div class="slide-content">
                    <div class="container">
                        <p class="hero-subtitle"><?php echo getCurrentLang() == 'tr' ? 'Kripto pazarında lider pozisyon' : 'Leading position in crypto market'; ?></p>
                        <h1 class="hero-title">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Bitcoin ve Altcoin Ticaretinde <span class="highlight">Lider Platform</span>' : 
                                'Leading Platform in <span class="highlight">Bitcoin & Altcoin Trading</span>'; ?>
                        </h1>
                        <p class="hero-description">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Türkiye\'de 100.000\'den fazla yatırımcının tercih ettiği güvenilir platform. Düşük komisyonlar ve hızlı işlem garantisi.' : 
                                'Trusted platform preferred by over 100,000 investors in Turkey. Low commissions and fast transaction guarantee.'; ?>
                        </p>
                        <a href="register.php" class="btn-cta">
                            <?php echo getCurrentLang() == 'tr' ? 'Canlı Hesap Aç' : 'Open Live Account'; ?>
                        </a>
                        <p class="hero-disclaimer">
                            *<?php echo getCurrentLang() == 'tr' ? 'Risk uyarısı geçerlidir' : 'Risk warning applies'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="slide">
                <div class="slide-background slide-bg-3"></div>
                <div class="slide-content">
                    <div class="container">
                        <p class="hero-subtitle"><?php echo getCurrentLang() == 'tr' ? 'Profesyonel analist desteği' : 'Professional analyst support'; ?></p>
                        <h1 class="hero-title">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Uzman Analist Desteği ile <span class="highlight">Kazanmaya Başlayın</span>' : 
                                'Start Winning with <span class="highlight">Expert Analyst Support</span>'; ?>
                        </h1>
                        <p class="hero-description">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Günlük kripto analizleri, webinarlar ve eğitim materyalleri ile yatırım bilginizi artırın. Başarılı trader\'ların sırlarını öğrenin.' : 
                                'Increase your investment knowledge with daily crypto analysis, webinars and training materials. Learn the secrets of successful traders.'; ?>
                        </p>
                        <a href="markets.php" class="btn-cta">
                            <?php echo getCurrentLang() == 'tr' ? 'Piyasalara Göz Atın' : 'View Markets'; ?>
                        </a>
                        <p class="hero-disclaimer">
                            *<?php echo getCurrentLang() == 'tr' ? 'Eğitim materyalleri ücretsizdir' : 'Training materials are free'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Auto-play Progress Bar -->
        <div class="slider-progress" id="sliderProgress"></div>
    </section>

    <!-- US Stocks Ticker -->
    <section class="coin-ticker" id="coin-ticker">
        <div class="ticker-header">
            <h2><?php echo getCurrentLang() == 'tr' ? 'Amerika\'nın en büyük şirketlerine yatırım yapın' : 'Invest in America\'s largest companies'; ?></h2>
        </div>
        <div class="ticker-container">
            <div class="ticker-track">
                <!-- First set of US Stocks -->
                <div class="coin-item">
                    <div class="coin-flag logo-aapl"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">AAPL</div>
                        <div class="coin-name">Apple Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-msft"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">MSFT</div>
                        <div class="coin-name">Microsoft</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-googl"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">GOOGL</div>
                        <div class="coin-name">Alphabet Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-amzn"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">AMZN</div>
                        <div class="coin-name">Amazon</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-tsla"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">TSLA</div>
                        <div class="coin-name">Tesla Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-meta"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">META</div>
                        <div class="coin-name">Meta Platforms</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-nflx"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">NFLX</div>
                        <div class="coin-name">Netflix</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-v"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">V</div>
                        <div class="coin-name">Visa Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-ko"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">KO</div>
                        <div class="coin-name">Coca-Cola</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-jpm"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">JPM</div>
                        <div class="coin-name">JPMorgan Chase</div>
                    </div>
                </div>
                
                <!-- Duplicate set for seamless loop -->
                <div class="coin-item">
                    <div class="coin-flag logo-aapl"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">AAPL</div>
                        <div class="coin-name">Apple Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-msft"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">MSFT</div>
                        <div class="coin-name">Microsoft</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-googl"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">GOOGL</div>
                        <div class="coin-name">Alphabet Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-amzn"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">AMZN</div>
                        <div class="coin-name">Amazon</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-tsla"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">TSLA</div>
                        <div class="coin-name">Tesla Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-meta"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">META</div>
                        <div class="coin-name">Meta Platforms</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-nflx"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">NFLX</div>
                        <div class="coin-name">Netflix</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-v"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">V</div>
                        <div class="coin-name">Visa Inc.</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-ko"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">KO</div>
                        <div class="coin-name">Coca-Cola</div>
                    </div>
                </div>
                
                <div class="coin-item">
                    <div class="coin-flag logo-jpm"></div>
                    <div class="coin-info">
                        <div class="coin-symbol">JPM</div>
                        <div class="coin-name">JPMorgan Chase</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Cards -->
    <section class="services" id="services">
        <div class="container">
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3><?php echo getCurrentLang() == 'tr' ? 'Güvenli ve Şifreli' : 'Secure and Encrypted'; ?></h3>
                    <p><?php echo getCurrentLang() == 'tr' ? 'SSL şifreleme ve çoklu güvenlik katmanları ile paranız her zaman güvende.' : 'Your money is always safe with SSL encryption and multiple security layers.'; ?></p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3><?php echo getCurrentLang() == 'tr' ? 'Gelişmiş Ticaret Araçları' : 'Advanced Trading Tools'; ?></h3>
                    <p><?php echo getCurrentLang() == 'tr' ? 'Profesyonel grafik araçları ve teknik analiz göstergeleri ile ticaret yapın.' : 'Trade with professional charting tools and technical analysis indicators.'; ?></p>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3><?php echo getCurrentLang() == 'tr' ? '7/24 Türkçe Destek' : '24/7 Turkish Support'; ?></h3>
                    <p><?php echo getCurrentLang() == 'tr' ? 'Uzman ekibimiz 7 gün 24 saat Türkçe destek hizmeti sunmaktadır.' : 'Our expert team provides 24/7 Turkish support service.'; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Market Indicators -->
    <section class="market-indicators" id="indicators">
        <div class="container">
            <h2 class="section-title"><?php echo getCurrentLang() == 'tr' ? 'Canlı Piyasa Göstergeleri' : 'Live Market Indicators'; ?></h2>
            <div class="indicators-grid">
                <?php foreach (array_slice($markets, 0, 6) as $market): ?>
                <div class="indicator-item">
                    <span class="pair"><?php echo $market['symbol']; ?></span>
                    <span class="price"><?php echo formatPrice($market['price']); ?> TL</span>
                    <span class="change <?php echo $market['change_24h'] >= 0 ? 'positive' : 'negative'; ?>">
                        <?php echo ($market['change_24h'] >= 0 ? '+' : '') . number_format($market['change_24h'], 2); ?>%
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Promo Cards Section -->
    <section class="promo-cards" id="promo-cards">
        <div class="container">
            <h2 class="section-title animate-on-scroll">
                <?php echo getCurrentLang() == 'tr' ? 'Yatırımcılarımızın' : 'Take a look at our investors\''; ?>
                <span class="highlight"><?php echo getCurrentLang() == 'tr' ? 'favorilerine' : 'favorites'; ?></span> 
                <?php echo getCurrentLang() == 'tr' ? 'göz atın' : ''; ?>
            </h2>
            <p class="section-subtitle animate-on-scroll">
                <?php echo getCurrentLang() == 'tr' ? 
                    'Yatırımda herkesin ilk tercihi olmamızı sağlayan bazı vazgeçilmez ürünlerimiz hakkında bilgi edinin.' : 
                    'Learn about some of our indispensable products that make us everyone\'s first choice in investment.'; ?>
            </p>
            
            <div class="promo-grid">
                <!-- App Card -->
                <div class="promo-card dark-card">
                    <div class="promo-content">
                        <div class="promo-header">
                            <h3><?php echo getCurrentLang() == 'tr' ? 'GlobalBorsa uygulaması' : 'GlobalBorsa app'; ?></h3>
                            <div class="app-ratings">
                                <div class="rating">
                                    <i class="fab fa-apple"></i>
                                    <span>★★★★★</span>
                                </div>
                                <div class="rating">
                                    <i class="fab fa-google-play"></i>
                                    <span>★★★★★</span>
                                </div>
                            </div>
                        </div>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Yüksek puanlı, ödüllü GlobalBorsa uygulamasıyla hizmetlerine eksiksiz erişin.' : 'Get complete access to services with the highly-rated, award-winning GlobalBorsa app.'; ?></p>
                        <a href="profile.php" class="promo-btn">
                            <?php echo getCurrentLang() == 'tr' ? 'Hesabınıza Giriş Yapın' : 'Login to Your Account'; ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <div class="app-icon">📱</div>
                                <div class="app-name">GlobalBorsa</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bonus Card -->
                <div class="promo-card blue-card">
                    <div class="promo-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? '%100 bonus' : '100% bonus'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Daha fazla yatırım, daha az risk ve daha çok getiri için fonlarınızı kullanın.' : 'Use your funds for more investment, less risk and more returns.'; ?></p>
                        <div class="bonus-amount">
                            <?php echo getCurrentLang() == 'tr' ? '1.000 TL\'ye varan %100 bonus alın' : 'Get up to 1,000 TL 100% bonus'; ?>
                        </div>
                        <a href="register.php" class="promo-btn">
                            <?php echo getCurrentLang() == 'tr' ? 'Bonusunuzu alın' : 'Get your bonus'; ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <div class="bonus-visual">
                            <div class="gift-box">🎁</div>
                            <div class="bonus-text">%100</div>
                        </div>
                    </div>
                </div>

                <!-- Competition Card -->
                <div class="promo-card green-card">
                    <div class="promo-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? 'GlobalBorsa yarışmaları' : 'GlobalBorsa competitions'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Yatırımlarınızla zirveye ilerleyin ve toplam 50.000 TL çekilebilir nakit ödülden payınızı alın.' : 'Advance to the top with your investments and get your share of 50,000 TL total withdrawable cash prizes.'; ?></p>
                        <a href="trading.php" class="promo-btn">
                            <?php echo getCurrentLang() == 'tr' ? 'Hemen katılın' : 'Join now'; ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <div class="trophy-visual">
                            <div class="trophy">🏆</div>
                            <div class="prize-text">50.000 TL</div>
                        </div>
                    </div>
                </div>

                <!-- Copy Trade Card -->
                <div class="promo-card light-card">
                    <div class="promo-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? 'GlobalBorsa copy trade' : 'GlobalBorsa copy trade'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Kazançlı yatırım stratejilerini kopyalayan 1.000\'den fazla yatırımcıya katılın ya da işlemlerinizi paylaşıp komisyon kazanın.' : 'Join over 1,000 investors copying profitable investment strategies or share your trades and earn commissions.'; ?></p>
                        <a href="wallet.php" class="promo-btn">
                            <?php echo getCurrentLang() == 'tr' ? 'Cüzdanınızı Görüntüleyin' : 'View Your Wallet'; ?> 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="promo-visual">
                        <div class="copy-visual">
                            <div class="user-avatar">👤</div>
                            <div class="copy-arrows">📈</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Education Section -->
    <section class="education" id="egitim">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php echo getCurrentLang() == 'tr' ? 'Eğitim ve Analiz Merkezi' : 'Education and Analysis Center'; ?></h2>
                <p class="section-description"><?php echo getCurrentLang() == 'tr' ? 'Başarılı yatırımcı olmak için gereken tüm bilgileri uzman ekibimizden öğrenin.' : 'Learn everything you need to become a successful investor from our expert team.'; ?></p>
            </div>

            <div class="education-grid">
                <div class="education-card">
                    <div class="card-image">
                        <i class="fas fa-video"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? 'Canlı Webinarlar' : 'Live Webinars'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Uzman analistlerden canlı kripto piyasa analizleri ve ticaret stratejileri öğrenin.' : 'Learn live crypto market analysis and trading strategies from expert analysts.'; ?></p>
                        <button class="card-btn"><?php echo getCurrentLang() == 'tr' ? 'Katıl' : 'Join'; ?></button>
                    </div>
                </div>

                <div class="education-card">
                    <div class="card-image">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? 'Kripto Sözlüğü' : 'Crypto Dictionary'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Kripto para ticaretinde kullanılan tüm terimleri detaylı açıklamalarıyla öğrenin.' : 'Learn all terms used in cryptocurrency trading with detailed explanations.'; ?></p>
                        <button class="card-btn"><?php echo getCurrentLang() == 'tr' ? 'Keşfet' : 'Explore'; ?></button>
                    </div>
                </div>

                <div class="education-card">
                    <div class="card-image">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-content">
                        <h3><?php echo getCurrentLang() == 'tr' ? 'Temel Teknik Analiz' : 'Basic Technical Analysis'; ?></h3>
                        <p><?php echo getCurrentLang() == 'tr' ? 'Grafik okuma, indikatörler ve ticaret sinyalleri hakkında temel bilgileri edinin.' : 'Get basic information about chart reading, indicators and trading signals.'; ?></p>
                        <button class="card-btn"><?php echo getCurrentLang() == 'tr' ? 'Başla' : 'Start'; ?></button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact/CTA Section -->
    <section class="contact-cta" id="iletisim">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info">
                    <h2><?php echo getCurrentLang() == 'tr' ? 'Sizi Arayalım' : 'Let Us Call You'; ?></h2>
                    <p><?php echo getCurrentLang() == 'tr' ? 'Yatırım danışmanlarımız size en uygun hesap türünü ve yatırım stratejisini belirlemek için iletişime geçsin.' : 'Let our investment advisors contact you to determine the most suitable account type and investment strategy for you.'; ?></p>
                    <div class="contact-features">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo getCurrentLang() == 'tr' ? 'Ücretsiz danışmanlık' : 'Free consultation'; ?></span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo getCurrentLang() == 'tr' ? 'Kişiselleştirilmiş strateji' : 'Personalized strategy'; ?></span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo getCurrentLang() == 'tr' ? 'Risk yönetimi' : 'Risk management'; ?></span>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <form id="callbackForm">
                        <div class="form-group">
                            <input type="text" id="name" name="name" placeholder="<?php echo getCurrentLang() == 'tr' ? 'Adınız Soyadınız' : 'Your Name Surname'; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" id="phone" name="phone" placeholder="<?php echo getCurrentLang() == 'tr' ? 'Telefon Numaranız' : 'Your Phone Number'; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" name="email" placeholder="<?php echo getCurrentLang() == 'tr' ? 'E-posta Adresiniz' : 'Your Email Address'; ?>" required>
                        </div>
                        <div class="form-group">
                            <select id="experience" name="experience" required>
                                <option value=""><?php echo getCurrentLang() == 'tr' ? 'Yatırım Deneyiminiz' : 'Your Investment Experience'; ?></option>
                                <option value="beginner"><?php echo getCurrentLang() == 'tr' ? 'Yeni başlıyorum' : 'Just starting'; ?></option>
                                <option value="intermediate"><?php echo getCurrentLang() == 'tr' ? 'Orta seviye' : 'Intermediate'; ?></option>
                                <option value="advanced"><?php echo getCurrentLang() == 'tr' ? 'İleri seviye' : 'Advanced'; ?></option>
                            </select>
                        </div>
                        <button type="submit" class="submit-btn"><?php echo getCurrentLang() == 'tr' ? 'Beni Arayın' : 'Call Me'; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Include existing footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Live Support Button -->
    <div class="live-support" id="liveSupport">
        <button class="support-btn">
            <i class="fas fa-comments"></i>
            <span><?php echo getCurrentLang() == 'tr' ? 'Canlı Destek' : 'Live Support'; ?></span>
        </button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/landing-new.js"></script>
    
    <!-- Inline JavaScript for Slider -->
    <script>
        // Manual navigation click handlers - Force fix
        document.addEventListener('DOMContentLoaded', function() {
            // Specifically handle navigation menu links
            const navMenuLinks = document.querySelectorAll('.navbar-nav .nav-link');
            console.log('Found nav menu links:', navMenuLinks.length);
            
            navMenuLinks.forEach(function(link, index) {
                console.log('Setting up click handler for nav link', index, link.textContent);
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const href = this.getAttribute('href');
                    console.log('Navigation link clicked:', href);
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
            });
            
            // Handle navbar brand
            const navbarBrand = document.querySelector('.navbar-brand');
            if (navbarBrand) {
                navbarBrand.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const href = this.getAttribute('href');
                    console.log('Brand clicked:', href);
                    if (href && href !== '#') {
                        window.location.href = href;
                    }
                });
            }
            
        // Initialize slider
        initSlider();
        });
        
        // Hero Slider function
        function initSlider() {
            console.log('DOM loaded, initializing slider...');
            
            const slides = document.querySelectorAll('.slide');
            const progressBar = document.getElementById('sliderProgress');
            let currentSlide = 0;
            let slideInterval;
            
            console.log('Found slides:', slides.length);
            
            if (slides.length === 0) {
                console.log('No slides found!');
                return;
            }
            
            // Function to show specific slide
            function showSlide(index) {
                console.log('Showing slide:', index);
                
                // Remove active class from all slides
                slides.forEach((slide, i) => {
                    slide.classList.remove('active');
                    console.log('Removed active from slide', i);
                });
                
                // Add active class to current slide
                slides[index].classList.add('active');
                console.log('Added active to slide', index);
                
                currentSlide = index;
                
                // Update progress bar
                if (progressBar) {
                    progressBar.style.width = '0%';
                    setTimeout(() => {
                        progressBar.style.width = '100%';
                    }, 100);
                }
            }
            
            // Auto-slide functionality
            function startAutoSlide() {
                slideInterval = setInterval(() => {
                    console.log('Auto advancing from slide', currentSlide);
                    currentSlide = (currentSlide + 1) % slides.length;
                    showSlide(currentSlide);
                }, 5000); // 5 seconds
            }
            
            // Initialize progress bar
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.style.transition = 'width 5s linear';
                setTimeout(() => {
                    progressBar.style.width = '100%';
                }, 100);
            }
            
            // Start auto-slide
            startAutoSlide();
            console.log('Auto-slide started');
            
            // Manual controls for testing
            window.nextSlide = function() {
                clearInterval(slideInterval);
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
                setTimeout(startAutoSlide, 1000);
            };
            
            window.prevSlide = function() {
                clearInterval(slideInterval);
                currentSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1;
                showSlide(currentSlide);
                setTimeout(startAutoSlide, 1000);
            };
            
            // Pause on hover
            const heroSlider = document.querySelector('.hero-slider');
            if (heroSlider) {
                heroSlider.addEventListener('mouseenter', () => {
                    clearInterval(slideInterval);
                    console.log('Paused on hover');
                });
                
                heroSlider.addEventListener('mouseleave', () => {
                    startAutoSlide();
                    console.log('Resumed on leave');
                });
            }
            
            // Keyboard controls
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    window.prevSlide();
                } else if (e.key === 'ArrowRight') {
                    window.nextSlide();
                }
            });
            
            console.log('Slider initialization complete');
        });
        
        // Test function - you can call this in browser console
        window.testSlider = function() {
            console.log('Testing slider...');
            const slides = document.querySelectorAll('.slide');
            console.log('Slides found:', slides.length);
            slides.forEach((slide, i) => {
                console.log('Slide', i, 'classes:', slide.className);
            });
        };
    </script>
</body>
</html>

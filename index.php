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
    
    <style>
        /* Ticker Animation Keyframes */
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        
        /* Force ticker styles */
        .ticker-track {
            display: flex !important;
            animation: ticker 30s linear infinite !important;
            gap: 2rem !important;
            width: max-content !important;
        }
        
        .coin-ticker:hover .ticker-track {
            animation-play-state: paused !important;
        }
        
        .coin-item {
            flex-shrink: 0 !important;
            background: white !important;
            border-radius: 15px !important;
            padding: 1.5rem !important;
            min-width: 200px !important;
            text-align: center !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1) !important;
            transition: transform 0.3s ease !important;
        }
        
        .coin-item:hover {
            transform: translateY(-5px) !important;
        }
        
        .coin-flag {
            font-size: 2rem !important;
            margin-bottom: 0.5rem !important;
        }
    </style>
</head>
<body>
    <!-- STYLED NAVBAR (Bootstrap-like) -->
    <nav style="position: fixed; top: 0; left: 0; right: 0; background: #ffffff; z-index: 999999; padding: 12px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-bottom: 1px solid #dee2e6;">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Brand -->
                <a href="index.php" style="text-decoration: none; color: #007bff; font-weight: 700; font-size: 1.5rem; display: flex; align-items: center;">
                    <i class="fas fa-chart-line" style="margin-right: 8px; color: #007bff;"></i>
                    <?php echo SITE_NAME; ?>
                </a>
                
                <!-- Navigation Links -->
                <div class="d-flex align-items-center" style="flex-grow: 1; justify-content: center;">
                    <div class="d-flex gap-1">
                        <a href="index.php" style="text-decoration: none; color: <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#007bff' : '#495057'; ?>; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; background: <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>;" onmouseover="this.style.background='rgba(0,123,255,0.1)'; this.style.color='#007bff';" onmouseout="this.style.background='<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>'; this.style.color='<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#007bff' : '#495057'; ?>';">
                            <i class="fas fa-home" style="margin-right: 6px; font-size: 14px;"></i>
                            <?php echo getCurrentLang() == 'tr' ? 'Ana Sayfa' : 'Home'; ?>
                        </a>
                        
                        <a href="markets.php" style="text-decoration: none; color: <?php echo basename($_SERVER['PHP_SELF']) == 'markets.php' ? '#007bff' : '#495057'; ?>; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; background: <?php echo basename($_SERVER['PHP_SELF']) == 'markets.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>;" onmouseover="this.style.background='rgba(0,123,255,0.1)'; this.style.color='#007bff';" onmouseout="this.style.background='<?php echo basename($_SERVER['PHP_SELF']) == 'markets.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>'; this.style.color='<?php echo basename($_SERVER['PHP_SELF']) == 'markets.php' ? '#007bff' : '#495057'; ?>';">
                            <i class="fas fa-chart-bar" style="margin-right: 6px; font-size: 14px;"></i>
                            <?php echo t('markets'); ?>
                        </a>
                        
                        <?php if (isLoggedIn()): ?>
                        <a href="trading.php" style="text-decoration: none; color: <?php echo basename($_SERVER['PHP_SELF']) == 'trading.php' ? '#007bff' : '#495057'; ?>; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; background: <?php echo basename($_SERVER['PHP_SELF']) == 'trading.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>;" onmouseover="this.style.background='rgba(0,123,255,0.1)'; this.style.color='#007bff';" onmouseout="this.style.background='<?php echo basename($_SERVER['PHP_SELF']) == 'trading.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>'; this.style.color='<?php echo basename($_SERVER['PHP_SELF']) == 'trading.php' ? '#007bff' : '#495057'; ?>';">
                            <i class="fas fa-exchange-alt" style="margin-right: 6px; font-size: 14px;"></i>
                            <?php echo t('trading'); ?>
                        </a>
                        
                        <a href="wallet.php" style="text-decoration: none; color: <?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? '#007bff' : '#495057'; ?>; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; background: <?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>;" onmouseover="this.style.background='rgba(0,123,255,0.1)'; this.style.color='#007bff';" onmouseout="this.style.background='<?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>'; this.style.color='<?php echo basename($_SERVER['PHP_SELF']) == 'wallet.php' ? '#007bff' : '#495057'; ?>';">
                            <i class="fas fa-wallet" style="margin-right: 6px; font-size: 14px;"></i>
                            <?php echo t('wallet'); ?>
                        </a>
                        
                        <a href="profile.php" style="text-decoration: none; color: <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? '#007bff' : '#495057'; ?>; padding: 8px 16px; border-radius: 6px; font-weight: 500; transition: all 0.2s ease; background: <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>;" onmouseover="this.style.background='rgba(0,123,255,0.1)'; this.style.color='#007bff';" onmouseout="this.style.background='<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'rgba(0,123,255,0.1)' : 'transparent'; ?>'; this.style.color='<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? '#007bff' : '#495057'; ?>';">
                            <i class="fas fa-user" style="margin-right: 6px; font-size: 14px;"></i>
                            <?php echo t('profile'); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right Side -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Language Switcher -->
                    <div class="d-flex" style="margin-right: 16px;">
                        <a href="?lang=tr" style="text-decoration: none; padding: 6px 12px; background: <?php echo getCurrentLang() == 'tr' ? '#007bff' : '#f8f9fa'; ?>; color: <?php echo getCurrentLang() == 'tr' ? 'white' : '#6c757d'; ?>; border-radius: 4px 0 0 4px; font-size: 0.875rem; font-weight: 500; border: 1px solid <?php echo getCurrentLang() == 'tr' ? '#007bff' : '#dee2e6'; ?>; border-right: none;">TR</a>
                        <a href="?lang=en" style="text-decoration: none; padding: 6px 12px; background: <?php echo getCurrentLang() == 'en' ? '#007bff' : '#f8f9fa'; ?>; color: <?php echo getCurrentLang() == 'en' ? 'white' : '#6c757d'; ?>; border-radius: 0 4px 4px 0; font-size: 0.875rem; font-weight: 500; border: 1px solid <?php echo getCurrentLang() == 'en' ? '#007bff' : '#dee2e6'; ?>;">EN</a>
                    </div>
                    
                    <?php if (isLoggedIn()): ?>
                        <!-- User Balance -->
                        <div style="margin-right: 16px; font-size: 0.875rem;">
                            <span style="color: #6c757d;"><?php echo t('balance'); ?>:</span>
                            <strong style="color: #28a745;"><?php echo getFormattedHeaderBalance($_SESSION['user_id']); ?></strong>
                        </div>
                        
                        <!-- User Menu Button -->
                        <div style="position: relative; display: inline-block;">
                            <button style="background: #fff; border: 1px solid #007bff; color: #007bff; padding: 8px 16px; border-radius: 6px; font-weight: 500; cursor: pointer; display: flex; align-items: center; font-size: 0.9rem;" onclick="toggleUserMenu()">
                                <i class="fas fa-user" style="margin-right: 6px; font-size: 14px;"></i>
                                <?php echo $_SESSION['username']; ?>
                                <i class="fas fa-chevron-down" style="margin-left: 6px; font-size: 12px;"></i>
                            </button>
                            <div id="userMenu" style="position: absolute; top: 100%; right: 0; background: white; border: 1px solid #dee2e6; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); min-width: 180px; z-index: 1000; display: none; margin-top: 4px;">
                                <a href="profile.php" style="display: block; padding: 12px 16px; color: #495057; text-decoration: none; border-bottom: 1px solid #f8f9fa;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='white';">
                                    <i class="fas fa-user" style="margin-right: 8px; color: #6c757d;"></i><?php echo t('profile'); ?>
                                </a>
                                <a href="wallet.php" style="display: block; padding: 12px 16px; color: #495057; text-decoration: none; border-bottom: 1px solid #f8f9fa;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='white';">
                                    <i class="fas fa-wallet" style="margin-right: 8px; color: #6c757d;"></i><?php echo t('wallet'); ?>
                                </a>
                                <a href="logout.php" style="display: block; padding: 12px 16px; color: #dc3545; text-decoration: none;" onmouseover="this.style.background='#f8f9fa';" onmouseout="this.style.background='white';">
                                    <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i><?php echo t('logout'); ?>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" style="text-decoration: none; color: #007bff; padding: 8px 16px; border: 1px solid #007bff; border-radius: 6px; font-weight: 500; margin-right: 8px; transition: all 0.2s ease;" onmouseover="this.style.background='#007bff'; this.style.color='white';" onmouseout="this.style.background='transparent'; this.style.color='#007bff';">
                            <?php echo t('login'); ?>
                        </a>
                        <a href="register.php" style="text-decoration: none; color: white; padding: 8px 16px; background: #007bff; border: 1px solid #007bff; border-radius: 6px; font-weight: 500; transition: all 0.2s ease;" onmouseover="this.style.background='#0056b3'; this.style.borderColor='#0056b3';" onmouseout="this.style.background='#007bff'; this.style.borderColor='#007bff';">
                            <?php echo t('register'); ?>
                        </a>
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

    <!-- PURE INLINE TICKER SECTION -->
    <section style="background: #1a1a1a; padding: 60px 0; overflow: hidden;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: #fff; font-size: 2rem; font-weight: 600; margin: 0;">
                    <?php echo getCurrentLang() == 'tr' ? 'Amerika\'nın en büyük şirketlerine yatırım yapın' : 'Invest in America\'s largest companies'; ?>
                </h2>
            </div>
            
            <div style="overflow: hidden; position: relative; mask: linear-gradient(90deg, transparent, white 10%, white 90%, transparent); -webkit-mask: linear-gradient(90deg, transparent, white 10%, white 90%, transparent);">
                <div style="display: flex; animation: stockTicker 30s linear infinite; gap: 30px; width: max-content;" onmouseover="this.style.animationPlayState='paused'" onmouseout="this.style.animationPlayState='running'">
                    
                    <!-- Apple -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #000, #333); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">🍎</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">AAPL</div>
                        <div style="font-size: 0.9rem; color: #666;">Apple Inc.</div>
                    </div>
                    
                    <!-- Microsoft -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #0078d4, #005a9e); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">MS</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">MSFT</div>
                        <div style="font-size: 0.9rem; color: #666;">Microsoft</div>
                    </div>
                    
                    <!-- Google -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #4285f4, #34a853, #fbbc04, #ea4335); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">G</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">GOOGL</div>
                        <div style="font-size: 0.9rem; color: #666;">Alphabet Inc.</div>
                    </div>
                    
                    <!-- Amazon -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #ff9900, #ff6600); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">📦</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">AMZN</div>
                        <div style="font-size: 0.9rem; color: #666;">Amazon</div>
                    </div>
                    
                    <!-- Tesla -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #cc0000, #990000); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">⚡</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">TSLA</div>
                        <div style="font-size: 0.9rem; color: #666;">Tesla Inc.</div>
                    </div>
                    
                    <!-- Meta -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #1877f2, #0d47a1); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">f</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">META</div>
                        <div style="font-size: 0.9rem; color: #666;">Meta Platforms</div>
                    </div>
                    
                    <!-- Netflix -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #e50914, #b20710); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">N</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">NFLX</div>
                        <div style="font-size: 0.9rem; color: #666;">Netflix</div>
                    </div>
                    
                    <!-- Visa -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #1a1f71, #0d1348); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">💳</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">V</div>
                        <div style="font-size: 0.9rem; color: #666;">Visa Inc.</div>
                    </div>
                    
                    <!-- Coca-Cola -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #fe0000, #cc0000); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">🥤</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">KO</div>
                        <div style="font-size: 0.9rem; color: #666;">Coca-Cola</div>
                    </div>
                    
                    <!-- JPMorgan -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #005daa, #004080); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">🏦</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">JPM</div>
                        <div style="font-size: 0.9rem; color: #666;">JPMorgan Chase</div>
                    </div>
                    
                    <!-- DUPLICATE SET FOR SEAMLESS LOOP -->
                    
                    <!-- Apple Duplicate -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #000, #333); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">🍎</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">AAPL</div>
                        <div style="font-size: 0.9rem; color: #666;">Apple Inc.</div>
                    </div>
                    
                    <!-- Microsoft Duplicate -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #0078d4, #005a9e); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">MS</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">MSFT</div>
                        <div style="font-size: 0.9rem; color: #666;">Microsoft</div>
                    </div>
                    
                    <!-- Google Duplicate -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #4285f4, #34a853, #fbbc04, #ea4335); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: bold;">G</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">GOOGL</div>
                        <div style="font-size: 0.9rem; color: #666;">Alphabet Inc.</div>
                    </div>
                    
                    <!-- Amazon Duplicate -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #ff9900, #ff6600); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">📦</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">AMZN</div>
                        <div style="font-size: 0.9rem; color: #666;">Amazon</div>
                    </div>
                    
                    <!-- Tesla Duplicate -->
                    <div style="flex-shrink: 0; background: white; border-radius: 15px; padding: 20px; min-width: 220px; text-align: center; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="width: 60px; height: 60px; margin: 0 auto 12px; background: linear-gradient(135deg, #cc0000, #990000); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem; font-weight: bold;">⚡</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #000; margin-bottom: 4px;">TSLA</div>
                        <div style="font-size: 0.9rem; color: #666;">Tesla Inc.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        @keyframes stockTicker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

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

    <!-- WORKING TICKER AS MARKET INDICATORS -->
    <section style="background: #0d1b4c; padding: 60px 0; overflow: hidden;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: #fff; font-size: 2rem; font-weight: 600; margin: 0;">
                    <?php echo getCurrentLang() == 'tr' ? 'Canlı Piyasa Göstergeleri' : 'Live Market Indicators'; ?>
                </h2>
            </div>
            
            <div style="overflow: hidden; position: relative; mask: linear-gradient(90deg, transparent, white 10%, white 90%, transparent); -webkit-mask: linear-gradient(90deg, transparent, white 10%, white 90%, transparent);">
                <div style="display: flex; animation: marketTicker 25s linear infinite; gap: 30px; width: max-content;" onmouseover="this.style.animationPlayState='paused'" onmouseout="this.style.animationPlayState='running'">
                    
                    <!-- Apple -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #000, #333); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">🍎</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">AAPL</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Apple Inc.</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$189.25</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+2.15%</div>
                    </div>
                    
                    <!-- Microsoft -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #0078d4, #005a9e); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; font-weight: bold;">MS</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">MSFT</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Microsoft</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$412.80</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+1.87%</div>
                    </div>
                    
                    <!-- Google -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #4285f4, #34a853, #fbbc04, #ea4335); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; font-weight: bold;">G</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">GOOGL</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Alphabet Inc.</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$139.65</div>
                        <div style="font-size: 0.9rem; color: #ef4444; background: rgba(239, 68, 68, 0.2); padding: 4px 8px; border-radius: 4px;">-0.95%</div>
                    </div>
                    
                    <!-- Amazon -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #ff9900, #ff6600); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem;">📦</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">AMZN</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Amazon</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$145.32</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+3.24%</div>
                    </div>
                    
                    <!-- Tesla -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #cc0000, #990000); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem;">⚡</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">TSLA</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Tesla Inc.</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$248.50</div>
                        <div style="font-size: 0.9rem; color: #ef4444; background: rgba(239, 68, 68, 0.2); padding: 4px 8px; border-radius: 4px;">-1.45%</div>
                    </div>
                    
                    <!-- Meta -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #1877f2, #0d47a1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; font-weight: bold;">f</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">META</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Meta Platforms</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$494.75</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+0.87%</div>
                    </div>
                    
                    <!-- DUPLICATE FOR SEAMLESS LOOP -->
                    
                    <!-- Apple Duplicate -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #000, #333); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">🍎</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">AAPL</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Apple Inc.</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$189.25</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+2.15%</div>
                    </div>
                    
                    <!-- Microsoft Duplicate -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #0078d4, #005a9e); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; font-weight: bold;">MS</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">MSFT</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Microsoft</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$412.80</div>
                        <div style="font-size: 0.9rem; color: #22c55e; background: rgba(34, 197, 94, 0.2); padding: 4px 8px; border-radius: 4px;">+1.87%</div>
                    </div>
                    
                    <!-- Google Duplicate -->
                    <div style="flex-shrink: 0; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; min-width: 200px; text-align: center; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-5px)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(0)'">
                        <div style="width: 50px; height: 50px; margin: 0 auto 12px; background: linear-gradient(135deg, #4285f4, #34a853, #fbbc04, #ea4335); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; font-weight: bold;">G</div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 4px;">GOOGL</div>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-bottom: 8px;">Alphabet Inc.</div>
                        <div style="font-size: 1.2rem; font-weight: 700; color: #ffd700; margin-bottom: 4px;">$139.65</div>
                        <div style="font-size: 0.9rem; color: #ef4444; background: rgba(239, 68, 68, 0.2); padding: 4px 8px; border-radius: 4px;">-0.95%</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
        @keyframes marketTicker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>

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
        // User menu toggle function
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            if (userMenu.style.display === 'none' || userMenu.style.display === '') {
                userMenu.style.display = 'block';
            } else {
                userMenu.style.display = 'none';
            }
        }
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            if (!userButton && userMenu) {
                userMenu.style.display = 'none';
            }
        });
        
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

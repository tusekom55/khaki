<?php
require_once 'includes/functions.php';

$page_title = 'GlobalBorsa - Financial Markets';

// Get current category
$category = $_GET['group'] ?? 'us_stocks';
$valid_categories = array_keys(getFinancialCategories());
if (!in_array($category, $valid_categories)) {
    $category = 'us_stocks';
}

// Get market data
$markets = getMarketData($category, 50);

// Get trading currency settings for modals
$trading_currency = getTradingCurrency();
$currency_field = getCurrencyField($trading_currency);
$currency_symbol = getCurrencySymbol($trading_currency);
$usd_try_rate = getUSDTRYRate();

// Get user balances if logged in
$user_balances = [];
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $user_balances = [
        'primary' => getUserBalance($user_id, $currency_field),
        'tl' => getUserBalance($user_id, 'tl'),
        'usd' => getUserBalance($user_id, 'usd'),
        'btc' => getUserBalance($user_id, 'btc'),
        'eth' => getUserBalance($user_id, 'eth')
    ];
}

// Debug POST data always (even if conditions not met)
if ($_POST) {
    echo "<div style='position: fixed; top: 0; left: 0; right: 0; background: red; color: white; padding: 20px; z-index: 99999; font-family: monospace; white-space: pre-wrap; max-height: 200px; overflow-y: auto;'>";
    echo "🔴 POST DATA RECEIVED:\n" . print_r($_POST, true);
    echo "\n👤 USER LOGGED IN: " . (isLoggedIn() ? 'YES (ID: ' . $_SESSION['user_id'] . ')' : 'NO');
    echo "\n💰 TL BALANCE: " . (isLoggedIn() ? getUserBalance($_SESSION['user_id'], 'tl') : 'N/A');
    echo "\n🔗 REQUEST URI: " . $_SERVER['REQUEST_URI'];
    echo "</div>";
    
    // 10 saniye sonra otomatik gizle
    echo "<script>setTimeout(function(){ document.querySelector('div[style*=\"background: red\"]').style.display = 'none'; }, 10000);</script>";
    
    error_log("ALL POST DEBUG: " . print_r($_POST, true));
}

// Handle modal trading form submission
if ($_POST && isset($_POST['modal_action']) && isLoggedIn()) {
    echo "<div style='position: fixed; top: 220px; left: 0; right: 0; background: green; color: white; padding: 15px; z-index: 99999; font-family: monospace;'>";
    echo "✅ TRADE FORM PROCESSING...";
    echo "</div>";
    
    // 5 saniye sonra otomatik gizle
    echo "<script>setTimeout(function(){ var el = document.querySelector('div[style*=\"background: green\"]'); if(el) el.style.display = 'none'; }, 5000);</script>";
    
    // Debug POST data
    error_log("FORM POST DEBUG: " . print_r($_POST, true));
    
    $modal_action = $_POST['modal_action']; // 'buy', 'sell', or 'leverage'
    $symbol = $_POST['symbol'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $leverage = (int)($_POST['leverage'] ?? 1);
    $is_leverage_trade = ($_POST['trade_type'] ?? '') === 'leverage';
    
    error_log("FORM PARSED: action=$modal_action, symbol=$symbol, amount=$amount, leverage=$leverage, is_leverage=$is_leverage_trade");
    
    // Get market data for this symbol
    $current_market = null;
    foreach ($markets as $market) {
        if ($market['symbol'] === $symbol) {
            $current_market = $market;
            break;
        }
    }
    
    if ($current_market && $amount > 0) {
        $price_usd = (float)$current_market['price'];
        
        // Execute trade using parametric system
        if (executeTradeParametric($_SESSION['user_id'], $symbol, $modal_action, $amount, $price_usd, $leverage, $is_leverage_trade)) {
            $success = getCurrentLang() == 'tr' ? 'İşlem başarıyla gerçekleştirildi!' : 'Trade executed successfully!';
            // Refresh user balances
            $user_balances = [
                'primary' => getUserBalance($_SESSION['user_id'], $currency_field),
                'tl' => getUserBalance($_SESSION['user_id'], 'tl'),
                'usd' => getUserBalance($_SESSION['user_id'], 'usd'),
                'btc' => getUserBalance($_SESSION['user_id'], 'btc'),
                'eth' => getUserBalance($_SESSION['user_id'], 'eth')
            ];
        } else {
            $error = getCurrentLang() == 'tr' ? 'İşlem gerçekleştirilemedi. Bakiye yetersiz.' : 'Trade failed. Insufficient balance.';
        }
    }
}

// Update market data if it's been more than 10 minutes (to save API quota)
$database = new Database();
$db = $database->getConnection();

$query = "SELECT updated_at FROM markets WHERE category = ? ORDER BY updated_at DESC LIMIT 1";
$stmt = $db->prepare($query);
$stmt->execute([$category]);
$last_update = $stmt->fetchColumn();

// OTOMATIK GÜNCELLEME KAPATILDI - Sadece manuel güncelleme
// Auto update disabled - Manual update only via admin panel
// if (!$last_update || (time() - strtotime($last_update)) > 600) {
//     if (TWELVE_DATA_API_KEY !== 'demo') {
//         updateFinancialData($category);
//         $markets = getMarketData($category, 50);
//     }
// }

// Search functionality
$search = $_GET['search'] ?? '';
if ($search) {
    $markets = array_filter($markets, function($market) use ($search) {
        return stripos($market['name'], $search) !== false || 
               stripos($market['symbol'], $search) !== false;
    });
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0"><?php echo getFinancialCategories()[$category] ?? 'Financial Markets'; ?></h1>
            <p class="text-muted">
                <?php echo getCurrentLang() == 'tr' ? 'Canlı finansal piyasa verileri' : 'Live financial market data'; ?>
            </p>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="<?php echo getCurrentLang() == 'tr' ? 'Enstrüman ara...' : 'Search instruments...'; ?>" 
                       value="<?php echo htmlspecialchars($search); ?>" id="marketSearch">
            </div>
        </div>
    </div>
    
    <!-- Financial Categories Grid -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="mb-3 text-secondary">
                <i class="fas fa-layer-group me-2"></i>Piyasa Kategorileri
            </h5>
            <div class="row g-3">
                <?php 
                $categories = getFinancialCategories();
                $icons = getCategoryIcons();
                $descriptions = getCategoryDescriptions();
                foreach ($categories as $cat_key => $cat_name): 
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="?group=<?php echo $cat_key; ?>" class="text-decoration-none">
                        <div class="category-card card h-100 border-0 shadow-sm <?php echo $category == $cat_key ? 'category-active' : ''; ?>">
                            <div class="card-body p-3 text-center">
                                <div class="category-icon mb-2">
                                    <i class="<?php echo $icons[$cat_key] ?? 'fas fa-chart-line'; ?> fa-2x"></i>
                                </div>
                                <h6 class="card-title mb-2 fw-bold"><?php echo $cat_name; ?></h6>
                                <p class="card-text text-muted small mb-0">
                                    <?php echo $descriptions[$cat_key] ?? ''; ?>
                                </p>
                                <?php if ($category == $cat_key): ?>
                                <div class="mt-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-check me-1"></i>Aktif
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Market Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover market-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 ps-4"><?php echo t('market_name'); ?></th>
                            <th class="border-0 text-end"><?php echo t('last_price'); ?></th>
                            <th class="border-0 text-end"><?php echo t('change'); ?></th>
                            <th class="border-0 text-end"><?php echo t('low_24h'); ?></th>
                            <th class="border-0 text-end"><?php echo t('high_24h'); ?></th>
                            <th class="border-0 text-end"><?php echo t('volume_24h'); ?></th>
                            <th class="border-0 text-center pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($markets)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">
                                    <?php echo getCurrentLang() == 'tr' ? 'Henüz piyasa verisi yok' : 'No market data available'; ?>
                                </p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($markets as $market): ?>
                        <tr class="market-row" data-symbol="<?php echo $market['symbol']; ?>">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center">
                                    <?php if ($market['logo_url']): ?>
                                    <img src="<?php echo $market['logo_url']; ?>" 
                                         alt="<?php echo $market['name']; ?>" 
                                         class="me-3 rounded-circle" 
                                         width="32" height="32"
                                         onerror="this.outerHTML='<div class=&quot;bg-primary rounded-circle d-flex align-items-center justify-content-center me-3&quot; style=&quot;width: 32px; height: 32px;&quot;><i class=&quot;fas fa-coins text-white&quot;></i></div>';">
                                    <?php else: ?>
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-coins text-white"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-bold"><?php echo $market['symbol']; ?></div>
                                        <small class="text-muted"><?php echo $market['name']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end py-3">
                                <div class="fw-bold price-cell" data-price="<?php echo $market['price']; ?>">
                                    <?php echo formatPrice($market['price']); ?>
                                    <small class="text-muted ms-1">
                                        <?php echo $category == 'crypto_tl' ? 'TL' : ($category == 'crypto_usd' ? 'USDT' : 'USD'); ?>
                                    </small>
                                </div>
                            </td>
                            <td class="text-end py-3">
                                <?php echo formatChange($market['change_24h']); ?>
                            </td>
                            <td class="text-end py-3">
                                <span class="text-muted"><?php echo formatPrice($market['low_24h']); ?></span>
                                <small class="text-muted ms-1">
                                    <?php echo $category == 'crypto_tl' ? 'TL' : ($category == 'crypto_usd' ? 'USDT' : 'USD'); ?>
                                </small>
                            </td>
                            <td class="text-end py-3">
                                <span class="text-muted"><?php echo formatPrice($market['high_24h']); ?></span>
                                <small class="text-muted ms-1">
                                    <?php echo $category == 'crypto_tl' ? 'TL' : ($category == 'crypto_usd' ? 'USDT' : 'USD'); ?>
                                </small>
                            </td>
                            <td class="text-end py-3">
                                <span class="text-muted"><?php echo formatVolume($market['volume_24h']); ?></span>
                                <small class="text-muted ms-1">
                                    <?php 
                                    $symbol_parts = explode('_', $market['symbol']);
                                    echo $symbol_parts[0];
                                    ?>
                                </small>
                            </td>
                            <td class="text-center py-3 pe-4">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success btn-sm trade-btn" 
                                            data-symbol="<?php echo $market['symbol']; ?>" 
                                            data-name="<?php echo $market['name']; ?>" 
                                            data-price="<?php echo $market['price']; ?>" 
                                            data-action="buy"
                                            data-type="simple"
                                            onclick="event.stopPropagation(); openTradeModal(this);">
                                        <i class="fas fa-shopping-cart me-1"></i>AL
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm trade-btn" 
                                            data-symbol="<?php echo $market['symbol']; ?>" 
                                            data-name="<?php echo $market['name']; ?>" 
                                            data-price="<?php echo $market['price']; ?>" 
                                            data-action="sell"
                                            data-type="simple"
                                            onclick="event.stopPropagation(); openTradeModal(this);">
                                        <i class="fas fa-hand-holding-usd me-1"></i>SAT
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm trade-btn" 
                                            data-symbol="<?php echo $market['symbol']; ?>" 
                                            data-name="<?php echo $market['name']; ?>" 
                                            data-price="<?php echo $market['price']; ?>" 
                                            data-action="leverage"
                                            data-type="leverage"
                                            onclick="event.stopPropagation(); openTradeModal(this);">
                                        <i class="fas fa-bolt me-1"></i>KALDIRAÇ
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Market Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h5 class="text-success mb-1"><?php echo count($markets); ?></h5>
                    <small class="text-muted">
                        <?php echo getCurrentLang() == 'tr' ? 'Toplam Piyasa' : 'Total Markets'; ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h5 class="text-primary mb-1">
                        <?php 
                        $gainers = array_filter($markets, function($m) { return $m['change_24h'] > 0; });
                        echo count($gainers);
                        ?>
                    </h5>
                    <small class="text-muted">
                        <?php echo getCurrentLang() == 'tr' ? 'Yükselenler' : 'Gainers'; ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h5 class="text-danger mb-1">
                        <?php 
                        $losers = array_filter($markets, function($m) { return $m['change_24h'] < 0; });
                        echo count($losers);
                        ?>
                    </h5>
                    <small class="text-muted">
                        <?php echo getCurrentLang() == 'tr' ? 'Düşenler' : 'Losers'; ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <h5 class="text-info mb-1">
                        <?php 
                        $total_volume = array_sum(array_column($markets, 'volume_24h'));
                        echo formatVolume($total_volume);
                        ?>
                    </h5>
                    <small class="text-muted">
                        <?php echo getCurrentLang() == 'tr' ? '24S Hacim' : '24h Volume'; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Parametric system constants
const TRADING_CURRENCY = <?php echo $trading_currency; ?>; // 1=TL, 2=USD
const CURRENCY_SYMBOL = '<?php echo $currency_symbol; ?>';
const USD_TRY_RATE = <?php echo $usd_try_rate; ?>;

// Search functionality
document.getElementById('marketSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.market-row');
    
    rows.forEach(row => {
        const symbol = row.querySelector('.fw-bold').textContent.toLowerCase();
        const name = row.querySelector('.text-muted').textContent.toLowerCase();
        
        if (symbol.includes(searchTerm) || name.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Auto refresh market data
function refreshMarketData() {
    fetch('api/get_market_data.php?category=<?php echo $category; ?>')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    updateMarketTable(data.markets);
                }
            } catch (e) {
                console.error('JSON Parse Error:', e);
                console.error('Response text:', text);
            }
        })
        .catch(error => console.error('Error refreshing market data:', error));
}

function updateMarketTable(markets) {
    markets.forEach(market => {
        const row = document.querySelector(`[data-symbol="${market.symbol}"]`);
        if (row) {
            const priceCell = row.querySelector('.price-cell');
            const oldPrice = parseFloat(priceCell.dataset.price);
            const newPrice = parseFloat(market.price);
            
            // Update price
            priceCell.textContent = formatPrice(newPrice);
            priceCell.dataset.price = newPrice;
            
            // Animate price change
            if (newPrice !== oldPrice) {
                animatePriceChange(priceCell, newPrice > oldPrice);
            }
            
            // Update change percentage
            const changeCell = row.querySelector('.text-success, .text-danger');
            if (changeCell) {
                const sign = market.change_24h >= 0 ? '+' : '';
                changeCell.className = market.change_24h >= 0 ? 'text-success' : 'text-danger';
                changeCell.innerHTML = `<span class="${changeCell.className}">${sign} %${formatTurkishNumber(market.change_24h, 2)}</span>`;
            }
        }
    });
}

function formatPrice(price) {
    if (price >= 1000) {
        return formatTurkishNumber(price, 2);
    } else if (price >= 1) {
        return formatTurkishNumber(price, 4);
    } else {
        return formatTurkishNumber(price, 8);
    }
}

// Turkish number formatting function
function formatTurkishNumber(number, decimals = 2) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

// Price change animation
function animatePriceChange(element, isUp) {
    element.classList.remove('price-up', 'price-down');
    element.classList.add(isUp ? 'price-up' : 'price-down');
    setTimeout(() => {
        element.classList.remove('price-up', 'price-down');
    }, 500);
}
// Trading modal functions
function openTradeModal(button) {
    const symbol = button.dataset.symbol;
    const name = button.dataset.name;
    const price = parseFloat(button.dataset.price);
    const action = button.dataset.action;
    const type = button.dataset.type; // simple or leverage
    
    // Update modal content
    document.getElementById('modalSymbol').textContent = symbol;
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalPrice').textContent = formatPrice(price);
    document.getElementById('modalChange').textContent = document.querySelector(`[data-symbol="${symbol}"] .text-success, [data-symbol="${symbol}"] .text-danger`).textContent;
    
    // Set hidden fields for forms
    document.getElementById('buySymbol').value = symbol;
    document.getElementById('sellSymbol').value = symbol;
    document.getElementById('buyTradeType').value = type;
    document.getElementById('sellTradeType').value = type;
    
    // Configure modal based on type
    configureModalForType(type, action);
    
    // Update TradingView widget
    updateTradingViewWidget(symbol);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('tradeModal'));
    modal.show();
}

function configureModalForType(type, action) {
    const buyTab = document.getElementById('buy-tab');
    const sellTab = document.getElementById('sell-tab');
    const buyPane = document.getElementById('buy-pane');
    const sellPane = document.getElementById('sell-pane');
    
    if (type === 'simple') {
        // Simple buy/sell - hide leverage elements
        setupSimpleTrading(action);
    } else if (type === 'leverage') {
        // Leverage trading - show all elements
        setupLeverageTrading();
    }
    
    // Set active tab based on action
    if (action === 'buy' || action === 'leverage') {
        buyTab.classList.add('active');
        sellTab.classList.remove('active');
        buyPane.classList.add('show', 'active');
        sellPane.classList.remove('show', 'active');
    } else if (action === 'sell') {
        sellTab.classList.add('active');
        buyTab.classList.remove('active');
        sellPane.classList.add('show', 'active');
        buyPane.classList.remove('show', 'active');
    }
}

function setupSimpleTrading(action) {
    // Update tab labels for simple trading
    const buyTab = document.getElementById('buy-tab');
    const sellTab = document.getElementById('sell-tab');
    
    buyTab.innerHTML = '<i class="fas fa-shopping-cart me-1"></i>SATIN AL';
    sellTab.innerHTML = '<i class="fas fa-hand-holding-usd me-1"></i>SAT';
    
    // Hide leverage controls
    const leverageControls = document.querySelectorAll('.leverage-control');
    leverageControls.forEach(control => {
        control.style.display = 'none';
    });
    
    // Hide stop loss / take profit for simple trading
    const advancedControls = document.querySelectorAll('.advanced-control');
    advancedControls.forEach(control => {
        control.style.display = 'none';
    });
    
    // Update button text
    const buyButton = document.querySelector('#buy-pane button[type="submit"]');
    const sellButton = document.querySelector('#sell-pane button[type="submit"]');
    
    buyButton.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>SATIN AL';
    sellButton.innerHTML = '<i class="fas fa-hand-holding-usd me-2"></i>SAT';
    
    // Update calculation labels
    updateSimpleCalculationLabels();
}

function setupLeverageTrading() {
    // Update tab labels for leverage trading
    const buyTab = document.getElementById('buy-tab');
    const sellTab = document.getElementById('sell-tab');
    
    buyTab.innerHTML = '<i class="fas fa-arrow-up me-1"></i>LONG';
    sellTab.innerHTML = '<i class="fas fa-arrow-down me-1"></i>SHORT';
    
    // Show leverage controls
    const leverageControls = document.querySelectorAll('.leverage-control');
    leverageControls.forEach(control => {
        control.style.display = 'block';
    });
    
    // Show advanced controls
    const advancedControls = document.querySelectorAll('.advanced-control');
    advancedControls.forEach(control => {
        control.style.display = 'block';
    });
    
    // Update button text
    const buyButton = document.querySelector('#buy-pane button[type="submit"]');
    const sellButton = document.querySelector('#sell-pane button[type="submit"]');
    
    buyButton.innerHTML = '<i class="fas fa-arrow-up me-2"></i>LONG POZISYON AÇ';
    sellButton.innerHTML = '<i class="fas fa-arrow-down me-2"></i>SHORT POZISYON AÇ';
    
    // Update calculation labels
    updateLeverageCalculationLabels();
}

function updateSimpleCalculationLabels() {
    // Update calculation display for simple trading
    const labels = document.querySelectorAll('.calculation-label');
    labels.forEach(label => {
        if (label.textContent === 'Gerekli Margin:') {
            label.textContent = 'Ödenecek Tutar:';
        }
    });
}

function updateLeverageCalculationLabels() {
    // Update calculation display for leverage trading
    const labels = document.querySelectorAll('.calculation-label');
    labels.forEach(label => {
        if (label.textContent === 'Ödenecek Tutar:') {
            label.textContent = 'Gerekli Margin:';
        }
    });
}

function updateTradingViewWidget(symbol) {
    // Clean symbol for TradingView format
    let tvSymbol = symbol;
    
    // Convert our symbols to TradingView format
    if (symbol.includes('=X')) {
        tvSymbol = symbol.replace('=X', '');
    } else if (symbol.includes('=F')) {
        tvSymbol = symbol.replace('=F', '');
    } else if (symbol.startsWith('^')) {
        tvSymbol = symbol.replace('^', '');
    }
    
    // Update TradingView iframe src
    const iframe = document.getElementById('tradingview-widget');
    iframe.src = `https://www.tradingview.com/widgetembed/?frameElementId=tradingview_chart&symbol=${tvSymbol}&interval=1D&hidesidetoolbar=1&hidetoptoolbar=1&symboledit=1&saveimage=1&toolbarbg=F1F3F6&studies=[]&hideideas=1&theme=Light&style=1&timezone=Etc%2FUTC&studies_overrides={}&overrides={}&enabled_features=[]&disabled_features=[]&locale=en&utm_source=localhost&utm_medium=widget&utm_campaign=chart&utm_term=${tvSymbol}`;
}

function calculateTrade() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const leverage = parseInt(document.getElementById('leverage').value) || 1;
    const priceUSD = parseFloat(document.getElementById('modalPrice').textContent.replace(',', '.'));
    const amountType = document.querySelector('input[name="amountType"]:checked').value;
    
    let totalUSD, lotAmount;
    
    // Calculate based on amount type
    if (amountType === 'usd') {
        totalUSD = amount;
        lotAmount = amount / priceUSD;
    } else {
        // Lot ile işlem
        totalUSD = amount * priceUSD;
        lotAmount = amount;
    }
    
    // TL MODE HESAPLAMASI - Parametre 1 ise TL modu
    if (TRADING_CURRENCY === 1) { // TL mode
        // USD miktarını TL'ye çevir
        const totalTL = totalUSD * USD_TRY_RATE;
        const feeTL = totalTL * 0.001; // %0.1 işlem ücreti
        const totalWithFeeTL = totalTL + feeTL;
        
        // Kaldıraç hesabı (sadece leverage trading için)
        const requiredMarginTL = leverage > 1 ? totalTL / leverage : totalWithFeeTL;
        
        // Kalan bakiye hesabı (varsayılan 900 TL - demo amaçlı)
        const currentBalance = <?php echo isLoggedIn() ? getUserBalance($_SESSION['user_id'], 'tl') : 900; ?>;
        const remainingBalance = currentBalance - totalWithFeeTL;
        
        // Display update
        document.getElementById('totalValue').textContent = formatTurkishNumber(totalTL, 2) + ' TL';
        document.getElementById('requiredMargin').textContent = formatTurkishNumber(requiredMarginTL, 2) + ' TL';
        document.getElementById('tradingFee').textContent = formatTurkishNumber(remainingBalance, 2) + ' TL';
        
        // Label güncelleme
        document.querySelector('#buy-pane .card-body .d-flex:last-child .text-muted').textContent = 'Kalan Bakiye:';
        
        // Exchange rate info göster
        const exchangeInfo = document.getElementById('exchangeInfo');
        if (exchangeInfo) {
            exchangeInfo.style.display = 'flex';
            exchangeInfo.style.setProperty('display', 'flex', 'important');
            const exchangeRateSpan = document.getElementById('exchangeRate');
            if (exchangeRateSpan) {
                exchangeRateSpan.textContent = `1 USD = ${formatTurkishNumber(USD_TRY_RATE, 4)} TL`;
            }
        }
        
    } else { // USD mode
        const feeUSD = totalUSD * 0.001;
        const totalWithFeeUSD = totalUSD + feeUSD;
        const requiredMarginUSD = leverage > 1 ? totalUSD / leverage : totalWithFeeUSD;
        
        // Kalan bakiye hesabı
        const currentBalance = <?php echo isLoggedIn() ? getUserBalance($_SESSION['user_id'], 'usd') : 100; ?>;
        const remainingBalance = currentBalance - totalWithFeeUSD;
        
        document.getElementById('totalValue').textContent = formatTurkishNumber(totalUSD, 2) + ' USD';
        document.getElementById('requiredMargin').textContent = formatTurkishNumber(requiredMarginUSD, 2) + ' USD';
        document.getElementById('tradingFee').textContent = formatTurkishNumber(remainingBalance, 2) + ' USD';
        
        // Label güncelleme
        document.querySelector('#buy-pane .card-body .d-flex:last-child .text-muted').textContent = 'Kalan Bakiye:';
        
        // Exchange rate info gizle
        const exchangeInfo = document.getElementById('exchangeInfo');
        if (exchangeInfo) {
            exchangeInfo.style.display = 'none';
        }
    }
    
    // Lot miktarı göster
    document.getElementById('lotEquivalent').style.display = 'flex';
    document.getElementById('lotAmount').textContent = formatTurkishNumber(lotAmount, 4) + ' Lot';
    
    // Leverage display
    document.getElementById('leverageDisplay').textContent = leverage + 'x';
}

function calculateTradeSell() {
    const amount = parseFloat(document.getElementById('amountSell').value) || 0;
    const price = parseFloat(document.getElementById('modalPrice').textContent.replace(',', '.'));
    const amountType = document.querySelector('input[name="amountTypeSell"]:checked').value;
    
    let total;
    if (amountType === 'usd') {
        // USD ile işlem
        total = amount;
    } else {
        // Lot ile işlem
        total = amount * price;
    }
    
    const fee = total * 0.001; // 0.1% fee
    
    // Update sell form calculations (we'll add IDs to sell form elements)
}

// Amount type change handlers
document.addEventListener('DOMContentLoaded', function() {
    // Buy form amount type handlers
    document.getElementById('amountLot').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('amountUnit').textContent = 'Lot';
            calculateTrade();
        }
    });
    
    document.getElementById('amountUSD').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('amountUnit').textContent = 'USD';
            calculateTrade();
        }
    });
    
    // Sell form amount type handlers
    document.getElementById('amountLotSell').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('amountUnitSell').textContent = 'Lot';
            calculateTradeSell();
        }
    });
    
    document.getElementById('amountUSDSell').addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('amountUnitSell').textContent = 'USD';
            calculateTradeSell();
        }
    });
});
</script>

<!-- Trading Modal -->
<div class="modal fade" id="tradeModal" tabindex="-1" aria-labelledby="tradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="modalSymbol">AAPL</h5>
                        <small class="text-muted" id="modalName">Apple Inc.</small>
                    </div>
                    <div class="ms-auto text-end">
                        <div class="h5 mb-0" id="modalPrice">$175.50</div>
                        <small id="modalChange">+1.25%</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <!-- Chart Section -->
                    <div class="col-md-8 border-end">
                        <div class="p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">Fiyat Grafiği</h6>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary">1D</button>
                                    <button type="button" class="btn btn-outline-secondary active">1H</button>
                                    <button type="button" class="btn btn-outline-secondary">15M</button>
                                </div>
                            </div>
                            <!-- TradingView Widget -->
                            <div style="height: 400px; border-radius: 8px; overflow: hidden;">
                                <iframe id="tradingview-widget" 
                                        src="https://www.tradingview.com/widgetembed/?frameElementId=tradingview_chart&symbol=AAPL&interval=1D&hidesidetoolbar=1&hidetoptoolbar=1&symboledit=1&saveimage=1&toolbarbg=F1F3F6&studies=[]&hideideas=1&theme=Light&style=1&timezone=Etc%2FUTC&studies_overrides={}&overrides={}&enabled_features=[]&disabled_features=[]&locale=en&utm_source=localhost&utm_medium=widget&utm_campaign=chart&utm_term=AAPL"
                                        style="width: 100%; height: 100%; border: none;">
                                </iframe>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trading Section -->
                    <div class="col-md-4">
                        <div class="p-3">
                            <!-- Buy/Sell Tabs -->
                            <ul class="nav nav-pills nav-fill mb-3" id="tradingTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="buy-tab" data-bs-toggle="pill" data-bs-target="#buy-pane" type="button">
                                        <i class="fas fa-arrow-up me-1"></i>LONG
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sell-tab" data-bs-toggle="pill" data-bs-target="#sell-pane" type="button">
                                        <i class="fas fa-arrow-down me-1"></i>SHORT
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="tradingTabsContent">
                                <!-- Buy/Long Form -->
                                <div class="tab-pane fade show active" id="buy-pane" role="tabpanel">
                                    <?php if (isset($success)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (isset($error)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isLoggedIn()): ?>
                                    <form id="buyForm" method="POST" action="markets.php?group=<?php echo $category; ?>" onsubmit="console.log('BUY FORM SUBMIT!'); return true;">
                                        <input type="hidden" name="modal_action" value="buy">
                                        <input type="hidden" name="symbol" id="buySymbol" value="">
                                        <input type="hidden" name="trade_type" id="buyTradeType" value="">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Miktar</label>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <input type="radio" class="btn-check" name="amountType" id="amountLot" value="lot">
                                                    <label class="btn btn-outline-primary" for="amountLot">Lot</label>
                                                    
                                                    <input type="radio" class="btn-check" name="amountType" id="amountUSD" value="usd" checked>
                                                    <label class="btn btn-outline-primary" for="amountUSD">USD</label>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0.01" 
                                                       placeholder="0.00" oninput="calculateTrade()" required>
                                                <span class="input-group-text" id="amountUnit">USD</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 leverage-control">
                                            <label class="form-label">Kaldıraç <span id="leverageDisplay" class="badge bg-primary">1x</span></label>
                                            <input type="range" class="form-range" id="leverage" name="leverage" min="1" max="100" value="1" 
                                                   oninput="calculateTrade()">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">1x</small>
                                                <small class="text-muted">100x</small>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3 advanced-control">
                                            <div class="col-6">
                                                <label class="form-label">Stop Loss</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Take Profit</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Trade Summary -->
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Toplam Değer:</small>
                                                    <small class="fw-bold" id="totalValue">$0.00</small>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1" id="lotEquivalent" style="display: none;">
                                                    <small class="text-muted">Lot Miktarı:</small>
                                                    <small class="fw-bold" id="lotAmount">0.00 Lot</small>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted calculation-label">Gerekli Margin:</small>
                                                    <small class="fw-bold" id="requiredMargin">$0.00</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">İşlem Ücreti:</small>
                                                    <small class="fw-bold" id="tradingFee">$0.00</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Exchange Rate Info for TL Mode -->
                                        <div class="alert alert-info d-flex align-items-center py-2" id="exchangeInfo" style="display: none !important;">
                                            <i class="fas fa-exchange-alt me-2"></i>
                                            <div class="flex-grow-1">
                                                <small><strong>Canlı Kur:</strong> <span id="exchangeRate">1 USD = <?php echo formatTurkishNumber($usd_try_rate, 4); ?> TL</span></small>
                                                <br>
                                                <small class="text-muted">TL bakiyeniz ile USD kuruna göre işlem yapıyorsunuz</small>
                                            </div>
                                            <span class="badge bg-success">CANLI</span>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-arrow-up me-2"></i>LONG POZISYON AÇ
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">
                                            <?php echo getCurrentLang() == 'tr' ? 'İşlem yapmak için giriş yapmanız gerekiyor' : 'Please login to trade'; ?>
                                        </p>
                                        <a href="login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt me-2"></i><?php echo getCurrentLang() == 'tr' ? 'Giriş Yap' : 'Login'; ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Sell/Short Form -->
                                <div class="tab-pane fade" id="sell-pane" role="tabpanel">
                                    <?php if (isLoggedIn()): ?>
                                    <form id="sellForm" method="POST" action="">
                                        <input type="hidden" name="modal_action" value="sell">
                                        <input type="hidden" name="symbol" id="sellSymbol" value="">
                                        <input type="hidden" name="trade_type" id="sellTradeType" value="">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label class="form-label mb-0">Miktar</label>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <input type="radio" class="btn-check" name="amountTypeSell" id="amountLotSell" value="lot">
                                                    <label class="btn btn-outline-primary" for="amountLotSell">Lot</label>
                                                    
                                                    <input type="radio" class="btn-check" name="amountTypeSell" id="amountUSDSell" value="usd" checked>
                                                    <label class="btn btn-outline-primary" for="amountUSDSell">USD</label>
                                                </div>
                                            </div>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="amountSell" step="0.01" min="0.01" 
                                                       placeholder="0.00" oninput="calculateTradeSell()">
                                                <span class="input-group-text" id="amountUnitSell">Lot</span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 leverage-control">
                                            <label class="form-label">Kaldıraç <span class="badge bg-primary">1x</span></label>
                                            <input type="range" class="form-range" min="1" max="100" value="1">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">1x</small>
                                                <small class="text-muted">100x</small>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3 advanced-control">
                                            <div class="col-6">
                                                <label class="form-label">Stop Loss</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label">Take Profit</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" step="0.01" placeholder="0.00">
                                                    <span class="input-group-text">$</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Trade Summary -->
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Toplam Değer:</small>
                                                    <small class="fw-bold">$0.00</small>
                                                </div>
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted calculation-label">Gerekli Margin:</small>
                                                    <small class="fw-bold">$0.00</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">İşlem Ücreti:</small>
                                                    <small class="fw-bold">$0.00</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fas fa-arrow-down me-2"></i>SHORT POZISYON AÇ
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-3">
                                            <?php echo getCurrentLang() == 'tr' ? 'İşlem yapmak için giriş yapmanız gerekiyor' : 'Please login to trade'; ?>
                                        </p>
                                        <a href="login.php" class="btn btn-primary">
                                            <i class="fas fa-sign-in-alt me-2"></i><?php echo getCurrentLang() == 'tr' ? 'Giriş Yap' : 'Login'; ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

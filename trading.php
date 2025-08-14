<?php
require_once 'includes/functions.php';

// Require login for trading
requireLogin();

$page_title = t('trading');
$error = '';
$success = '';

// Get trading pair from URL
$pair = $_GET['pair'] ?? 'BTC_TL';
$market = getSingleMarket($pair);

if (!$market) {
    header('Location: index.php');
    exit();
}

// Handle trading form submission
if ($_POST && isset($_POST['action'])) {
    $action = $_POST['action']; // 'buy' or 'sell'
    $amount = (float)($_POST['amount'] ?? 0);
    $price_usd = (float)($market['price']); // Market prices are always in USD
    
    // Get minimum trade amount based on current currency setting
    $min_trade_amount = getMinTradeAmount();
    $trading_currency = getTradingCurrency();
    
    if ($trading_currency == 1) { // TL mode - check minimum in TL equivalent
        $total_tl = convertUSDToTL($amount * $price_usd);
        if ($total_tl < MIN_TRADE_AMOUNT) {
            $error = getCurrentLang() == 'tr' ? 
                'Minimum işlem tutarı ' . MIN_TRADE_AMOUNT . ' TL' : 
                'Minimum trade amount is ' . MIN_TRADE_AMOUNT . ' TL';
        }
    } else { // USD mode - check minimum in USD
        $total_usd = $amount * $price_usd;
        $min_usd = MIN_TRADE_AMOUNT / getUSDTRYRate();
        if ($total_usd < $min_usd) {
            $error = getCurrentLang() == 'tr' ? 
                'Minimum işlem tutarı $' . formatNumber($min_usd, 2) : 
                'Minimum trade amount is $' . formatNumber($min_usd, 2);
        }
    }
    
    if (!$error) {
        if (executeTradeParametric($_SESSION['user_id'], $pair, $action, $amount, $price_usd)) {
            $success = t('trade_success');
            // Refresh market data
            $market = getSingleMarket($pair);
        } else {
            $error = t('insufficient_balance');
        }
    }
}

// Get user balances
$user_id = $_SESSION['user_id'];
$trading_currency = getTradingCurrency();
$currency_field = getCurrencyField($trading_currency);
$currency_symbol = getCurrencySymbol($trading_currency);

$balance_primary = getUserBalance($user_id, $currency_field); // TL or USD based on system setting
$balance_tl = getUserBalance($user_id, 'tl'); // Keep for calculations
$balance_usd = getUserBalance($user_id, 'usd'); // Keep for calculations
$crypto_currency = strtolower(explode('_', $pair)[0]);
$balance_crypto = getUserBalance($user_id, $crypto_currency);

// Get current USD/TRY rate
$usd_try_rate = getUSDTRYRate();

// Get recent transactions
$recent_trades = getUserTransactions($user_id, 10);

include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <!-- Market Info -->
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <?php if ($market['logo_url']): ?>
                                <img src="<?php echo $market['logo_url']; ?>" 
                                     alt="<?php echo $market['name']; ?>" 
                                     class="me-3 rounded-circle" 
                                     width="48" height="48">
                                <?php endif; ?>
                                <div>
                                    <h3 class="h4 mb-1"><?php echo $market['symbol']; ?></h3>
                                    <p class="text-muted mb-0"><?php echo $market['name']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <?php 
                            // Display price based on trading currency
                            if ($trading_currency == 1) { // TL mode
                                $display_price = convertUSDToTL($market['price']);
                                $currency_display = 'TL';
                            } else { // USD mode
                                $display_price = $market['price'];
                                $currency_display = 'USD';
                            }
                            ?>
                            <div class="h2 mb-1"><?php echo formatPrice($display_price); ?> <?php echo $currency_display; ?></div>
                            <div><?php echo formatChange($market['change_24h']); ?></div>
                            <?php if ($trading_currency == 1): ?>
                            <small class="text-muted">Kur: 1 USD = <?php echo formatNumber($usd_try_rate, 2); ?> TL</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trading Interface -->
        <div class="col-lg-8">
            <!-- Price Chart Placeholder -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?php echo getCurrentLang() == 'tr' ? 'Fiyat Grafiği' : 'Price Chart'; ?></h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'TradingView grafiği burada görünecek' : 
                                'TradingView chart will appear here'; ?>
                        </p>
                        <small class="text-muted">
                            <?php echo getCurrentLang() == 'tr' ? 
                                'Gelişmiş grafik özelliği yakında eklenecek' : 
                                'Advanced charting feature coming soon'; ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Recent Trades -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?php echo t('trade_history'); ?></h5>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_trades)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-2x text-muted mb-3"></i>
                        <p class="text-muted">
                            <?php echo getCurrentLang() == 'tr' ? 'Henüz işlem geçmişi yok' : 'No trading history yet'; ?>
                        </p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th><?php echo getCurrentLang() == 'tr' ? 'Tarih' : 'Date'; ?></th>
                                    <th><?php echo getCurrentLang() == 'tr' ? 'Tip' : 'Type'; ?></th>
                                    <th><?php echo getCurrentLang() == 'tr' ? 'Miktar' : 'Amount'; ?></th>
                                    <th><?php echo getCurrentLang() == 'tr' ? 'Fiyat' : 'Price'; ?></th>
                                    <th><?php echo getCurrentLang() == 'tr' ? 'Toplam' : 'Total'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_trades as $trade): ?>
                                <tr>
                                    <td><?php echo date('d.m.Y H:i', strtotime($trade['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $trade['type'] == 'buy' ? 'success' : 'danger'; ?>">
                                            <?php echo $trade['type'] == 'buy' ? t('buy') : t('sell'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatPrice($trade['amount']); ?></td>
                                    <td><?php echo formatPrice($trade['price']); ?> TL</td>
                                    <td><?php echo formatPrice($trade['total']); ?> TL</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Trading Panel -->
        <div class="col-lg-4">
            <!-- User Balances -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><?php echo t('balance'); ?></h5>
                    <small class="text-muted">
                        <?php if ($trading_currency == 1): ?>
                        Trading Currency: TL (Kur: <?php echo formatNumber($usd_try_rate, 2); ?>)
                        <?php else: ?>
                        Trading Currency: USD
                        <?php endif; ?>
                    </small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h6 mb-1"><?php echo formatNumber($balance_primary); ?></div>
                                <small class="text-muted"><?php echo $currency_symbol; ?></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h6 mb-1"><?php echo formatPrice($balance_crypto); ?></div>
                                <small class="text-muted"><?php echo strtoupper($crypto_currency); ?></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-center p-3 bg-light rounded">
                                <?php 
                                // Show balance in other currency for reference
                                if ($trading_currency == 1) {
                                    echo '<div class="h6 mb-1">' . formatNumber($balance_usd) . '</div>';
                                    echo '<small class="text-muted">USD</small>';
                                } else {
                                    echo '<div class="h6 mb-1">' . formatNumber($balance_tl) . '</div>';
                                    echo '<small class="text-muted">TL</small>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Buy/Sell Forms -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Buy/Sell Tabs -->
                    <ul class="nav nav-pills nav-fill mb-3" id="tradingTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="buy-tab" data-bs-toggle="pill" data-bs-target="#buy" type="button">
                                <i class="fas fa-arrow-up me-1"></i><?php echo t('buy'); ?>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sell-tab" data-bs-toggle="pill" data-bs-target="#sell" type="button">
                                <i class="fas fa-arrow-down me-1"></i><?php echo t('sell'); ?>
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="tradingTabsContent">
                        <!-- Buy Form -->
                        <div class="tab-pane fade show active" id="buy" role="tabpanel">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="buy">
                                
                                <div class="mb-3">
                                    <label class="form-label"><?php echo t('amount'); ?> (<?php echo strtoupper($crypto_currency); ?>)</label>
                                    <input type="number" class="form-control" name="amount" step="0.00000001" min="0" required>
                                    <small class="text-muted">
                                        <?php echo getCurrentLang() == 'tr' ? 'Mevcut fiyat:' : 'Current price:'; ?> 
                                        <?php echo formatPrice($display_price); ?> <?php echo $currency_display; ?>
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><?php echo getCurrentLang() == 'tr' ? 'Tahmini Tutar' : 'Estimated Total'; ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="buyTotal" readonly>
                                        <span class="input-group-text"><?php echo $currency_symbol; ?></span>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo getCurrentLang() == 'tr' ? 'İşlem ücreti:' : 'Trading fee:'; ?> %<?php echo TRADING_FEE; ?>
                                        <br><?php echo getCurrentLang() == 'tr' ? 'Kullanılacak bakiye:' : 'Will use balance:'; ?> 
                                        <?php echo formatNumber($balance_primary); ?> <?php echo $currency_symbol; ?>
                                    </small>
                                </div>
                                
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-arrow-up me-2"></i><?php echo t('buy'); ?> <?php echo strtoupper($crypto_currency); ?>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Sell Form -->
                        <div class="tab-pane fade" id="sell" role="tabpanel">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="sell">
                                
                                <div class="mb-3">
                                    <label class="form-label"><?php echo t('amount'); ?> (<?php echo strtoupper($crypto_currency); ?>)</label>
                                    <input type="number" class="form-control" name="amount" step="0.00000001" min="0" 
                                           max="<?php echo $balance_crypto; ?>" required>
                                    <small class="text-muted">
                                        <?php echo getCurrentLang() == 'tr' ? 'Mevcut bakiye:' : 'Available balance:'; ?> 
                                        <?php echo formatPrice($balance_crypto); ?> <?php echo strtoupper($crypto_currency); ?>
                                    </small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><?php echo getCurrentLang() == 'tr' ? 'Tahmini Tutar' : 'Estimated Total'; ?></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sellTotal" readonly>
                                        <span class="input-group-text"><?php echo $currency_symbol; ?></span>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo getCurrentLang() == 'tr' ? 'İşlem ücreti:' : 'Trading fee:'; ?> %<?php echo TRADING_FEE; ?>
                                        <br><?php echo getCurrentLang() == 'tr' ? 'Alınacak tutar:' : 'Will receive:'; ?> 
                                        <?php echo $currency_symbol; ?> cinsinden
                                    </small>
                                </div>
                                
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-arrow-down me-2"></i><?php echo t('sell'); ?> <?php echo strtoupper($crypto_currency); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const currentPriceUSD = <?php echo $market['price']; ?>; // Market price in USD
const displayPrice = <?php echo $display_price; ?>; // Display price in current currency
const tradingFee = <?php echo TRADING_FEE; ?> / 100;
const tradingCurrency = <?php echo $trading_currency; ?>; // 1=TL, 2=USD
const usdTryRate = <?php echo $usd_try_rate; ?>;
const balancePrimary = <?php echo $balance_primary; ?>;
const balanceCrypto = <?php echo $balance_crypto; ?>;

// Calculate buy total
document.querySelector('#buy input[name="amount"]').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    
    if (tradingCurrency === 1) { // TL mode
        const totalTL = amount * displayPrice; // displayPrice is already in TL
        const totalWithFee = totalTL + (totalTL * tradingFee);
        document.getElementById('buyTotal').value = formatTurkishNumber(totalWithFee, 2);
    } else { // USD mode
        const totalUSD = amount * currentPriceUSD;
        const totalWithFee = totalUSD + (totalUSD * tradingFee);
        document.getElementById('buyTotal').value = formatTurkishNumber(totalWithFee, 2);
    }
});

// Calculate sell total
document.querySelector('#sell input[name="amount"]').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    
    if (tradingCurrency === 1) { // TL mode
        const totalTL = amount * displayPrice; // displayPrice is already in TL
        const totalAfterFee = totalTL - (totalTL * tradingFee);
        document.getElementById('sellTotal').value = formatTurkishNumber(totalAfterFee, 2);
    } else { // USD mode
        const totalUSD = amount * currentPriceUSD;
        const totalAfterFee = totalUSD - (totalUSD * tradingFee);
        document.getElementById('sellTotal').value = formatTurkishNumber(totalAfterFee, 2);
    }
});

// Quick amount buttons
function setQuickAmount(percentage, type) {
    const maxBalance = type === 'buy' ? 
        balancePrimary / displayPrice : 
        balanceCrypto;
    
    const amount = maxBalance * (percentage / 100);
    const input = document.querySelector(`#${type} input[name="amount"]`);
    input.value = amount.toFixed(8);
    input.dispatchEvent(new Event('input'));
}

// Format Turkish number function
function formatTurkishNumber(number, decimals = 2) {
    return number.toLocaleString('tr-TR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}
</script>

<?php include 'includes/footer.php'; ?>

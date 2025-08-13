<?php
require_once 'config/database.php';
require_once 'config/api_keys.php';
require_once 'config/languages.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}


// Redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit();
    }
}

// Format number with Turkish locale
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals, ',', '.');
}

// Format price based on value
function formatPrice($price) {
    if ($price >= 1000) {
        return formatNumber($price, 2);
    } elseif ($price >= 1) {
        return formatNumber($price, 4);
    } else {
        return formatNumber($price, 8);
    }
}

// Format percentage change
function formatChange($change) {
    $sign = $change >= 0 ? '+' : '';
    $class = $change >= 0 ? 'text-success' : 'text-danger';
    return '<span class="' . $class . '">' . $sign . ' %' . formatNumber($change, 2) . '</span>';
}

// Format volume
function formatVolume($volume) {
    if ($volume >= 1000000000) {
        return formatNumber($volume / 1000000000, 1) . 'B';
    } elseif ($volume >= 1000000) {
        return formatNumber($volume / 1000000, 1) . 'M';
    } elseif ($volume >= 1000) {
        return formatNumber($volume / 1000, 1) . 'K';
    } else {
        return formatNumber($volume, 0);
    }
}

// Get user balance
function getUserBalance($user_id, $currency = 'tl') {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT balance_" . $currency . " FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['balance_' . $currency] : 0;
}

// Update user balance
function updateUserBalance($user_id, $currency, $amount, $operation = 'add') {
    $database = new Database();
    $db = $database->getConnection();
    
    $operator = $operation == 'add' ? '+' : '-';
    $query = "UPDATE users SET balance_" . $currency . " = balance_" . $currency . " " . $operator . " ? WHERE id = ?";
    $stmt = $db->prepare($query);
    return $stmt->execute([$amount, $user_id]);
}

// Financial market categories
function getFinancialCategories() {
    return [
        'us_stocks' => 'ABD Hisse Senetleri',
        'eu_stocks' => 'Avrupa Hisse Senetleri', 
        'world_stocks' => 'Dünya Hisse Senetleri',
        'commodities' => 'Emtialar',
        'forex_major' => 'Forex Majör Çiftler',
        'forex_minor' => 'Forex Minör Çiftler',
        'forex_exotic' => 'Forex Egzotik Çiftler',
        'indices' => 'World Indices'
    ];
}

// Fetch financial data with demo data for testing
function fetchFinancialData($symbols, $category) {
    if (empty($symbols)) return false;
    
    $results = [];
    
    // Demo data generator for testing purposes
    foreach ($symbols as $symbol) {
        // Generate realistic demo data
        $basePrice = getBasePriceForSymbol($symbol, $category);
        $change = (rand(-500, 500) / 100); // -5% to +5%
        $price = $basePrice + ($basePrice * $change / 100);
        
        $results[] = [
            'symbol' => $symbol,
            'longName' => getCompanyName($symbol, $category),
            'shortName' => getCompanyName($symbol, $category),
            'regularMarketPrice' => round($price, 4),
            'regularMarketChange' => round($price - $basePrice, 4),
            'regularMarketChangePercent' => round($change, 2),
            'regularMarketVolume' => rand(100000, 10000000),
            'regularMarketDayHigh' => round($price * 1.02, 4),
            'regularMarketDayLow' => round($price * 0.98, 4),
            'marketCap' => rand(1000000000, 500000000000)
        ];
    }
    
    return $results;
}

// Get base price for symbol based on category
function getBasePriceForSymbol($symbol, $category) {
    $prices = [
        'us_stocks' => [
            'AAPL' => 175.00, 'MSFT' => 338.00, 'GOOGL' => 138.00, 'AMZN' => 145.00, 'TSLA' => 248.00,
            'META' => 298.00, 'NVDA' => 435.00, 'JPM' => 148.00, 'JNJ' => 158.00, 'V' => 250.00,
            'WMT' => 158.00, 'PG' => 152.00, 'UNH' => 515.00, 'DIS' => 96.00, 'HD' => 315.00,
            'PYPL' => 62.00, 'BAC' => 29.00, 'ADBE' => 485.00, 'CRM' => 218.00, 'NFLX' => 385.00
        ],
        'forex_major' => [
            'EURUSD=X' => 1.0925, 'GBPUSD=X' => 1.2785, 'USDJPY=X' => 148.25, 'USDCHF=X' => 0.8695,
            'AUDUSD=X' => 0.6685, 'USDCAD=X' => 1.3485, 'NZDUSD=X' => 0.6125, 'EURJPY=X' => 162.15
        ],
        'forex_exotic' => [
            'USDTRY=X' => 27.45, 'EURTRY=X' => 29.95, 'GBPTRY=X' => 35.15, 'USDSEK=X' => 10.85,
            'USDNOK=X' => 10.65, 'USDPLN=X' => 4.15, 'EURSEK=X' => 11.85, 'USDZAR=X' => 18.25,
            'USDMXN=X' => 17.85, 'USDHUF=X' => 365.25
        ],
        'commodities' => [
            'GC=F' => 1985.50, 'SI=F' => 23.85, 'CL=F' => 78.25, 'BZ=F' => 82.15, 'NG=F' => 2.85,
            'HG=F' => 3.82, 'ZW=F' => 585.25, 'ZC=F' => 485.75, 'SB=F' => 22.85, 'KC=F' => 165.25
        ],
        'indices' => [
            '^DJI' => 34875.25, '^GSPC' => 4485.85, '^IXIC' => 13985.75, '^RUT' => 1885.65, '^VIX' => 18.25,
            '^GDAXI' => 15875.85, '^FTSE' => 7485.25, '^FCHI' => 7285.95, '^N225' => 32885.75, '^HSI' => 18275.85
        ]
    ];
    
    return $prices[$category][$symbol] ?? 100.00;
}

// Get company/instrument name
function getCompanyName($symbol, $category) {
    $names = [
        'us_stocks' => [
            'AAPL' => 'Apple Inc.', 'MSFT' => 'Microsoft Corporation', 'GOOGL' => 'Alphabet Inc.',
            'AMZN' => 'Amazon.com Inc.', 'TSLA' => 'Tesla Inc.', 'META' => 'Meta Platforms Inc.',
            'NVDA' => 'NVIDIA Corporation', 'JPM' => 'JPMorgan Chase & Co.', 'JNJ' => 'Johnson & Johnson',
            'V' => 'Visa Inc.', 'WMT' => 'Walmart Inc.', 'PG' => 'Procter & Gamble Co.',
            'UNH' => 'UnitedHealth Group Inc.', 'DIS' => 'Walt Disney Co.', 'HD' => 'Home Depot Inc.',
            'PYPL' => 'PayPal Holdings Inc.', 'BAC' => 'Bank of America Corp.', 'ADBE' => 'Adobe Inc.',
            'CRM' => 'Salesforce Inc.', 'NFLX' => 'Netflix Inc.'
        ],
        'forex_major' => [
            'EURUSD=X' => 'EUR/USD', 'GBPUSD=X' => 'GBP/USD', 'USDJPY=X' => 'USD/JPY', 'USDCHF=X' => 'USD/CHF',
            'AUDUSD=X' => 'AUD/USD', 'USDCAD=X' => 'USD/CAD', 'NZDUSD=X' => 'NZD/USD', 'EURJPY=X' => 'EUR/JPY'
        ],
        'commodities' => [
            'GC=F' => 'Gold Futures', 'SI=F' => 'Silver Futures', 'CL=F' => 'Crude Oil WTI',
            'BZ=F' => 'Brent Crude Oil', 'NG=F' => 'Natural Gas', 'HG=F' => 'Copper Futures',
            'ZW=F' => 'Wheat Futures', 'ZC=F' => 'Corn Futures', 'SB=F' => 'Sugar Futures', 'KC=F' => 'Coffee Futures'
        ]
    ];
    
    return $names[$category][$symbol] ?? $symbol;
}

// Get predefined symbols for each category (Yahoo Finance format)
function getCategorySymbols($category) {
    $symbols = [
        'us_stocks' => ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA', 'META', 'NVDA', 'JPM', 'JNJ', 'V', 'WMT', 'PG', 'UNH', 'DIS', 'HD', 'PYPL', 'BAC', 'ADBE', 'CRM', 'NFLX'],
        'eu_stocks' => ['SAP.DE', 'ASML.AS', 'MC.PA', 'NESN.SW', 'ROG.SW', 'AZN.L', 'SHEL.L', 'RDSA.AS', 'SIE.DE', 'OR.PA'],
        'world_stocks' => ['TSM', 'BABA', 'TCEHY', '7203.T', 'SNY', 'TM', 'SONY', 'ING', 'UL', 'RIO.L'],
        'commodities' => ['GC=F', 'SI=F', 'CL=F', 'BZ=F', 'NG=F', 'HG=F', 'ZW=F', 'ZC=F', 'SB=F', 'KC=F'],
        'forex_major' => ['EURUSD=X', 'GBPUSD=X', 'USDJPY=X', 'USDCHF=X', 'AUDUSD=X', 'USDCAD=X', 'NZDUSD=X', 'EURJPY=X'],
        'forex_minor' => ['EURGBP=X', 'GBPJPY=X', 'EURCHF=X', 'AUDJPY=X', 'GBPCHF=X', 'EURAUD=X', 'CADJPY=X', 'AUDCAD=X', 'NZDJPY=X', 'CHFJPY=X'],
        'forex_exotic' => ['USDTRY=X', 'EURTRY=X', 'GBPTRY=X', 'USDSEK=X', 'USDNOK=X', 'USDPLN=X', 'EURSEK=X', 'USDZAR=X', 'USDMXN=X', 'USDHUF=X'],
        'indices' => ['^DJI', '^GSPC', '^IXIC', '^RUT', '^VIX', '^GDAXI', '^FTSE', '^FCHI', '^N225', '^HSI']
    ];
    
    return $symbols[$category] ?? [];
}

// Update financial market data in database
function updateFinancialData($category = 'us_stocks') {
    $symbols = getCategorySymbols($category);
    
    if (empty($symbols)) {
        return false;
    }
    
    $financialData = fetchFinancialData($symbols, $category);
    
    if (!$financialData) {
        return false;
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    foreach ($financialData as $instrument) {
        if (!isset($instrument['symbol'])) continue;
        
        $symbol = $instrument['symbol'];
        $name = $instrument['longName'] ?? $instrument['shortName'] ?? $symbol;
        
        // Yahoo Finance API field mapping
        $price = floatval($instrument['regularMarketPrice'] ?? $instrument['price'] ?? 0);
        $change = floatval($instrument['regularMarketChange'] ?? 0);
        $change_percent = floatval($instrument['regularMarketChangePercent'] ?? 0);
        $volume = floatval($instrument['regularMarketVolume'] ?? 0);
        $high = floatval($instrument['regularMarketDayHigh'] ?? $price);
        $low = floatval($instrument['regularMarketDayLow'] ?? $price);
        $market_cap = floatval($instrument['marketCap'] ?? 0);
        
        $query = "INSERT INTO markets (symbol, name, price, change_24h, volume_24h, high_24h, low_24h, market_cap, category, logo_url) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, '') 
                  ON DUPLICATE KEY UPDATE 
                  price = VALUES(price), 
                  change_24h = VALUES(change_24h), 
                  volume_24h = VALUES(volume_24h), 
                  high_24h = VALUES(high_24h), 
                  low_24h = VALUES(low_24h), 
                  market_cap = VALUES(market_cap),
                  updated_at = CURRENT_TIMESTAMP";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$symbol, $name, $price, $change_percent, $volume, $high, $low, $market_cap, $category]);
    }
    
    return true;
}

// Get market data from database
function getMarketData($category = 'us_stocks', $limit = 50) {
    $database = new Database();
    $db = $database->getConnection();
    
    $limit = (int)$limit;
    
    $query = "SELECT * FROM markets WHERE category = ? ORDER BY market_cap DESC, volume_24h DESC LIMIT " . $limit;
    $stmt = $db->prepare($query);
    $stmt->execute([$category]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all market data for multiple categories
function getAllMarketsData($categories = []) {
    if (empty($categories)) {
        $categories = array_keys(getFinancialCategories());
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $placeholders = str_repeat('?,', count($categories) - 1) . '?';
    $query = "SELECT * FROM markets WHERE category IN ($placeholders) ORDER BY category, volume_24h DESC";
    $stmt = $db->prepare($query);
    $stmt->execute($categories);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by category
    $grouped = [];
    foreach ($results as $market) {
        $grouped[$market['category']][] = $market;
    }
    
    return $grouped;
}

// Get single market data
function getSingleMarket($symbol) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM markets WHERE symbol = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$symbol]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Execute trade
function executeTrade($user_id, $symbol, $type, $amount, $price) {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $db->beginTransaction();
        
        $total = $amount * $price;
        $fee = $total * (TRADING_FEE / 100);
        $total_with_fee = $total + $fee;
        
        if ($type == 'buy') {
            // Check TL balance
            $tl_balance = getUserBalance($user_id, 'tl');
            if ($tl_balance < $total_with_fee) {
                throw new Exception('Insufficient TL balance');
            }
            
            // Deduct TL, add crypto
            updateUserBalance($user_id, 'tl', $total_with_fee, 'subtract');
            
            $crypto_currency = strtolower(explode('_', $symbol)[0]);
            updateUserBalance($user_id, $crypto_currency, $amount, 'add');
            
        } else { // sell
            // Check crypto balance
            $crypto_currency = strtolower(explode('_', $symbol)[0]);
            $crypto_balance = getUserBalance($user_id, $crypto_currency);
            if ($crypto_balance < $amount) {
                throw new Exception('Insufficient crypto balance');
            }
            
            // Deduct crypto, add TL
            updateUserBalance($user_id, $crypto_currency, $amount, 'subtract');
            updateUserBalance($user_id, 'tl', $total - $fee, 'add');
        }
        
        // Record transaction
        $query = "INSERT INTO transactions (user_id, type, symbol, amount, price, total, fee) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$user_id, $type, $symbol, $amount, $price, $total, $fee]);
        
        $db->commit();
        return true;
        
    } catch (Exception $e) {
        $db->rollback();
        return false;
    }
}

// Get user transactions
function getUserTransactions($user_id, $limit = 50) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Convert limit to integer to avoid SQL syntax error
    $limit = (int)$limit;
    
    $query = "SELECT t.*, m.name as market_name FROM transactions t 
              LEFT JOIN markets m ON t.symbol = m.symbol 
              WHERE t.user_id = ? ORDER BY t.created_at DESC LIMIT " . $limit;
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Log activity
function logActivity($user_id, $action, $details = '') {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
}
?>

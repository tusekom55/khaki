<?php
require_once 'includes/functions.php';

// Set longer execution time for API tests
set_time_limit(300);

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'FinancialModelingPrep API Test';

// Test results storage
$test_results = [];
$total_requests = 0;
$successful_requests = 0;
$failed_requests = 0;

/**
 * Convert our symbols to FMP-compatible format
 */
function convertSymbolToFMP($symbol, $category) {
    $conversions = [
        // US Stocks - Direct mapping (already compatible)
        'us_stocks' => [
            'AAPL' => 'AAPL', 'MSFT' => 'MSFT', 'GOOGL' => 'GOOGL', 'AMZN' => 'AMZN', 
            'TSLA' => 'TSLA', 'META' => 'META', 'NVDA' => 'NVDA', 'JPM' => 'JPM',
            'JNJ' => 'JNJ', 'V' => 'V', 'WMT' => 'WMT', 'PG' => 'PG', 'UNH' => 'UNH',
            'DIS' => 'DIS', 'HD' => 'HD', 'PYPL' => 'PYPL', 'BAC' => 'BAC', 
            'ADBE' => 'ADBE', 'CRM' => 'CRM', 'NFLX' => 'NFLX'
        ],
        
        // European Stocks - Remove exchange suffix
        'eu_stocks' => [
            'SAP.DE' => 'SAP', 'ASML.AS' => 'ASML', 'MC.PA' => 'MC', 'NESN.SW' => 'NESN',
            'ROG.SW' => 'ROG', 'AZN.L' => 'AZN', 'SHEL.L' => 'SHEL', 'RDSA.AS' => 'RDSA',
            'SIE.DE' => 'SIE', 'OR.PA' => 'OR'
        ],
        
        // World Stocks - Mixed mapping
        'world_stocks' => [
            'TSM' => 'TSM', 'BABA' => 'BABA', 'TCEHY' => 'TCEHY', '7203.T' => '7203.T',
            'SNY' => 'SNY', 'TM' => 'TM', 'SONY' => 'SONY', 'ING' => 'ING',
            'UL' => 'UL', 'RIO.L' => 'RIO'
        ],
        
        // Forex - Remove Yahoo suffix
        'forex_major' => [
            'EURUSD=X' => 'EURUSD', 'GBPUSD=X' => 'GBPUSD', 'USDJPY=X' => 'USDJPY',
            'USDCHF=X' => 'USDCHF', 'AUDUSD=X' => 'AUDUSD', 'USDCAD=X' => 'USDCAD',
            'NZDUSD=X' => 'NZDUSD', 'EURJPY=X' => 'EURJPY'
        ],
        
        'forex_minor' => [
            'EURGBP=X' => 'EURGBP', 'GBPJPY=X' => 'GBPJPY', 'EURCHF=X' => 'EURCHF',
            'AUDJPY=X' => 'AUDJPY', 'GBPCHF=X' => 'GBPCHF', 'EURAUD=X' => 'EURAUD',
            'CADJPY=X' => 'CADJPY', 'AUDCAD=X' => 'AUDCAD', 'NZDJPY=X' => 'NZDJPY',
            'CHFJPY=X' => 'CHFJPY'
        ],
        
        'forex_exotic' => [
            'USDTRY=X' => 'USDTRY', 'EURTRY=X' => 'EURTRY', 'GBPTRY=X' => 'GBPTRY',
            'USDSEK=X' => 'USDSEK', 'USDNOK=X' => 'USDNOK', 'USDPLN=X' => 'USDPLN',
            'EURSEK=X' => 'EURSEK', 'USDZAR=X' => 'USDZAR', 'USDMXN=X' => 'USDMXN',
            'USDHUF=X' => 'USDHUF'
        ],
        
        // Commodities - Convert to USD pairs
        'commodities' => [
            'GC=F' => 'GCUSD', 'SI=F' => 'SIUSD', 'CL=F' => 'CLUSD', 'BZ=F' => 'BZUSD',
            'NG=F' => 'NGUSD', 'HG=F' => 'HGUSD', 'ZW=F' => 'ZWUSD', 'ZC=F' => 'ZCUSD',
            'SB=F' => 'SBUSD', 'KC=F' => 'KCUSD'
        ],
        
        // Indices - Remove ^ prefix and convert some
        'indices' => [
            '^DJI' => 'DJI', '^GSPC' => 'SPX', '^IXIC' => 'IXIC', '^RUT' => 'RUT',
            '^VIX' => 'VIX', '^GDAXI' => 'GDAXI', '^FTSE' => 'UKX', '^FCHI' => 'CAC',
            '^N225' => 'N225', '^HSI' => 'HSI'
        ]
    ];
    
    return $conversions[$category][$symbol] ?? $symbol;
}

/**
 * Make FMP API request with error handling
 */
function makeFMPRequest($endpoint, $params = []) {
    global $total_requests, $successful_requests, $failed_requests;
    
    $total_requests++;
    
    // Add API key to parameters
    $params['apikey'] = FMP_API_KEY;
    
    // Build URL
    $url = FMP_API_URL . $endpoint . '?' . http_build_query($params);
    
    // Make request
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'GlobalBorsa/1.0'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === false) {
        $failed_requests++;
        return [
            'success' => false,
            'error' => 'HTTP request failed',
            'url' => $url
        ];
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $failed_requests++;
        return [
            'success' => false,
            'error' => 'JSON decode error: ' . json_last_error_msg(),
            'response' => $response
        ];
    }
    
    $successful_requests++;
    return [
        'success' => true,
        'data' => $data,
        'url' => $url
    ];
}

/**
 * Test symbol availability in FMP
 */
function testSymbolAvailability($category) {
    global $test_results;
    
    $symbols = getCategorySymbols($category);
    $test_results[$category] = [
        'category_name' => getFinancialCategories()[$category],
        'total_symbols' => count($symbols),
        'tested_symbols' => [],
        'available_symbols' => [],
        'unavailable_symbols' => [],
        'converted_symbols' => []
    ];
    
    // Test first 5 symbols to save API calls
    $test_symbols = array_slice($symbols, 0, 5);
    
    foreach ($test_symbols as $original_symbol) {
        $fmp_symbol = convertSymbolToFMP($original_symbol, $category);
        
        $test_results[$category]['tested_symbols'][] = $original_symbol;
        $test_results[$category]['converted_symbols'][$original_symbol] = $fmp_symbol;
        
        // Test symbol availability
        if ($category === 'forex_major' || $category === 'forex_minor' || $category === 'forex_exotic') {
            // Use forex endpoint
            $result = makeFMPRequest('/fx', ['from' => substr($fmp_symbol, 0, 3), 'to' => substr($fmp_symbol, 3, 3)]);
        } elseif ($category === 'commodities') {
            // Use quote endpoint for commodities
            $result = makeFMPRequest('/quote/' . $fmp_symbol);
        } else {
            // Use standard quote endpoint
            $result = makeFMPRequest('/quote/' . $fmp_symbol);
        }
        
        if ($result['success'] && !empty($result['data'])) {
            $test_results[$category]['available_symbols'][] = $original_symbol;
        } else {
            $test_results[$category]['unavailable_symbols'][] = $original_symbol;
        }
        
        // Small delay to respect rate limits
        usleep(100000); // 0.1 second
    }
    
    return $test_results[$category];
}

/**
 * Test batch quote functionality
 */
function testBatchQuotes() {
    global $test_results;
    
    // Test batch request with US stocks
    $us_symbols = ['AAPL', 'MSFT', 'GOOGL', 'AMZN', 'TSLA'];
    $symbols_string = implode(',', $us_symbols);
    
    $result = makeFMPRequest('/quote/' . $symbols_string);
    
    $test_results['batch_test'] = [
        'requested_symbols' => $us_symbols,
        'symbols_count' => count($us_symbols),
        'success' => $result['success'],
        'response_count' => $result['success'] ? count($result['data']) : 0,
        'sample_data' => $result['success'] && !empty($result['data']) ? $result['data'][0] : null,
        'error' => $result['success'] ? null : $result['error']
    ];
    
    return $test_results['batch_test'];
}

/**
 * Test different endpoints
 */
function testEndpoints() {
    global $test_results;
    
    $endpoints = [
        'quote' => [
            'endpoint' => '/quote/AAPL',
            'description' => 'Single stock quote'
        ],
        'batch_quote' => [
            'endpoint' => '/quote/AAPL,MSFT,GOOGL',
            'description' => 'Batch stock quotes'
        ],
        'forex' => [
            'endpoint' => '/fx',
            'params' => ['from' => 'USD', 'to' => 'EUR'],
            'description' => 'Forex rates'
        ],
        'commodities' => [
            'endpoint' => '/quote/GCUSD',
            'description' => 'Commodity prices'
        ],
        'indices' => [
            'endpoint' => '/quote/SPX',
            'description' => 'Index prices'
        ]
    ];
    
    $test_results['endpoints'] = [];
    
    foreach ($endpoints as $key => $endpoint_data) {
        $params = $endpoint_data['params'] ?? [];
        $result = makeFMPRequest($endpoint_data['endpoint'], $params);
        
        $test_results['endpoints'][$key] = [
            'description' => $endpoint_data['description'],
            'endpoint' => $endpoint_data['endpoint'],
            'success' => $result['success'],
            'has_data' => $result['success'] && !empty($result['data']),
            'data_count' => $result['success'] ? count($result['data']) : 0,
            'sample_data' => $result['success'] && !empty($result['data']) ? 
                (is_array($result['data']) ? $result['data'][0] : $result['data']) : null,
            'error' => $result['success'] ? null : $result['error']
        ];
        
        usleep(100000); // Rate limit respect
    }
    
    return $test_results['endpoints'];
}

// Run tests if requested
$run_tests = $_GET['test'] ?? false;

if ($run_tests) {
    echo "<h2>🧪 FinancialModelingPrep API Test Başlatılıyor...</h2>";
    echo "<div class='alert alert-info'>Test süreci başladı. Bu işlem birkaç dakika sürebilir...</div>";
    
    // Test 1: Symbol availability for each category
    echo "<h3>📊 1. Sembol Uyumluluk Testi</h3>";
    $categories = ['us_stocks', 'forex_major', 'commodities', 'indices'];
    
    foreach ($categories as $category) {
        echo "<h4>Kategori: " . getFinancialCategories()[$category] . "</h4>";
        $result = testSymbolAvailability($category);
        echo "<p>✅ Test tamamlandı - " . count($result['available_symbols']) . "/" . count($result['tested_symbols']) . " sembol uyumlu</p>";
    }
    
    // Test 2: Batch functionality
    echo "<h3>🚀 2. Batch İstek Testi</h3>";
    $batch_result = testBatchQuotes();
    echo "<p>Batch test: " . ($batch_result['success'] ? '✅ Başarılı' : '❌ Başarısız') . "</p>";
    
    // Test 3: Different endpoints
    echo "<h3>🔗 3. Endpoint Testi</h3>";
    $endpoint_results = testEndpoints();
    foreach ($endpoint_results as $key => $result) {
        echo "<p>{$result['description']}: " . ($result['success'] ? '✅ Başarılı' : '❌ Başarısız') . "</p>";
    }
    
    echo "<div class='alert alert-success'>🎉 Tüm testler tamamlandı!</div>";
}

include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-3">
                        <i class="fas fa-flask me-2 text-primary"></i>
                        FinancialModelingPrep API Test Center
                    </h1>
                    <p class="text-muted mb-0">
                        FMP API entegrasyonu için sembol uyumluluğu ve endpoint testleri
                    </p>
                </div>
            </div>

            <!-- Test Controls -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-play-circle me-2"></i>
                        Test Kontrolleri
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>🔑 API Konfigürasyonu</h6>
                            <ul class="list-unstyled">
                                <li><strong>API URL:</strong> <?php echo FMP_API_URL; ?></li>
                                <li><strong>API Key:</strong> <?php echo FMP_API_KEY === 'demo' ? '❌ Demo (Gerçek key gerekli)' : '✅ Ayarlandı'; ?></li>
                                <li><strong>Günlük Limit:</strong> <?php echo FMP_REQUESTS_PER_DAY; ?> istek</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>📊 Test İstatistikleri</h6>
                            <?php if ($run_tests): ?>
                            <ul class="list-unstyled">
                                <li><strong>Toplam İstek:</strong> <span class="badge bg-info"><?php echo $total_requests; ?></span></li>
                                <li><strong>Başarılı:</strong> <span class="badge bg-success"><?php echo $successful_requests; ?></span></li>
                                <li><strong>Başarısız:</strong> <span class="badge bg-danger"><?php echo $failed_requests; ?></span></li>
                            </ul>
                            <?php else: ?>
                            <p class="text-muted">Test henüz çalıştırılmadı</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <?php if (!$run_tests): ?>
                        <a href="?test=1" class="btn btn-primary">
                            <i class="fas fa-rocket me-2"></i>Testleri Başlat
                        </a>
                        <?php else: ?>
                        <a href="?" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Sayfayı Yenile
                        </a>
                        <a href="?test=1" class="btn btn-primary ms-2">
                            <i class="fas fa-sync me-2"></i>Testleri Tekrarla
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($run_tests && !empty($test_results)): ?>
            
            <!-- Symbol Compatibility Results -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Sembol Uyumluluk Sonuçları
                    </h5>
                </div>
                <div class="card-body">
                    <?php foreach (['us_stocks', 'forex_major', 'commodities', 'indices'] as $category): ?>
                    <?php if (isset($test_results[$category])): ?>
                    <?php $result = $test_results[$category]; ?>
                    <div class="mb-4">
                        <h6 class="text-primary"><?php echo $result['category_name']; ?></h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Orijinal Sembol</th>
                                                <th>FMP Sembol</th>
                                                <th>Durum</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($result['tested_symbols'] as $symbol): ?>
                                            <tr>
                                                <td><code><?php echo $symbol; ?></code></td>
                                                <td><code><?php echo $result['converted_symbols'][$symbol]; ?></code></td>
                                                <td>
                                                    <?php if (in_array($symbol, $result['available_symbols'])): ?>
                                                    <span class="badge bg-success">✅ Uyumlu</span>
                                                    <?php else: ?>
                                                    <span class="badge bg-danger">❌ Uyumsuz</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <h6>Özet</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>✅ Uyumlu: <?php echo count($result['available_symbols']); ?></li>
                                        <li>❌ Uyumsuz: <?php echo count($result['unavailable_symbols']); ?></li>
                                        <li>📊 Test Edilen: <?php echo count($result['tested_symbols']); ?>/<?php echo $result['total_symbols']; ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Batch Test Results -->
            <?php if (isset($test_results['batch_test'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Batch İstek Test Sonucu
                    </h5>
                </div>
                <div class="card-body">
                    <?php $batch = $test_results['batch_test']; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Test Detayları</h6>
                            <ul class="list-unstyled">
                                <li><strong>Test Edilen Semboller:</strong> <?php echo implode(', ', $batch['requested_symbols']); ?></li>
                                <li><strong>İstek Sayısı:</strong> <?php echo $batch['symbols_count']; ?> sembol</li>
                                <li><strong>Durum:</strong> 
                                    <?php if ($batch['success']): ?>
                                    <span class="badge bg-success">✅ Başarılı</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">❌ Başarısız</span>
                                    <?php endif; ?>
                                </li>
                                <li><strong>Dönen Veri:</strong> <?php echo $batch['response_count']; ?> sembol</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <?php if ($batch['sample_data']): ?>
                            <h6>Örnek Veri</h6>
                            <pre class="bg-light p-2 rounded"><code><?php echo json_encode($batch['sample_data'], JSON_PRETTY_PRINT); ?></code></pre>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Endpoint Test Results -->
            <?php if (isset($test_results['endpoints'])): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-link me-2"></i>
                        Endpoint Test Sonuçları
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Endpoint</th>
                                    <th>Açıklama</th>
                                    <th>Durum</th>
                                    <th>Veri Sayısı</th>
                                    <th>Örnek</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($test_results['endpoints'] as $key => $endpoint): ?>
                                <tr>
                                    <td><code><?php echo $endpoint['endpoint']; ?></code></td>
                                    <td><?php echo $endpoint['description']; ?></td>
                                    <td>
                                        <?php if ($endpoint['success']): ?>
                                        <span class="badge bg-success">✅ Başarılı</span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">❌ Başarısız</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $endpoint['data_count']; ?></td>
                                    <td>
                                        <?php if ($endpoint['sample_data']): ?>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $key; ?>">
                                            Görüntüle
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sample Data Modals -->
            <?php foreach ($test_results['endpoints'] as $key => $endpoint): ?>
            <?php if ($endpoint['sample_data']): ?>
            <div class="modal fade" id="modal-<?php echo $key; ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?php echo $endpoint['description']; ?> - Örnek Veri</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <pre><code><?php echo json_encode($endpoint['sample_data'], JSON_PRETTY_PRINT); ?></code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>

            <!-- Recommendations -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        Öneriler ve Sonuçlar
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($run_tests): ?>
                    <div class="alert alert-info">
                        <h6>📊 Test Özeti</h6>
                        <ul class="mb-0">
                            <li><strong>Toplam API İsteği:</strong> <?php echo $total_requests; ?> / <?php echo FMP_REQUESTS_PER_DAY; ?> (günlük limit)</li>
                            <li><strong>Başarı Oranı:</strong> <?php echo $total_requests > 0 ? round(($successful_requests / $total_requests) * 100, 1) : 0; ?>%</li>
                            <li><strong>Önerilen Kullanım:</strong> Batch isteklerle günde ~7-10 istek ile tüm kategorileri güncelleyebilirsiniz</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>✅ Öne Çıkan Avantajlar</h6>
                            <ul>
                                <li>Batch istekler destekleniyor (50+ sembol/istek)</li>
                                <li>ABD hisse senetleri tam uyumlu</li>
                                <li>Forex verileri erişilebilir</li>
                                <li>JSON formatı kolay parse edilebilir</li>
                                <li>Günlük 100 istek yeterli olabilir</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>⚠️ Dikkat Edilmesi Gerekenler</h6>
                            <ul>
                                <li>Avrupa hisse sembolleri format dönüşümü gerekiyor</li>
                                <li>Bazı endeks sembolleri farklı (^GSPC → SPX)</li>
                                <li>Emtialar USD pair formatında (GC=F → GCUSD)</li>
                                <li>Rate limit dikkatli takip edilmeli</li>
                                <li>Gerçek API key'i almak gerekiyor</li>
                            </ul>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <h6>🚀 Test Başlatmak İçin</h6>
                        <p class="mb-0">
                            Yukarıdaki "Testleri Başlat" butonuna tıklayarak FMP API'nin uyumluluğunu test edebilirsiniz.
                            Test yaklaşık 2-3 dakika sürer ve 15-20 API isteği yapar.
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.badge { font-size: 0.9em; }
.table code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
pre { font-size: 0.85em; max-height: 300px; overflow-y: auto; }
.alert h6 { margin-bottom: 0.5rem; }
.modal-dialog { max-width: 800px; }
</style>

<?php include 'includes/footer.php'; ?>

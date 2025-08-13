<?php
/* ===== AYARLAR ===== */
$dbHost = 'localhost';
$dbUser = 'u225998063_seccc';
$dbPass = '123456Tubb';
$dbName = 'u225998063_hurrra';
$apiKey = 'Pt5IwxHnQLEUskikphYk55M186mqPCWL'; // https://financialmodelingprep.com

$symbol = strtoupper($_GET['symbol'] ?? 'ADBE'); // istersen ?symbol=MSFT gibi kullan

/* ===== DB ===== */
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
$mysqli->set_charset('utf8mb4');
if ($mysqli->connect_error) { http_response_code(500); exit('DB bağlantı hatası: '.$mysqli->connect_error); }

/* ===== API ===== */
$url = 'https://financialmodelingprep.com/api/v3/quote/' . urlencode($symbol) . '?apikey=' . urlencode($apiKey);
$json = @file_get_contents($url);
if ($json === false) { http_response_code(502); exit('API okunamadı'); }
$arr = json_decode($json, true);
if (!is_array($arr) || empty($arr[0]['symbol'])) { http_response_code(404); exit('Sembol bulunamadı'); }

$q = $arr[0];
$chg = isset($q['changesPercentage']) ? (float)str_replace(['%','+'], '', $q['changesPercentage']) : 0.0;

$name  = $q['name']   ?? $symbol;
$price = (float)($q['price']    ?? 0);
$vol   = (float)($q['volume']   ?? 0);
$high  = (float)($q['dayHigh']  ?? 0);
$low   = (float)($q['dayLow']   ?? 0);
$mcap  = (float)($q['marketCap']?? 0);
$cat   = 'us_stocks'; // ADBE için sabit; istersen query ile değiştir

/* ===== UPSERT ===== */
$sql = "INSERT INTO markets
 (symbol, name, price, change_24h, volume_24h, high_24h, low_24h, market_cap, category)
 VALUES (?,?,?,?,?,?,?,?,?)
 ON DUPLICATE KEY UPDATE
  name=VALUES(name),
  price=VALUES(price),
  change_24h=VALUES(change_24h),
  volume_24h=VALUES(volume_24h),
  high_24h=VALUES(high_24h),
  low_24h=VALUES(low_24h),
  market_cap=VALUES(market_cap),
  category=VALUES(category)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) { http_response_code(500); exit('Prepare hatası: '.$mysqli->error); }
$stmt->bind_param('ssdddddds', $symbol, $name, $price, $chg, $vol, $high, $low, $mcap, $cat);
$ok = $stmt->execute();
$stmt->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
  'ok'      => (bool)$ok,
  'symbol'  => $symbol,
  'price'   => $price,
  'chg%'    => $chg,
  'high'    => $high,
  'low'     => $low,
  'volume'  => $vol,
  'market_cap' => $mcap
], JSON_UNESCAPED_UNICODE);

<?php
// Financial Data API Keys
define('TWELVE_DATA_API_KEY', '7a0311c2af9a48eab8277a4bfe598a30'); // Live API Key - 800 requests/day
define('TWELVE_DATA_API_URL', 'https://api.twelvedata.com');

define('ALPHA_VANTAGE_API_KEY', 'demo'); // Backup API - Replace with real API key  
define('ALPHA_VANTAGE_API_URL', 'https://www.alphavantage.co/query');

// Payment API configuration (demo)
define('PAPARA_API_KEY', 'demo_key');
define('PAPARA_API_URL', 'https://merchant-api.papara.com');

// Site configuration
define('SITE_NAME', 'GlobalBorsa');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@globalborsa.com');

// Trading fees (percentage)
define('TRADING_FEE', 0.25); // 0.25%
define('WITHDRAWAL_FEE_TL', 5.00); // 5 TL fixed fee
define('WITHDRAWAL_FEE_USD', 2.00); // 2 USD fixed fee

// Minimum amounts
define('MIN_TRADE_AMOUNT', 10.00); // Minimum 10 TL trade
define('MIN_WITHDRAWAL_AMOUNT', 50.00); // Minimum 50 TL withdrawal
define('MIN_DEPOSIT_AMOUNT', 20.00); // Minimum 20 TL deposit
?>

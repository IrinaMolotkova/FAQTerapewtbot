<?php

$TOKEN = 'ТВОЙ_ТЕЛЕГРАМ_ТОКЕН';
$ZAPIER_WEBHOOK_URL = 'https://hooks.zapier.com/hooks/catch/123456/abcdef'; // 🔁 заміни на свій

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!isset($update["message"])) exit;

$chat_id = $update["message"]["chat"]["id"];
$message = trim($update["message"]["text"]);
$user_name = $update["message"]["from"]["first_name"] ?? "Анонім";

$response = '';
$pattern = '/([А-ЯІЄЇа-яіїєґ\'\s]{2,})[,\s]+(\+?\d{9,15})[,\s]+(\d{2}\.\d{2}\.\d{4})/u';

if ($message == "/start") {
    $response = "Привіт, $user_name! Щоб записатись, напиши: Ім’я, телефон, дата (дд.мм.рррр)";
} elseif (preg_match($pattern, $message, $matches)) {
    $name = trim($matches[1]);
    $phone = trim($matches[2]);
    $date = trim($matches[3]);

    $response = "Дякую, $name! Вас записано на $date. Ми зателефонуємо вам за номером $phone.";

    // Надсилаємо в Zapier для Google Sheets
    $data = [
        'Ім’я' => $name,
        'Телефон' => $phone,
        'Дата візиту' => $date,
        'Повідомлення' => $message,
        'Telegram name' => $user_name,
        'Час' => date("Y-m-d H:i:s")
    ];

    file_get_contents($ZAPIER_WEBHOOK_URL . '?' . http_build_query($data));
} else {
    $response = "Будь ласка, напиши дані у форматі:\nІм’я, телефон, дата (дд.мм.рррр)";
}

// Відповідь користувачу
file_get_contents("https://api.telegram.org/bot$TOKEN/sendMessage?" . http_build_query([
    'chat_id' => $chat_id,
    'text' => $response
]));

<?php

// 🔑 Введи свій токен тут
$TOKEN = '8402915098:AAEooydjUEyjOghtZ_k36WehYSenfLmuDlk';

// 🌐 Твій Zapier Webhook URL (заміни на свій)
$ZAPIER_WEBHOOK_URL = 'https://hooks.zapier.com/hooks/catch/XXXXXX/YYYYYY'; // ← заміни!

// Отримай дані з Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Перевірка, чи є повідомлення
if (!isset($update["message"])) {
    exit;
}

// Отримання даних
$chat_id = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];
$user_name = $update["message"]["from"]["first_name"] ?? "Анонім";

// Відповіді бота
$response = '';

if ($message == "/start") {
    $response = "Привіт, $user_name! Я бот, який допоможе вам записатись до терапевта. Напишіть \"допомога\" та заповніть відповідні дані.";
} elseif (strtolower($message) == "допомога") {
    $response = "Щоб записатись, напишіть: Ваше ім’я, телефон, бажана дата візиту.";
} else {
    $response = "Ви написали: " . $message;
}

// Надсилання повідомлення назад у Telegram
file_get_contents("https://api.telegram.org/bot$TOKEN/sendMessage?" . http_build_query([
    'chat_id' => $chat_id,
    'text' => $response
]));

// 🔄 Відправляємо повідомлення у Zapier
file_get_contents($ZAPIER_WEBHOOK_URL . '?' . http_build_query([
    'chat_id' => $chat_id,
    'user_name' => $user_name,
    'message' => $message,
    'datetime' => date("Y-m-d H:i:s")
]));

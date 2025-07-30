<?php

// 🔑 Введи свій токен тут
$TOKEN = '8402915098:AAEooydjUEyjOghtZ_k36WehYSenfLmuDlk';

// Отримай дані з Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

// Перевірка
if (!isset($update["message"])) {
    exit;
}

// Отримання даних
$chat_id = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// Відповіді бота
$response = '';

if ($message == "/start") {
    $response = "Привіт! Я бот, який допоможе вам записатись до терапевта.";
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

<?php
require_once __DIR__ . '/bootstrap.php';

use src\Bot\TelegramBot;
use src\Processors\JsonProcessor;
use src\Utils\Utils;

// Initialize the Telegram Bot with the token from .env
$telegramBot = new TelegramBot($_ENV['TELEGRAM_BOT_TOKEN']);

// Get the JSON directory path from .env
$jsonDirectory = rtrim($_ENV['JSON_DIRECTORY'], '/') . '/';
$interval = isset($_ENV['CRON_INTERVAL']) ? $_ENV['CRON_INTERVAL'] : 60; // Default to 60 seconds

Utils::logMessage("Cron job started. Checking JSON files every $interval seconds.");

while (true) {
    Utils::logMessage("Checking for new JSON files in the directory: $jsonDirectory");

    // Scan the directory for JSON files
    $jsonFiles = glob($jsonDirectory . '*.json');

    foreach ($jsonFiles as $file) {
        Utils::logMessage("Processing file: $file");
        JsonProcessor::processJsonFile($file, $telegramBot);
    }

    // Wait for the specified interval before the next check
    Utils::logMessage("Waiting for $interval seconds before the next check.");
    sleep($interval);
}

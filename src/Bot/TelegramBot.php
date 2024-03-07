<?php
namespace src\Bot;

use src\Utils\Utils;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramBot {
    private $bot;

    public function __construct($token) {
        $this->bot = new BotApi($token);
        Utils::logMessage("Telegram Bot initialized with token.");
    }

    public function postToTelegram($channel, $message, $imageUrl, $buttonUrl) {
        Utils::logMessage("Preparing to post message to Telegram channel: " . $channel);

        $keyboard = new InlineKeyboardMarkup([
            [['text' => $_ENV['AMAZON_BUTTON_TEXT'], 'url' => $buttonUrl]],
            [['text' => $_ENV['WEBSITE_BUTTON_TEXT'], 'url' => $_ENV['WEBSITE_URL']]]
        ]);

        try {
            if (!empty($imageUrl)) {
                $this->bot->sendPhoto($channel, $imageUrl, $message, null, $keyboard);
                Utils::logMessage("Photo message posted to channel: " . $channel);
            } else {
                $this->bot->sendMessage($channel, $message, 'Markdown', false, null, $keyboard);
                Utils::logMessage("Text message posted to channel: " . $channel);
            }
            return true;
        } catch (Exception $e) {
            Utils::logMessage("Error posting message to Telegram: " . $e->getMessage());
            return false;
        }
    }
}

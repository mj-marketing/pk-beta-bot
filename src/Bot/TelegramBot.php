<?php
namespace PKBetaBot\Bot;

use PKBetaBot\Utils\Utils;
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
        Utils::logMessage("Preparing to post message to Telegram channel: $channel");

        $keyboard = new InlineKeyboardMarkup([
            [['text' => $_ENV['AMAZON_BUTTON_TEXT'], 'url' => $buttonUrl]],
            [['text' => $_ENV['WEBSITE_BUTTON_TEXT'], 'url' => $_ENV['WEBSITE_URL']]]
        ]);
        Utils::logMessage("Image URL: " . $imageUrl.PHP_EOL);

        try {
            if (!empty($imageUrl)) {
                // Send photo with caption, specifying all needed parameters
                $this->bot->sendPhoto(
                    $channel,
                    $imageUrl,
                    $message,
                    null, // replyToMessageId
                    $keyboard, // replyMarkup
                    false, // disableNotification
                    'HTML' // parseMode
                // Omitting the rest of the parameters as they are optional and have default values
                );
                Utils::logMessage("Photo and HTML message posted to channel: $channel");
            } else {
                // Send text message in HTML format
                $this->bot->sendMessage($channel, $message, 'HTML', false, null, $keyboard);
                Utils::logMessage("HTML text message posted to channel: $channel");
            }
            return true;
        } catch (Exception $e) {
            Utils::logMessage("Failed to post message to Telegram: " . $e->getMessage());
            return false;
        }

    }
}

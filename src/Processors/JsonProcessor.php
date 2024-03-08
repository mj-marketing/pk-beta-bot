<?php
namespace PKBetaBot\Processors;

use PKBetaBot\Bot\TelegramBot;
use PKBetaBot\Utils\Utils;

class JsonProcessor {
    public static function processJsonFile($filePath, TelegramBot $telegramBot) {
        Utils::logMessage("Processing JSON file: " . $filePath);

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            Utils::logMessage("Failed to read JSON file: " . $filePath);
            return;
        }

        $data = json_decode($jsonContent, true);
        if (!isset($data['image_url'], $data['text'])) {
            Utils::logMessage("Essential data missing in JSON file: " . $filePath);
            return;
        }

        $imageUrl = $data['image_url'];
        $originalMessage = $data['text'];
        $resolvedAmazonUrl = null;
        $originalAmazonUrl = null;

        if (!empty($data['button_url'])) {
            $resolvedAmazonUrl = Utils::resolveUrl($data['button_url']);
            $originalAmazonUrl = $data['button_url'];
        } else {
            // Extract all URLs from the text and resolve them
            $originalUrls = Utils::extractOriginalUrlFromText($originalMessage);

            // Ensure that $originalUrls is an array before iterating
            if (is_array($originalUrls)) {
                foreach ($originalUrls as $url) {
                    $resolvedUrl = Utils::resolveUrl($url);
                    if (strpos($resolvedUrl, 'amazon.de') !== false) {
                        $resolvedAmazonUrl = $resolvedUrl;
                        $originalAmazonUrl = $url;
                        break;
                    }
                }
            }
        }

        if ($resolvedAmazonUrl) {
            $amazonUrlWithReferral = Utils::appendReferralTag($resolvedAmazonUrl, $_ENV['REFERRAL_TAG']);
            $messageToSend = ($originalAmazonUrl !== $data['button_url']) ? str_replace($originalAmazonUrl, '', $originalMessage) : $originalMessage;

            $messageToSend = Utils::formatMessageAsHtml($messageToSend);
            if ($telegramBot->postToTelegram($_ENV['TELEGRAM_CHANNEL'], $messageToSend, $imageUrl, $amazonUrlWithReferral)) {
                Utils::logMessage("Successfully posted message to Telegram.");

                $whatsAppMessage = self::convertHtmlToWhatsAppFormatting($messageToSend);
                $whatsAppMessage .= "\n\n🔗 " . $amazonUrlWithReferral; // Append link with emoticon

                $whatsAppResponse = Utils::sendToWhatsApp($whatsAppMessage, $imageUrl);
                Utils::logMessage("WhatsApp response: " . $whatsAppResponse);


                // Delete the JSON file
                unlink($filePath);
                Utils::logMessage("Successfully processed and deleted JSON file: " . $filePath);

                // Delete the corresponding image file
                $imageFilePath = str_replace('.json', '.jpg', $filePath);
                if (file_exists($imageFilePath)) {
                    unlink($imageFilePath);
                    Utils::logMessage("Successfully deleted corresponding image file: " . $imageFilePath);
                }
            } else {
                Utils::logMessage("Failed to post message to Telegram.");
            }
        } else {
            Utils::logMessage("No valid Amazon URL found in message. Skipping message.");
        }
    }
    private static function convertHtmlToWhatsAppFormatting($text) {
        // Convert bold
        $text = preg_replace('/<b>(.*?)<\/b>/', '*$1*', $text);

        // Convert italic
        $text = preg_replace('/<i>(.*?)<\/i>/', '_$1_', $text);

        // Convert strikethrough
        $text = preg_replace('/<s>(.*?)<\/s>/', '~$1~', $text);

        return $text;
    }
}

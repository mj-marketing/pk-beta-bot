<?php
namespace PKBetaBot\Utils;

class Utils {
    // Logs a message to the console
    public static function logMessage($message) {
        echo date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    }

    // Extracts the first URL found in a given text
    public static function extractOriginalUrlFromText($text) {
        $urlPattern = '/https?:\/\/\S+/';
        preg_match($urlPattern, $text, $matches);
        return $matches[0] ?? null;
    }

    // Resolves a URL to its final destination
    public static function resolveUrl($url) {
        $apiUrl = "https://api.redirect-checker.net/";
        $params = [
            'url' => $url,  // URL without urlencode
            'timeout' => 5,
            'maxhops' => 10,
            'meta-refresh' => 1,
            'format' => 'json',
            'more' => 1
        ];

        $apiRequestUrl = $apiUrl . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiRequestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            self::logMessage("Failed to get a response from the redirect checker API.");
            return null;
        }

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($responseData['data'])) {
            self::logMessage("Failed to decode JSON response from the redirect checker API.");
            return null;
        }

        // Get the last element in the 'data' array which should be the final redirect URL
        $arrayReverse = end($responseData['data']);
        $responseUrl = $arrayReverse['response']['info']['url'];

        return $responseUrl;
    }



    // Appends a referral tag to an Amazon URL
    public static function appendReferralTag($url, $referralTag) {
        $asinPattern = '/\/dp\/([A-Z0-9]{10})/i';
        $referralBrand = $_ENV['REFERRAL_BRAND']; // Get referral brand from environment variable

        if (preg_match($asinPattern, $url, $matches)) {
            $asin = $matches[1];
            return "https://www.amazon.de/$referralBrand/dp/$asin?tag=$referralTag";
        }

        return $url;
    }

    public static function sendToWhatsApp($message, $imageUrl, $link) {
        $apiKey = $_ENV['WHAPI_API_KEY']; // Retrieve API key from .env
        $whapiUrl = 'https://whapi.cloud/api/sendMessageImage';

        $postData = [
            'key' => $apiKey,
            'phone' => 'recipient_number', // Set the recipient's phone number
            'caption' => $message,
            'image' => $imageUrl,
            'link' => $link
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $whapiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response; // Or handle the response as needed
    }

    public static function formatMessageAsHtml($text) {
        // Replace Markdown-style bold and strikethrough with HTML tags
        $text = str_replace('**', '<b>', $text);
        $text = str_replace('~~', '<s>', $text);

        // Close the HTML tags
        $text = preg_replace('/<b>(.*?)<b>/', '<b>$1</b>', $text);
        $text = preg_replace('/<s>(.*?)<s>/', '<s>$1</s>', $text);

        return $text;
    }


}

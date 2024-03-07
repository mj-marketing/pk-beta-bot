<?php
namespace src\Utils;

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
            'url' => urlencode($url),
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
        if (json_last_error() !== JSON_ERROR_NONE || !isset($responseData['data']['response']['redirect_url'])) {
            self::logMessage("Failed to decode JSON response from the redirect checker API or no redirect URL found.");
            return null;
        }

        return $responseData['data']['response']['redirect_url'];
    }

    // Appends a referral tag to an Amazon URL
    public static function appendReferralTag($url, $referralTag) {
        $asinPattern = '/\/dp\/([A-Z0-9]{10})/i';

        if (preg_match($asinPattern, $url, $matches)) {
            $asin = $matches[1];
            return "https://www.amazon.de/dp/$asin?tag=$referralTag";
        }

        return $url;
    }
}

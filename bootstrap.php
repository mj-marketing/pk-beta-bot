<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use PKBetaBot\Utils\Utils;

// Initialize Dotenv and load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configure error reporting based on environment settings
if ($_ENV['DEBUG_MODE'] === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    Utils::logMessage("Debug mode is ON.");
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
    Utils::logMessage("Debug mode is OFF.");
}

// Any additional bootstrap code can be added here

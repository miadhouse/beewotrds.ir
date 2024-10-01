<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Check if RECAPTCHA_SECRET_KEY is set
if (!isset($_ENV['RECAPTCHA_SECRET_KEY']) || empty($_ENV['RECAPTCHA_SECRET_KEY'])) {
    throw new \Exception('RECAPTCHA_SECRET_KEY is not set in the environment variables.');
}

// Proceed to include routes
require_once 'routes.php';

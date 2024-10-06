<?php
// index.php

// فعال‌سازی نمایش خطاها برای دیباگینگ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بارگذاری اتولودر کامپوزر
require_once 'vendor/autoload.php';

// بارگذاری متغیرهای محیطی با استفاده از Dotenv
use Dotenv\Dotenv;
use App\Handlers\ExceptionHandler;

// بارگذاری متغیرهای محیطی
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// بررسی وجود RECAPTCHA_SECRET_KEY
if (!isset($_ENV['6LdypVMqAAAAALEMKytEtDHnHPxGKcLkaz_-R_CU']) || empty($_ENV['RECAPTCHA_SECRET_KEY'])) {
    throw new \Exception('RECAPTCHA_SECRET_KEY is not set in the environment variables.');
}

// تنظیم هندلر خطاهای عمومی
set_exception_handler([new ExceptionHandler(), 'handle']);

// بارگذاری فایل `routes.php` که منطق مسیریابی را دارد
require_once 'routes.php';

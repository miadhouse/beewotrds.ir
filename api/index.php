<?php
// index.php
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;
use App\Handlers\ExceptionHandler;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
if (!isset($_ENV['6LdypVMqAAAAALEMKytEtDHnHPxGKcLkaz_-R_CU']) || empty($_ENV['RECAPTCHA_SECRET_KEY'])) {
    throw new \Exception('RECAPTCHA_SECRET_KEY is not set in the environment variables.');
}

// تنظیم هندلر خطاهای عمومی
set_exception_handler([new ExceptionHandler(), 'handle']);

// بارگذاری فایل `routes.php` که منطق مسیریابی را دارد
require_once 'routes.php';

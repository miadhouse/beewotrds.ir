<?php
namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogHelper {
    public static function logError($message) {
        $log = new Logger('error_logger');
        $log->pushHandler(new StreamHandler(__DIR__.'/../../logs/error.log', Logger::ERROR));
        $log->error($message);
    }
}

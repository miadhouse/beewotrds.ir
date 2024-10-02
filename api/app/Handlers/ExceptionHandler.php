<?php
// src/Handlers/ExceptionHandler.php

namespace App\Handlers;

use App\Exceptions\ApiException;

class ExceptionHandler
{
    /**
     * مدیریت خطاهای پرتاب شده
     *
     * @param \Throwable $exception
     * @return void
     */
    public function handle(\Throwable $exception): void
    {
        if ($exception instanceof ApiException) {
            $this->sendResponse(
                [
                    'status' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                    'errors' => $exception->getErrors()
                ],
                $exception->getStatusCode()
            );
        } else {
            // ثبت خطاهای غیرمنتظره برای بررسی‌های بعدی
            error_log($exception->getMessage());
            $this->sendResponse(
                [
                    'status' => 500,
                    'message' => 'Internal Server Error'
                ],
                500
            );
        }
    }

    /**
     * ارسال پاسخ JSON به کلاینت
     *
     * @param array $data
     * @param int $statusCode
     * @return void
     */
    private function sendResponse(array $data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

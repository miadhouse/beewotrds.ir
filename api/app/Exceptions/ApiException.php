<?php
// src/Exceptions/ApiException.php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected int $statusCode;
    protected array $errors;

    /**
     * ApiException constructor.
     *
     * @param string $message
     * @param int $statusCode
     * @param array $errors
     */
    public function __construct(string $message, int $statusCode = 400, array $errors = [])
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    /**
     * دریافت کد وضعیت HTTP
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * دریافت جزئیات خطاها
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

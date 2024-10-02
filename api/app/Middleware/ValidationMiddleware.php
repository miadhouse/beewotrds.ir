<?php
// src/Middleware/ValidationMiddleware.php

namespace App\Middleware;

use App\Services\RequestValidator;
use App\Exceptions\ApiException;

class ValidationMiddleware
{
    private RequestValidator $validator;

    /**
     * ValidationMiddleware constructor.
     *
     * @param RequestValidator $validator
     */
    public function __construct(RequestValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * انجام اعتبارسنجی بر اساس قوانین مشخص شده
     *
     * @param array $data
     * @param array $rules
     * @return void
     * @throws ApiException
     */
    public function handle(array $data, array $rules): void
    {
        $this->validator->validate($data, $rules);
        if ($this->validator->hasErrors()) {
            throw new ApiException('Validation errors', 400, $this->validator->getErrors());
        }
    }
}

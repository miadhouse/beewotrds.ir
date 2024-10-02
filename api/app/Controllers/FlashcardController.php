<?php

namespace App\Controllers;

use App\Models\Flashcard;
use App\Services\RequestValidator;
use App\Services\SessionManager;
use App\Services\JWTManager;
use App\Middleware\ValidationMiddleware;
use App\Exceptions\ApiException;

class FlashcardController
{
    private Flashcard $flashcardModel;
    private RequestValidator $validator;
    private SessionManager $sessionManager;
    private JWTManager $jwtManager;
    private ValidationMiddleware $validationMiddleware;

    public function __construct(
        Flashcard        $flashcardModel,
        RequestValidator $validator,
        SessionManager   $sessionManager,
        JWTManager       $jwtManager,
        ValidationMiddleware $validationMiddleware
    )
    {
        $this->flashcardModel = $flashcardModel;
        $this->validator = $validator;
        $this->sessionManager = $sessionManager;
        $this->jwtManager = $jwtManager;
        $this->validationMiddleware = $validationMiddleware;
    }

    /**
     * ایجاد یک فلشکارد جدید
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function createFlashcard($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'categoryId'    => ['categoryId'],
            'baseLang'      => ['language'],
            'translateLang' => ['language'],
            'frontWord'     => ['word'],
            'backWord'      => ['word'],
            'level'         => ['level']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // آماده‌سازی داده‌ها
        $data = [
            'userId'         => $userId,
            'categoryId'     => $request['categoryId'],
            'baseLang'       => strtolower($request['baseLang']),
            'translateLang'  => strtolower($request['translateLang']),
            'frontWord'      => $request['frontWord'],
            'backWord'       => $request['backWord'],
            'level'          => strtolower($request['level']),
            'createdAt'      => date('Y-m-d H:i:s'),
            'updatedAt'      => date('Y-m-d H:i:s'),
            'status'         => 'active'
        ];

        // ایجاد فلشکارد
        $flashcardId = $this->flashcardModel->createFlashcard($data);
        if ($flashcardId) {
            return ['status' => 201, 'message' => 'Flashcard created successfully', 'flashcardId' => $flashcardId];
        }

        throw new ApiException('Failed to create flashcard', 500);
    }

    /**
     * دریافت تمام فلشکاردهای کاربر
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function getFlashcards($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // دریافت فلشکاردها
        $flashcards = $this->flashcardModel->findByUserId($userId);
        if ($flashcards !== false) {
            return ['status' => 200, 'data' => $flashcards];
        }

        throw new ApiException('Failed to retrieve flashcards', 500);
    }

    /**
     * دریافت یک فلشکارد بر اساس ID
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function getFlashcardById($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id' => ['categoryId'] // می‌توانید یک ولیدیتور مخصوص برای ID تعریف کنید
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت فلشکارد
        $flashcardId = (int)$request['id'];
        $flashcard = $this->flashcardModel->findById($flashcardId);

        if (!$flashcard || $flashcard['userId'] != $userId) {
            throw new ApiException('Flashcard not found', 404);
        }

        return ['status' => 200, 'data' => $flashcard];
    }

    /**
     * به‌روزرسانی یک فلشکارد موجود
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function updateFlashcard($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id'            => ['categoryId'],
            'categoryId'    => ['categoryId'],
            'baseLang'      => ['language'],
            'translateLang' => ['language'],
            'frontWord'     => ['word'],
            'backWord'      => ['word'],
            'level'         => ['level'],
            'status'        => ['status']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت فلشکارد
        $flashcardId = (int)$request['id'];
        $flashcard = $this->flashcardModel->findById($flashcardId);

        if (!$flashcard || $flashcard['userId'] != $userId) {
            throw new ApiException('Flashcard not found', 404);
        }

        // آماده‌سازی داده‌ها
        $data = [
            'categoryId'     => $request['categoryId'],
            'baseLang'       => strtolower($request['baseLang']),
            'translateLang'  => strtolower($request['translateLang']),
            'frontWord'      => $request['frontWord'],
            'backWord'       => $request['backWord'],
            'level'          => strtolower($request['level']),
            'updatedAt'      => date('Y-m-d H:i:s'),
            'status'         => $request['status'] ?? 'active'
        ];

        // به‌روزرسانی فلشکارد
        $updateResult = $this->flashcardModel->updateFlashcard($flashcardId, $data);
        if ($updateResult) {
            return ['status' => 200, 'message' => 'Flashcard updated successfully'];
        }

        throw new ApiException('Failed to update flashcard', 500);
    }

    /**
     * حذف یک فلشکارد
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function deleteFlashcard($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id' => ['categoryId'] // می‌توانید یک ولیدیتور مخصوص برای ID تعریف کنید
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت فلشکارد
        $flashcardId = (int)$request['id'];
        $flashcard = $this->flashcardModel->findById($flashcardId);

        if (!$flashcard || $flashcard['userId'] != $userId) {
            throw new ApiException('Flashcard not found', 404);
        }

        // حذف فلشکارد
        $deleteResult = $this->flashcardModel->deleteFlashcard($flashcardId);
        if ($deleteResult) {
            return ['status' => 200, 'message' => 'Flashcard deleted successfully'];
        }

        throw new ApiException('Failed to delete flashcard', 500);
    }

    /**
     * دریافت تعداد فلشکاردهای کاربر
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    public function getFlashcardCount($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // دریافت تعداد فلشکاردها
        $count = $this->flashcardModel->countFlashcardsByUser($userId);
        if ($count !== false) {
            return ['status' => 200, 'count' => $count];
        }

        throw new ApiException('Failed to retrieve flashcard count', 500);
    }

    /**
     * احراز هویت کاربر با استفاده از JWT
     *
     * @param array $request
     * @return array
     * @throws ApiException
     */
    private function authenticate($request): array
    {
        // دریافت توکن از هدرها
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new ApiException('Authorization token not provided', 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedToken = $this->jwtManager->verifyToken($token);

        if (!$decodedToken) {
            throw new ApiException('Invalid or expired token', 401);
        }

        return ['status' => 200, 'userId' => $decodedToken['userId']];
    }
}

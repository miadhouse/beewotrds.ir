<?php

namespace App\Controllers;

use App\Models\Category;
use App\Services\RequestValidator;
use App\Services\SessionManager;
use App\Services\JWTManager;
use App\Middleware\ValidationMiddleware;
use App\Exceptions\ApiException;

class CategoryController
{
    private Category $categoryModel;
    private RequestValidator $validator;
    private SessionManager $sessionManager;
    private JWTManager $jwtManager;
    private ValidationMiddleware $validationMiddleware;

    public function __construct(
        Category         $categoryModel,
        RequestValidator $validator,
        SessionManager   $sessionManager,
        JWTManager       $jwtManager,
        ValidationMiddleware $validationMiddleware
    )
    {
        $this->categoryModel = $categoryModel;
        $this->validator = $validator;
        $this->sessionManager = $sessionManager;
        $this->jwtManager = $jwtManager;
        $this->validationMiddleware = $validationMiddleware;
    }

    /**
     * ایجاد یک دسته‌بندی جدید
     */
    public function createCategory($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'title' => ['title'],
            'description' => ['description'],
            'thumbnail' => ['thumbnail'] // در صورت نیاز
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // آماده‌سازی داده‌ها
        $data = [
            'title' => $request['title'],
            'description' => $request['description'],
            'thumbnail' => $request['thumbnail'] ?? null,
            'userId' => $userId,
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // ایجاد دسته‌بندی
        $categoryId = $this->categoryModel->createCategory($data);
        if ($categoryId) {
            return ['status' => 201, 'message' => 'Category created successfully', 'categoryId' => $categoryId];
        }

        throw new ApiException('Failed to create category', 500);
    }

    /**
     * دریافت تمام دسته‌بندی‌های کاربر
     */
    public function getCategories($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // دریافت دسته‌بندی‌ها
        $categories = $this->categoryModel->getCategoriesByUser($userId);
        return ['status' => 200, 'data' => $categories];
    }

    /**
     * دریافت یک دسته‌بندی بر اساس ID
     */
    public function getCategoryById($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id' => ['categoryId']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت دسته‌بندی
        $categoryId = (int)$request['id'];
        $category = $this->categoryModel->findById($categoryId, $userId);
        if (!$category) {
            throw new ApiException('Category not found', 404);
        }

        return ['status' => 200, 'data' => $category];
    }

    /**
     * به‌روزرسانی دسته‌بندی
     */
    public function updateCategory($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id' => ['categoryId'],
            'title' => ['title'],
            'description' => ['description'],
            'thumbnail' => ['thumbnail'] // در صورت نیاز
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت ID
        $categoryId = (int)$request['id'];

        // دریافت دسته‌بندی
        $category = $this->categoryModel->findById($categoryId, $userId);
        if (!$category) {
            throw new ApiException('Category not found', 404);
        }

        // آماده‌سازی داده‌ها
        $data = [
            'title' => $request['title'],
            'description' => $request['description'],
            'thumbnail' => $request['thumbnail'] ?? null,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // به‌روزرسانی دسته‌بندی
        $updateResult = $this->categoryModel->updateCategory($categoryId, $userId, $data);
        if ($updateResult) {
            return ['status' => 200, 'message' => 'Category updated successfully'];
        }

        throw new ApiException('Failed to update category', 500);
    }

    /**
     * حذف دسته‌بندی
     */
    public function deleteCategory($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // قوانین اعتبارسنجی
        $rules = [
            'id' => ['categoryId']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت ID
        $categoryId = (int)$request['id'];

        // دریافت دسته‌بندی
        $category = $this->categoryModel->findById($categoryId, $userId);
        if (!$category) {
            throw new ApiException('Category not found', 404);
        }

        // حذف دسته‌بندی
        $deleteResult = $this->categoryModel->deleteCategory($categoryId, $userId);
        if ($deleteResult) {
            return ['status' => 200, 'message' => 'Category deleted successfully'];
        }

        throw new ApiException('Failed to delete category', 500);
    }

    /**
     * دریافت تعداد دسته‌بندی‌های کاربر
     */
    public function getCategoryCount($request): array
    {
        // احراز هویت کاربر
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // دریافت تعداد دسته‌بندی‌ها
        $count = $this->categoryModel->countCategoriesByUser($userId);
        if ($count !== false) {
            return ['status' => 200, 'count' => $count];
        }

        throw new ApiException('Failed to retrieve category count', 500);
    }

    /**
     * احراز هویت کاربر با استفاده از JWT
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

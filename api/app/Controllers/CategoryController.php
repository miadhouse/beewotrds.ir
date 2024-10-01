<?php

namespace App\Controllers;

use App\Models\Category;
use App\Services\RequestValidator;
use App\Services\SessionManager;
use App\Services\JWTManager;

class CategoryController
{
    private Category $categoryModel;
    private RequestValidator $validator;
    private SessionManager $sessionManager;
    private JWTManager $jwtManager;

    public function __construct(
        Category         $categoryModel,
        RequestValidator $validator,
        SessionManager   $sessionManager,
        JWTManager       $jwtManager
    )
    {
        $this->categoryModel = $categoryModel;
        $this->validator = $validator;
        $this->sessionManager = $sessionManager;
        $this->jwtManager = $jwtManager;
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

        // اعتبارسنجی ورودی‌ها
        $this->validator->validateTitle($request['title'] ?? '');
        $this->validator->validateDescription($request['description'] ?? '');
        // اعتبارسنجی thumbnail در صورت نیاز

        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

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

        return ['status' => 500, 'message' => 'Failed to create category'];
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

        // اعتبارسنجی ID
        $categoryId = $request['id'] ?? null;
        if (!$categoryId || !is_numeric($categoryId)) {
            return ['status' => 400, 'message' => 'Valid category ID is required'];
        }

        // دریافت دسته‌بندی
        $category = $this->categoryModel->findById((int)$categoryId, $userId);
        if (!$category) {
            return ['status' => 404, 'message' => 'Category not found'];
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

        // اعتبارسنجی ID
        $categoryId = $request['id'] ?? null;
        if (!$categoryId || !is_numeric($categoryId)) {
            return ['status' => 400, 'message' => 'Valid category ID is required'];
        }

        // دریافت دسته‌بندی
        $category = $this->categoryModel->findById((int)$categoryId, $userId);
        if (!$category) {
            return ['status' => 404, 'message' => 'Category not found'];
        }

        // اعتبارسنجی ورودی‌ها
        $this->validator->validateTitle($request['title'] ?? '');
        $this->validator->validateDescription($request['description'] ?? '');
        // اعتبارسنجی thumbnail در صورت نیاز

        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // آماده‌سازی داده‌ها
        $data = [
            'title' => $request['title'],
            'description' => $request['description'],
            'thumbnail' => $request['thumbnail'] ?? null,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // به‌روزرسانی دسته‌بندی
        $updateResult = $this->categoryModel->updateCategory((int)$categoryId, $userId, $data);
        if ($updateResult) {
            return ['status' => 200, 'message' => 'Category updated successfully'];
        }

        return ['status' => 500, 'message' => 'Failed to update category'];
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

        // اعتبارسنجی ID
        $categoryId = $request['id'] ?? null;
        if (!$categoryId || !is_numeric($categoryId)) {
            return ['status' => 400, 'message' => 'Valid category ID is required'];
        }

        // دریافت دسته‌بندی
        $category = $this->categoryModel->findById((int)$categoryId, $userId);
        if (!$category) {
            return ['status' => 404, 'message' => 'Category not found'];
        }

        // حذف دسته‌بندی
        $deleteResult = $this->categoryModel->deleteCategory((int)$categoryId, $userId);
        if ($deleteResult) {
            return ['status' => 200, 'message' => 'Category deleted successfully'];
        }

        return ['status' => 500, 'message' => 'Failed to delete category'];
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

        return ['status' => 500, 'message' => 'Failed to retrieve category count'];
    }

    /**
     * احراز هویت کاربر با استفاده از JWT
     */
    private function authenticate($request): array
    {
        // دریافت توکن از هدرها
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return ['status' => 401, 'message' => 'Authorization token not provided'];
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedToken = $this->jwtManager->verifyToken($token);

        if (!$decodedToken) {
            return ['status' => 401, 'message' => 'Invalid or expired token'];
        }

        return ['status' => 200, 'userId' => $decodedToken['userId']];
    }
}

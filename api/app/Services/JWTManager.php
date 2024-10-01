<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Helpers\LogHelper;

class JWTManager
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = 'your_secret_key';  // از اینجا می‌توانید کلید سری خود را تنظیم کنید
    }

    // متد برای تولید توکن JWT
    public function createToken($userId, $role): string
    {
        $payload = [
            'iss' => "beewords.ir",  // صادرکننده توکن
            'aud' => "",  // مخاطب توکن
            'iat' => time(),             // زمان صدور
            'exp' => time() + (60 * 60 * 24 * 7), // زمان انقضا (اینجا برای 1 هفته تنظیم شده)
            'userId' => $userId,         // اطلاعات کاربر
            'role' => $role
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    // متد برای بررسی و اعتبارسنجی توکن
    public function verifyToken($token): bool|array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Firebase\JWT\ExpiredException $e) {
            LogHelper::logError("Token expired: " . $e->getMessage());
            return false;  // توکن منقضی شده
        } catch (\Exception $e) {
            LogHelper::logError("Invalid token: " . $e->getMessage());
            return false;  // خطای عمومی
        }
    }

    // متد برای استخراج اطلاعات از توکن
    public function getPayload($token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            LogHelper::logError("Error extracting payload from token: " . $e->getMessage());
            return null;  // اگر توکن نامعتبر باشد یا منقضی شده باشد
        }
    }
}

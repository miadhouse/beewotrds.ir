<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    public static function verifyToken($token) {
        try {
            $key = 'your_secret_key';
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function checkRole($token, $requiredRole) {
        $user = self::verifyToken($token);
        if ($user && $user->role === $requiredRole) {
            return true;
        }
        return false;
    }
}

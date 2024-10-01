<?php

namespace App\Services;

class SessionManager
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();  // شروع سشن اگر هنوز شروع نشده است
        }
    }

    // ذخیره داده در سشن
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    // دریافت داده از سشن
    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    // حذف داده از سشن
    public function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // حذف تمام سشن‌ها
    public function destroy()
    {
        $_SESSION = [];  // پاک کردن تمام داده‌های سشن
        session_destroy();
    }
}

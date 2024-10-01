<?php

namespace App\Helpers;

class TokenGenerator
{
    public function generateToken($length = 32)
    {
        // تولید یک رشته تصادفی با استفاده از random_bytes و تبدیل به فرمت hexadecimal
        return bin2hex(random_bytes($length / 2));
    }
}

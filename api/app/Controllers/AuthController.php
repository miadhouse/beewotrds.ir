<?php
// src/Controllers/AuthController.php

namespace App\Controllers;

use App\Exceptions\ApiException;
use App\Models\User;
use App\Services\JWTManager;
use App\Services\SessionManager;
use App\Services\RequestValidator;
use App\Helpers\EmailService;
use App\Helpers\TokenGenerator;
use App\Services\ReCaptchaService;
use App\Middleware\ValidationMiddleware;

class AuthController
{
    private User $userModel;
    private JWTManager $jwtManager;
    private SessionManager $sessionManager;
    private RequestValidator $validator;
    private EmailService $emailService;
    private TokenGenerator $tokenGenerator;
    private ReCaptchaService $reCaptcha;
    private ValidationMiddleware $validationMiddleware;

    public function __construct(
        User             $userModel,
        JWTManager       $jwtManager,
        SessionManager   $sessionManager,
        RequestValidator $validator,
        EmailService     $emailService,
        TokenGenerator   $tokenGenerator,
        ReCaptchaService $reCaptcha
    )
    {
        $this->userModel = $userModel;
        $this->jwtManager = $jwtManager;
        $this->sessionManager = $sessionManager;
        $this->validator = $validator;
        $this->emailService = $emailService;
        $this->tokenGenerator = $tokenGenerator;
        $this->reCaptcha = $reCaptcha;
        $this->validationMiddleware = new ValidationMiddleware($this->validator);
    }

    /**
     * @throws ApiException
     */
    public function register($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'password'],
            'userName' => ['required', 'username'],
            'mobile' => ['required', 'mobile'],
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // بررسی وجود توکن reCAPTCHA
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            throw new ApiException('reCAPTCHA verification failed', 400);
        }

        // چک کردن ایمیل تکراری
        if ($this->userModel->findByEmail($request['email'])) {
            throw new ApiException('Email already exists', 409);
        }

        // چک کردن موبایل تکراری
        if ($this->userModel->findByMobile($request['mobile'])) {
            throw new ApiException('Mobile number already exists', 409);
        }

        // رمزنگاری پسورد
        $hashedPassword = password_hash($request['password'], PASSWORD_BCRYPT);
        $verificationCodeExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // تولید کد تأیید
        $verificationCode = $this->tokenGenerator->generateToken();

        // ایجاد کاربر جدید
        $userId = $this->userModel->createUser([
            'userName' => $request['userName'],
            'role' => 'user',
            'email' => $request['email'],
            'mobile' => $request['mobile'],
            'language' => 'en', // Set default language
            'age' => $request['age'] ?? null,
            'imageProfile' => $request['imageProfile'] ?? null,
            'password' => $hashedPassword,
            'verificationCode' => $verificationCode,
            'verificationCodeExpiresAt' => $verificationCodeExpiresAt,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ]);

        // ایجاد لینک تأیید ایمیل
        $verificationLink = "https://beewords.ir/verify?code=$verificationCode";
        $emailSubject = 'Verify your email';
        $emailBody = "
            <p>Hi {$request['userName']},</p>
            <p>To verify your account, please click the link below:</p>
            <p><a href='$verificationLink'>$verificationLink</a></p>
            <p>Thanks,<br>Support team</p>
        ";

        // ارسال ایمیل تأیید
        $this->emailService->sendEmail($request['email'], $emailSubject, $emailBody);

        return ['status' => 201, 'message' => 'Registration successful. Please check your email to verify your account.'];
    }

    /**
     * @throws ApiException
     */
    public function verifyEmail($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'code' => ['required']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت کد تأیید
        $verificationCode = $request['code'];

        // پیدا کردن کاربر با کد تأیید و بررسی انقضا
        $user = $this->userModel->findByVerificationCode($verificationCode);

        if (!$user) {
            throw new ApiException('Invalid or expired verification code', 400);
        }

        // بررسی انقضای کد تأیید
        if (new \DateTime() > new \DateTime($user['verificationCodeExpiresAt'])) {
            throw new ApiException('Verification code has expired', 400);
        }

        // به‌روزرسانی وضعیت کاربر به "active"
        $this->userModel->updateUserStatus($user['id'], 'active');

        // حذف کد تأیید و زمان انقضا
        $this->userModel->clearVerificationCode($user['id']);

        return ['status' => 200, 'message' => 'Email verified successfully. You can now log in.'];
    }

    public function resendVerificationEmail($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'email' => ['required', 'email']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت ایمیل کاربر
        $email = $request['email'];

        // پیدا کردن کاربر با ایمیل
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            throw new ApiException('User not found', 404);
        }

        if ($user['status'] === 'active') {
            throw new ApiException('Account is already verified', 400);
        }

        // تولید کد تأیید جدید
        $verificationCode = $this->tokenGenerator->generateToken();
        $verificationCodeExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // به‌روزرسانی کد تأیید و زمان انقضا
        $this->userModel->updateVerificationCode($user['id'], $verificationCode, $verificationCodeExpiresAt);

        // ایجاد لینک تأیید ایمیل جدید
        $verificationLink = "http://localhost:3000/verify?code=$verificationCode";
        $emailSubject = 'Re-verify your email';
        $emailBody = "
            <p>Hi {$user['userName']},</p>
            <p>To verify your account, please click the link below:</p>
            <p><a href='$verificationLink'>$verificationLink</a></p>
            <p>Thanks,<br>Support team</p>
        ";

        // ارسال ایمیل تأیید
        $this->emailService->sendEmail($email, $emailSubject, $emailBody);

        return ['status' => 200, 'message' => 'A new verification email has been sent'];
    }

    public function login($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'password']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت کاربر بر اساس ایمیل
        $user = $this->userModel->findByEmail($request['email']);

        if (!$user) {
            // برای امنیت بیشتر، پیام مشابهی به کاربر نمایش داده می‌شود
            throw new ApiException('Invalid email or password', 401);
        }

        // بررسی قفل بودن حساب کاربر
        if ($user['lockedUntil'] && new \DateTime() < new \DateTime($user['lockedUntil'])) {
            throw new ApiException(
                'Your account is locked due to multiple failed login attempts. Please try again later.',
                403
            );
        }

        // بررسی صحت پسورد
        if (!password_verify($request['password'], $user['password'])) {
            // افزایش تعداد تلاش‌های ناموفق
            $failedAttempts = $user['failedLoginAttempts'] + 1;
            $currentTime = date('Y-m-d H:i:s');

            $this->userModel->updateFailedLogin($user['id'], $failedAttempts, $currentTime);

            // تنظیم حداکثر تلاش‌ها و بازه زمانی قفل
            $maxFailedAttempts = 5;
            $lockDuration = '+15 minutes';

            if ($failedAttempts >= $maxFailedAttempts) {
                $lockedUntil = date('Y-m-d H:i:s', strtotime($lockDuration));
                $this->userModel->lockUser($user['id'], $lockedUntil);
                throw new ApiException(
                    'Your account has been locked due to multiple failed login attempts. Please try again later.',
                    403
                );
            }

            throw new ApiException('Invalid email or password', 401);
        }

        // رمز عبور صحیح است، بازنشانی تعداد تلاش‌های ناموفق
        $this->userModel->resetFailedLogin($user['id']);

        // بررسی وضعیت حساب کاربر
        if ($user['status'] !== 'active') {
            throw new ApiException('Account is not verified. Please verify your email before logging in.', 403);
        }

        if ($user['status'] === 'suspended') {
            throw new ApiException('Your account has been suspended. Please contact support.', 403);
        }

        // تولید توکن JWT
        $token = $this->jwtManager->createToken($user['id'], $user['role']);

        // ذخیره اطلاعات کاربر در سشن
        $this->sessionManager->set('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'token' => $token
        ]);

        return ['status' => 200, 'message' => 'Login successful', 'token' => $token];
    }

    public function logout(): array
    {
        // حذف تمام سشن‌ها
        $this->sessionManager->destroy();
        return ['status' => 200, 'message' => 'Logged out successfully'];
    }

    public function getUser(): array
    {
        // دریافت توکن از هدر Authorization
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            throw new ApiException('Authorization token not provided', 401);
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedToken = $this->jwtManager->verifyToken($token);

        if (!$decodedToken) {
            throw new ApiException('Invalid or expired token', 401);
        }

        // بازیابی اطلاعات کاربر با استفاده از id موجود در توکن
        $user = $this->userModel->findById($decodedToken['userId']);
        if ($user) {
            return ['status' => 200, 'data' => $user];
        } else {
            throw new ApiException('User not found', 404);
        }
    }

    public function recoverPassword($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'email' => ['required', 'email']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت ایمیل کاربر
        $email = $request['email'];

        // پیدا کردن کاربر با ایمیل
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            // برای امنیت بیشتر، پیام مشابهی به کاربر نمایش داده می‌شود
            return ['status' => 200, 'message' => 'If the email exists, a password reset link has been sent.'];
        }

        // بررسی تعداد درخواست‌های بازیابی رمز عبور
        $currentTime = new \DateTime();
        $lastRequestTime = $user['passwordResetRequestLastAt'] ? new \DateTime($user['passwordResetRequestLastAt']) : null;
        $requestCount = $user['passwordResetRequestCount'];

        // تنظیم حداکثر تعداد درخواست‌ها و بازه زمانی (مثلاً 5 درخواست در هر 24 ساعت)
        $maxRequests = 5;
        $timeWindow = new \DateInterval('P1D'); // 1 روز

        if ($lastRequestTime) {
            $interval = $lastRequestTime->diff($currentTime);
            if ($interval < $timeWindow) {
                if ($requestCount >= $maxRequests) {
                    throw new ApiException(
                        'You have exceeded the maximum number of password reset requests. Please try again later.',
                        429
                    );
                } else {
                    // افزایش تعداد درخواست‌ها
                    $requestCount += 1;
                }
            } else {
                // بازنشانی شمارنده درخواست‌ها
                $requestCount = 1;
            }
        } else {
            // اولین درخواست
            $requestCount = 1;
        }

        // به‌روزرسانی شمارنده و زمان آخرین درخواست
        $this->userModel->updatePasswordResetRequest($user['id'], $requestCount, $currentTime->format('Y-m-d H:i:s'));

        // تولید توکن بازیابی رمز عبور
        $resetToken = $this->tokenGenerator->generateToken();
        $resetTokenExpiresAt = date('Y-m-d H:i:s', strtotime('+1 hour')); // اعتبار توکن یک ساعت

        // به‌روزرسانی توکن در پایگاه داده
        $this->userModel->updatePasswordResetToken($user['id'], $resetToken, $resetTokenExpiresAt);

        // ایجاد لینک بازیابی
        $resetLink = "http://localhost:3000/reset-password?token=$resetToken";

        // ارسال ایمیل به کاربر
        $emailSubject = 'Password Reset Request';
        $emailBody = "
            <p>Hi {$user['userName']},</p>
            <p>You requested to reset your password. Please click the link below to reset your password:</p>
            <p><a href='$resetLink'>$resetLink</a></p>
            <p>This link will expire in 1 hour.</p>
            <p>Thanks,<br>Support team</p>
        ";

        $this->emailService->sendEmail($user['email'], $emailSubject, $emailBody);

        return ['status' => 200, 'message' => 'If the email exists, a password reset link has been sent.'];
    }

    public function resetPassword($request): array
    {
        // قوانین اعتبارسنجی
        $rules = [
            'token' => ['required'],
            'password' => ['required', 'password']
        ];

        // انجام اعتبارسنجی با Middleware
        $this->validationMiddleware->handle($request, $rules);

        // دریافت توکن و رمز عبور جدید
        $resetToken = $request['token'];
        $newPassword = $request['password'];

        // پیدا کردن کاربر با توکن بازیابی و بررسی انقضا
        $user = $this->userModel->findByPasswordResetToken($resetToken);
        if (!$user) {
            throw new ApiException('Invalid or expired reset token', 400);
        }

        // بررسی انقضا
        $currentDateTime = date('Y-m-d H:i:s');
        if ($currentDateTime > $user['passwordResetExpiresAt']) {
            throw new ApiException('Reset token has expired', 400);
        }

        // رمزنگاری رمز عبور جدید
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // به‌روزرسانی رمز عبور در پایگاه داده و حذف توکن
        $this->userModel->updatePassword($user['id'], $hashedPassword);
        $this->userModel->clearPasswordResetToken($user['id']);

        return ['status' => 200, 'message' => 'Password has been reset successfully. You can now log in with your new password.'];
    }
}

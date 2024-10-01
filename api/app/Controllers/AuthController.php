<?php
// AuthController.php

namespace App\Controllers;

use App\Models\User;
use App\Services\JWTManager;
use App\Services\SessionManager;
use App\Services\RequestValidator;
use App\Helpers\EmailService;
use App\Helpers\TokenGenerator;
use App\Services\ReCaptchaService;

class AuthController
{
    private User $userModel;
    private JWTManager $jwtManager;
    private SessionManager $sessionManager;
    private RequestValidator $validator;
    private EmailService $emailService;
    private TokenGenerator $tokenGenerator;
    private ReCaptchaService $reCaptcha;

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
    }

    public function register($request): array
    {
        // بررسی وجود توکن reCAPTCHA
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        // اعتبارسنجی ورودی‌ها
        $this->validator->validateEmail($request['email']);
        $this->validator->validatePassword($request['password']);
        $this->validator->validateUsername($request['userName']);
        $this->validator->validateMobile($request['mobile']);
        $language = $request['language'] ?? 'en'; // مقدار پیش‌فرض زبان انگلیسی
        $age = $request['age'] ?? null; // مقدار پیش‌فرض null
        $imageProfile = $request['imageProfile'] ?? null; // مقدار پیش‌فرض null
        // بررسی وجود خطاها
        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // چک کردن ایمیل تکراری
        $existingUserByEmail = $this->userModel->findByEmail($request['email']);
        if ($existingUserByEmail) {
            return ['status' => 409, 'message' => 'Email already exists'];
        }

        // چک کردن موبایل تکراری
        $existingUserByMobile = $this->userModel->findByMobile($request['mobile']);
        if ($existingUserByMobile) {
            return ['status' => 409, 'message' => 'Mobile number already exists'];
        }

        // رمزنگاری پسورد
        $hashedPassword = password_hash($request['password'], PASSWORD_BCRYPT);
        $verificationCodeExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // تولید کد تأیید
        $verificationCode = $this->tokenGenerator->generateToken();

        $userId = $this->userModel->createUser([
            'userName' => $request['userName'],
            'role' => 'user',
            'email' => $request['email'],
            'mobile' => $request['mobile'],
            'language' => $language,  // استفاده از مقدار پیش‌فرض در صورت عدم وجود
            'age' => $age,  // استفاده از مقدار پیش‌فرض در صورت عدم وجود
            'imageProfile' => $imageProfile,  // استفاده از مقدار پیش‌فرض در صورت عدم وجود
            'password' => $hashedPassword,
            'verificationCode' => $verificationCode,
            'verificationCodeExpiresAt' => $verificationCodeExpiresAt,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'pending' // وضعیت حساب در حالت "منتظر تأیید"
        ]);

        $verificationLink = "http://localhost:3000/verify?code=$verificationCode";
        $emailSubject = 'verify email';
        $emailBody = "
        <p>Hi {$request['email']},</p>
        <p>To verify your account, please click the link below:</p>
        <p><a href='$verificationLink'>$verificationLink</a></p>
        <p>Thanks,<br>Support team</p>
    ";

        $this->emailService->sendEmail($request['email'], $emailSubject, $emailBody);

        return ['status' => 201, 'message' => 'Registration successful. Please check your email to verify your account.'];
    }

    public function verifyEmail($request): array
    {
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        // دریافت کد تأیید از درخواست
        $verificationCode = $request['code'] ?? null;

        if (!$verificationCode) {
            return ['status' => 400, 'message' => 'Verification code is required'];
        }

        // پیدا کردن کاربر با کد تأیید و بررسی انقضا
        $user = $this->userModel->findByVerificationCode($verificationCode);

        if (!$user) {
            return ['status' => 400, 'message' => 'Invalid or expired verification code'];
        }

        // به‌روزرسانی وضعیت کاربر به "active"
        $this->userModel->updateUserStatus($user['id'], 'active');

        // حذف کد تأیید و زمان انقضا
        $this->userModel->clearVerificationCode($user['id']);

        return ['status' => 200, 'message' => 'Email verified successfully. You can now log in.'];
    }

    public function resendVerificationEmail($request): array
    {
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        // دریافت ایمیل کاربر
        $email = $request['email'] ?? null;

        if (!$email) {
            return ['status' => 400, 'message' => 'Email is required'];
        }

        // پیدا کردن کاربر با ایمیل
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['status' => 404, 'message' => 'User not found'];
        }

        if ($user['status'] === 'active') {
            return ['status' => 400, 'message' => 'Account is already verified'];
        }

        // تولید کد تأیید جدید
        $verificationCode = $this->tokenGenerator->generateToken();
        $verificationCodeExpiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // به‌روزرسانی کد تأیید و زمان انقضا
        $this->userModel->updateVerificationCode($user['id'], $verificationCode, $verificationCodeExpiresAt);

        // ارسال ایمیل تأیید
        $verificationLink = "http://localhost:3000/verify?code=$verificationCode";
        $emailSubject = 'Re-verify email';
        $emailBody = "
        <p>Hi {$user['userName']},</p>
        <p>To verify your account, please click the link below:</p>
        <p><a href='$verificationLink'>$verificationLink</a></p>
        <p>Thanks,<br>Support team</p>
    ";

        $this->emailService->sendEmail($email, $emailSubject, $emailBody);

        return ['status' => 200, 'message' => 'A new verification email has been sent'];
    }

    public function login($request): array
    {
        error_log("Login method called.");

        $recaptchaToken = $request['recaptchaToken'] ?? '';
        error_log("Received recaptchaToken: " . substr($recaptchaToken, 0, 10) . "...");

        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            error_log("reCAPTCHA verification failed.");
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        error_log("reCAPTCHA verification passed.");
        // اعتبارسنجی ایمیل و پسورد
        $this->validator->validateEmail($request['email']);
        $this->validator->validatePassword($request['password']);

        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        $user = $this->userModel->findByEmail($request['email']);

        if (!$user) {
            // برای امنیت بیشتر، پیام مشابهی به کاربر نمایش می‌دهیم
            return ['status' => 401, 'message' => 'Invalid email or password'];
        }

        // بررسی قفل بودن حساب کاربر
        if ($user['lockedUntil'] && new \DateTime() < new \DateTime($user['lockedUntil'])) {
            return [
                'status' => 403,
                'message' => 'Your account is locked due to multiple failed login attempts. Please try again later.'
            ];
        }

        if (!password_verify($request['password'], $user['password'])) {
            // رمز عبور نامعتبر است، افزایش تعداد تلاش‌های ناموفق
            $failedAttempts = $user['failedLoginAttempts'] + 1;
            $currentTime = date('Y-m-d H:i:s');

            $this->userModel->updateFailedLogin($user['id'], $failedAttempts, $currentTime);

            // تنظیم حداکثر تلاش‌ها و بازه زمانی قفل
            $maxFailedAttempts = 5;
            $lockDuration = '+15 minutes';

            if ($failedAttempts >= $maxFailedAttempts) {
                $lockedUntil = date('Y-m-d H:i:s', strtotime($lockDuration));
                $this->userModel->lockUser($user['id'], $lockedUntil);
                return [
                    'status' => 403,
                    'message' => 'Your account has been locked due to multiple failed login attempts. Please try again later.'
                ];
            }

            return ['status' => 401, 'message' => 'Invalid email or password'];
        }

        // رمز عبور صحیح است، بازنشانی تعداد تلاش‌های ناموفق
        $this->userModel->resetFailedLogin($user['id']);

        // بررسی وضعیت حساب کاربر
        if ($user['status'] !== 'active') {
            return ['status' => 403, 'message' => 'Account is not verified. Please verify your email before logging in.'];
        }
        if ($user['status'] === 'pending') {
            return ['status' => 403, 'message' => 'Account is not verified. Please verify your email before logging in.'];
        }
        if ($user['status'] === 'suspended') {
            return ['status' => 403, 'message' => 'Your account has been suspended. Please contact support.'];
        }

        // تولید توکن JWT
        $token = $this->jwtManager->createToken($user['id'], $user['role']);

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
            return ['status' => 401, 'message' => 'Authorization token not provided'];
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedToken = $this->jwtManager->verifyToken($token);

        if (!$decodedToken) {
            return ['status' => 401, 'message' => 'Invalid or expired token'];
        }

        // بازیابی اطلاعات کاربر با استفاده از id موجود در توکن
        $user = $this->userModel->findById($decodedToken['userId']);
        if ($user) {
            return ['status' => 200, 'data' => $user];
        } else {
            return ['status' => 404, 'message' => 'User not found'];
        }
    }
    public function recoverPassword($request): array
    {
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        // اعتبارسنجی ایمیل
        $this->validator->validateEmail($request['email'] ?? '');
        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // پیدا کردن کاربر با ایمیل
        $user = $this->userModel->findByEmail($request['email']);
        if (!$user) {
            // برای امنیت بیشتر، پیام مشابهی به کاربر نمایش می‌دهیم
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
                    return [
                        'status' => 429,
                        'message' => 'You have exceeded the maximum number of password reset requests. Please try again later.'
                    ];
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
    /**
     * تنظیم رمز عبور جدید
     */
    public function resetPassword($request): array
    {
        $recaptchaToken = $request['recaptchaToken'] ?? '';
        if (!$this->reCaptcha->verifyToken($recaptchaToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
            return ['status' => 400, 'message' => 'reCAPTCHA verification failed'];
        }
        // دریافت توکن و رمز عبور جدید از درخواست
        $resetToken = $request['token'] ?? null;
        $newPassword = $request['password'] ?? null;

        if (!$resetToken || !$newPassword) {
            return ['status' => 400, 'message' => 'Token and new password are required'];
        }

        // اعتبارسنجی رمز عبور
        $this->validator->validatePassword($newPassword);
        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // پیدا کردن کاربر با توکن بازیابی و بررسی انقضا
        $user = $this->userModel->findByPasswordResetToken($resetToken);
        if (!$user) {
            return ['status' => 400, 'message' => 'Invalid or expired reset token'];
        }

        // بررسی انقضا
        $currentDateTime = date('Y-m-d H:i:s');
        if ($currentDateTime > $user['passwordResetExpiresAt']) {
            return ['status' => 400, 'message' => 'Reset token has expired'];
        }

        // رمزنگاری رمز عبور جدید
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // به‌روزرسانی رمز عبور در پایگاه داده و حذف توکن
        $this->userModel->updatePassword($user['id'], $hashedPassword);
        $this->userModel->clearPasswordResetToken($user['id']);

        return ['status' => 200, 'message' => 'Password has been reset successfully. You can now log in with your new password.'];
    }
}

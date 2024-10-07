<?php

namespace App\Models;

use App\Models\Database;

class User {
    private $db;

    public function __construct() {
        // فرض بر این است که کلاس Database به درستی پیاده‌سازی شده است
        $this->db = new Database();
    }

    // سایر متدهای موجود...

    /**
     * پیدا کردن کاربر بر اساس ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * پیدا کردن کاربر بر اساس ایمیل
     *
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        $result = $this->db->single();
        return $result ? $result : null;
    }


    /**
     * پیدا کردن کاربر بر اساس شماره موبایل
     *
     * @param string $mobile
     * @return array|null
     */
    public function findByMobile(string $mobile): ?array {
        $sql = "SELECT * FROM users WHERE mobile = :mobile LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':mobile', $mobile);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * پیدا کردن کاربر بر اساس کد تأیید
     *
     * @param string $verificationCode
     * @return array|null
     */
    public function findByVerificationCode(string $verificationCode): ?array {
        $sql = "SELECT * FROM users WHERE verificationCode = :code AND verificationCodeExpiresAt > NOW() LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':code', $verificationCode);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * پیدا کردن کاربر بر اساس توکن بازیابی رمز عبور
     *
     * @param string $resetToken
     * @return array|null
     */
    public function findByPasswordResetToken(string $resetToken): ?array {
        $sql = "SELECT * FROM users WHERE passwordResetToken = :token AND passwordResetExpiresAt > NOW() LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':token', $resetToken);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * ایجاد کاربر جدید
     *
     * @param array $data
     * @return int|false
     */
    public function createUser(array $data) {
        $sql = "INSERT INTO users (userName, role, email, mobile, age, imageProfile, password, verificationCode, verificationCodeExpiresAt, created_at, status) 
                VALUES (:userName, :role, :email, :mobile, :language, :age, :imageProfile, :password, :verificationCode, :verificationCodeExpiresAt, :created_at, :status)";
        $this->db->query($sql);
        $this->db->bind(':userName', $data['userName']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':mobile', $data['mobile']);
        $this->db->bind(':language', $data['language']);
        $this->db->bind(':age', $data['age']);
        $this->db->bind(':imageProfile', $data['imageProfile']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':verificationCode', $data['verificationCode']);
        $this->db->bind(':verificationCodeExpiresAt', $data['verificationCodeExpiresAt']);
        $this->db->bind(':created_at', $data['created_at']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * به‌روزرسانی وضعیت کاربر
     *
     * @param int $userId
     * @param string $status
     * @return bool
     */
    public function updateUserStatus(int $userId, string $status): bool {
        $sql = "UPDATE users SET status = :status WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * پاک کردن کد تأیید و زمان انقضا
     *
     * @param int $userId
     * @return bool
     */
    public function clearVerificationCode(int $userId): bool {
        $sql = "UPDATE users SET verificationCode = NULL, verificationCodeExpiresAt = NULL WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * به‌روزرسانی کد تأیید و زمان انقضا
     *
     * @param int $userId
     * @param string $verificationCode
     * @param string $verificationCodeExpiresAt
     * @return bool
     */
    public function updateVerificationCode(int $userId, string $verificationCode, string $verificationCodeExpiresAt): bool {
        $sql = "UPDATE users SET verificationCode = :code, verificationCodeExpiresAt = :expiresAt WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':code', $verificationCode);
        $this->db->bind(':expiresAt', $verificationCodeExpiresAt);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * به‌روزرسانی شمارنده درخواست‌های بازیابی رمز عبور
     *
     * @param int $userId
     * @param int $count
     * @param string $lastAt
     * @return bool
     */
    public function updatePasswordResetRequest(int $userId, int $count, string $lastAt): bool {
        $sql = "UPDATE users SET passwordResetRequestCount = :count, passwordResetRequestLastAt = :lastAt WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':count', $count);
        $this->db->bind(':lastAt', $lastAt);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * به‌روزرسانی توکن بازیابی رمز عبور
     *
     * @param int $userId
     * @param string $resetToken
     * @param string $resetTokenExpiresAt
     * @return bool
     */
    public function updatePasswordResetToken(int $userId, string $resetToken, string $resetTokenExpiresAt): bool {
        $sql = "UPDATE users SET passwordResetToken = :token, passwordResetExpiresAt = :expiresAt WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':token', $resetToken);
        $this->db->bind(':expiresAt', $resetTokenExpiresAt);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * پاک کردن توکن بازیابی رمز عبور
     *
     * @param int $userId
     * @return bool
     */
    public function clearPasswordResetToken(int $userId): bool {
        $sql = "UPDATE users SET passwordResetToken = NULL, passwordResetExpiresAt = NULL WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * به‌روزرسانی رمز عبور
     *
     * @param int $userId
     * @param string $hashedPassword
     * @return bool
     */
    public function updatePassword(int $userId, string $hashedPassword): bool {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * به‌روزرسانی تعداد تلاش‌های ناموفق ورود
     *
     * @param int $userId
     * @param int $count
     * @param string $lastAttempt
     * @return bool
     */
    public function updateFailedLogin(int $userId, int $count, string $lastAttempt): bool {
        $sql = "UPDATE users SET failedLoginAttempts = :count, lastFailedLoginAttempt = :lastAttempt WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':count', $count);
        $this->db->bind(':lastAttempt', $lastAttempt);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * قفل کردن حساب کاربر تا زمان مشخص
     *
     * @param int $userId
     * @param string $lockedUntil
     * @return bool
     */
    public function lockUser(int $userId, string $lockedUntil): bool {
        $sql = "UPDATE users SET lockedUntil = :lockedUntil WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':lockedUntil', $lockedUntil);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }

    /**
     * بازنشانی تلاش‌های ناموفق ورود
     *
     * @param int $userId
     * @return bool
     */
    public function resetFailedLogin(int $userId): bool {
        $sql = "UPDATE users SET failedLoginAttempts = 0, lastFailedLoginAttempt = NULL, lockedUntil = NULL WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);
        return $this->db->execute();
    }
}

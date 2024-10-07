<?php

namespace App\Services;

class RequestValidator
{
    private array $errors = [];

    // متدهای اعتبارسنجی...

    public function validateEmail(string $email): void
    {
        $email = trim($email);
        if (empty($email)) {
            $this->errors['email'] = 'Email is required.';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format.';
            return;
        }

        // بررسی وجود رکوردهای MX برای دامنه ایمیل
        $domain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            $this->errors['email'] = 'Email domain does not have valid MX records.';
        }
    }

    public function validatePassword(string $password): void
    {
        $password = trim($password);
        if (empty($password)) {
            $this->errors['password'] = 'Password is required.';
            return;
        }

        if (strlen($password) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters long.';
        }
    }

    public function validateMobile(string $mobile): void
    {
        $mobile = trim($mobile);
        if (empty($mobile)) {
            $this->errors['mobile'] = 'Mobile number is required.';
            return;
        }

        if (strlen($mobile) < 11 || !preg_match('/^[0-9]+$/', $mobile)) {
            $this->errors['mobile'] = 'Mobile number must be at least 11 digits long and contain only numbers.';
        }
    }

    public function validateUsername(string $username): void
    {
        $username = trim($username);
        if (empty($username)) {
            $this->errors['username'] = 'Username is required.';
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            $this->errors['username'] = 'Username must contain only letters and numbers.';
        }
    }

    public function validateTitle(string $title): void
    {
        $title = trim($title);
        if (empty($title)) {
            $this->errors['title'] = 'Title is required.';
            return;
        }

        if (strlen($title) < 3 || strlen($title) > 255) {
            $this->errors['title'] = 'Title must be between 3 and 255 characters.';
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
            $this->errors['title'] = 'Title can only contain letters, numbers, and spaces.';
        }
    }

    public function validateDescription(string $description): void
    {
        $description = trim($description);
        if (empty($description)) {
            $this->errors['description'] = 'Description is required.';
            return;
        }

        if (strlen($description) < 5 || strlen($description) > 1000) {
            $this->errors['description'] = 'Description must be between 5 and 1000 characters.';
        }
    }

    public function validateThumbnail(?string $thumbnail): void
    {
        if ($thumbnail && !filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            $this->errors['thumbnail'] = 'Thumbnail must be a valid URL.';
        }
    }

    public function validateCategoryId($categoryId): void
    {
        if (empty($categoryId) || !is_numeric($categoryId) || (int)$categoryId <= 0) {
            $this->errors['categoryId'] = 'Valid category ID is required.';
        }
        // Optionally, you can add a check to ensure the category exists in the database
    }

    public function validateLanguage(?string $language, string $fieldName = 'Language'): void
    {
        $allowedLanguages = ['en', 'es', 'fr', 'de', 'it', 'fa', 'ar', 'zh', 'jp'];

        if ($language !== null && !in_array(strtolower($language), $allowedLanguages)) {
            $this->errors[$fieldName] = "Valid $fieldName is required.";
        }
    }

    public function validateWord(?string $word, string $fieldName = 'Word'): void
    {
        if (empty($word)) {
            $this->errors[$fieldName] = "$fieldName is required.";
            return;
        }

        if (!preg_match('/^[\p{L}\p{N}\s]+$/u', $word)) { // \p{L} for any kind of letter from any language
            $this->errors[$fieldName] = "$fieldName can only contain letters, numbers, and spaces.";
        }

        if (strlen($word) < 1 || strlen($word) > 255) {
            $this->errors[$fieldName] = "$fieldName must be between 1 and 255 characters.";
        }
    }

    public function validateLevel($level): void
    {
        $allowedLevels = ['beginner', 'intermediate', 'advanced'];

        if (empty($level) || !in_array(strtolower($level), $allowedLevels)) {
            $this->errors['level'] = 'Valid level is required (beginner, intermediate, advanced).';
        }
    }

    public function validateStatus($status): void
    {
        $allowedStatuses = ['active', 'inactive', 'pending', 'suspended'];

        if (empty($status) || !in_array(strtolower($status), $allowedStatuses)) {
            $this->errors['status'] = 'Valid status is required (active, inactive, pending, suspended).';
        }
    }

    /**
     * بررسی اینکه آیا خطایی وجود دارد یا خیر
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * دریافت تمام خطاها
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * پاک کردن خطاها
     *
     * @return void
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * متد کلی برای اعتبارسنجی مجموعه‌ای از فیلدها با قوانین متنوع
     *
     * @param array $data
     * @param array $rules
     * @return void
     */
    public function validate(array $data, array $rules): void
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            foreach ($fieldRules as $rule) {
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    switch ($rule) {
                        case 'language':
                            $this->$method($value, ucfirst($field));
                            break;
                        case 'word':
                            $this->$method($value, ucfirst($field));
                            break;
                        case 'status':
                            $this->$method($value);
                            break;
                        default:
                            $this->$method($value);
                            break;
                    }
                }
            }
        }
    }
}

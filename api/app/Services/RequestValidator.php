<?php

namespace App\Services;

class RequestValidator
{
    private array $errors = [];

    // Existing validation methods...

    // متد برای اعتبارسنجی ایمیل
    public function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Invalid email format.';
        }
    }

    // متد برای اعتبارسنجی رمز عبور
    public function validatePassword(string $password): void
    {
        if (strlen($password) < 6) {
            $this->errors['password'] = 'Password must be at least 6 characters long.';
        }
    }

    // متد برای اعتبارسنجی شماره موبایل
    public function validateMobile(string $mobile): void
    {
        if (strlen($mobile) < 11 || !preg_match('/^[0-9]+$/', $mobile)) {
            $this->errors['mobile'] = 'Mobile number must be at least 11 digits long and contain only numbers.';
        }
    }

    // متد برای اعتبارسنجی نام کاربری (فقط حروف و اعداد)
    public function validateUsername(string $username): void
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
            $this->errors['username'] = 'Username must contain only letters and numbers.';
        }
    }

    /**
     * اعتبارسنجی عنوان
     *
     * @param string $title
     * @return void
     */
    public function validateTitle(string $title): void
    {
        // بررسی خالی نبودن عنوان
        if (empty($title)) {
            $this->errors['title'] = 'Title is required.';
        }

        // بررسی طول عنوان (باید حداقل 3 و حداکثر 255 کاراکتر باشد)
        if (strlen($title) < 3 || strlen($title) > 255) {
            $this->errors['title'] = 'Title must be between 3 and 255 characters.';
        }

        // بررسی وجود کاراکترهای غیرمجاز
        if (!preg_match('/^[a-zA-Z0-9\s]+$/', $title)) {
            $this->errors['title'] = 'Title can only contain letters, numbers, and spaces.';
        }
    }



    /**
     * Validate description
     *
     * @param string $description
     * @return void
     */
    public function validateDescription(string $description): void
    {
        if (empty($description)) {
            $this->errors['description'] = 'Description is required.';
            return;
        }

        if (strlen($description) < 5 || strlen($description) > 1000) {
            $this->errors['description'] = 'Description must be between 5 and 1000 characters.';
        }
    }

    // Thumbnail validation can be added if needed
    public function validateThumbnail(?string $thumbnail): void
    {
        if ($thumbnail && !filter_var($thumbnail, FILTER_VALIDATE_URL)) {
            $this->errors['thumbnail'] = 'Thumbnail must be a valid URL.';
        }
    }
    /**
     * اعتبارسنجی شناسه دسته‌بندی
     *
     * @param mixed $categoryId
     * @return void
     */
    public function validateCategoryId($categoryId): void
    {
        if (empty($categoryId) || !is_numeric($categoryId) || (int)$categoryId <= 0) {
            $this->errors['categoryId'] = 'Valid category ID is required.';
        }
        // Optionally, you can add a check to ensure the category exists in the database
    }

    /**
     * اعتبارسنجی زبان پایه و ترجمه
     *
     * @param string|null $language
     * @param string $fieldName
     * @return void
     */
    public function validateLanguage(?string $language, string $fieldName = 'Language'): void
    {
        $allowedLanguages = ['en', 'es', 'fr', 'de', 'it', 'fa', 'ar', 'zh', 'jp']; // Extend as needed

        if (empty($language) || !in_array(strtolower($language), $allowedLanguages)) {
            $this->errors[$fieldName] = "Valid $fieldName is required.";
        }
    }

    /**
     * اعتبارسنجی کلمه (Front Word و Back Word)
     *
     * @param string|null $word
     * @param string $fieldName
     * @return void
     */
    public function validateWord(?string $word, string $fieldName = 'Word'): void
    {
        if (empty($word)) {
            $this->errors[$fieldName] = "$fieldName is required.";
            return;
        }

        // بررسی اینکه کلمه فقط حروف، اعداد و فضای خالی باشد
        if (!preg_match('/^[\p{L}\p{N}\s]+$/u', $word)) { // \p{L} for any kind of letter from any language
            $this->errors[$fieldName] = "$fieldName can only contain letters, numbers, and spaces.";
        }

        // بررسی طول کلمه (باید حداقل 1 و حداکثر 255 کاراکتر باشد)
        if (strlen($word) < 1 || strlen($word) > 255) {
            $this->errors[$fieldName] = "$fieldName must be between 1 and 255 characters.";
        }
    }

    /**
     * اعتبارسنجی سطح (Level)
     *
     * @param mixed $level
     * @return void
     */
    public function validateLevel($level): void
    {
        $allowedLevels = ['beginner', 'intermediate', 'advanced'];

        if (empty($level) || !in_array(strtolower($level), $allowedLevels)) {
            $this->errors['level'] = 'Valid level is required (beginner, intermediate, advanced).';
        }
    }

    /**
     * اعتبارسنجی وضعیت (Status)
     *
     * @param mixed $status
     * @return void
     */
    public function validateStatus($status): void
    {
        $allowedStatuses = ['active', 'inactive', 'pending', 'suspended'];

        if (empty($status) || !in_array(strtolower($status), $allowedStatuses)) {
            $this->errors['status'] = 'Valid status is required (active, inactive, pending, suspended).';
        }
    }

    // بررسی اینکه آیا خطایی وجود دارد یا خیر
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    // دریافت تمام خطاها
    public function getErrors(): array
    {
        return $this->errors;
    }

    // متد برای پاک کردن خطاها
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * متد کلی برای اعتبارسنجی مجموعه‌ای از فیلدها با قوانین متنوع
     *
     * @param array $fields
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
    }}

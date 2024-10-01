<?php
namespace App\Models;

use Exception;
use PDO;
use PDOException;

class Database {
    private $host = 'localhost';       // نام سرور
    private $db_name = 'beewords'; // نام دیتابیس
    private $username = 'root';// نام کاربری دیتابیس
    private $password = 'Farnaz@0509654327';// رمز عبور دیتابیس
    private $charset = 'utf8mb4';       // charset دیتابیس
    private $pdo;
    private $error;
    private $stmt;

    // متد سازنده برای اتصال به دیتابیس
    public function __construct() {
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
        $options = [
            PDO::ATTR_PERSISTENT => true,            // اتصال پایدار
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // حالت خطا به شکل Exception
        ];

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection error");
        }
    }

    // متد برای اجرای کوئری SQL
    public function query($sql): void
    {
        $this->stmt = $this->pdo->prepare($sql);
    }

    // متد برای بایند کردن ورودی‌ها
    public function bind($param, $value, $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // اجرای کوئری
    public function execute() {
        return $this->stmt->execute();
    }

    // گرفتن همه نتایج به شکل آرایه
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // گرفتن یک نتیجه
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // گرفتن تعداد ردیف‌های متاثر از کوئری
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // شروع تراکنش
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    // پایان تراکنش
    public function endTransaction(): bool
    {
        return $this->pdo->commit();
    }

    // بازگردانی تراکنش
    public function cancelTransaction(): bool
    {
        return $this->pdo->rollBack();
    }

    // گرفتن آخرین ID درج شده
    public function lastInsertId(): bool|string
    {
        return $this->pdo->lastInsertId();
    }

    // بستن اتصال
    public function closeConnection(): void
    {
        $this->pdo = null;
    }
    public function fetchColumn() {
        $this->execute();
        return $this->stmt->fetchColumn();
    }
    public function getError() {
        return $this->error;
    }
}

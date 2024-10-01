<?php

namespace App\Models;

use App\Models\Database;

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * ایجاد یک دسته‌بندی جدید
     *
     * @param array $data
     * @return int|false
     */
    public function createCategory(array $data) {
        $sql = "INSERT INTO categories (title, description, thumbnail, userId, createdAt, updatedAt) 
                VALUES (:title, :description, :thumbnail, :userId, :createdAt, :updatedAt)";
        $this->db->query($sql);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':thumbnail', $data['thumbnail']);
        $this->db->bind(':userId', $data['userId']);
        $this->db->bind(':createdAt', $data['createdAt']);
        $this->db->bind(':updatedAt', $data['updatedAt']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * دریافت دسته‌بندی بر اساس ID و userId
     *
     * @param int $id
     * @param int $userId
     * @return array|null
     */
    public function findById(int $id, int $userId): ?array {
        $sql = "SELECT * FROM categories WHERE id = :id AND userId = :userId LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':userId', $userId);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * دریافت تمام دسته‌بندی‌های یک کاربر
     *
     * @param int $userId
     * @return array
     */
    public function getCategoriesByUser(int $userId): array {
        $sql = "SELECT * FROM categories WHERE userId = :userId";
        $this->db->query($sql);
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    /**
     * به‌روزرسانی دسته‌بندی
     *
     * @param int $id
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateCategory(int $id, int $userId, array $data): bool {
        $sql = "UPDATE categories SET 
                    title = :title, 
                    description = :description, 
                    thumbnail = :thumbnail, 
                    updatedAt = :updatedAt 
                WHERE id = :id AND userId = :userId";
        $this->db->query($sql);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':thumbnail', $data['thumbnail']);
        $this->db->bind(':updatedAt', $data['updatedAt']);
        $this->db->bind(':id', $id);
        $this->db->bind(':userId', $userId);

        return $this->db->execute();
    }

    /**
     * حذف دسته‌بندی
     *
     * @param int $id
     * @param int $userId
     * @return bool
     */
    public function deleteCategory(int $id, int $userId): bool {
        $sql = "DELETE FROM categories WHERE id = :id AND userId = :userId";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $this->db->bind(':userId', $userId);
        return $this->db->execute();
    }

    /**
     * شمارش تعداد دسته‌بندی‌های یک کاربر
     *
     * @param int $userId
     * @return int|false
     */
    public function countCategoriesByUser(int $userId) {
        $sql = "SELECT COUNT(*) as total FROM categories WHERE userId = :userId";
        $this->db->query($sql);
        $this->db->bind(':userId', $userId);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : false;
    }
}

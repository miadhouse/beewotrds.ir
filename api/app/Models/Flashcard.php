<?php

namespace App\Models;

use App\Models\Database;

class Flashcard {
    private $db;

    public function __construct() {
        // Assumes the Database class is properly implemented
        $this->db = new Database();
    }

    /**
     * Create a new flashcard
     *
     * @param array $data
     * @return int|false
     */
    public function createFlashcard(array $data) {
        $sql = "INSERT INTO flashcards 
                (userId, categoryId, baseLang, translateLang, frontWord, backWord, level, createdAt, updatedAt, status) 
                VALUES 
                (:userId, :categoryId, :baseLang, :translateLang, :frontWord, :backWord, :level, :createdAt, :updatedAt, :status)";
        $this->db->query($sql);
        $this->db->bind(':userId', $data['userId']);
        $this->db->bind(':categoryId', $data['categoryId']);
        $this->db->bind(':baseLang', $data['baseLang']);
        $this->db->bind(':translateLang', $data['translateLang']);
        $this->db->bind(':frontWord', $data['frontWord']);
        $this->db->bind(':backWord', $data['backWord']);
        $this->db->bind(':level', $data['level']);
        $this->db->bind(':createdAt', $data['createdAt']);
        $this->db->bind(':updatedAt', $data['updatedAt']);
        $this->db->bind(':status', $data['status']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Retrieve a flashcard by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array {
        $sql = "SELECT * FROM flashcards WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    /**
     * Retrieve all flashcards for a specific user
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array {
        $sql = "SELECT * FROM flashcards WHERE userId = :userId AND status = 'active'";
        $this->db->query($sql);
        $this->db->bind(':userId', $userId);
        return $this->db->resultSet();
    }

    /**
     * Update a flashcard
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateFlashcard(int $id, array $data): bool {
        $sql = "UPDATE flashcards SET 
                    categoryId = :categoryId, 
                    baseLang = :baseLang, 
                    translateLang = :translateLang, 
                    frontWord = :frontWord, 
                    backWord = :backWord, 
                    level = :level, 
                    updatedAt = :updatedAt, 
                    status = :status 
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':categoryId', $data['categoryId']);
        $this->db->bind(':baseLang', $data['baseLang']);
        $this->db->bind(':translateLang', $data['translateLang']);
        $this->db->bind(':frontWord', $data['frontWord']);
        $this->db->bind(':backWord', $data['backWord']);
        $this->db->bind(':level', $data['level']);
        $this->db->bind(':updatedAt', $data['updatedAt']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /**
     * Delete a flashcard
     *
     * @param int $id
     * @return bool
     */
    public function deleteFlashcard(int $id): bool {
        $sql = "DELETE FROM flashcards WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count flashcards for a specific user
     *
     * @param int $userId
     * @return int|false
     */
    public function countFlashcardsByUser(int $userId) {
        $sql = "SELECT COUNT(*) as total FROM flashcards WHERE userId = :userId AND status = 'active'";
        $this->db->query($sql);
        $this->db->bind(':userId', $userId);
        $result = $this->db->single();
        return $result ? (int)$result['total'] : false;
    }

    /**
     * Retrieve all flashcards
     *
     * @return array
     */
    public function getAllFlashcards(): array {
        $sql = "SELECT * FROM flashcards";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
}

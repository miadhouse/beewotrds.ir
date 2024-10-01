<?php

namespace App\Controllers;

use App\Models\Flashcard;
use App\Services\RequestValidator;
use App\Services\SessionManager;
use App\Services\JWTManager;

class FlashcardController
{
    private Flashcard $flashcardModel;
    private RequestValidator $validator;
    private SessionManager $sessionManager;
    private JWTManager $jwtManager;

    public function __construct(
        Flashcard        $flashcardModel,
        RequestValidator $validator,
        SessionManager   $sessionManager,
        JWTManager       $jwtManager
    )
    {
        $this->flashcardModel = $flashcardModel;
        $this->validator = $validator;
        $this->sessionManager = $sessionManager;
        $this->jwtManager = $jwtManager;
    }

    /**
     * Create a new flashcard
     */
    public function createFlashcard($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Validate input using the updated RequestValidator
        $this->validator->validate($request, [
            'categoryId'    => ['categoryId'],
            'baseLang'      => ['language'],
            'translateLang' => ['language'],
            'frontWord'     => ['word'],
            'backWord'      => ['word'],
            'level'         => ['level']
        ]);

        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // Prepare data
        $data = [
            'userId'         => $userId,
            'categoryId'     => $request['categoryId'],
            'baseLang'       => strtolower($request['baseLang']),
            'translateLang'  => strtolower($request['translateLang']),
            'frontWord'      => $request['frontWord'],
            'backWord'       => $request['backWord'],
            'level'          => strtolower($request['level']),
            'createdAt'      => date('Y-m-d H:i:s'),
            'updatedAt'      => date('Y-m-d H:i:s'),
            'status'         => 'active'
        ];

        // Create flashcard
        $flashcardId = $this->flashcardModel->createFlashcard($data);
        if ($flashcardId) {
            return ['status' => 201, 'message' => 'Flashcard created successfully', 'flashcardId' => $flashcardId];
        }

        return ['status' => 500, 'message' => 'Failed to create flashcard'];
    }
    /**
     * Retrieve all flashcards for the authenticated user
     */
    public function getFlashcards($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Optionally handle filtering via query parameters
        // For simplicity, we'll retrieve all flashcards for the user

        $flashcards = $this->flashcardModel->findByUserId($userId);
        if ($flashcards !== false) {
            return ['status' => 200, 'data' => $flashcards];
        }

        return ['status' => 500, 'message' => 'Failed to retrieve flashcards'];
    }

    /**
     * Retrieve a single flashcard by ID
     */
    public function getFlashcardById($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Validate ID
        $flashcardId = $request['id'] ?? null;
        if (!$flashcardId || !is_numeric($flashcardId)) {
            return ['status' => 400, 'message' => 'Valid flashcard ID is required'];
        }

        // Retrieve flashcard
        $flashcard = $this->flashcardModel->findById((int)$flashcardId);
        if (!$flashcard || $flashcard['userId'] != $userId) {
            return ['status' => 404, 'message' => 'Flashcard not found'];
        }

        return ['status' => 200, 'data' => $flashcard];
    }

    /**
     * Update an existing flashcard
     */
    public function updateFlashcard($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Validate ID
        $flashcardId = $request['id'] ?? null;
        if (!$flashcardId || !is_numeric($flashcardId)) {
            return ['status' => 400, 'message' => 'Valid flashcard ID is required'];
        }

        // Retrieve flashcard
        $flashcard = $this->flashcardModel->findById((int)$flashcardId);
        if (!$flashcard || $flashcard['userId'] != $userId) {
            return ['status' => 404, 'message' => 'Flashcard not found'];
        }

        // Validate input
        $this->validator->validate([
            'categoryId'    => ['categoryId'],
            'baseLang'      => ['language'],
            'translateLang' => ['language'],
            'frontWord'     => ['word'],
            'backWord'      => ['word'],
            'level'         => ['level']
        ]);

        if ($this->validator->hasErrors()) {
            return ['status' => 400, 'errors' => $this->validator->getErrors()];
        }

        // Prepare data
        $data = [
            'categoryId'     => $request['categoryId'],
            'baseLang'       => strtolower($request['baseLang']),
            'translateLang'  => strtolower($request['translateLang']),
            'frontWord'      => $request['frontWord'],
            'backWord'       => $request['backWord'],
            'level'          => strtolower($request['level']),
            'updatedAt'      => date('Y-m-d H:i:s'),
            'status'         => $request['status'] ?? 'active'
        ];

        // Update flashcard
        $updateResult = $this->flashcardModel->updateFlashcard((int)$flashcardId, $data);
        if ($updateResult) {
            return ['status' => 200, 'message' => 'Flashcard updated successfully'];
        }

        return ['status' => 500, 'message' => 'Failed to update flashcard'];
    }

    /**
     * Delete a flashcard
     */
    public function deleteFlashcard($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Validate ID
        $flashcardId = $request['id'] ?? null;
        if (!$flashcardId || !is_numeric($flashcardId)) {
            return ['status' => 400, 'message' => 'Valid flashcard ID is required'];
        }

        // Retrieve flashcard
        $flashcard = $this->flashcardModel->findById((int)$flashcardId);
        if (!$flashcard || $flashcard['userId'] != $userId) {
            return ['status' => 404, 'message' => 'Flashcard not found'];
        }

        // Delete flashcard
        $deleteResult = $this->flashcardModel->deleteFlashcard((int)$flashcardId);
        if ($deleteResult) {
            return ['status' => 200, 'message' => 'Flashcard deleted successfully'];
        }

        return ['status' => 500, 'message' => 'Failed to delete flashcard'];
    }

    /**
     * Retrieve the count of flashcards for the authenticated user
     */
    public function getFlashcardCount($request): array
    {
        // Authenticate user
        $authResult = $this->authenticate($request);
        if ($authResult['status'] !== 200) {
            return $authResult;
        }
        $userId = $authResult['userId'];

        // Retrieve flashcard count
        $count = $this->flashcardModel->countFlashcardsByUser($userId);
        if ($count !== false) {
            return ['status' => 200, 'count' => $count];
        }

        return ['status' => 500, 'message' => 'Failed to retrieve flashcard count'];
    }

    /**
     * Authenticate the user using JWT
     *
     * @param array $request
     * @return array
     */
    private function authenticate($request): array
    {
        // Retrieve token from headers
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return ['status' => 401, 'message' => 'Authorization token not provided'];
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $decodedToken = $this->jwtManager->verifyToken($token);

        if (!$decodedToken) {
            return ['status' => 401, 'message' => 'Invalid or expired token'];
        }

        return ['status' => 200, 'userId' => $decodedToken['userId']];
    }
}

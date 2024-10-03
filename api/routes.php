<?php

use App\Controllers\AuthController;
use App\Controllers\FlashcardController;
use App\Controllers\CategoryController; // Include CategoryController
use App\Models\User;
use App\Models\Flashcard;
use App\Models\Category; // Include Category model
use App\Services\JWTManager;
use App\Services\SessionManager;
use App\Services\RequestValidator;
use App\Helpers\EmailService;
use App\Helpers\TokenGenerator;
use App\Services\ReCaptchaService; // Import ReCaptchaService
use App\Middleware\ValidationMiddleware; // Import ValidationMiddleware
use App\Handlers\ExceptionHandler; // Import ExceptionHandler

// تنظیم هدرهای CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// مدیریت درخواست OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// فعال‌سازی نمایش خطاها (برای توسعه)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بارگذاری اتوماتیک کلاس‌ها
require_once 'vendor/autoload.php';

// بارگذاری متغیرهای محیطی
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// بررسی تنظیمات متغیرهای محیطی
if (!isset($_ENV['RECAPTCHA_SECRET_KEY']) || empty($_ENV['RECAPTCHA_SECRET_KEY'])) {
    throw new \Exception('RECAPTCHA_SECRET_KEY is not set in the environment variables.');
}

// ایجاد نمونه‌های مورد نیاز
$userModel = new User();
$flashcardModel = new Flashcard();
$categoryModel = new Category(); // Instantiate Category model
$jwtManager = new JWTManager();
$sessionManager = new SessionManager();
$validator = new RequestValidator();
$emailService = new EmailService();
$tokenGenerator = new TokenGenerator();

// ایجاد نمونه ValidationMiddleware
$validationMiddleware = new ValidationMiddleware($validator);

// ایجاد نمونه ReCaptchaService
$reCaptchaService = new ReCaptchaService($_ENV['RECAPTCHA_SECRET_KEY']); // Correct instantiation

// ایجاد نمونه ExceptionHandler
$exceptionHandler = new ExceptionHandler();

// تنظیم ExceptionHandler به عنوان هندلر جهانی
set_exception_handler([$exceptionHandler, 'handle']);

// ایجاد کنترلرها با استفاده از Dependency Injection
$authController = new AuthController(
    $userModel,
    $jwtManager,
    $sessionManager,
    $validator,
    $emailService,
    $tokenGenerator,
    $reCaptchaService // Pass ReCaptchaService
);

$flashcardController = new FlashcardController(
    $flashcardModel,
    $validator,
    $sessionManager,
    $jwtManager,
    $validationMiddleware // Pass ValidationMiddleware as the 5th parameter
);

$categoryController = new CategoryController(
    $categoryModel,
    $validator,
    $sessionManager,
    $jwtManager,
    $validationMiddleware // Pass ValidationMiddleware as the 5th parameter
);

// دریافت مسیر و متد درخواست
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// حذف پایه URI در صورت نیاز
$baseUri = '/api'; // Set to '/api' if your API is under the /api path
$requestUri = str_replace($baseUri, '', $requestUri);

// تعریف مسیرها با استفاده از کنترلرها
try {
    if ($requestUri === '/register' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($authController->register($data));
    } elseif ($requestUri === '/login' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($authController->login($data));
    } elseif ($requestUri === '/logout' && $requestMethod === 'POST') {
        echo json_encode($authController->logout());
    } elseif ($requestUri === '/user' && $requestMethod === 'GET') {
        echo json_encode($authController->getUser());
    } elseif ($requestUri === '/verify' && $requestMethod === 'GET') {
        $code = $_GET['code'] ?? null;
        $data = ['code' => $code];
        echo json_encode($authController->verifyEmail($data));
    } elseif ($requestUri === '/resend-verification' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($authController->resendVerificationEmail($data));
    } elseif ($requestUri === '/recover-password' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($authController->recoverPassword($data));
    }

    // اضافه کردن مسیر /reset-password
    elseif ($requestUri === '/reset-password' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($authController->resetPassword($data));
    }

    // مسیرهای مربوط به Flashcard
    elseif ($requestUri === '/flashcards' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($flashcardController->createFlashcard($data));
    } elseif ($requestUri === '/flashcards' && $requestMethod === 'GET') {
        echo json_encode($flashcardController->getFlashcards($_GET));
    } elseif (preg_match('/\/flashcards\/(\d+)/', $requestUri, $matches)) {
        // Matches URLs like /flashcards/123
        $flashcardId = (int)$matches[1];
        if ($requestMethod === 'GET') {
            $data = ['id' => $flashcardId];
            echo json_encode($flashcardController->getFlashcardById($data));
        } elseif ($requestMethod === 'PUT') {
            parse_str(file_get_contents("php://input"), $putData);
            $putData['id'] = $flashcardId;
            echo json_encode($flashcardController->updateFlashcard($putData));
        } elseif ($requestMethod === 'DELETE') {
            $data = ['id' => $flashcardId];
            echo json_encode($flashcardController->deleteFlashcard($data));
        } else {
            http_response_code(405);
            echo json_encode(['status' => 405, 'message' => 'Method Not Allowed']);
        }
    } elseif ($requestUri === '/flashcards/count' && $requestMethod === 'GET') {
        echo json_encode($flashcardController->getFlashcardCount($_GET));
    }

    // مسیرهای مربوط به Category
    elseif ($requestUri === '/categories' && $requestMethod === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode($categoryController->createCategory($data));
    } elseif ($requestUri === '/categories' && $requestMethod === 'GET') {
        echo json_encode($categoryController->getCategories($_GET));
    } elseif (preg_match('/\/categories\/count/', $requestUri)) {
        // Matches /categories/count
        if ($requestMethod === 'GET') {
            echo json_encode($categoryController->getCategoryCount($_GET));
        } else {
            http_response_code(405);
            echo json_encode(['status' => 405, 'message' => 'Method Not Allowed']);
        }
    } elseif (preg_match('/\/categories\/(\d+)/', $requestUri, $matches)) {
        // Matches URLs like /categories/123
        $categoryId = (int)$matches[1];
        if ($requestMethod === 'GET') {
            $data = ['id' => $categoryId];
            echo json_encode($categoryController->getCategoryById($data));
        } elseif ($requestMethod === 'PUT') {
            parse_str(file_get_contents("php://input"), $putData);
            $putData['id'] = $categoryId;
            echo json_encode($categoryController->updateCategory($putData));
        } elseif ($requestMethod === 'DELETE') {
            $data = ['id' => $categoryId];
            echo json_encode($categoryController->deleteCategory($data));
        } else {
            http_response_code(405);
            echo json_encode(['status' => 405, 'message' => 'Method Not Allowed']);
        }
    } else {
        // If the route is invalid
        http_response_code(404);
        echo json_encode(['status' => 404, 'message' => 'Not Found']);
    }
} catch (\Throwable $e) {
    // مدیریت خطاها با استفاده از ExceptionHandler
    $exceptionHandler->handle($e);
}

<?php
/**
 * Pulse AI Blog Platform - Main Entry Point & Router
 */

declare(strict_types=1);

// 1. PSR-4 Autoloader fallback (works with or without composer dump-autoload)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// If composer vendor exists, load it
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use App\Helpers\Env;
use App\Helpers\Csrf;
use App\Controllers\AuthController;
use App\Controllers\PostController;
use App\Controllers\AIController;
use App\Controllers\CommentController;

// 2. Initialize Environment & Security
Env::load(__DIR__ . '/../.env');
Csrf::init();

// 3. Routing Engine
$route = $_GET['route'] ?? 'home';

switch ($route) {
    // Blog Feed & Reader
    case 'home':
        (new PostController())->index();
        break;

    case 'posts.show':
        (new PostController())->show();
        break;

    case 'api.posts.like':
        (new PostController())->like();
        break;

    // Blog Management
    case 'posts.create':
        (new PostController())->create();
        break;

    case 'posts.store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new PostController())->store();
        } else {
            header('Location: ?route=posts.create');
        }
        break;

    case 'posts.edit':
        (new PostController())->edit();
        break;

    case 'posts.update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new PostController())->update();
        } else {
            header('Location: ?route=home');
        }
        break;

    case 'posts.delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new PostController())->delete();
        } else {
            header('Location: ?route=home');
        }
        break;

    // Comments
    case 'comments.store':
        (new CommentController())->store();
        break;

    case 'comments.delete':
        (new CommentController())->delete();
        break;

    // Authentication
    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'login.post':
        (new AuthController())->login();
        break;

    case 'register':
        (new AuthController())->showRegister();
        break;

    case 'register.post':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'forgot-password':
        (new AuthController())->showForgotPassword();
        break;

    case 'forgot-password.post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->sendResetLink();
        } else {
            header('Location: ?route=forgot-password');
        }
        break;

    case 'reset-password':
        (new AuthController())->showResetPassword();
        break;

    case 'reset-password.post':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->resetPassword();
        } else {
            header('Location: ?route=forgot-password');
        }
        break;

    // AI API Endpoints (JSON)
    case 'api.ai.summarize':
        (new AIController())->summarize();
        break;

    case 'api.ai.copilot':
        (new AIController())->copilot();
        break;

    case 'api.ai.tags':
        (new AIController())->tags();
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/../views/posts/404.php';
        break;
}

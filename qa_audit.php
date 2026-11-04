<?php
/**
 * Senior QA Automation & Integration Test Suite
 * Pulse AI Blog Engine
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Helpers\Env;
use App\Helpers\Csrf;
use App\Helpers\Auth;
use App\Helpers\Markdown;
use App\Config\Database;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Services\GeminiService;

Env::load(__DIR__ . '/.env');

$results = [];
function test($name, $closure) {
    global $results;
    try {
        $closure();
        $results[] = ['name' => $name, 'status' => 'PASS', 'error' => null];
    } catch (\Throwable $e) {
        $results[] = ['name' => $name, 'status' => 'FAIL', 'error' => $e->getMessage()];
    }
}

// TEST 1: Database Connection
test('Database Connection (PDO)', function() {
    $db = Database::getConnection();
    if (!$db) throw new Exception("Database connection returned null");
});

// TEST 2: User Retrieval & Auth Helpers
test('User Model & Auth Verification', function() {
    $admin = User::findByEmail('admin@aurablog.com');
    if (!$admin) throw new Exception("Default admin user not found");
    if (!password_verify('admin123', $admin['password'])) throw new Exception("Admin password verification failed");
});

// TEST 3: Post Creation, Unique Slug & Retrieval
$testPostId = null;
test('Post Model (Create, Unique Slug, Read)', function() use (&$testPostId) {
    $testTitle = "Automated QA Test Article " . time();
    $postId = Post::create([
        'user_id'     => 1,
        'category_id' => 1,
        'title'       => $testTitle,
        'content'     => "<h1>QA Content</h1><p>Testing post creation workflow.</p>",
        'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe',
        'status'      => 'published'
    ]);
    if (!$postId) throw new Exception("Failed to insert post");
    $testPostId = $postId;

    $post = Post::getById($postId);
    if (!$post || $post['title'] !== $testTitle) throw new Exception("Post title mismatch");
    if (empty($post['slug'])) throw new Exception("Slug was not generated");
});

// TEST 4: View Count Increment
test('Post View Count Increment', function() use (&$testPostId) {
    $before = (int)Post::getById($testPostId)['views'];
    Post::incrementViews($testPostId);
    $after = (int)Post::getById($testPostId)['views'];
    if ($after !== $before + 1) throw new Exception("Views did not increment properly. Before: {$before}, After: {$after}");
});

// TEST 5: Comments System
test('Comment Model (Add, Read, Delete)', function() use (&$testPostId) {
    $commId = Comment::create($testPostId, 1, "Automated QA test comment");
    if (!$commId) throw new Exception("Failed to add comment");

    $comments = Comment::getByPostId($testPostId);
    if (count($comments) === 0) throw new Exception("Comments not retrieved for post");

    $deleted = Comment::delete($commId, 1, true);
    if (!$deleted) throw new Exception("Failed to delete comment");
});

// TEST 6: XSS Protection & Video Sanitization
test('Markdown & Rich-Text XSS Sanitizer', function() {
    $dangerousInput = "<p>Safe text</p><script>alert('HACK')</script><iframe src='https://www.youtube.com/embed/dQw4w9WgXcQ'></iframe>";
    $output = Markdown::toHtml($dangerousInput);
    if (strpos($output, '<script>') !== false) throw new Exception("Script tag was not sanitized!");
    if (strpos($output, 'aspect-video') === false) throw new Exception("YouTube iframe was not converted to responsive container!");
});

// TEST 7: CSRF Token Integrity
test('CSRF Token Generation & Validation', function() {
    Csrf::init();
    $token = Csrf::getToken();
    if (empty($token) || strlen($token) !== 64) throw new Exception("Invalid CSRF token format");
    if (!Csrf::validate($token)) throw new Exception("Valid token rejected by validator");
    if (Csrf::validate("fake_token_123")) throw new Exception("Invalid token falsely accepted");
});

// TEST 8: AI Service Fallback & Error Handling
test('AI Service Resilience & Fallback', function() {
    $gemini = new GeminiService();
    // Testing fallback behavior if no key
    $summary = $gemini->summarizePost("This is a test article content for senior engineer QA audit.");
    // Must return an array with either success=true or a clear structured error, never uncaught exception
    if (!isset($summary['success'])) throw new Exception("AI response malformed");
});

// TEST 9: Password Reset Token Security & Password Update
test('Password Reset Token Lifecycle & Update', function() {
    $email = 'admin@aurablog.com';
    $token = User::createPasswordReset($email);
    if (empty($token) || strlen($token) !== 64) throw new Exception("Token was not properly generated");

    $record = User::verifyPasswordResetToken($token);
    if (!$record || $record['email'] !== $email) throw new Exception("Token verification failed");

    // Test updating password with secure bcrypt
    $updated = User::updatePassword(1, 'admin123');
    if (!$updated) throw new Exception("Password update query failed");

    // Invalidate token
    User::deletePasswordReset($token);
    $checkAfter = User::verifyPasswordResetToken($token);
    if ($checkAfter !== null) throw new Exception("Token was not invalidated after use");
});

// Cleanup test post
if ($testPostId) {
    Post::delete($testPostId, 1, true);
}

// Output summary table
echo "\n============================================================\n";
echo "       SENIOR QA & INTEGRATION TEST RESULTS                 \n";
echo "============================================================\n";
$allPass = true;
foreach ($results as $r) {
    $statusStr = $r['status'] === 'PASS' ? "\033[32m[PASS]\033[0m" : "\033[31m[FAIL]\033[0m";
    echo sprintf("%-45s %s\n", $r['name'], $statusStr);
    if ($r['error']) {
        echo "   -> Error: " . $r['error'] . "\n";
        $allPass = false;
    }
}
echo "============================================================\n";
echo $allPass ? "ALL 9 SYSTEM QA TESTS PASSED WITH 100% SUCCESS!\n" : "SOME TESTS FAILED!\n";
echo "============================================================\n";

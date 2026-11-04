<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Models\User;

class AuthController {
    public function showLogin(?string $error = null): void {
        if (Auth::check()) {
            header('Location: ?route=home');
            exit;
        }
        require __DIR__ . '/../../views/auth/login.php';
    }

    public function showRegister(?string $error = null): void {
        if (Auth::check()) {
            header('Location: ?route=home');
            exit;
        }
        require __DIR__ . '/../../views/auth/register.php';
    }

    public function login(): void {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->showLogin('Security token expired. Please try again.');
            return;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->showLogin('Please fill in all required fields.');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $this->showLogin('Invalid email or password.');
            return;
        }

        Auth::login($user);
        header('Location: ?route=home&msg=welcome');
        exit;
    }

    public function register(): void {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->showRegister('Security token expired. Please try again.');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (strlen($username) < 3 || strlen($password) < 6 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showRegister('Please provide a valid email, username (min 3 chars), and password (min 6 chars).');
            return;
        }

        if (User::findByEmail($email)) {
            $this->showRegister('An account with this email already exists.');
            return;
        }

        if (User::findByUsername($username)) {
            $this->showRegister('This username is already taken. Please choose another.');
            return;
        }

        $userId = User::create($username, $email, $password);
        $user = User::findById($userId);
        Auth::login($user);

        header('Location: ?route=home&msg=registered');
        exit;
    }

    public function logout(): void {
        Auth::logout();
        header('Location: ?route=login&msg=logged_out');
        exit;
    }

    public function showForgotPassword(?string $error = null, ?string $success = null, ?string $resetLink = null): void {
        if (Auth::check()) {
            header('Location: ?route=home');
            exit;
        }
        require __DIR__ . '/../../views/auth/forgot-password.php';
    }

    public function sendResetLink(): void {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->showForgotPassword('Security token expired. Please try again.');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showForgotPassword('Please enter a valid email address.');
            return;
        }

        $user = User::findByEmail($email);
        if (!$user) {
            // Friendly message without leaking user existence
            $this->showForgotPassword(null, "If an account exists with {$email}, a password reset link has been prepared.");
            return;
        }

        // Generate 32-byte cryptographic token with 15-minute expiry
        $token = User::createPasswordReset($email);
        $resetUrl = "?route=reset-password&token=" . urlencode($token);

        $this->showForgotPassword(
            null,
            "A secure password reset link has been generated (Valid for 15 minutes).",
            $resetUrl
        );
    }

    public function showResetPassword(?string $token = null, ?string $error = null): void {
        if (Auth::check()) {
            header('Location: ?route=home');
            exit;
        }

        $token = $token ?? trim($_GET['token'] ?? '');
        if (empty($token)) {
            header('Location: ?route=forgot-password');
            exit;
        }

        $reset = User::verifyPasswordResetToken($token);
        if (!$reset) {
            $error = 'This password reset link is invalid or has expired (15-minute limit). Please request a new one.';
        }

        require __DIR__ . '/../../views/auth/reset-password.php';
    }

    public function resetPassword(): void {
        if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
            $this->showResetPassword($_POST['token'] ?? '', 'Security token expired. Please try again.');
            return;
        }

        $token           = trim($_POST['token'] ?? '');
        $password        = $_POST['password'] ?? '';
        $confirmPassword = $_POST['password_confirmation'] ?? '';

        if (empty($token)) {
            header('Location: ?route=forgot-password');
            exit;
        }

        $reset = User::verifyPasswordResetToken($token);
        if (!$reset) {
            $this->showResetPassword($token, 'This reset link has expired or is invalid. Please request a new one.');
            return;
        }

        if (strlen($password) < 6) {
            $this->showResetPassword($token, 'New password must be at least 6 characters long.');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->showResetPassword($token, 'Passwords do not match. Please re-enter.');
            return;
        }

        $user = User::findByEmail($reset['email']);
        if (!$user) {
            $this->showResetPassword($token, 'Associated account could not be found.');
            return;
        }

        // Update password with secure bcrypt hash
        User::updatePassword((int)$user['id'], $password);

        // Invalidate token so it can never be reused (prevent replay attacks)
        User::deletePasswordReset($token);

        header('Location: ?route=login&msg=password_reset');
        exit;
    }
}

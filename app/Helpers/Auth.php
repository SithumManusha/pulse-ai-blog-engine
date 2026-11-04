<?php
namespace App\Helpers;

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::init();
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array {
        self::init();
        if (!self::check()) {
            return null;
        }

        $username = $_SESSION['username'] ?? 'User';
        $avatar = $_SESSION['avatar'] ?? '';
        if (empty($avatar) || str_contains($avatar, 'unsplash') || str_contains($avatar, 'photo-')) {
            $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=4f46e5&color=fff&bold=true';
            $_SESSION['avatar'] = $avatar;
        }

        return [
            'id'       => $_SESSION['user_id'],
            'username' => $username,
            'email'    => $_SESSION['email'] ?? '',
            'role'     => $_SESSION['role'] ?? 'reader',
            'avatar'   => $avatar
        ];
    }

    public static function id(): ?int {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function login(array $user): void {
        self::init();
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $user['email'];
        $_SESSION['role']     = $user['role'];
        $username = $user['username'] ?? 'User';
        $userAvatar = $user['avatar'] ?? '';
        if (empty($userAvatar) || str_contains($userAvatar, 'unsplash') || str_contains($userAvatar, 'photo-') || str_contains($userAvatar, 'pravatar')) {
            $userAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=4f46e5&color=fff&bold=true';
        }
        $_SESSION['avatar'] = $userAvatar;
    }

    public static function logout(): void {
        self::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: ?route=login');
            exit;
        }
    }
}

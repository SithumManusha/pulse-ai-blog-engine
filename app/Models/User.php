<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        if (!$db) return null;

        $stmt = $db->prepare("SELECT id, username, email, role, avatar, bio, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $db = Database::getConnection();
        if (!$db) return null;

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findByUsername(string $username): ?array {
        $db = Database::getConnection();
        if (!$db) return null;

        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(string $username, string $email, string $password): int {
        $db = Database::getConnection();
        if (!$db) throw new \Exception("Database not available.");

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($username) . '&background=4f46e5&color=fff&bold=true';
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role, avatar) VALUES (:username, :email, :password, 'author', :avatar)");
        $stmt->execute([
            'username' => $username,
            'email'    => $email,
            'password' => $hash,
            'avatar'   => $avatar
        ]);

        return (int)$db->lastInsertId();
    }

    public static function updatePassword(int $userId, string $newPassword): bool {
        $db = Database::getConnection();
        if (!$db) return false;

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        return $stmt->execute([
            'password' => $hash,
            'id'       => $userId
        ]);
    }

    public static function createPasswordReset(string $email): string {
        $db = Database::getConnection();
        if (!$db) throw new \Exception("Database not available.");

        // Ensure password_resets table exists
        $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + 900); // 15 minutes validity

        // Invalidate older tokens for this email
        $del = $db->prepare("DELETE FROM password_resets WHERE email = :email");
        $del->execute(['email' => $email]);

        $stmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :token, :expires_at)");
        $stmt->execute([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt
        ]);

        return $token;
    }

    public static function verifyPasswordResetToken(string $token): ?array {
        $db = Database::getConnection();
        if (!$db) return null;

        // Ensure table exists
        $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token TEXT NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $db->prepare("SELECT * FROM password_resets WHERE token = :token AND expires_at > CURRENT_TIMESTAMP ORDER BY id DESC LIMIT 1");
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function deletePasswordReset(string $token): void {
        $db = Database::getConnection();
        if (!$db) return;

        $stmt = $db->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }
}

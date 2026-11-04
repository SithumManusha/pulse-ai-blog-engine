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
}

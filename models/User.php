<?php

class User
{
    private const PUBLIC_FIELDS = 'id, name, email, created_at, updated_at';

    public static function all(): array
    {
        $sql = 'SELECT ' . self::PUBLIC_FIELDS . ' FROM users ORDER BY id';
        return Database::getConnection()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $sql = 'SELECT ' . self::PUBLIC_FIELDS . ' FROM users WHERE id = :id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function findByEmail(string $email): ?array
    {
        $sql = 'SELECT ' . self::PUBLIC_FIELDS . ' FROM users WHERE email = :email';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function findByEmailWithHash(string $email): ?array
    {
        $sql = 'SELECT id, name, email, password_hash, created_at, updated_at FROM users WHERE email = :email';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row !== false ? $row : null;
    }

    public static function create(string $name, string $email, string $passwordHash): array
    {
        $pdo = Database::getConnection();
        $sql = 'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :hash)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':hash' => $passwordHash,
        ]);
        $id = (int)$pdo->lastInsertId();
        return self::find($id);
    }

    public static function updateName(int $id, string $name): void
    {
        $sql = 'UPDATE users SET name = :name, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public static function updateEmail(int $id, string $email): void
    {
        $sql = 'UPDATE users SET email = :email, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':email' => $email, ':id' => $id]);
    }

    public static function updatePassword(int $id, string $passwordHash): void
    {
        $sql = 'UPDATE users SET password_hash = :hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':hash' => $passwordHash, ':id' => $id]);
    }

    public static function delete(int $id): bool
    {
        $sql = 'DELETE FROM users WHERE id = :id';
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}

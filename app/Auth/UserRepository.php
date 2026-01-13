<?php

declare(strict_types=1);

namespace App\Auth;

use App\Database\Connection;
use PDO;
use PDOException;

final class UserRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::pdo();
    }

    /** @return array{id:int,name:string,email:string,password_hash:string,role_id:int,role_key:string,created_at:string}|null */
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT u.id, u.name, u.email, u.password_hash, u.role_id, r.role_key, u.created_at 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                WHERE u.email = :email LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    /** @return array{id:int,name:string,email:string,role_id:int,created_at:string} */
    public function create(string $name, string $email, string $plainPassword, int $roleId = 3): array
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash, role_id, created_at)
             VALUES (:name, :email, :password_hash, :role_id, :created_at)'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => $hash,
            ':role_id' => $roleId,
            ':created_at' => gmdate('c'),
        ]);

        $id = (int)$this->pdo->lastInsertId();
        return [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'role_id' => $roleId,
            'created_at' => gmdate('c'),
        ];
    }

    /** @return array<array{id:int,name:string,email:string,role_id:int,role_name:string,created_at:string}> */
    public function findAll(): array
    {
        $sql = 'SELECT u.id, u.name, u.email, u.role_id, r.role_name, u.created_at 
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                ORDER BY u.id DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT u.*, r.role_key, r.role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function update(int $id, string $name, string $email, int $roleId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE users SET name = :name, email = :email, role_id = :role_id WHERE id = :id');
        return $stmt->execute([':id' => $id, ':name' => $name, ':email' => $email, ':role_id' => $roleId]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /** @return array<array{id:int,role_key:string,role_name:string}> */
    public function getAllRoles(): array
    {
        return $this->pdo->query('SELECT id, role_key, role_name FROM roles ORDER BY id')->fetchAll();
    }
}

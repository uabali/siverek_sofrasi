<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use PDO;

final class CommentRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::pdo();
    }

    public function findByRecipeId(int $recipeId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, u.name as user_name 
             FROM comments c 
             LEFT JOIN users u ON c.user_id = u.id 
             WHERE c.recipe_id = :recipe_id 
             ORDER BY c.id DESC'
        );
        $stmt->execute([':recipe_id' => $recipeId]);
        return $stmt->fetchAll();
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, r.title as recipe_title 
             FROM comments c 
             LEFT JOIN recipes r ON c.recipe_id = r.id 
             WHERE c.user_id = :user_id 
             ORDER BY c.id DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findAll(): array
    {
        return $this->pdo->query(
            'SELECT c.*, u.name as user_name, r.title as recipe_title 
             FROM comments c 
             LEFT JOIN users u ON c.user_id = u.id 
             LEFT JOIN recipes r ON c.recipe_id = r.id 
             ORDER BY c.id DESC'
        )->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM comments WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function create(int $userId, int $recipeId, string $content, int $rating): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO comments (user_id, recipe_id, content, rating, created_at) 
             VALUES (:user_id, :recipe_id, :content, :rating, :created_at)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':recipe_id' => $recipeId,
            ':content' => $content,
            ':rating' => $rating,
            ':created_at' => gmdate('c'),
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, string $content, int $rating): bool
    {
        $stmt = $this->pdo->prepare('UPDATE comments SET content = :content, rating = :rating WHERE id = :id');
        return $stmt->execute([':id' => $id, ':content' => $content, ':rating' => $rating]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM comments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getAverageRating(int $recipeId): float
    {
        $stmt = $this->pdo->prepare('SELECT AVG(rating) FROM comments WHERE recipe_id = :recipe_id');
        $stmt->execute([':recipe_id' => $recipeId]);
        return round((float)$stmt->fetchColumn(), 1);
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Connection;
use PDO;

final class RecipeRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::pdo();
    }

    /** @return array<array{id:int,title:string,slug:string,description:string,user_id:int,user_name:string,category_name:string,created_at:string}> */
    public function findAll(): array
    {
        $sql = 'SELECT r.*, u.name as user_name, c.name as category_name 
                FROM recipes r 
                LEFT JOIN users u ON r.user_id = u.id 
                LEFT JOIN categories c ON r.category_id = c.id 
                ORDER BY r.id DESC';
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, u.name as user_name, c.name as category_name 
             FROM recipes r 
             LEFT JOIN users u ON r.user_id = u.id 
             LEFT JOIN categories c ON r.category_id = c.id 
             WHERE r.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, u.name as user_name, c.name as category_name 
             FROM recipes r 
             LEFT JOIN users u ON r.user_id = u.id 
             LEFT JOIN categories c ON r.category_id = c.id 
             WHERE r.slug = :slug'
        );
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return is_array($row) ? $row : null;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT r.*, c.name as category_name 
             FROM recipes r 
             LEFT JOIN categories c ON r.category_id = c.id 
             WHERE r.user_id = :user_id 
             ORDER BY r.id DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $slug = $this->createSlug($data['title']);
        
        $stmt = $this->pdo->prepare(
            'INSERT INTO recipes (title, slug, description, instructions, prep_time_minutes, cook_time_minutes, cover_image, user_id, category_id, created_at)
             VALUES (:title, :slug, :description, :instructions, :prep_time, :cook_time, :cover_image, :user_id, :category_id, :created_at)'
        );
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $slug,
            ':description' => $data['description'] ?? '',
            ':instructions' => $data['instructions'] ?? '',
            ':prep_time' => (int)($data['prep_time_minutes'] ?? 0),
            ':cook_time' => (int)($data['cook_time_minutes'] ?? 0),
            ':cover_image' => $data['cover_image'] ?? null,
            ':user_id' => (int)$data['user_id'],
            ':category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            ':created_at' => gmdate('c'),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE recipes SET 
                title = :title, 
                description = :description, 
                instructions = :instructions,
                prep_time_minutes = :prep_time,
                cook_time_minutes = :cook_time,
                category_id = :category_id
             WHERE id = :id'
        );
        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':description' => $data['description'] ?? '',
            ':instructions' => $data['instructions'] ?? '',
            ':prep_time' => (int)($data['prep_time_minutes'] ?? 0),
            ':cook_time' => (int)($data['cook_time_minutes'] ?? 0),
            ':category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
        ]);
    }

    public function delete(int $id): bool
    {
        // Önce yorumları sil
        $this->pdo->prepare('DELETE FROM comments WHERE recipe_id = :id')->execute([':id' => $id]);
        // Sonra tarifi sil
        $stmt = $this->pdo->prepare('DELETE FROM recipes WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function createSlug(string $title): string
    {
        $slug = mb_strtolower($title);
        $slug = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], 
                           ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Benzersiz yap
        $original = $slug;
        $counter = 1;
        while ($this->findBySlug($slug) !== null) {
            $slug = $original . '-' . $counter++;
        }
        return $slug;
    }

    /** @return array<array{id:int,name:string}> */
    public function getAllCategories(): array
    {
        return $this->pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
    }
}

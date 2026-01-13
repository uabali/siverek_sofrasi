<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

final class Connection
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        /** @var array{default:string, sqlite:array{path:string}, oracle?:array{host:string,port:int,service:string,username:string,password:string}} $config */
        $config = require __DIR__ . '/../../config/database.php';

        try {
            $driver = (string)($config['default'] ?? 'sqlite');

            if ($driver === 'oracle') {
                if (!in_array('oci', PDO::getAvailableDrivers(), true)) {
                    throw new PDOException('Oracle seçildi ama PDO_OCI (oci) driver yüklü değil. Instant Client + pdo_oci kurulumu gerekiyor.');
                }

                $ora = $config['oracle'] ?? null;
                $host = (string)($ora['host'] ?? 'oracle-db');
                $port = (int)($ora['port'] ?? 1521);
                $service = (string)($ora['service'] ?? 'XEPDB1');
                $username = (string)($ora['username'] ?? 'system');
                $password = (string)($ora['password'] ?? 'oracle123');

                $dsn = sprintf('oci:dbname=//%s:%d/%s;charset=AL32UTF8', $host, $port, $service);
                self::$pdo = new PDO($dsn, $username, $password);
            } else {
                $path = (string)($config['sqlite']['path'] ?? (__DIR__ . '/../../storage/app.sqlite'));
                $dir = dirname($path);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $dsn = 'sqlite:' . $path;
                self::$pdo = new PDO($dsn);
                self::initSchema(self::$pdo);
            }

            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('DB bağlantısı kurulamadı: ' . $e->getMessage(), (int)$e->getCode(), $e);
        }
        return self::$pdo;
    }

    private static function initSchema(PDO $pdo): void
    {
        // ROLES tablosu
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                role_key TEXT NOT NULL UNIQUE,
                role_name TEXT NOT NULL
            )'
        );

        // USERS tablosu (role_id ile ilişkili)
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role_id INTEGER NOT NULL DEFAULT 3,
                created_at TEXT NOT NULL,
                FOREIGN KEY (role_id) REFERENCES roles(id)
            )'
        );

        // CATEGORIES tablosu
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at TEXT NOT NULL
            )'
        );

        // RECIPES tablosu (user_id ve category_id ile ilişkili)
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS recipes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                slug TEXT NOT NULL UNIQUE,
                description TEXT,
                instructions TEXT,
                prep_time_minutes INTEGER DEFAULT 0,
                cook_time_minutes INTEGER DEFAULT 0,
                cover_image TEXT,
                user_id INTEGER NOT NULL,
                category_id INTEGER,
                created_at TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )'
        );

        // COMMENTS tablosu (user_id ve recipe_id ile ilişkili)
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS comments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                content TEXT NOT NULL,
                rating INTEGER CHECK(rating >= 1 AND rating <= 5),
                user_id INTEGER NOT NULL,
                recipe_id INTEGER NOT NULL,
                created_at TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (recipe_id) REFERENCES recipes(id)
            )'
        );

        // Varsayılan rolleri ekle
        $pdo->exec("INSERT OR IGNORE INTO roles (id, role_key, role_name) VALUES (1, 'admin', 'Yönetici')");
        $pdo->exec("INSERT OR IGNORE INTO roles (id, role_key, role_name) VALUES (2, 'chef', 'Şef')");
        $pdo->exec("INSERT OR IGNORE INTO roles (id, role_key, role_name) VALUES (3, 'customer', 'Müşteri')");

        // Varsayılan kategoriler
        $pdo->exec("INSERT OR IGNORE INTO categories (id, name, created_at) VALUES (1, 'Ana Yemek', datetime('now'))");
        $pdo->exec("INSERT OR IGNORE INTO categories (id, name, created_at) VALUES (2, 'Çorba', datetime('now'))");
        $pdo->exec("INSERT OR IGNORE INTO categories (id, name, created_at) VALUES (3, 'Tatlı', datetime('now'))");
        $pdo->exec("INSERT OR IGNORE INTO categories (id, name, created_at) VALUES (4, 'Salata', datetime('now'))");
    }
}

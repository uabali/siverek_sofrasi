<?php

declare(strict_types=1);

require __DIR__ . '/../public/bootstrap.php';

$pdo = App\Database\Connection::pdo();

// Varsayılan admin kullanıcısı oluştur (yoksa)
$adminCheck = $pdo->query("SELECT COUNT(*) FROM users WHERE email = 'admin@siverek.com'")->fetchColumn();
if ($adminCheck == 0) {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT INTO users (name, email, password_hash, role_id, created_at) VALUES ('Admin', 'admin@siverek.com', '$hash', 1, datetime('now'))");
    echo "✅ Admin kullanıcısı oluşturuldu: admin@siverek.com / admin123" . PHP_EOL . PHP_EOL;
}

echo "=== ROLLER ===" . PHP_EOL;
$roles = $pdo->query('SELECT * FROM roles')->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $r) {
    echo "  {$r['id']}. {$r['role_name']} ({$r['role_key']})" . PHP_EOL;
}

echo PHP_EOL . "=== KATEGORİLER ===" . PHP_EOL;
$cats = $pdo->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
foreach ($cats as $c) {
    echo "  {$c['id']}. {$c['name']}" . PHP_EOL;
}

echo PHP_EOL . "=== KULLANICI TABLOSU ===" . PHP_EOL;
$users = $pdo->query('SELECT u.id, u.name, u.email, r.role_name, u.created_at FROM users u LEFT JOIN roles r ON u.role_id = r.id')->fetchAll(PDO::FETCH_ASSOC);

if (empty($users)) {
    echo "Henüz kullanıcı yok." . PHP_EOL;
} else {
    echo str_pad("ID", 5) . str_pad("Ad", 15) . str_pad("Email", 25) . str_pad("Rol", 12) . "Tarih" . PHP_EOL;
    echo str_repeat("-", 70) . PHP_EOL;
    foreach ($users as $u) {
        echo str_pad((string)$u['id'], 5);
        echo str_pad((string)$u['name'], 15);
        echo str_pad((string)$u['email'], 25);
        echo str_pad((string)($u['role_name'] ?? '-'), 12);
        echo substr((string)$u['created_at'], 0, 10) . PHP_EOL;
    }
}
echo PHP_EOL . "Toplam: " . count($users) . " kullanıcı" . PHP_EOL;

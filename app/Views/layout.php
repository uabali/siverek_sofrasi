<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Siverek Sofrası') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="navbar">
    <div class="navbar-inner">
        <a href="/" class="brand">Siverek Sofrası</a>
        <button class="nav-toggle" aria-label="Menüyü aç/kapat">
            <span></span><span></span><span></span>
        </button>
        <nav class="nav-menu">
            <a href="/">Anasayfa</a>
            <?php if (!empty($_SESSION['user'])): ?>
                <?php $role = $_SESSION['user']['role'] ?? 'customer'; ?>
                <?php if ($role === 'admin'): ?>
                    <a href="/admin">Admin Panel</a>
                <?php endif; ?>
                <?php if ($role === 'chef' || $role === 'admin'): ?>
                    <a href="/chef">Tariflerim</a>
                <?php endif; ?>
                <a href="/my-comments">Yorumlarım</a>
                <span style="padding: 10px 12px; color: #334155;">
                    👤 <?= htmlspecialchars((string)($_SESSION['user']['name'] ?? '')) ?>
                    <small>(<?= $role === 'admin' ? 'Yönetici' : ($role === 'chef' ? 'Şef' : 'Müşteri') ?>)</small>
                </span>
                <a href="/logout">Çıkış</a>
            <?php else: ?>
                <a href="/login">Giriş</a>
                <a href="/register" class="btn-primary">Kayıt Ol</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-container">
    <?php if (!empty($flash['success'])): ?>
        <div class="flash flash-success"><?= htmlspecialchars($flash['success']) ?></div>
    <?php endif; ?>
    <?php if (!empty($flash['error'])): ?>
        <div class="flash flash-error"><?= htmlspecialchars($flash['error']) ?></div>
    <?php endif; ?>
    <?php include $viewPath; ?>
</main>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-links">
            <a href="/about">Hakkımızda</a>
            <a href="/contact">İletişim</a>
        </div>
        <p>&copy; <?= date('Y') ?> Siverek Sofrası. Tüm hakları saklıdır.</p>
    </div>
</footer>

<script>
    document.querySelector('.nav-toggle').addEventListener('click', () => {
        document.querySelector('.nav-menu').classList.toggle('open');
    });
</script>

</body>
</html>

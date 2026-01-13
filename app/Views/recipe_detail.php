<?php
// $recipe dizisi controller'dan geliyor
$titleText = isset($recipe['title']) ? (string)$recipe['title'] : 'Tarif';
$desc = isset($recipe['description']) ? (string)$recipe['description'] : '';
$instructions = isset($recipe['instructions']) ? (string)$recipe['instructions'] : '';
$cover = !empty($recipe['cover_image']) ? (string)$recipe['cover_image'] : '/assets/images/logo.png';
$prep = isset($recipe['prep_time_minutes']) ? (int)$recipe['prep_time_minutes'] : 0;
$cook = isset($recipe['cook_time_minutes']) ? (int)$recipe['cook_time_minutes'] : 0;
$rating = isset($avgRating) ? (float)$avgRating : (isset($recipe['average_rating']) ? (float)$recipe['average_rating'] : 0.0);
$comments = $comments ?? [];
?>
<article class="recipe-detail">
    <div class="recipe-header">
        <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($titleText) ?>" class="recipe-cover">
        <div class="recipe-info">
            <h1><?= htmlspecialchars($titleText) ?></h1>
            <p class="recipe-desc"><?= htmlspecialchars($desc) ?></p>
            <div class="recipe-meta">
                <span>🍳 Hazırlık: <?= $prep ?> dk</span>
                <span>🔥 Pişirme: <?= $cook ?> dk</span>
                <span>⏱️ Toplam: <?= $prep + $cook ?> dk</span>
                <span>⭐ <?= number_format($rating, 1) ?></span>
            </div>
        </div>
    </div>

    <section class="recipe-body">
        <h2>Yapılışı</h2>
        <?php if ($instructions): ?>
            <p><?= nl2br(htmlspecialchars($instructions)) ?></p>
        <?php else: ?>
            <p class="no-data">Tarif adımları henüz eklenmedi.</p>
        <?php endif; ?>
    </section>

    <!-- Yorumlar -->
    <section class="comments-section">
        <h2>💬 Yorumlar (<?= count($comments) ?>)</h2>
        
        <?php if (!empty($_SESSION['user'])): ?>
        <form method="post" class="comment-form">
            <textarea name="content" placeholder="Yorumunuzu yazın..." required></textarea>
            <div class="comment-form-footer">
                <label>
                    Puan: 
                    <select name="rating">
                        <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4">⭐⭐⭐⭐ (4)</option>
                        <option value="3">⭐⭐⭐ (3)</option>
                        <option value="2">⭐⭐ (2)</option>
                        <option value="1">⭐ (1)</option>
                    </select>
                </label>
                <button type="submit" class="btn-primary">Yorum Yap</button>
            </div>
        </form>
        <?php else: ?>
        <p class="no-data">Yorum yapmak için <a href="/login">giriş yapın</a>.</p>
        <?php endif; ?>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p class="no-data">Henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
            <?php else: ?>
                <?php foreach ($comments as $c): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <strong><?= htmlspecialchars((string)($c['user_name'] ?? 'Anonim')) ?></strong>
                        <span>⭐ <?= (int)$c['rating'] ?></span>
                        <span class="comment-date"><?= htmlspecialchars(substr((string)$c['created_at'], 0, 10)) ?></span>
                    </div>
                    <p><?= htmlspecialchars((string)$c['content']) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <p style="margin-top: 2rem;"><a href="/">← Ana Sayfaya Dön</a></p>
</article>

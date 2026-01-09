<?php
// $recipes dizisi controller’dan geliyor
?>
<section class="hero">
    <div class="hero-inner">
        <h1>En Popüler Tarifler</h1>
        <p>Lezzetli ve pratik tariflerimizi keşfedin.</p>
    </div>
</section>

<section class="recipe-grid">
    <?php if (empty($recipes)): ?>
        <p class="no-data">Henüz tarif bulunamadı.</p>
    <?php else: ?>
        <?php foreach ($recipes as $recipe): ?>
            <?php
                $slug = isset($recipe['slug']) ? (string)$recipe['slug'] : '';
                $titleText = isset($recipe['title']) ? (string)$recipe['title'] : '';
                $desc = isset($recipe['description']) ? (string)$recipe['description'] : '';
                $cover = !empty($recipe['cover_image']) ? (string)$recipe['cover_image'] : '/assets/images/logo.png';

                $prep = isset($recipe['prep_time_minutes']) ? (int)$recipe['prep_time_minutes'] : 0;
                $cook = isset($recipe['cook_time_minutes']) ? (int)$recipe['cook_time_minutes'] : 0;
                $totalMinutes = $prep + $cook;

                $rating = isset($recipe['average_rating']) ? (float)$recipe['average_rating'] : 0.0;
            ?>
            <article class="card">
                <a href="/recipe/<?= htmlspecialchars($slug) ?>" class="card-image">
                    <img src="<?= htmlspecialchars($cover) ?>" alt="<?= htmlspecialchars($titleText) ?>">
                </a>
                <div class="card-content">
                    <h2 class="card-title">
                        <a href="/recipe/<?= htmlspecialchars($slug) ?>">
                            <?= htmlspecialchars($titleText) ?>
                        </a>
                    </h2>
                    <p class="card-desc"><?= htmlspecialchars(mb_substr($desc, 0, 120)) ?>…</p>
                    <div class="card-meta">
                        <span>⏱️ <?= $totalMinutes ?> dk</span>
                        <span>⭐ <?= number_format($rating, 1) ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

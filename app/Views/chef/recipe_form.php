<section class="auth" style="max-width: 600px;">
    <h1><?= $editRecipe ? 'Tarif Düzenle' : 'Yeni Tarif Ekle' ?></h1>

    <form method="post" class="auth-form">
        <label>
            Tarif Adı
            <input type="text" name="title" value="<?= htmlspecialchars((string)($editRecipe['title'] ?? '')) ?>" required>
        </label>

        <label>
            Kategori
            <select name="category_id">
                <option value="">-- Seçiniz --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= (($editRecipe['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)$cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Açıklama
            <textarea name="description" rows="3"><?= htmlspecialchars((string)($editRecipe['description'] ?? '')) ?></textarea>
        </label>

        <label>
            Hazırlık Süresi (dakika)
            <input type="number" name="prep_time" value="<?= (int)($editRecipe['prep_time_minutes'] ?? 0) ?>" min="0">
        </label>

        <label>
            Pişirme Süresi (dakika)
            <input type="number" name="cook_time" value="<?= (int)($editRecipe['cook_time_minutes'] ?? 0) ?>" min="0">
        </label>

        <label>
            Yapılışı
            <textarea name="instructions" rows="6"><?= htmlspecialchars((string)($editRecipe['instructions'] ?? '')) ?></textarea>
        </label>

        <button type="submit" class="btn-primary"><?= $editRecipe ? 'Güncelle' : 'Tarifi Ekle' ?></button>
    </form>

    <p style="margin-top: 12px;"><a href="/chef/recipes">← Tariflerime Dön</a></p>
</section>

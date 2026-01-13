<section class="admin-panel">
    <h1>👨‍🍳 Tariflerim</h1>
    
    <div class="admin-nav">
        <a href="/chef/recipes/create" class="btn-primary">+ Yeni Tarif Ekle</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Hazırlık</th>
                <th>Pişirme</th>
                <th>Tarih</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($myRecipes)): ?>
                <tr><td colspan="7" class="no-data">Henüz tarif eklemediniz.</td></tr>
            <?php else: ?>
                <?php foreach ($myRecipes as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars((string)$r['title']) ?></td>
                    <td><?= htmlspecialchars((string)($r['category_name'] ?? '-')) ?></td>
                    <td><?= (int)$r['prep_time_minutes'] ?> dk</td>
                    <td><?= (int)$r['cook_time_minutes'] ?> dk</td>
                    <td><?= htmlspecialchars(substr((string)$r['created_at'], 0, 10)) ?></td>
                    <td>
                        <a href="/recipe/<?= htmlspecialchars((string)$r['slug']) ?>" class="btn-sm">Görüntüle</a>
                        <a href="/chef/recipes/<?= (int)$r['id'] ?>/edit" class="btn-sm">Düzenle</a>
                        <a href="/chef/recipes/<?= (int)$r['id'] ?>/delete" class="btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

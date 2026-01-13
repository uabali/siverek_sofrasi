<section class="admin-panel">
    <h1>🍳 Tarif Yönetimi</h1>
    
    <div class="admin-nav">
        <a href="/admin/users">Kullanıcılar</a>
        <a href="/admin/recipes" class="btn-primary">Tarifler</a>
        <a href="/admin/comments">Yorumlar</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Başlık</th>
                <th>Kategori</th>
                <th>Ekleyen</th>
                <th>Tarih</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allRecipes)): ?>
                <tr><td colspan="6" class="no-data">Tarif bulunamadı.</td></tr>
            <?php else: ?>
                <?php foreach ($allRecipes as $r): ?>
                <tr>
                    <td><?= (int)$r['id'] ?></td>
                    <td><?= htmlspecialchars((string)$r['title']) ?></td>
                    <td><?= htmlspecialchars((string)($r['category_name'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($r['user_name'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(substr((string)$r['created_at'], 0, 10)) ?></td>
                    <td>
                        <a href="/recipe/<?= htmlspecialchars((string)$r['slug']) ?>" class="btn-sm">Görüntüle</a>
                        <a href="/admin/recipes/<?= (int)$r['id'] ?>/delete" class="btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

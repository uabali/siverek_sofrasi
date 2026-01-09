<section class="admin-panel">
    <h1>💬 Yorum Yönetimi</h1>
    
    <div class="admin-nav">
        <a href="/admin/users">Kullanıcılar</a>
        <a href="/admin/recipes">Tarifler</a>
        <a href="/admin/comments" class="btn-primary">Yorumlar</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tarif</th>
                <th>Kullanıcı</th>
                <th>Yorum</th>
                <th>Puan</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($allComments)): ?>
                <tr><td colspan="6" class="no-data">Yorum bulunamadı.</td></tr>
            <?php else: ?>
                <?php foreach ($allComments as $c): ?>
                <tr>
                    <td><?= (int)$c['id'] ?></td>
                    <td><?= htmlspecialchars((string)($c['recipe_title'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)($c['user_name'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars(mb_substr((string)$c['content'], 0, 50)) ?>...</td>
                    <td>⭐ <?= (int)$c['rating'] ?></td>
                    <td>
                        <a href="/admin/comments/<?= (int)$c['id'] ?>/delete" class="btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

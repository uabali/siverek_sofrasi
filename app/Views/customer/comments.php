<section class="admin-panel">
    <h1>💬 Yorumlarım</h1>

    <table class="data-table">
        <thead>
            <tr>
                <th>Tarif</th>
                <th>Yorum</th>
                <th>Puan</th>
                <th>Tarih</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($myComments)): ?>
                <tr><td colspan="5" class="no-data">Henüz yorum yapmadınız.</td></tr>
            <?php else: ?>
                <?php foreach ($myComments as $c): ?>
                <tr>
                    <td><?= htmlspecialchars((string)($c['recipe_title'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars((string)$c['content']) ?></td>
                    <td>⭐ <?= (int)$c['rating'] ?></td>
                    <td><?= htmlspecialchars(substr((string)$c['created_at'], 0, 10)) ?></td>
                    <td>
                        <a href="/comment/<?= (int)$c['id'] ?>/delete" class="btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

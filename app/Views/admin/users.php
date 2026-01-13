<section class="admin-panel">
    <h1>👤 Kullanıcı Yönetimi</h1>
    
    <div class="admin-nav">
        <a href="/admin/users" class="btn-primary">Kullanıcılar</a>
        <a href="/admin/recipes">Tarifler</a>
        <a href="/admin/comments">Yorumlar</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Kayıt Tarihi</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="6" class="no-data">Kullanıcı bulunamadı.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= (int)$u['id'] ?></td>
                    <td><?= htmlspecialchars((string)$u['name']) ?></td>
                    <td><?= htmlspecialchars((string)$u['email']) ?></td>
                    <td><span class="role-badge role-<?= htmlspecialchars((string)($u['role_name'] ?? 'Müşteri')) ?>"><?= htmlspecialchars((string)($u['role_name'] ?? 'Müşteri')) ?></span></td>
                    <td><?= htmlspecialchars(substr((string)$u['created_at'], 0, 10)) ?></td>
                    <td>
                        <a href="/admin/users/<?= (int)$u['id'] ?>/edit" class="btn-sm">Düzenle</a>
                        <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                        <a href="/admin/users/<?= (int)$u['id'] ?>/delete" class="btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</section>

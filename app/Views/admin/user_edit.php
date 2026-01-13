<section class="auth">
    <h1>Kullanıcı Düzenle</h1>

    <form method="post" class="auth-form">
        <label>
            Ad Soyad
            <input type="text" name="name" value="<?= htmlspecialchars((string)$editUser['name']) ?>" required>
        </label>

        <label>
            E-posta
            <input type="email" name="email" value="<?= htmlspecialchars((string)$editUser['email']) ?>" required>
        </label>

        <label>
            Rol
            <select name="role_id" required>
                <?php foreach ($roles as $role): ?>
                <option value="<?= (int)$role['id'] ?>" <?= $editUser['role_id'] == $role['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)$role['role_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit" class="btn-primary">Güncelle</button>
    </form>

    <p style="margin-top: 12px;"><a href="/admin/users">← Kullanıcı Listesine Dön</a></p>
</section>

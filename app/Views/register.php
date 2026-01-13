<section class="auth">
    <h1>Kayıt Ol</h1>

    <form method="post" action="/register" class="auth-form">
        <label>
            Ad Soyad
            <input type="text" name="name" required>
        </label>

        <label>
            E-posta
            <input type="email" name="email" required>
        </label>

        <label>
            Şifre
            <input type="password" name="password" minlength="6" required>
        </label>

        <button type="submit" class="btn-primary">Hesap Oluştur</button>
    </form>

    <p style="margin-top: 12px;">Zaten hesabın var mı? <a href="/login">Giriş Yap</a></p>
</section>

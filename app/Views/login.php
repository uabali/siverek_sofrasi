<section class="auth">
    <h1>Giriş</h1>

    <form method="post" action="/login" class="auth-form">
        <label>
            E-posta
            <input type="email" name="email" required>
        </label>

        <label>
            Şifre
            <input type="password" name="password" required>
        </label>

        <button type="submit" class="btn-primary">Giriş Yap</button>
    </form>

    <p style="margin-top: 12px;">Hesabın yok mu? <a href="/register">Kayıt Ol</a></p>
</section>

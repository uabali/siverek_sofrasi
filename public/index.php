<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

session_start();

function redirect(string $to): never
{
    header('Location: ' . $to, true, 302);
    exit;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return is_string($val) ? $val : null;
}

// Demo datası (DB bağlanınca burası değişecek)
$recipes = [
    [
        'title' => 'Siverek Tava',
        'slug' => 'siverek-tava',
        'description' => 'Siverek yöresine özgü, kuşbaşı et ve sebzelerle yapılan geleneksel tava yemeği.',
        'cover_image' => '/assets/images/siverek-tava.svg',
        'prep_time_minutes' => 15,
        'cook_time_minutes' => 30,
        'average_rating' => 4.8,
    ],
    [
        'title' => 'Spagetti',
        'slug' => 'spagetti',
        'description' => 'Lezzetli domates soslu spagetti.',
        'cover_image' => '/assets/images/spaghetti.svg',
        'prep_time_minutes' => 10,
        'cook_time_minutes' => 15,
        'average_rating' => 4.6,
    ],
    [
        'title' => 'Köfte',
        'slug' => 'kofte',
        'description' => 'Izgara köfte tarifi.',
        'cover_image' => '/assets/images/kofte.svg',
        'prep_time_minutes' => 20,
        'cook_time_minutes' => 25,
        'average_rating' => 4.3,
    ],
];

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? rtrim($path, '/') : '';
$path = $path === '' ? '/' : $path;

// Türkçe kısa yollar
if ($path === '/giris') {
    $path = '/login';
}
if ($path === '/kayit-ol') {
    $path = '/register';
}

$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Auth (DB-backed)
if ($path === '/login' && $method === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        flash('error', 'E-posta ve şifre zorunludur.');
        redirect('/login');
    }

    try {
        $repo = new App\Auth\UserRepository();
        $user = $repo->findByEmail($email);
        if ($user === null || !password_verify($password, (string)$user['password_hash'])) {
            flash('error', 'E-posta veya şifre hatalı.');
            redirect('/login');
        }

        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'email' => (string)$user['email'],
            'name' => (string)$user['name'],
            'role_id' => (int)($user['role_id'] ?? 3),
            'role' => (string)($user['role_key'] ?? 'customer'),
        ];
        flash('success', 'Giriş başarılı.');
        
        // Role göre yönlendir
        $role = $_SESSION['user']['role'];
        if ($role === 'admin') {
            redirect('/admin');
        } elseif ($role === 'chef') {
            redirect('/chef');
        } else {
            redirect('/');
        }
    } catch (Throwable $e) {
        flash('error', 'Giriş sırasında hata: ' . $e->getMessage());
        redirect('/login');
    }
}

if ($path === '/register' && $method === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        flash('error', 'Tüm alanlar zorunludur.');
        redirect('/register');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Geçerli bir e-posta gir.');
        redirect('/register');
    }

    if (mb_strlen($password) < 6) {
        flash('error', 'Şifre en az 6 karakter olmalı.');
        redirect('/register');
    }

    try {
        $repo = new App\Auth\UserRepository();
        $existing = $repo->findByEmail($email);
        if ($existing !== null) {
            flash('error', 'Bu e-posta ile zaten kayıt var.');
            redirect('/register');
        }

        $repo->create($name, $email, $password);
        flash('success', 'Kayıt oluşturuldu. Şimdi giriş yapabilirsin.');
        redirect('/login');
    } catch (Throwable $e) {
        flash('error', 'Kayıt sırasında hata: ' . $e->getMessage());
        redirect('/register');
    }
}

if ($path === '/logout') {
    unset($_SESSION['user']);
    flash('success', 'Çıkış yapıldı.');
    redirect('/');
}

// Helper: Rol kontrolü
function requireRole(string ...$roles): void
{
    if (empty($_SESSION['user'])) {
        flash('error', 'Bu sayfaya erişmek için giriş yapmalısınız.');
        redirect('/login');
    }
    $userRole = $_SESSION['user']['role'] ?? 'customer';
    if (!in_array($userRole, $roles, true)) {
        flash('error', 'Bu sayfaya erişim yetkiniz yok.');
        redirect('/');
    }
}

// ==================== ADMIN CRUD ====================
if (str_starts_with($path, '/admin')) {
    requireRole('admin');
    
    // Admin: Kullanıcı listesi
    if ($path === '/admin' || $path === '/admin/users') {
        $repo = new App\Auth\UserRepository();
        $users = $repo->findAll();
        $viewPath = __DIR__ . '/../app/Views/admin/users.php';
        $title = 'Admin - Kullanıcılar';
    }
    // Admin: Kullanıcı düzenle
    elseif (preg_match('#^/admin/users/(\d+)/edit$#', $path, $m)) {
        $repo = new App\Auth\UserRepository();
        $editUser = $repo->findById((int)$m[1]);
        $roles = $repo->getAllRoles();
        if (!$editUser) {
            flash('error', 'Kullanıcı bulunamadı.');
            redirect('/admin/users');
        }
        if ($method === 'POST') {
            $repo->update((int)$m[1], $_POST['name'], $_POST['email'], (int)$_POST['role_id']);
            flash('success', 'Kullanıcı güncellendi.');
            redirect('/admin/users');
        }
        $viewPath = __DIR__ . '/../app/Views/admin/user_edit.php';
        $title = 'Kullanıcı Düzenle';
    }
    // Admin: Kullanıcı sil
    elseif (preg_match('#^/admin/users/(\d+)/delete$#', $path, $m)) {
        $repo = new App\Auth\UserRepository();
        $repo->delete((int)$m[1]);
        flash('success', 'Kullanıcı silindi.');
        redirect('/admin/users');
    }
    // Admin: Tarifler
    elseif ($path === '/admin/recipes') {
        $repo = new App\Repository\RecipeRepository();
        $allRecipes = $repo->findAll();
        $viewPath = __DIR__ . '/../app/Views/admin/recipes.php';
        $title = 'Admin - Tarifler';
    }
    // Admin: Tarif sil
    elseif (preg_match('#^/admin/recipes/(\d+)/delete$#', $path, $m)) {
        $repo = new App\Repository\RecipeRepository();
        $repo->delete((int)$m[1]);
        flash('success', 'Tarif silindi.');
        redirect('/admin/recipes');
    }
    // Admin: Yorumlar
    elseif ($path === '/admin/comments') {
        $repo = new App\Repository\CommentRepository();
        $allComments = $repo->findAll();
        $viewPath = __DIR__ . '/../app/Views/admin/comments.php';
        $title = 'Admin - Yorumlar';
    }
    // Admin: Yorum sil
    elseif (preg_match('#^/admin/comments/(\d+)/delete$#', $path, $m)) {
        $repo = new App\Repository\CommentRepository();
        $repo->delete((int)$m[1]);
        flash('success', 'Yorum silindi.');
        redirect('/admin/comments');
    }
    else {
        redirect('/admin/users');
    }
}

// ==================== CHEF CRUD ====================
elseif (str_starts_with($path, '/chef')) {
    requireRole('chef', 'admin');
    $userId = (int)$_SESSION['user']['id'];
    
    // Chef: Tariflerim
    if ($path === '/chef' || $path === '/chef/recipes') {
        $repo = new App\Repository\RecipeRepository();
        $myRecipes = $repo->findByUserId($userId);
        $viewPath = __DIR__ . '/../app/Views/chef/recipes.php';
        $title = 'Şef - Tariflerim';
    }
    // Chef: Yeni tarif
    elseif ($path === '/chef/recipes/create') {
        $repo = new App\Repository\RecipeRepository();
        $categories = $repo->getAllCategories();
        if ($method === 'POST') {
            $repo->create([
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'instructions' => $_POST['instructions'],
                'prep_time_minutes' => $_POST['prep_time'],
                'cook_time_minutes' => $_POST['cook_time'],
                'category_id' => $_POST['category_id'],
                'user_id' => $userId,
            ]);
            flash('success', 'Tarif eklendi.');
            redirect('/chef/recipes');
        }
        $viewPath = __DIR__ . '/../app/Views/chef/recipe_form.php';
        $title = 'Yeni Tarif Ekle';
        $editRecipe = null;
    }
    // Chef: Tarif düzenle
    elseif (preg_match('#^/chef/recipes/(\d+)/edit$#', $path, $m)) {
        $repo = new App\Repository\RecipeRepository();
        $editRecipe = $repo->findById((int)$m[1]);
        $categories = $repo->getAllCategories();
        if (!$editRecipe || $editRecipe['user_id'] != $userId) {
            flash('error', 'Tarif bulunamadı veya yetkiniz yok.');
            redirect('/chef/recipes');
        }
        if ($method === 'POST') {
            $repo->update((int)$m[1], [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'instructions' => $_POST['instructions'],
                'prep_time_minutes' => $_POST['prep_time'],
                'cook_time_minutes' => $_POST['cook_time'],
                'category_id' => $_POST['category_id'],
            ]);
            flash('success', 'Tarif güncellendi.');
            redirect('/chef/recipes');
        }
        $viewPath = __DIR__ . '/../app/Views/chef/recipe_form.php';
        $title = 'Tarif Düzenle';
    }
    // Chef: Tarif sil
    elseif (preg_match('#^/chef/recipes/(\d+)/delete$#', $path, $m)) {
        $repo = new App\Repository\RecipeRepository();
        $recipe = $repo->findById((int)$m[1]);
        if ($recipe && $recipe['user_id'] == $userId) {
            $repo->delete((int)$m[1]);
            flash('success', 'Tarif silindi.');
        }
        redirect('/chef/recipes');
    }
    else {
        redirect('/chef/recipes');
    }
}

// ==================== CUSTOMER: Yorum CRUD ====================
elseif ($path === '/my-comments') {
    requireRole('customer', 'chef', 'admin');
    $repo = new App\Repository\CommentRepository();
    $myComments = $repo->findByUserId((int)$_SESSION['user']['id']);
    $viewPath = __DIR__ . '/../app/Views/customer/comments.php';
    $title = 'Yorumlarım';
}
elseif (preg_match('#^/comment/(\d+)/delete$#', $path, $m)) {
    requireRole('customer', 'chef', 'admin');
    $repo = new App\Repository\CommentRepository();
    $comment = $repo->findById((int)$m[1]);
    if ($comment && $comment['user_id'] == $_SESSION['user']['id']) {
        $repo->delete((int)$m[1]);
        flash('success', 'Yorum silindi.');
    }
    redirect('/my-comments');
}

// ==================== PUBLIC ROUTES ====================
elseif ($path === '/') {
    // Ana sayfa: DB'den tarifleri çek
    $repo = new App\Repository\RecipeRepository();
    $dbRecipes = $repo->findAll();
    if (!empty($dbRecipes)) {
        $recipes = $dbRecipes;
    }
    $viewPath = __DIR__ . '/../app/Views/home.php';
    $title = 'Ana Sayfa';
} elseif ($path === '/search') {
    $viewPath = __DIR__ . '/../app/Views/search.php';
    $title = 'Tarif Ara';
    $q = isset($_GET['q']) ? (string)$_GET['q'] : '';
} elseif ($path === '/about') {
    $viewPath = __DIR__ . '/../app/Views/about.php';
    $title = 'Hakkımızda';
} elseif ($path === '/contact') {
    $viewPath = __DIR__ . '/../app/Views/contact.php';
    $title = 'İletişim';
} elseif ($path === '/login') {
    $viewPath = __DIR__ . '/../app/Views/login.php';
    $title = 'Giriş';
} elseif ($path === '/register') {
    $viewPath = __DIR__ . '/../app/Views/register.php';
    $title = 'Kayıt Ol';
} elseif (preg_match('#^/recipe/([a-z0-9\-]+)$#i', $path, $m)) {
    $slug = $m[1];
    
    // Önce DB'den bak
    $repo = new App\Repository\RecipeRepository();
    $recipe = $repo->findBySlug($slug);
    
    // DB'de yoksa demo dataya bak
    if (!$recipe) {
        foreach ($recipes as $r) {
            if (($r['slug'] ?? '') === $slug) {
                $recipe = $r;
                break;
            }
        }
    }

    if ($recipe === null) {
        http_response_code(404);
        $viewPath = __DIR__ . '/../app/Views/404.php';
        $title = 'Bulunamadı';
    } else {
        // Yorumları çek
        $commentRepo = new App\Repository\CommentRepository();
        $comments = $commentRepo->findByRecipeId((int)($recipe['id'] ?? 0));
        $avgRating = $commentRepo->getAverageRating((int)($recipe['id'] ?? 0));
        
        // Yorum ekleme
        if ($method === 'POST' && !empty($_SESSION['user'])) {
            $commentRepo->create(
                (int)$_SESSION['user']['id'],
                (int)$recipe['id'],
                trim($_POST['content'] ?? ''),
                (int)($_POST['rating'] ?? 5)
            );
            flash('success', 'Yorumunuz eklendi.');
            redirect('/recipe/' . $slug);
        }
        
        $viewPath = __DIR__ . '/../app/Views/recipe_detail.php';
        $title = (string)($recipe['title'] ?? 'Tarif');
    }
} else {
    http_response_code(404);
    $viewPath = __DIR__ . '/../app/Views/404.php';
    $title = 'Bulunamadı';
}

$flash = [
    'success' => flash('success'),
    'error' => flash('error'),
];

include __DIR__ . '/../app/Views/layout.php';
# Siverek Sofrası - Tarif Paylaşım Web Uygulaması

## 📋 Proje Bilgileri

| Bilgi | Değer |
|-------|-------|
| **Proje Adı** | Siverek Sofrası |
| **Geliştirici** | Umut ABALI |
| **Öğrenci No** | 22120205040 |
| **İletişim** | abaliumut@outlook.com |
| **Teknoloji** | PHP 8.2, SQLite, Docker |
| **Mimari** | MVC (Model-View-Controller) |

---

## 📖 Proje Açıklaması

Siverek Sofrası, kullanıcıların yemek tariflerini paylaşabildiği, yorum yapabildiği ve puanlayabildiği bir web uygulamasıdır. Proje, **rol tabanlı yetkilendirme sistemi** ile farklı kullanıcı tiplerine farklı yetkiler sunmaktadır.

---

## 🛠️ Kullanılan Teknolojiler

| Kategori | Teknoloji |
|----------|-----------|
| **Backend** | PHP 8.2 |
| **Veritabanı** | SQLite (DB First yaklaşımı) |
| **Web Sunucusu** | Apache (mod_rewrite) |
| **Container** | Docker, Docker Compose |
| **Frontend** | HTML5, CSS3, Vanilla JS |

---

## 🗄️ Veritabanı Tasarımı (İlişkisel)

### ER Diyagramı

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│   ROLES     │         │   USERS     │         │ CATEGORIES  │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id (PK)     │◄───┐    │ id (PK)     │    ┌───►│ id (PK)     │
│ role_key    │    └────│ role_id(FK) │    │    │ name        │
│ role_name   │         │ name        │    │    │ created_at  │
└─────────────┘         │ email       │    │    └─────────────┘
                        │ password    │    │
                        │ created_at  │    │
                        └──────┬──────┘    │
                               │           │
              ┌────────────────┼───────────┘
              │                │
              ▼                ▼
      ┌───────────────┐  ┌─────────────┐
      │   COMMENTS    │  │   RECIPES   │
      ├───────────────┤  ├─────────────┤
      │ id (PK)       │  │ id (PK)     │
      │ user_id (FK)  │──│ user_id(FK) │
      │ recipe_id(FK) │──│ category_id │
      │ content       │  │ title       │
      │ rating (1-5)  │  │ slug        │
      │ created_at    │  │ description │
      └───────────────┘  │ instructions│
                         │ prep_time   │
                         │ cook_time   │
                         │ created_at  │
                         └─────────────┘
```

### Tablolar ve İlişkiler

| Tablo | Açıklama | İlişki |
|-------|----------|--------|
| **roles** | Kullanıcı rolleri | 1:N → users |
| **users** | Kullanıcı bilgileri | N:1 → roles, 1:N → recipes, 1:N → comments |
| **categories** | Tarif kategorileri | 1:N → recipes |
| **recipes** | Yemek tarifleri | N:1 → users, N:1 → categories, 1:N → comments |
| **comments** | Kullanıcı yorumları | N:1 → users, N:1 → recipes |

---

## 👥 Rol Tabanlı Yetkilendirme Sistemi

Sistemde **3 farklı kullanıcı rolü** bulunmaktadır:

### 1. Yönetici (Admin)
| İşlem | Yetki |
|-------|-------|
| Kullanıcı Listele | ✅ |
| Kullanıcı Düzenle | ✅ |
| Kullanıcı Sil | ✅ |
| Tarif Listele | ✅ |
| Tarif Sil | ✅ |
| Yorum Listele | ✅ |
| Yorum Sil | ✅ |

### 2. Şef (Chef)
| İşlem | Yetki |
|-------|-------|
| Tarif Ekle | ✅ |
| Kendi Tariflerini Listele | ✅ |
| Kendi Tariflerini Düzenle | ✅ |
| Kendi Tariflerini Sil | ✅ |
| Yorum Yap | ✅ |

### 3. Müşteri (Customer)
| İşlem | Yetki |
|-------|-------|
| Tarifleri Görüntüle | ✅ |
| Yorum Yap | ✅ |
| Kendi Yorumlarını Listele | ✅ |
| Kendi Yorumlarını Sil | ✅ |

---

## 📝 CRUD İşlemleri

### Admin Paneli (/admin)
```
CREATE  : Kullanıcı ekleme (kayıt sistemi üzerinden)
READ    : Tüm kullanıcıları, tarifleri, yorumları listeleme
UPDATE  : Kullanıcı bilgilerini ve rolünü güncelleme
DELETE  : Kullanıcı, tarif ve yorum silme
```

### Şef Paneli (/chef)
```
CREATE  : Yeni tarif ekleme
READ    : Kendi tariflerini listeleme
UPDATE  : Kendi tariflerini düzenleme
DELETE  : Kendi tariflerini silme
```

### Müşteri İşlemleri
```
CREATE  : Tariflere yorum ekleme
READ    : Kendi yorumlarını listeleme (/my-comments)
UPDATE  : - (Yorum düzenleme yok)
DELETE  : Kendi yorumlarını silme
```

---

## 📁 Proje Yapısı

```
siverek-sofrasi/
├── app/
│   ├── Auth/
│   │   └── UserRepository.php      # Kullanıcı CRUD işlemleri
│   ├── Database/
│   │   └── Connection.php          # PDO veritabanı bağlantısı
│   ├── Repository/
│   │   ├── RecipeRepository.php    # Tarif CRUD işlemleri
│   │   └── CommentRepository.php   # Yorum CRUD işlemleri
│   └── Views/
│       ├── admin/                  # Admin panel sayfaları
│       │   ├── users.php
│       │   ├── user_edit.php
│       │   ├── recipes.php
│       │   └── comments.php
│       ├── chef/                   # Şef panel sayfaları
│       │   ├── recipes.php
│       │   └── recipe_form.php
│       ├── customer/               # Müşteri sayfaları
│       │   └── comments.php
│       ├── layout.php              # Ana şablon
│       ├── home.php                # Ana sayfa
│       ├── login.php               # Giriş sayfası
│       ├── register.php            # Kayıt sayfası
│       ├── recipe_detail.php       # Tarif detay
│       └── 404.php                 # Hata sayfası
├── config/
│   └── database.php                # DB yapılandırması
├── docker/
│   ├── docker-compose.yaml         # Container yapılandırması
│   └── php/
│       └── Dockerfile              # PHP image
├── public/
│   ├── index.php                   # Front Controller (Router)
│   ├── bootstrap.php               # Autoloader
│   ├── .htaccess                   # URL Rewrite kuralları
│   └── assets/
│       ├── css/style.css           # Stiller
│       └── images/                 # Görseller
├── scripts/
│   └── db_check.php                # DB kontrol scripti
├── storage/
│   └── app.sqlite                  # SQLite veritabanı
└── README.md                       # Bu dosya
```

---

## 🚀 Kurulum ve Çalıştırma

### Gereksinimler
- Docker Desktop
- Web tarayıcı

### Adımlar

```bash
# 1. Proje dizinine git
cd docker

# 2. Container'ları başlat
docker compose up -d

# 3. Veritabanını kontrol et (opsiyonel)
docker compose exec php-app php /var/www/scripts/db_check.php
```

### Erişim
- **Web Arayüzü:** http://localhost:8080

### Varsayılan Admin Hesabı
| E-posta | Şifre |
|---------|-------|
| admin@siverek.com | admin123 |

---

## 🔐 Güvenlik Özellikleri

- ✅ Şifre hashleme (`password_hash` / `password_verify`)
- ✅ SQL Injection koruması (PDO Prepared Statements)
- ✅ XSS koruması (`htmlspecialchars`)
- ✅ CSRF koruması (Session tabanlı)
- ✅ Rol tabanlı erişim kontrolü

---

## 📸 Ekran Görüntüleri

### Ana Sayfa
- Tarif kartları grid düzeninde
- Responsive tasarım
- Hero banner

### Admin Paneli
- Kullanıcı yönetimi tablosu
- Tarif yönetimi
- Yorum moderasyonu

### Şef Paneli
- Tarif ekleme formu
- Tarif listeleme ve düzenleme

---

## 📊 Teknik Özellikler

| Özellik | Detay |
|---------|-------|
| **Mimari** | MVC (Model-View-Controller) |
| **Routing** | Front Controller Pattern |
| **ORM** | Manuel Repository Pattern |
| **Session** | PHP Native Session |
| **Autoloading** | PSR-4 benzeri (custom) |

---

## 📄 Lisans

Bu proje eğitim amaçlı geliştirilmiştir.

---

**© 2026 Umut ABALI - Tüm hakları saklıdır.**
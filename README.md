# Siverek Sofrasi - Web Application

A web application where users can share recipes, leave comments, and rate dishes. The system implements role-based authorization to provide different permissions for different user types.

## Tech Stack

| Category | Technology |
|----------|------------|
| Backend | PHP 8.2 |
| Database | SQLite |
| Web Server | Apache (mod_rewrite) |
| Container | Docker, Docker Compose |
| Frontend | HTML5, CSS3, JavaScript |
| Architecture | MVC (Model-View-Controller) |

## Database Schema

```
ROLES              USERS              CATEGORIES
-----------        -----------        -----------
id (PK)      <---- role_id (FK)       id (PK)
role_key           id (PK)      ----> category_id
role_name          name               name
                   email              created_at
                   password
                   created_at
                        |
          +-------------+-------------+
          |                           |
          v                           v
     COMMENTS                     RECIPES
     -----------                  -----------
     id (PK)                      id (PK)
     user_id (FK)                 user_id (FK)
     recipe_id (FK)               category_id (FK)
     content                      title
     rating (1-5)                 slug
     created_at                   description
                                  instructions
                                  prep_time
                                  cook_time
                                  created_at
```

### Table Relationships

| Table | Description | Relationship |
|-------|-------------|--------------|
| roles | User roles | 1:N with users |
| users | User accounts | N:1 with roles, 1:N with recipes, 1:N with comments |
| categories | Recipe categories | 1:N with recipes |
| recipes | Recipe entries | N:1 with users, N:1 with categories, 1:N with comments |
| comments | User comments | N:1 with users, N:1 with recipes |

## Role-Based Authorization

The system has 3 user roles:

### Admin
- List, edit, and delete users
- List and delete recipes
- List and delete comments

### Chef
- Create, list, edit, and delete own recipes
- Add comments to recipes

### Customer
- View recipes
- Add comments to recipes
- List and delete own comments

## Project Structure

```
recipeasy/
├── app/
│   ├── Auth/
│   │   └── UserRepository.php
│   ├── Database/
│   │   └── Connection.php
│   ├── Repository/
│   │   ├── RecipeRepository.php
│   │   └── CommentRepository.php
│   └── Views/
│       ├── admin/
│       ├── chef/
│       ├── customer/
│       ├── layout.php
│       ├── home.php
│       ├── login.php
│       ├── register.php
│       ├── recipe_detail.php
│       └── 404.php
├── config/
│   └── database.php
├── docker/
│   ├── docker-compose.yaml
│   └── php/
│       └── Dockerfile
├── public/
│   ├── index.php
│   ├── bootstrap.php
│   ├── .htaccess
│   └── assets/
├── scripts/
│   └── db_check.php
├── storage/
│   └── app.sqlite
└── README.md
```

## Installation

### Requirements
- Docker Desktop

### Setup

```bash
# Navigate to docker directory
cd docker

# Start containers
docker compose up -d

# Verify database (optional)
docker compose exec php-app php /var/www/scripts/db_check.php
```

### Access
- Web Interface: http://localhost:8080

### Default Admin Account
| Email | Password |
|-------|----------|
| admin@siverek.com | admin123 |

## Security Features

- Password hashing with `password_hash` and `password_verify`
- SQL injection protection using PDO prepared statements
- XSS protection with `htmlspecialchars`
- Session-based CSRF protection
- Role-based access control

## License

This project is developed for educational purposes.
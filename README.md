# 🎬 diStreaming — REST API Platform

> REST API untuk platform streaming film berbasis Laravel, dilengkapi dengan autentikasi menggunakan Laravel Sanctum, CRUD Movies & Categories, filter, search, sorting, dan rating classification.

---

## 📌 Table of Contents

- [Deskripsi Project](#-deskripsi-project)
- [Tech Stack](#-tech-stack)
- [Struktur Project](#-struktur-project)
- [Database](#-database)
- [Instalasi & Setup](#-instalasi--setup)
- [Autentikasi](#-autentikasi)
- [API Endpoints](#-api-endpoints)
- [Fitur Bonus](#-fitur-bonus)
- [Response Format](#-response-format)
- [Author](#-author)

---

## 📖 Deskripsi Project

**diStreaming API** adalah REST API yang dibangun menggunakan Laravel untuk mengelola data film dan kategori film pada platform streaming. API ini mendukung operasi CRUD lengkap, autentikasi berbasis token menggunakan Laravel Sanctum, serta fitur tambahan seperti search, filter, sorting, dan rating classification.

Project ini merupakan bagian dari **Mini Project REST API — Dibimbing.id Fullstack Web Development Bootcamp 2026**.

---

## 🛠️ Tech Stack

| Technology | Usage |
|---|---|
| PHP 8.x | Backend language |
| Laravel 12.x | PHP Framework |
| Laravel Sanctum | API Token Authentication |
| MySQL | Database |
| Eloquent ORM | Database interaction |
| Postman | API testing |

---

## 📁 Struktur Project

```
streaming-api/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php        ← Register, Login, Logout
│   │       ├── MovieController.php       ← CRUD Movies + Bonus Features
│   │       └── MovieCategoryController.php ← CRUD Categories
│   └── Models/
│       ├── Movie.php                     ← Model + SoftDeletes + relasi
│       ├── MovieCategory.php             ← Model + relasi
│       └── User.php                      ← Model User
│
├── routes/
│   └── api.php                           ← Semua API routes
│
├── database/
│   └── migrations/                       ← Migration tables
│
└── README.md
```

---

## 🗄️ Database

### Tabel yang digunakan

**`users`** — data pengguna
**`movie_category`** — kategori film
**`movies`** — data film (dengan soft deletes)

### ERD

```
users                  movies                    movie_category
------                 -------                   --------------
id (PK)                id (PK)                   id (PK)
name                   title                     name
email                  description               description
password               rating                    created_at
created_at             release_year              updated_at
updated_at             category_id (FK) ────────→ id
                       thumbnail
                       deleted_at (soft delete)
                       created_at
                       updated_at
```

### Relasi
- `Movie` → `belongsTo` → `MovieCategory`
- `MovieCategory` → `hasMany` → `Movie`
- `Movie` menggunakan **SoftDeletes** (data tidak benar-benar terhapus dari database)

---

## ⚙️ Instalasi & Setup

```bash
# 1. Clone repository
git clone https://github.com/muhamadbn2025-debug/streaming-api.git
cd streaming-api

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_DATABASE=distreaming
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan migration
php artisan migrate

# 7. Install Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 8. Jalankan server
php artisan serve
```

> Base URL: `http://localhost:8000/api`

---

## 🔐 Autentikasi

API ini menggunakan **Laravel Sanctum** untuk autentikasi berbasis token.

### Register
```
POST /api/register
```
```json
{
    "name": "Muhamad Awod",
    "email": "awod@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
```
POST /api/login
```
```json
{
    "email": "awod@example.com",
    "password": "password123"
}
```

Response:
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": { "id": 1, "name": "Muhamad Awod", ... },
        "token": "1|abc123xyz..."
    }
}
```

### Logout
```
POST /api/logout
Authorization: Bearer {token}
```

> ⚠️ Semua endpoint Movies dan Categories memerlukan header:
> `Authorization: Bearer {token}`

---

## 📡 API Endpoints

### 🎬 Movies

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/movies` | Ambil semua film | ✅ |
| GET | `/api/movies/{id}` | Detail film by ID | ✅ |
| POST | `/api/movies` | Tambah film baru | ✅ |
| PUT | `/api/movies/{id}` | Update film | ✅ |
| DELETE | `/api/movies/{id}` | Hapus film (soft delete) | ✅ |

#### POST /api/movies — Request Body
```json
{
    "title": "The Adventure Begins",
    "description": "An epic journey of a lifetime...",
    "rating": 8.7,
    "release_year": 2024,
    "category_id": 1,
    "thumbnail": "adventure.jpg"
}
```

#### GET /api/movies — Response
```json
{
    "success": true,
    "message": "Data film berhasil diambil",
    "data": [
        {
            "id": 1,
            "title": "The Adventure Begins",
            "description": "An epic journey...",
            "rating": 8.7,
            "release_year": 2024,
            "thumbnail": "adventure.jpg",
            "rating_class": "Top Rated",
            "category": {
                "id": 1,
                "name": "Action"
            }
        }
    ]
}
```

---

### 🗂️ Categories

| Method | Endpoint | Deskripsi | Auth |
|---|---|---|---|
| GET | `/api/categories` | Ambil semua kategori | ✅ |
| GET | `/api/categories/{id}` | Detail kategori + daftar film | ✅ |
| POST | `/api/categories` | Tambah kategori baru | ✅ |
| PUT | `/api/categories/{id}` | Update kategori | ✅ |
| DELETE | `/api/categories/{id}` | Hapus kategori | ✅ |

#### POST /api/categories — Request Body
```json
{
    "name": "Action",
    "description": "Film dengan genre aksi dan laga"
}
```

---

## 🎯 Fitur Bonus

### 🔍 Search & Filter

```bash
# Search film berdasarkan judul
GET /api/movies?search=adventure

# Filter berdasarkan kategori
GET /api/movies?category_id=1
```

### 📊 Sorting

```bash
# Urutkan berdasarkan rating (descending)
GET /api/movies?sort_by=rating&order=desc

# Urutkan berdasarkan tahun rilis (ascending)
GET /api/movies?sort_by=release_year&order=asc
```

### ⭐ Rating Classification

Rating classification otomatis ditambahkan ke setiap response film:

| Rating | Class |
|---|---|
| ≥ 8.5 | `Top Rated` |
| 7.0 – 8.4 | `Popular` |
| < 7.0 | `Regular` |

---

## 📋 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Pesan sukses",
    "data": { }
}
```

### Error Response (Validasi)
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "title": ["The title field is required."]
    }
}
```

### Not Found Response
```json
{
    "success": false,
    "message": "Film tidak ditemukan"
}
```

### Unauthorized Response
```json
{
    "message": "Unauthenticated."
}
```

---

## ✅ Validasi Input

### Movie
```
title        → required, string, max:255
description  → required, string
rating       → required, numeric, min:0, max:10
release_year → required, integer, min:1900, max:2030
category_id  → required, exists:movie_category,id
thumbnail    → required, string, max:255
```

### Category
```
name         → required, string, max:100, unique
description  → nullable, string
```

---

## 👤 Author

**Muhamad Awod**
Fullstack Web Developer | Bootcamp Student at Dibimbing.id

- 🌐 GitHub: [github.com/muhamadbn2025-debug](https://github.com/muhamadbn2025-debug)
- 💼 LinkedIn: [linkedin.com/in/muhamad-awod-bn-awod-ali](https://www.linkedin.com/in/muhamad-awod-bn-awod-ali)
- 📧 Email: muhamadbn2025@gmail.com

---

*Built with ❤️ as part of Dibimbing.id Fullstack Web Development Bootcamp 2026*

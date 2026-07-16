# 🛒 Mini E-Commerce Platform

A modern full-stack e-commerce platform featuring authentication, product management, shopping cart, order processing, and an admin dashboard with analytics.

---

# ✨ Features

## 👤 Customer Features
- User Registration & Authentication
- Browse Products and Categories
- Product Search
- Product Details Page
- Shopping Cart Management
- Place Orders (Cash on Delivery)
- Contact Form
- Responsive Design (Mobile & Desktop)

---

## 🛠️ Admin Features
- Admin Authentication
- Dashboard Statistics
- Revenue Analytics Chart
- Manage Products (CRUD)
- Manage Categories (CRUD)
- Manage Orders & Update Status
- View Customer Messages
- Order Notifications
- Recent Orders Overview

---

# 🏗️ Tech Stack

## Backend
- Laravel 12
- PHP 8.2
- MySQL
- Laravel Sanctum
- REST API

## Frontend
- Vue 3 (Composition API)
- Vue Router
- Axios
- Chart.js
- Lucide Icons
- CSS3

---

# 📂 Project Structure

```text
Laravel_Vue_Shop/
│
├── backend/                 # Laravel API
│   ├── app/
│   │    └── Http/
│   │        └── Controllers/
│   │                    └── Api/
│   │                         ├── AdminController.php
│   │                         ├── AuthController.php
│   │                         ├── CartController.php
│   │                         ├── CategoryController.php
│   │                         ├── ContactController.php
│   │                         ├── DebugController.php
│   │                         ├── MessageController.php
│   │                         ├── OrderController.php
│   │                         └── ProductController.php
│   ├── routes/
│           └── Api.php
│   ├── database/
│   └── storage/
│
├── frontend/
│   └── vite-project/
│       ├── src/
│       │     └── components/
│       │           ├── Admin/
│       │           │    ├── AdminLayout.vue
│       │           │    ├── Categories.vue
│       │           │    ├── Dashboard.vue
│       │           │    ├── Messages.vue
│       │           │    ├── Orders.vue
│       │           │    ├── ProductManagement.vue
│       │           │    ├── RevenueChart.vue
│       │           │    └── TopProductsChart.vue
│       │           │
│       │           │
│       │           ├── Auth/
│       │           │     ├── Login.vue
│       │           │     └── Register.vue
│       │           │
│       │           ├── About.vue
│       │           ├── AuthDebug.vue
│       │           ├── Cart.vue
│       │           ├── CategoryProducts.vue
│       │           ├── Checkout.vue
│       │           ├── Contact.vue
│       │           ├── Footer.vue
│       │           ├── Home.vue
│       │           ├── Navbar.vue 
│       │           └── ProductDetails.vue
│       │
│       ├── views/
│       ├── composables/
│                  └── useAuth.js
│       ├── App.vue
│       └── services/
│                └── api.js      
│
└── README.md
```

---

# 🔐 Authentication

Authentication is handled using **Laravel Sanctum**.

### Login Request

```http
POST /login
```

```json
{
  "email": "kssp@gmail.com",
  "password": "azertyqwerty"
}
```

### Login Response

```json
{
  "message": "Logged in successfully",
  "user": {
    "id": 4,
    "name": "kssp",
    "email": "kssp@gmail.com",
    "is_admin": true,
  }
}
```

---

# 📡 Main API Endpoints

## Authentication

| Method | Endpoint | Description |
|--------|-----------|-------------|
| POST | /register | Register new user |
| POST | /login | Login |
| POST | /logout | Logout |
| GET | /user | Authenticated user |

---

## Products

| Method | Endpoint |
|--------|-----------|
| GET | /products |
| GET | /products/{id} |
| GET | /home-products |
| GET | /products/search |

---

## Categories

| Method | Endpoint |
|--------|-----------|
| GET | /categories |
| GET | /category/{id} |

---

## Cart

| Method | Endpoint |
|--------|-----------|
| GET | /cart |
| POST | /cart |
| PUT | /cart/{id} |
| DELETE | /cart/{id} |
| GET | /cart/count |

---

## Orders

| Method | Endpoint |
|--------|-----------|
| POST | /orders |
| GET | /orders |
| GET | /admin/orders |

---

## Contact

| Method | Endpoint |
|--------|-----------|
| POST | /contact |

---

# ⚙️ Local Installation

# 🚀 Live Demo

- Frontend:    https://laravel-vue-shop-sehf.vercel.app
- Backend API: https://laravelvueshop-production.up.railway.app

## 1. Clone Repository

```bash
git clone https://github.com/medaminehajjy-AI/Laravel_Vue_Shop.git
cd Laravel_Vue_Shop
```

---

## 2. Backend Setup

```bash
cd backend

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

php artisan storage:link

php artisan serve
```

---

## 3. Frontend Setup

```bash
cd frontend/vite-project

npm install

npm run dev
```

---

# 🌐 Production Deployment

### Backend
- Railway

### Frontend
- Vercel

---

# 📈 Future Improvements

- PayPal/Stripe Payment Integration
- Wishlist Feature
- Product Reviews & Ratings
- Email Notifications
- Multi-language Support
- Product Recommendations

---

# 👨‍💻 Author

**Med Amine Hajjy**

- GitHub: https://github.com/medaminehajjy-AI


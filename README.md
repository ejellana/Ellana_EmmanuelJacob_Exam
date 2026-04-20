# PurpleBug - E-commerce Management System

A full-stack e-commerce web application built with **Laravel 11** (Backend) and **Vue.js 3** (Frontend) featuring a clean, modern UI.

## Project Overview

PurpleBug is a complete product ordering and management system. It allows guests to browse products, register, and shop, while providing administrators with full control over users, products, orders, and system activity.

## Features

### User Features
- Browse all products on a clean landing page
- User registration and login
- Add products to cart (with quantity increment)
- Persistent cart (saved in database)
- Checkout and place orders
- View personal order history
- Cancel pending orders

### Admin Features
- Full User Management (CRUD + Activate/Deactivate)
- Product Management (CRUD with image upload)
- Order Management (View orders, update status: Pending, For Delivery, Delivered, Canceled)
- Activity Logs (Track all system actions)

## Technical Requirements Fulfilled

- **Admin Seeder** — Pre-created admin account (`admin@purplebug.com` / `password`)
- **API-Driven Frontend** — All product listings and cart operations use Laravel APIs
- **Backend Authentication** — All protected routes use Laravel Sanctum
- **Eloquent Relationships** — Proper use of foreign keys and model relationships (`User`, `Product`, `CartItem`, `Order`, `OrderItem`)

## Tech Stack

- **Backend**: Laravel 11 + MySQL + Sanctum + Spatie Activity Log
- **Frontend**: Vue 3 + Vite + Pinia + Tailwind CSS
- **Authentication**: Laravel Sanctum (Token-based)

## Login Credentials

**Admin Account:**
- Email: `admin@purplebug.com`
- Password: `password`

**Guest Users** can register normally.

## Database Relationships

- Users → Orders (One to Many)
- Orders → OrderItems (One to Many)
- OrderItems → Products (Many to One)
- Users → CartItems (One to Many)
- CartItems → Products (Many to One)

All foreign keys and primary keys are properly implemented with Eloquent relationships.

## Project Structure Highlights

- `resources/js/views/` — Vue components (separated by feature)
- `app/Http/Controllers/Api/` — All API controllers
- `routes/api.php` — Protected and public API routes
- Proper use of Form Requests for validation

# PurpleBug - E-commerce Management System

A full-stack e-commerce platform built with **Laravel 11** (Backend) and **Vue.js 3** (Frontend) as part of the Software Engineer Exam.

## Project Overview

PurpleBug is a complete product ordering and management system. It allows guest users to browse products, register, and make purchases, while providing administrators with full control over users, products, orders, and system activity.

## Modules & Features

### 1. Authentication (Login & Registration)
- Guest users can register (Email must be unique)
- Users Information: Full Name, Email, Password, Confirm Password
- Login system with role checking (Guest vs Admin)
- **Security Features**:
  - 5 incorrect login attempts → Account locked for 5 minutes
  - Automatic logout after 30 minutes of inactivity
  - Guests cannot access CMS/Admin panel

### 2. User Management (Admin Only)
- Admin can perform full CRUD operations on users
- Admin can deactivate/activate Guest accounts
- Role-based access control (Guest cannot access admin dashboard)

### 3. Products Management
- Admin can perform full CRUD on products (with image upload)
- Product Information: Image, Product Name, Price, Number of Stocks
- Public product listing for all users (Guest & Logged-in)

### 4. Cart & Checkout System
- Users can add products to cart
- Quantity automatically increases if same product is added again
- Stock validation — cannot add out-of-stock products
- Persistent cart (saved in database)
- Checkout button with total amount display
- Cart is cleared after successful checkout

### 5. Orders Management
- Users can view their own orders and status
- Admin can view all orders and update status:
  - Pending
  - For Delivery
  - Delivered
  - Canceled

## Technical Requirements Fulfilled

1. **Must create a seeder for the Admin user** — Implemented (`AdminSeeder`)
   **Email:** admin@purplebug.com
   **Password:** password
3. **Use APIs for frontend listings (Product Listing, Cart)** — All data loaded via Laravel APIs
4. **Backend routes must have authentication** — Protected using Laravel Sanctum
5. **Utilize Laravel Eloquent and relationships** — Proper foreign keys and relationships used (`User`, `Product`, `CartItem`, `Order`, `OrderItem`)
6. **Implement Laravel test cases for each transaction** — Feature tests created for Auth, Product, Cart, and Order
7. **All forms must have validation** — Implemented using Form Requests
8. **Implement activity logs** — Using Spatie Laravel Activity Log

## Test Results

```bash
PASS  Tests\Feature\AuthTest
  ✓ admin can login successfully
  ✓ guest can register

PASS  Tests\Feature\CartTest
  ✓ logged-in user can add to cart

PASS  Tests\Feature\OrderTest
  ✓ user can checkout cart

PASS  Tests\Feature\ProductTest
  ✓ admin can create product
  ✓ products are listed publicly
```
<img width="1611" height="796" alt="image" src="https://github.com/user-attachments/assets/120921eb-003e-428d-b7c8-38ef6462c99c" />

<img width="1612" height="797" alt="image" src="https://github.com/user-attachments/assets/48d94e8f-eb47-4949-83bf-2489bda051df" />




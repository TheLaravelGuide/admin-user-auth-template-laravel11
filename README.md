# Admin and User Authentication System

This Laravel project provides a robust and modular authentication system with separate tables, controllers, and views for **admin** and **user** roles. It features essential functionalities such as registration, login, password management, email verification, and profile management.

## Features

### Admin Authentication
- Admin registration and login.
- Password reset and update functionality.
- Profile management (update, delete).
- Email verification.
- Separate admin routes, controllers, and views.

### User Authentication
- User registration and login.
- Password reset and update functionality.
- Profile management (update, delete).
- Email verification.
- Separate user routes, controllers, and views.

### Testing
Comprehensive tests for both admin and user functionalities:
- Registration and login tests.
- Password management tests.
- Profile management tests.

> **Total Tests Passed:** 34

## File Structure
Key directories and files:
- **Admin Views:** `resources/views/admin`
- **User Views:** `resources/views`
- **Admin Controllers:** `app/Http/Controllers/Admin`
- **User Controllers:** `app/Http/Controllers/User`
- **Admin Routes:** `routes/admin.php`
- **User Routes:** `routes/user.php`
- **Web Routes:** `routes/web.php`

## Setup

### Requirements
- PHP >= 8.0
- Laravel >= 10.x
- Composer
- MySQL or SQLite database

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/TheLaravelGuide/admin-user-auth-template-laravel11.git 
   cd admin-user-auth-template-laravel11

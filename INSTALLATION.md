# Paaila Installation Manual

---

# Introduction

Paaila is a Laravel-based trekking tracking and booking application designed to provide real-time traveler tracking, booking management, and secure payment integration.

This manual provides the complete installation and setup process for running the project in a local development environment.

---

# System Requirements

Before installing the application, ensure the following software and tools are installed on your system.

## Required Software

1. PHP 8.0 or higher
2. Composer (Latest Stable Version)
3. XAMPP Control Panel
4. MySQL
5. Node.js and NPM
6. Git
7. Visual Studio Code or any preferred code editor

---

# Project Setup

## Step 1: Clone the Repository

Open terminal or command prompt and run:

```bash
git clone https://github.com/sanjit404/paaila-rebuild.git
```

---

## Step 2: Navigate to Project Directory

```bash
cd paaila-rebuild
```

---

## Step 3: Open the Project

```bash
code .
```

---

# Environment Configuration

## Step 4: Install PHP Dependencies

Run the following command:

```bash
composer install
```

---

## Step 5: Install Node Dependencies

```bash
npm install
```

---

## Step 6: Create Environment File

Copy the example environment file:

```bash
cp .env.example .env
```

If the above command does not work on Windows CMD:

```bash
copy .env.example .env
```

---

## Step 7: Generate Application Key

```bash
php artisan key:generate
```

---

# Database Configuration

## Step 8: Start XAMPP Services

Open XAMPP Control Panel and start:

- Apache
- MySQL

---

## Step 9: Create Database

Open XAMPP Shell or MySQL terminal and run:

```bash
mysql -u root -p
```

Press ENTER if no password is set.

Create the database:

```sql
CREATE DATABASE paaila;
```

Exit MySQL:

```sql
exit;
```

---

## Step 10: Configure Database Credentials

Open the `.env` file and update database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paaila
DB_USERNAME=root
DB_PASSWORD=
```

---

# Database Migration and Seeding

## Step 11: Run Migrations

```bash
php artisan migrate
```

---

## Step 12: Seed the Database

```bash
php artisan db:seed
```

---

# Storage Linking

## Step 13: Create Storage Symlink

```bash
php artisan storage:link
```

This command enables public access to uploaded images and files.

---

# Frontend Compilation

## Step 14: Run Vite Development Server

```bash
npm run dev
```

Keep this terminal running while using the application.

---

# Running the Application

## Step 15: Start Laravel Development Server

Open another terminal and run:

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

# Admin Login Credentials

## Default Administrator Account

### Admin Login

```text
Email: admin@paaila.com
Password: password
```

---

# Optional Commands

## Clear Application Cache

```bash
php artisan optimize:clear
```

---

## Restart Queue Worker

```bash
php artisan queue:work
```

---

## Run Tests

```bash
php artisan test
```

---

# Common Issues and Solutions

## Composer Install Fails

Ensure:
- PHP is added to system PATH
- Composer is installed correctly
- PHP version is 8+

---

## Database Connection Error

Verify:
- MySQL is running in XAMPP
- Database name matches `.env`
- Username and password are correct

---

## Images Not Loading

Run:

```bash
php artisan storage:link
```

---

## Vite Assets Not Loading

Ensure:

```bash
npm run dev
```

is running in a separate terminal.

---

# Production Deployment Notes

For production deployment:

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Configure proper database credentials
4. Configure mail services
5. Configure payment gateway credentials
6. Run optimization commands:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

# Technologies Used

- Laravel
- PHP
- MySQL
- Blade Templates
- JavaScript
- Leaflet.js
- Maptiler
- Vite
- Stripe
- eSewa
- Khalti

---

# Copyright

Copyright © PAAILA 2026. All rights reserved.
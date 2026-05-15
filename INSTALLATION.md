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
2. Composer (Latest / Stable Version)
3. php8.2-xml & php-pdo [See Here](#enable-extension)
4. XAMPP Control Panel
5. MySQL
6. Node.js and NPM
7. Git
8. Visual Studio Code or any preferred code editor

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

# Step 4: Pre-setup Requirement Check
Run:
```bash
php check.php
```
then:
```bash
composer install
```
---
# Step 4: Setup wizard
Run:
```bash
php artisan paaila:setup
```


---

# Database Configuration

## Step 5: Start XAMPP Services

Open XAMPP Control Panel and start:

- Apache
- MySQL

---

## Step 6: Create Database

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

## Step 7: Configure Database Credentials

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

## Step 8: Run Migrations

```bash
php artisan migrate
```

---

## Step 9: Seed the Database

```bash
php artisan db:seed
```
---

# Running the Application

## Step 10: Start Laravel Development Server

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
### Admin Login

```text
Email: admin@paaila.com
Password: password
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


# Enable Extension 
Run and follow the instructions:
```bash
php check.php
```

---

# Verify Installation

```bash
php -m | grep xml
```

Expected output:

```bash
libxml
xml
xmlreader
xmlwriter
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

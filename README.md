# 🚀 Symfony Project Management Application

<div align="center">

![Symfony](https://img.shields.io/badge/Symfony-6.4-black?logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-20.10+-2496ED?logo=docker&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![ELK](https://img.shields.io/badge/ELK_Stack-8.18.0-005571?logo=elastic&logoColor=white)

A modern Symfony application for project management with a complete ELK stack for logging.

</div>

---

## 📑 Table of Contents

- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
  - [Clone Repository](#1-clone-repository)
  - [Configure Environment](#2-configure-environment)
  - [Launch Docker Services](#3-launch-docker-services)
  - [Install PHP Dependencies](#4-install-php-dependencies)
  - [Configure Database](#5-configure-database)
  - [Create Admin User](#6-create-admin-user)
- [Application Access](#-application-access)
- [Login](#-login)
- [Troubleshooting](#-troubleshooting)

---

## 🧰 Prerequisites

- [Docker](https://www.docker.com/get-started) and Docker Compose
- [Git](https://git-scm.com/downloads)

---

## 🔧 Installation

### 1. Clone Repository

```bash
git clone https://github.com/your-user/gestionPRojetSymfony.git
cd gestionPRojetSymfony
```

### 2. Configure Environment

Create a `.env` file in the project root using the example below:

```bash
# Docker Compose Configuration
COMPOSE_PROJECT_NAME=lamp
PHPVERSION=php83
DATABASE=mysql8
DOCUMENT_ROOT=./www
APACHE_DOCUMENT_ROOT=/var/www/html
VHOSTS_DIR=./config/vhosts
SHARED_ROOT=./shared
APACHE_LOG_DIR=./logs/apache2
MYSQL_DATA_DIR=./data/mysql
MYSQL_LOG_DIR=./logs/mysql
ELASTIC_DATA_DIR=./data/elasticsearch
KIBANA_DATA_DIR=./data/kibana
LOGSTASH_CONFIG_DIR=./config/logstash

# Port Configuration
HOST_MACHINE_UNSECURE_APACHE_PORT=80
HOST_MACHINE_SECURE_APACHE_PORT=443
HOST_MACHINE_MYSQL_PORT=3306
HOST_MACHINE_PMA_PORT=8080
HOST_MACHINE_PMA_SECURE_PORT=8443
HOST_MACHINE_MH_HTTP_PORT=8025
HOST_MACHINE_MH_SMTP_PORT=1025
XDEBUG_PORT=9003

# MySQL Configuration
MYSQL_ROOT_PASSWORD=root_password
MYSQL_USER=app_user
MYSQL_PASSWORD=app_password
MYSQL_DATABASE=symfony_project

# PHP Configuration
PHP_INI=./config/php/php.ini
UPLOAD_LIMIT=512M
MEMORY_LIMIT=512M
```

You'll also need to configure Symfony environment variables in the `www/.env` file:

```bash
APP_ENV=dev
APP_SECRET=your_secret_key_here
DATABASE_URL="mysql://app_user:app_password@database:3306/symfony_project?serverVersion=8.0"
```

> ⚠️ **Important**: Make sure to replace default values (especially passwords) with your own secure values.

### 3. Launch Docker Services

```bash
docker-compose up -d
```

This command will create and start all necessary services:
- Apache/PHP web server
- MySQL database
- PHPMyAdmin
- Elasticsearch, Logstash, and Kibana
- MailHog (SMTP server for testing emails)

### 4. Install PHP Dependencies

Access the PHP container and install dependencies:

```bash
docker exec -it lamp-php83 bash
cd /var/www/html
composer install
```

If you encounter permission-related errors, ensure that the directories `var/cache` and `var/log` are writable.

### 5. Configure Database

From the PHP container, create/update the database:

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
```

### 6. Create Admin User

```bash
php bin/console app:create-user admin@example.com password123 "Admin User" "admin"
```

This command creates a user with:
- Email: admin@example.com
- Password: password123
- Name: Admin User
- Role: admin

---

## 🌐 Application Access

Once installation is complete, access:

| Service | URL |
|---------|-----|
| **Web Application** | http://localhost:80 |
| **PHPMyAdmin** | http://localhost:8080 |
| **MailHog** | http://localhost:8025 |
| **Kibana** | http://localhost:5601 |

---

## 🔐 Login

Use the credentials created earlier:
- Email: admin@example.com
- Password: password123

---

## ❓ Troubleshooting

### "Invalid credentials" Login Issue

If you encounter a login error with valid credentials:
1. Verify that the user exists in the database
2. Make sure the role is exactly "admin" or "gestionnaire" (lowercase)
3. Check error logs: `docker exec -it lamp-php83 tail -f var/log/dev.log`

### "Failed connecting to tcp://logstash:5000" Error

If you encounter this error, restart the containers:
```bash
docker-compose down
docker-compose up -d
```

---

<div align="center">
  <p>📊 <b>Project Management Application</b> - Built with Symfony and Docker</p>
</div> 
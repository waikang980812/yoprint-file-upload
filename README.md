# YoPrint File Upload System

This Laravel project implements a CSV file upload system with background processing using Laravel Horizon. It supports idempotent uploads, upserts based on `UNIQUE_KEY`, and displays upload status in real time.

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/waikang980812/yoprint-file-upload.git
cd yoprint-file-upload
```

### 2. Set Up Laravel Sail
```bash
cp .env.example .env
./vendor/bin/sail up -d
```


### 3. Install dependencies

```bash
./vendor/bin/sail composer install
./vendor/bin/sail artisan key:generate
```


### 4. Run Migrations

```bash
./vendor/bin/sail artisan migrate
```

### Horizon Setup
```bash
./vendor/bin/sail artisan horizon
```
Ensure Redis is enabled in .env:
```bash
QUEUE_CONNECTION=redis
```
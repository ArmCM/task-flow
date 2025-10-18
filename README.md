# TaskFlow API

## 🚀 Local Installation

### Requirements
- PHP 8.3+
- Composer
- Mysql or MariaDB

### Steps

```bash
# 1.
git clone git@github.com:ArmCM/task-flow.git
```
```
cd task-flow
```

```sql
# 2 Create database tasks_flow and tables

CREATE DATABASE tasks_flow;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tasks (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    expiration_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE tasks ADD COLUMN user_id INT UNSIGNED NULL AFTER expiration_date;

ALTER TABLE tasks ADD CONSTRAINT tasks_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id);
```

```shell
# 3.
composer install

# 4. Start local server
php -S localhost:8000 -t public/
```

---

### API Reference

#### List all tasks and Filter tasks
```php
# List
GET  localhost:8000/tasks

# Filter
query string sort can be asc or desc

GET  localhost:8000/tasks?title=example&description=example&status=pending&expiration_date=2025-04-24&sort=asc


# Paginator (can be used with filters)

GET  localhost:8000/tasks?title=example&page=1&per_page=3

# Response
{
    "status": "success",
    "message": "",
    "data": [
        {
            "id": 1,
            "title": "fake one",
            "description": "Ipsum commodo cupidatat veniam labori",
            "status": "pending",
            "expiration_date": "2025-04-25"
            "created_at": "2025-04-25 13:59:50",
            "updated_at": "2025-08-25 14:00:16"
        },
        {
            "id": 2,
            "title": "fake two",
            "description": "Consectetur nisi nostrud eu excepteur dolor quis cillum pariatur aliquip aliquip ad laboris",
            "status": "pending",
            "expiration_date": "2025-04-25"
            "created_at": "2025-04-25 13:59:50",
            "updated_at": "2025-08-25 14:00:16"
        }
    ],
        "meta": {
        "current_page": 1,
        "per_page": 3,
        "total": 11,
        "last_page": 4
    },
    "links": {
        "first": "/tasks?page=1&per_page=3",
        "last": "/tasks?page=4&per_page=3",
        "prev": null,
        "next": "/tasks?page=2&per_page=3"
    }
    "options": []
}
```

#### Store a task
```php
POST  localhost:8000/tasks

# Body (json)
{
    "title": "fake title",
    "description": "fake description",
    "expiration_date": "10-11-2025"
}

# Response
{
    "status": "success",
    "message": "Task created successfully",
}
```

#### Show a task
```php
GET  localhost:8000/tasks/{id}

# Response
{
    "status": "success",
    "message": "id",
    "data": {
        "id": 3,
        "title": "fieldnotes",
        "description": "por aute minim duis in officia labore dolore laborum. Fugiat nostrud qui ",
        "status": "completed",
        "expiration_date": "2025-04-25",
        "created_at": "2025-04-25 13:59:50",
        "updated_at": "2025-08-25 14:13:28"
    },
}
```

#### Update a task
```php
PUT  localhost:8000/tasks/{id}

# Body (json)
{
    "title": "update title",
    "description": "update descriotion",
    "status": "completed",
    "expiration_date": "2025-08-23"
}

# Response
{
    "status": "success",
    "message": "",
}
```

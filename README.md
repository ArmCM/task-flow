# TaskFlow API (Prueba técnica - PHP Developer)

## 🚀 Local Installation

### Requirements
- PHP 8.3+
- Composer
- Mysql or MariaDB

### Steps

```bash
# 1.
git clone git@github.com:ArmCM/task-flow.git
cd taskflow-api

# 2 Create dabase taskflow and tables

CREATE TABLE IF NOT EXISTS tasks (
               id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
                due_date DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            );


# 3.
composer install

# 4. Start local server
php -S localhost:8000 -t public/
```

### API Reference
```php

GET api-task-flow.test/tasks

# Response
{
    "status": "success",
    "message": "",
    "data": [
        {
            "id": 1,
            "title": "fake one",
            "description": "Ipsum commodo cupidatat veniam labori",
            "created_at": "2025-07-23"
        },
        {
            "id": 2,
            "title": "fake two",
            "description": "Consectetur nisi nostrud eu excepteur dolor quis cillum pariatur aliquip aliquip ad laboris",
            "created_at": "2025-07-23"
        }
    ],
    "options": []
}
```

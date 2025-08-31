<?php

namespace App\Models;

use Carbon\Carbon;
use Core\App;
use Core\Database;
use Core\QueryBuild;

class User
{
    protected Database $database;
    protected QueryBuild $query;

    public function __construct()
    {
        $this->query = new QueryBuild(App::resolve(Database::class), 'users');
    }

    public function store(array $data)
    {
        $this->query->store('name, email, password, created_at, updated_at', [
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':created_at' => Carbon::now()->toDateTimeString(),
            ':updated_at' => Carbon::now()->toDateTimeString(),

        ]);
    }
}

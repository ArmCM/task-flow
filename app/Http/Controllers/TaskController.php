<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;
use Core\App;
use Core\Database;

class TaskController
{
    use ApiResponses;

    public function index()
    {
        $db = App::resolve(Database::class);

        $tasks = $db->query("SELECT * FROM taskflow.tasks")->fetchAll();

        $this->ok('Tasks fetched successfully', $tasks);
    }

    public function show($id)
    {
        $db = App::resolve(Database::class);

        $task = $db->query("SELECT * FROM taskflow.tasks WHERE id = $id")->fetch();

        $this->ok('id', $task);
    }
}

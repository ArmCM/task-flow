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
    
    public function create()
    {

    }

    public function store()
    {
        App::resolve(Database::class)->query('INSERT INTO taskflow.tasks(title, description) VALUES (:title, :description)', [
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
        ]);

        $this->created('Task created successfully');
    }

    public function show($id)
    {
        $db = App::resolve(Database::class);

        $task = $db->query("SELECT * FROM taskflow.tasks WHERE id = $id")->fetch();

        $this->ok('id', $task);
    }

    public function edit($id)
    {

    }

    public function update($id)
    {
        $db = App::resolve(Database::class);

        $db->query("UPDATE taskflow.tasks SET taskflow.tasks.title = :title, taskflow.tasks.description = :description  WHERE taskflow.tasks.id = :id", [
            ':id' => $id,
            ':title' => $_POST['title'],
            ':description' => $_POST['description'],
        ])->fetch();

        $this->ok('id');
    }

    public function destroy($id)
    {
        $db = App::resolve(Database::class);

        $db->query("DELETE FROM taskflow.tasks WHERE id = :id", [
            ':id' => $id,
        ]);
    }
}

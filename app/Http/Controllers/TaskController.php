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

    public function edit()
    {
        
    }

    public function update()
    {
        
    }

    public function destroy()
    {

    }
}

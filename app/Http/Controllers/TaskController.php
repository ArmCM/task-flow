<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponses;
use Carbon\Carbon;
use Core\App;
use Core\Database;
use Core\Request;

class TaskController
{
    use ApiResponses;

    public function index()
    {
        $db = App::resolve(Database::class);
        $request = App::resolve(Request::class);

        $params = [];
        $conditions = [];

        if (notEmpty($request->params('title'))) {
            $conditions[] = "title = :title";
            $params[':title'] = $request->params('title');
        }

        if (notEmpty($request->params('description'))) {
            $conditions[] = "description = :description";
            $params[':description'] = $request->params('description');
        }

        if (notEmpty($request->params('expiration_date'))) {
            $conditions[] = "DATE(expiration_date) = :expiration_date";
            $params[':expiration_date'] = $request->params('expiration_date');
        }

        if (notEmpty($request->params('status'))) {

            $statusId = $db->query("SELECT taskflow.states.id FROM taskflow.states WHERE taskflow.states.name = :name", [
                ':name' => $request->params('status')
            ])->fetch();

            $conditions[] = "state_id = :state_id";
            $params[':state_id'] = $statusId['id'];
        }

        $where = "";

        if (!empty($conditions)) {
            $where = "WHERE " . implode(" AND ", $conditions);
        }

        $orderBy = "ORDER BY created_at DESC";

        if (!empty($request->params('sort'))) {
            $sort = strtolower($request->params('sort'));
            if ($sort === "asc") {
                $orderBy = "ORDER BY created_at ASC";
            }

            if ($sort === "desc") {
                $orderBy = "ORDER BY created_at DESC";
            }
        }

        $sql = "SELECT * FROM taskflow.tasks {$where} {$orderBy}";

        $tasks = $db->query($sql, $params)->fetchAll();

        $this->ok('Tasks fetched successfully', $tasks);
    }
    
    public function create()
    {

    }

    public function store()
    {
        $request = App::resolve(Request::class);

        App::resolve(Database::class)->query('INSERT INTO taskflow.tasks(title, description, state_id, expiration_date, created_at) VALUES (:title, :description, :state_id, :expiration_date, :created_at)', [
            ':title' => $request->json()['title'],
            ':description' => $request->json()['description'],
            ':state_id' => 1,
            ':expiration_date' => Carbon::parse($request->json()['expiration_date'])->format('Y-m-d H:i:s'),
            ':created_at' => Carbon::now()->toDateTimeString()
        ]);

        $this->created('Task created successfully');
    }

    public function show($id)
    {
        $db = App::resolve(Database::class);

        $task = $db->query("SELECT * FROM taskflow.tasks WHERE taskflow.tasks.id = :id", [
            ':id' => $id,
        ])->fetch();

        $this->ok('id', $task);
    }

    public function edit($id)
    {

    }

    public function update($id)
    {
        $db = App::resolve(Database::class);
        $request = App::resolve(Request::class);

        $db->query("
            UPDATE taskflow.tasks 
            SET taskflow.tasks.title = :title,
                taskflow.tasks.description = :description,
                taskflow.tasks.state_id = :state_id,
                taskflow.tasks.expiration_date = :expiration_date
            WHERE taskflow.tasks.id = :id", [
            ':id' => $id,
            ':title' => $request->json()['title'],
            ':description' => $request->json()['description'],
            ':state_id' => $request->json()['state_id'],
            ':expiration_date' => Carbon::parse($request->json()['expiration_date'])->format('Y-m-d'),
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

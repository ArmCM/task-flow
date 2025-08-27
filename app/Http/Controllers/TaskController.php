<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Traits\ApiResponses;
use Core\App;
use Core\Request;

class TaskController
{
    use ApiResponses;

    public function index()
    {
        $request = App::resolve(Request::class);

        $filters = [
            'title' => $request->params('title'),
            'description' => $request->params('description'),
            'expiration_date' => $request->params('expiration_date'),
            'status' => $request->params('status'),
            'page' => $request->params('page'),
            'per_page' => $request->params('per_page'),
        ];

        $tasks = (new Task)->paginate($filters, $request->params('sort'));

        $this->ok('Tasks fetched successfully', $tasks);
    }
    
    public function create()
    {

    }

    public function store()
    {
        $request = App::resolve(Request::class);

        (new Task)->store($request->json());

        $this->created('Task created successfully');
    }

    public function show($id)
    {
        $task = (new Task)->find($id);

        $this->ok('find resource', $task);
    }

    public function edit($id)
    {

    }

    public function update($id)
    {
        $request = App::resolve(Request::class);

        (new Task)->update($id, $request->json());

        $this->ok();
    }

    public function destroy($id)
    {
        $task = (new Task)->find($id);

        if (!$task) {
            $this->notFound();
        }

        (new Task)->delete($id);

        $this->noContent();
    }
}

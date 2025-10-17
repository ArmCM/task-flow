<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Policies\TaskPolicy;
use Core\App;
use Core\Request;
use Core\Traits\ApiResponses;

class TaskController
{
    use ApiResponses;

    public function __construct(public TaskPolicy $taskPolicy)
    {
    }

    public function index()
    {
        $request = App::resolve(Request::class);

        $policyFilter = $this->taskPolicy->viewAny($request);

        $params = [
            'title' => $request->params('title'),
            'description' => $request->params('description'),
            'expiration_date' => $request->params('expiration_date'),
            'status' => $request->params('status'),
            'page' => $request->params('page'),
            'per_page' => $request->params('per_page'),
        ];

        $filters = array_merge($params, $policyFilter);

        $tasks = (new Task)->paginate($filters, $request->params('sort'));

        $this->ok('Tasks fetched successfully', $tasks);
    }

    public function store()
    {
        $request = App::resolve(Request::class);

        $requestValidated = TaskStoreRequest::validate($request->json());

        (new Task)->store($requestValidated);

        $this->created('Task created successfully');
    }

    public function show($id)
    {
        $task = (new Task)->find($id);

        if (!$this->taskPolicy->view($task, App::resolve(Request::class))) {
            $this->forbidden('You cannot view this task');
        }

        $this->ok('find resource', $task);
    }

    public function update($id)
    {
        $request = App::resolve(Request::class);

        $task = (new Task)->find($id);

        if (!$this->taskPolicy->view($task, App::resolve(Request::class))) {
            $this->forbidden('You cannot update this task');
        }

        $requestValidated = TaskUpdateRequest::validate($request->json());

        (new Task)->update($id, $requestValidated);

        $this->ok('resource updated');
    }

    public function destroy($id)
    {
        $task = (new Task)->find($id);

        if (!$this->taskPolicy->view($task, App::resolve(Request::class))) {
            $this->forbidden('You cannot delete this task');
        }

        (new Task)->delete($id);

        $this->noContent();
    }
}

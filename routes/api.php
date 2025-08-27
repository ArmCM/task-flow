<?php

use App\Http\Controllers\TaskController;

/** @var $router */

$router->get('/tasks', [TaskController::class, 'index']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->get('/tasks/{id}', [TaskController::class, 'show']);
$router->patch('/tasks/{id}', [TaskController::class, 'update']);
$router->delete('/tasks/{id}', [TaskController::class, 'destroy']);

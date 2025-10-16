<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserRegisterController;

/** @var $router */

$router->get('/login', [LoginController::class, 'index']);
$router->post('/register', [UserRegisterController::class, 'store']);

$router->get('/tasks', [TaskController::class, 'index']);
$router->post('/tasks', [TaskController::class, 'store']);
$router->get('/tasks/{id}', [TaskController::class, 'show']);
$router->patch('/tasks/{id}', [TaskController::class, 'update']);
$router->delete('/tasks/{id}', [TaskController::class, 'destroy']);

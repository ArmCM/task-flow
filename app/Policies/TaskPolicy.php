<?php

namespace App\Policies;

use Core\Request;

class TaskPolicy
{
    public function viewAny(Request $request): array
    {
        $user = $request->user();
        return ['user_id' => $user['user_id']];
    }

    public function view(array $task, Request $request): bool
    {
        $user = $request->user();

        return isset($user['user_id']) && $task['user_id'] === $user['user_id'];
    }

    public function update(array $task, Request $request): bool
    {
        return $this->view($task, $request);
    }

    public function delete(array $task, Request $request): bool
    {
        return $this->view($task, $request);
    }
}

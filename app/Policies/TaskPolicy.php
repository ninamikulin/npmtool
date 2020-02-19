<?php

namespace App\Policies;

use App\Task;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function edit(User $user, Task $task)
    {
        return $task->user_id === $user->id;
    }

}

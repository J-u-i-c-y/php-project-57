<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $status = TaskStatus::first();

        if ($user && $status) {
            Task::create([
                'name' => 'Первая задача',
                'description' => 'Описание первой задачи',
                'status_id' => $status->id,
                'created_by_id' => $user->id,
                'assigned_to_id' => $user->id,
            ]);
        }
    }
}

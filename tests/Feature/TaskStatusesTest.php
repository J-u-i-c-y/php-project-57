<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_delete_status_with_tasks(): void
    {
        $this->actingAs($this->user);

        $status = TaskStatus::create([
            'name' => 'Test Status',
        ]);

        Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->delete(route('task_statuses.destroy', $status));

        $response->assertRedirect(route('task_statuses.index'));
        $response->assertSessionHas('flash_notification.0.level', 'error');
        $response->assertSessionHas('flash_notification.0.message', 'Не удалось удалить статус');

        $this->assertDatabaseHas('task_statuses', ['id' => $status->id]);
    }

    public function test_delete_status_without_tasks(): void
    {
        $this->actingAs($this->user);

        $status = TaskStatus::create([
            'name' => 'Test Status',
        ]);

        $response = $this->delete(route('task_statuses.destroy', $status));

        $response->assertRedirect(route('task_statuses.index'));
        $response->assertSessionHas('flash_notification.0.level', 'success');
        $response->assertSessionHas('flash_notification.0.message', 'Статус успешно удалён');

        $this->assertDatabaseMissing('task_statuses', ['id' => $status->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskStatusesTest extends TestCase
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

    public function testDeleteStatusWithTasks(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->actingAs($this->user)
            ->delete(route('task_statuses.destroy', $taskStatus));

        $response->assertRedirect(route('task_statuses.index'));

        $this->assertDatabaseHas('task_statuses', ['id' => $taskStatus->id]);
    }

    public function testDeleteStatusWithoutTasks(): void
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

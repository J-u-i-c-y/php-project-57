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
        $taskStatusId = $taskStatus->id;
        $this->assertDatabaseHas('task_statuses', ['id' => $taskStatusId]);
    }

    public function testDeleteStatusWithoutTasks(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $response = $this->actingAs($this->user)
            ->delete(route('task_statuses.destroy', $taskStatus));
        $response->assertRedirect(route('task_statuses.index'));
        $taskStatusId = $taskStatus->id;
        $this->assertDatabaseMissing('task_statuses', ['id' => $taskStatusId]);
    }
}

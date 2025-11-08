<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private TaskStatus $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([ // Используем фабрику
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->status = TaskStatus::create([
            'name' => 'новый',
        ]);
    }

    public function test_index(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
    }

    public function test_create_for_guest(): void
    {
        $response = $this->get(route('tasks.create'));
        $response->assertStatus(403);
    }

    public function test_create_for_authenticated_user(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.create'));
        $response->assertOk();
    }

    public function test_store_for_guest(): void
    {
        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
            'created_by_id' => 1,
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
        ]);
    }

    public function test_store_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        // Проверим что пользователь действительно аутентифицирован
        $this->assertAuthenticatedAs($this->user);

        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
            'assigned_to_id' => (string) $this->user->id,
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
        ]);
    }

    public function test_show(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.show', $task));
        $response->assertOk();
    }

    public function test_edit_for_guest(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertStatus(403);
    }

    public function test_edit_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertOk();
    }

    public function test_update_for_guest(): void
    {
        $task = Task::factory()->create([
            'name' => 'Original Task Name',
            'description' => 'Original Description',
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
            'status_id' => $this->status->id,
        ]);

        $updatedData = [
            'name' => 'Updated Task Name',
            'description' => 'Updated Description',
            'status_id' => (string) $this->status->id,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);
        $this->assertContains($response->getStatusCode(), [302, 403]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Original Task Name',
            'description' => 'Original Description',
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
        ]);
    }

    public function test_update_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Task Name',
            'description' => 'Updated Description',
            'status_id' => (string) $this->status->id,
            'assigned_to_id' => (string) $this->user->id,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $updatedData);
    }

    public function test_destroy_for_guest(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_destroy_for_authenticated_user(): void
    {
        $this->actingAs($this->user);

        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_validation(): void
    {
        $this->actingAs($this->user);

        // Test required name
        $response = $this->post(route('tasks.store'), [
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
        ]);
        $response->assertSessionHasErrors('name');

        // Test required status_id
        $response = $this->post(route('tasks.store'), [
            'name' => 'Test Task',
            'description' => 'Test Description',
        ]);
        $response->assertSessionHasErrors('status_id');
    }
}

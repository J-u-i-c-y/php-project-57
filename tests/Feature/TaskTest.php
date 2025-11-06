<?php

namespace Tests\Feature;

use App\Models\Label;
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

    public function testIndex(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertOk();
    }

    public function testCreateForGuest(): void
    {
        $response = $this->get(route('tasks.create'));
        $response->assertStatus(403);
    }

    public function testCreateForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        $response = $this->get(route('tasks.create'));
        $response->assertOk();
    }

    public function testStoreForGuest(): void
    {
        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        $response->assertStatus(403); // Должен быть 403, а не redirect
        $this->assertDatabaseMissing('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
        ]);
    }

    public function testStoreForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        
        // Проверим что пользователь действительно аутентифицирован
        $this->assertAuthenticatedAs($this->user);
        
        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
            'assigned_by_id' => (string) $this->user->id,
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        $response->assertRedirect(route('tasks.index'));
        
        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);
    }

    public function testShow(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.show', $task));
        $response->assertOk();
    }

    public function testEditForGuest(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertStatus(403);
    }

    public function testEditForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertOk();
    }

    public function testUpdateForGuest(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Task Name',
            'description' => 'Updated Description',
            'status_id' => (string) $this->status->id,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);
        $response->assertStatus(403); // Должен быть 403, а не redirect
        $this->assertDatabaseMissing('tasks', $updatedData);
    }

    public function testUpdateForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $updatedData = [
            'name' => 'Updated Task Name',
            'description' => 'Updated Description',
            'status_id' => (string) $this->status->id,
            'assigned_by_id' => (string) $this->user->id,
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', $updatedData);
    }

    public function testDestroyForGuest(): void
    {
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function testDestroyForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        
        $task = Task::create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'creator_by_id' => $this->user->id,
            'assigned_by_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testValidation(): void
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

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
    
    /** @var TaskStatus */
    private $status;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        /** @var TaskStatus $status */
        $this->status = TaskStatus::factory()->create([
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
            'created_by_id' => 1,
            '_token' => csrf_token(),
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        $response->assertStatus(403);

        $this->assertDatabaseMissing('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
        ]);
    }

    public function testStoreForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);
        $this->assertAuthenticatedAs($this->user);

        $taskData = [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
            'assigned_to_id' => (string) $this->user->id,
            '_token' => csrf_token(),
        ];

        $response = $this->post(route('tasks.store'), $taskData);
        
        // dd($response->getSession()->get('errors'));
        
        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
        ]);
    }

    public function testShow(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create([
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.show', $task));
        $response->assertOk();
    }

    public function testEditForGuest(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertStatus(403);
    }

    public function testEditForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);

        /** @var Task $task */
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->get(route('tasks.edit', $task));
        $response->assertOk();
    }

    public function testUpdateForGuest(): void
    {
        /** @var Task $task */
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
            '_token' => csrf_token(),
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

    public function testUpdateForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);

        /** @var Task $task */
        $task = Task::factory()->create([
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
            '_token' => csrf_token(),
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $updatedData));
    }

    public function testDestroyForGuest(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task), [
            '_token' => csrf_token(),
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function testDestroyForAuthenticatedUser(): void
    {
        $this->actingAs($this->user);

        /** @var Task $task */
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'Test Description',
            'status_id' => $this->status->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $response = $this->delete(route('tasks.destroy', $task), [
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testValidation(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('tasks.store'), [
            'description' => 'Test Description',
            'status_id' => (string) $this->status->id,
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHasErrors('name');

        $response = $this->post(route('tasks.store'), [
            'name' => 'Test Task',
            'description' => 'Test Description',
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHasErrors('status_id');
    }
}

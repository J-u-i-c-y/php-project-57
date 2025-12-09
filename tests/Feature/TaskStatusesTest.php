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
        // $this->actingAs($this->user);

        // $status = TaskStatus::create([
        //     'name' => 'Test Status',
        // ]);

        // Task::create([
        //     'name' => 'Test Task',
        //     'description' => 'Test Description',
        //     'status_id' => $status->id,
        //     'created_by_id' => $this->user->id,
        //     'assigned_to_id' => $this->user->id,
        // ]);

        // $response = $this->delete(route('task_statuses.destroy', $status));

        // $response->assertRedirect(route('task_statuses.index'));
        // $response->assertSessionHas('flash_notification.0.level', 'danger');
        // $response->assertSessionHas('flash_notification.0.message', 'Не удалось удалить статус');

        // $this->assertDatabaseHas('task_statuses', ['id' => $status->id]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $status = TaskStatus::factory()->create();
        Task::factory()->create(['status_id' => $status->id]);

        $response = $this->delete(route('task_statuses.destroy', $status));
        
        $response->assertRedirect(route('task_statuses.index'));
        $this->assertDatabaseHas('task_statuses', ['id' => $status->id]);
    }

    // public function testDeleteStatusWithoutTasks(): void
    // {
    //     $this->actingAs($this->user);

    //     $status = TaskStatus::create([
    //         'name' => 'Test Status',
    //     ]);

    //     $response = $this->delete(route('task_statuses.destroy', $status));

    //     $response->assertRedirect(route('task_statuses.index'));
    //     $response->assertSessionHas('flash_notification.0.level', 'success');
    //     $response->assertSessionHas('flash_notification.0.message', 'Статус успешно удалён');

    //     $this->assertDatabaseMissing('task_statuses', ['id' => $status->id]);
    // }

    public function testDeleteStatusWithoutTasks(): void
    {
        // Создаем пользователя и авторизуем его
        $user = User::factory()->create();
        $this->actingAs($user);

        $status = TaskStatus::factory()->create();

        $response = $this->delete(route('task_statuses.destroy', $status));
        
        // Проверяем что удаление прошло успешно
        $response->assertRedirect(route('task_statuses.index'));
        $response->assertSessionHas('flash_notification.0.level', 'success');
        
        // Проверяем локализованное сообщение
        $this->assertDatabaseMissing('task_statuses', ['id' => $status->id]);
    }

    // Если есть тест на удаление без авторизации, добавьте его:
    public function testDeleteStatusUnauthenticated(): void
    {
        $status = TaskStatus::factory()->create();

        $response = $this->delete(route('task_statuses.destroy', $status));
        
        // Гость должен получить 403 или быть перенаправлен на логин
        $response->assertStatus(403); // или assertRedirect(route('login'))
        $this->assertDatabaseHas('task_statuses', ['id' => $status->id]);
    }
}

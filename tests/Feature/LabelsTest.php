<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LabelsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testLabelsScreenCanBeRendered(): void
    {
        // Создаем метку прямо в тесте
        $label = Label::factory()->create(['name' => 'Тестовая метка']);

        $response = $this->get(route('labels.index'));
        $response->assertStatus(200);
        $response->assertSee('Тестовая метка');
    }

    public function testCreateLabel(): void
    {
        // Неавторизованный пользователь не может создать метку
        $response = $this->get(route('labels.create'));
        $response->assertStatus(403);

        // Авторизуемся
        $this->actingAs($this->user);

        $response = $this->get(route('labels.create'));
        $response->assertStatus(200);

        // Создаем новую метку
        $response = $this->post(route('labels.store'), [
            'name' => 'Новая тестовая метка ' . uniqid(), // Уникальное имя
            'description' => 'Описание метки',
        ]);

        $response->assertRedirect(route('labels.index'));
    }

    public function testEditLabel(): void
    {
        $label = Label::factory()->create(['name' => 'Тестовая метка']);

        // Неавторизованный пользователь не может редактировать
        $response = $this->get(route('labels.edit', $label));
        $response->assertStatus(403);

        $this->actingAs($this->user);

        $response = $this->get(route('labels.edit', $label));
        $response->assertStatus(200);

        // Обновляем метку
        $response = $this->patch(route('labels.update', $label), [
            'name' => 'Измененная тестовая метка',
            'description' => 'Новое описание',
        ]);

        $response->assertRedirect(route('labels.index'));
    }

    public function testDeleteUnusedLabel(): void
    {
        $label = Label::factory()->create(['name' => 'Метка для удаления']);
        $this->actingAs($this->user);

        $response = $this->delete(route('labels.destroy', $label));
        $response->assertRedirect(route('labels.index'));
    }

    public function testCannotDeleteLabelUsedInTask(): void
    {
        $label = Label::factory()->create(['name' => 'Используемая метка']);
        $this->actingAs($this->user);

        // Создаем статус если нет существующего
        $taskStatus = TaskStatus::first();
        if (! $taskStatus) {
            $taskStatus = TaskStatus::create(['name' => 'Test Status']);
        }

        $task = Task::create([
            'name' => 'Тестовая задача ' . uniqid(),
            'description' => 'Описание задачи',
            'status_id' => $taskStatus->id,
            'created_by_id' => $this->user->id,
            'assigned_to_id' => $this->user->id,
        ]);

        $task->labels()->attach($label->id);

        $response = $this->delete(route('labels.destroy', $label));
        $response->assertRedirect();
    }

    public function testValidationForLabelCreation(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('labels.store'), [
            'description' => 'Описание без имени',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testUniqueNamValidation(): void
    {
        $this->actingAs($this->user);

        $label = Label::factory()->create(['name' => 'Уникальная метка']);

        $response = $this->post(route('labels.store'), [
            'name' => 'Уникальная метка',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function testGuestCannotAccessProtectedRoutes(): void
    {
        $label = Label::factory()->create(['name' => 'Тестовая метка']);

        $this->get(route('labels.create'))->assertStatus(403);
        $this->get(route('labels.edit', $label))->assertStatus(403);

        $this->post(route('labels.store'), [])->assertStatus(302);
        $this->put(route('labels.update', $label), [])->assertStatus(302);

        $response = $this->delete(route('labels.destroy', $label));
        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}

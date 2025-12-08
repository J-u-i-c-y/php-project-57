<?php

namespace Tests\Unit\Models;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function testTaskBelongsToStatus(): void
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(BelongsTo::class, $task->status());
    }

    public function testTaskBelongsToCreatedByUser(): void
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(BelongsTo::class, $task->createdBy());
    }

    public function testTaskBelongsToAssignedToUser(): void
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(BelongsTo::class, $task->assignedTo());
    }

    public function testTaskBelongsToManyLabels(): void
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(BelongsToMany::class, $task->labels());
    }

    public function testTaskCanHaveLabels(): void
    {
        $task = Task::factory()->create();
        $label = Label::factory()->create();
        $task->labels()->attach($label->id);
        $this->assertTrue($task->labels->contains($label));
        $this->assertCount(1, $task->labels);
    }

    public function testTaskFillableAttributes(): void
    {
        $task = new Task();
        $expectedFillable = ['name', 'description', 'status_id', 'assigned_to_id', 'created_by_id'];
        $this->assertEquals($expectedFillable, $task->getFillable());
    }

    public function testTaskTableName(): void
    {
        $task = new Task();
        $this->assertEquals('tasks', $task->getTable());
    }
}

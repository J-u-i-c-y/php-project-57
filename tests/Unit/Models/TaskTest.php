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
        $relation = $task->status();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $task->load('status');
        $this->assertNotNull($task->status);
        $this->assertInstanceOf(TaskStatus::class, $task->status);
    }

    public function testTaskBelongsToCreatedByUser(): void
    {
        $task = Task::factory()->create();
        $relation = $task->createdBy();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $task->load('createdBy');
        $this->assertNotNull($task->createdBy);
        $this->assertInstanceOf(User::class, $task->createdBy);
    }

    public function testTaskBelongsToAssignedToUser(): void
    {
        $task = Task::factory()->create();
        $relation = $task->assignedTo();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $task->load('assignedTo');
        $this->assertNotNull($task->assignedTo);
        $this->assertInstanceOf(User::class, $task->assignedTo);
    }

    public function testTaskBelongsToManyLabels(): void
    {
        $task = Task::factory()->create();
        $relation = $task->labels();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    public function testTaskCanHaveLabels(): void
    {
        $task = Task::factory()->create();
        $label = Label::factory()->create();
        $task->labels()->attach($label->id);
        $task->load('labels');
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

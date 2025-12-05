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
        /** @var Task $task */
        $task = Task::factory()->create();
        $relation = $task->status();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertNotNull($task->status);
    }

    public function testTaskBelongsToCreatedByUser(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        $relation = $task->createdBy();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertNotNull($task->createdBy);
    }

    public function testTaskBelongsToAssignedToUser(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        $relation = $task->assignedTo();
        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertNotNull($task->assignedTo);
    }

    public function testTaskBelongsToManyLabels(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        $relation = $task->labels();
        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    public function testTaskCanHaveLabels(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        /** @var Label $label */
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

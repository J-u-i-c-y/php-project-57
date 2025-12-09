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
        /** @var TaskStatus $status */
        $status = TaskStatus::factory()->create();
        $task->status()->associate($status);
        $task->save();
        $this->assertEquals($status->id, $task->status_id);
        $this->assertTrue($task->status->is($status));
    }

    public function testTaskBelongsToCreatedByUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Task $task */
        $task = Task::factory()->create(['created_by_id' => $user->id]);
        $this->assertEquals($user->id, $task->created_by_id);
        $this->assertTrue($task->createdBy->is($user));
    }

    public function testTaskBelongsToAssignedToUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Task $task */
        $task = Task::factory()->create(['assigned_to_id' => $user->id]);
        $this->assertInstanceOf(User::class, $task->assignedTo);
        $this->assertEquals($user->id, $task->assigned_to_id);
    }

    public function testTaskBelongsToManyLabels(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        /** @var Label $label */
        $label = Label::factory()->create();
        $task->labels()->attach($label->id);
        $this->assertInstanceOf(Label::class, $task->labels->first());
        $this->assertEquals($label->id, $task->labels->first()->id);
    }

    public function testTaskCanHaveLabels(): void
    {
        /** @var Task $task */
        $task = Task::factory()->create();
        /** @var Label $label */
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

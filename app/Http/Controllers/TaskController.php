<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'filter' => 'nullable|array',
        ]);

        $filterTasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id'),
            ])
            ->with(['status', 'createdBy', 'assignedTo', 'labels']);

        $tasks = $filterTasks->paginate();
        $taskStatuses = TaskStatus::all();
        $users = User::all();

        $filter = [
            'status_id' => $request->input('filter.status_id'),
            'created_by_id' => $request->input('filter.created_by_id'),
            'assigned_to_id' => $request->input('filter.assigned_to_id'),
        ];

        return view('tasks.index', compact('tasks', 'taskStatuses', 'users', 'filter'));
    }

    public function create()
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.create', compact('taskStatuses', 'users', 'labels'));
    }

    // public function store(TaskRequest $request)
    // {
    //     $validated = $request->validated();
    //     $task = Auth::user()->createdTasks()->create($validated);

    //     if (!empty($validated['labels'])) {
    //         $task->labels()->attach(array_filter($validated['labels']));
    //     }

    //     flash(__('controllers.tasks_create'))->success();

    //     return redirect()->route('tasks.index');
    // }

    public function store(TaskRequest $request)
    {
        $validated = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $task = $user->createdTasks()->create($validated);

        if (!empty($validated['labels'])) {
            $task->labels()->attach(array_filter($validated['labels']));
        }

        flash(__('controllers.tasks_create'))->success();
        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        $task->load('status', 'createdBy', 'assignedTo', 'labels');
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.edit', compact('task', 'taskStatuses', 'users', 'labels'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        $validated = $request->validated();
        $task->update($validated);

        if (isset($validated['labels'])) {
            $task->labels()->sync(array_filter($validated['labels']));
        } else {
            $task->labels()->detach();
        }

        flash(__('controllers.tasks_update'))->success();

        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->labels()->detach();
        $task->delete();
        flash(__('controllers.tasks_destroy'))->success();

        return redirect()->route('tasks.index');
    }
}

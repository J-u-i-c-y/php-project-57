<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
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
        $request->validate([
            'filter' => 'nullable|array',
        ]);

        $filterTasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status_id'),
                AllowedFilter::exact('created_by_id'),
                AllowedFilter::exact('assigned_to_id'),
            ])
            ->with(['status', 'createdBy', 'assignedTo']);

        $tasks = $filterTasks->paginate();
        $taskStatuses = TaskStatus::pluck('name', 'id');
        $users = User::pluck('name', 'id');

        $filter = $request->input('filter', []);

        return view('tasks.index', compact('tasks', 'taskStatuses', 'users', 'filter'));
    }

    public function create()
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.create', compact('taskStatuses', 'users', 'labels'));
    }

    public function store(TaskStoreRequest $request)
    {
        $data = $request->validated();
        
        $task = Task::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status_id' => $data['status_id'],
            'assigned_to_id' => $data['assigned_to_id'] ?? null,
            'created_by_id' => Auth::id(),
        ]);

        $task->labels()->attach(
            collect($data['labels'] ?? [])->filter()->values()->all()
        );

        flash(__('controllers.tasks_create'))->success();
        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.edit', compact('task', 'taskStatuses', 'users', 'labels'));
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        $data = $request->validated();
        
        $task->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status_id' => $data['status_id'],
            'assigned_to_id' => $data['assigned_to_id'] ?? null,
        ]);

        $task->labels()->sync(
            collect($data['labels'] ?? [])->filter()->values()->all()
        );

        flash(__('controllers.tasks_update'))->success();
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->labels()->detach();
        $task->delete();

        flash(__('controllers.tasks_destroy'))->success();
        return redirect()->route('tasks.index');
    }
}

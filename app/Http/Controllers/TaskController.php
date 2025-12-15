<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:task_statuses,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ], [
            'name.required' => __('controllers.unique_error_task'),
            'status_id.exists' => 'The selected status is invalid.',
            'assigned_to_id.exists' => 'The selected user is invalid.',
        ]);

        $task = Task::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status_id' => (int) $data['status_id'],
            'assigned_to_id' => isset($data['assigned_to_id']) ? (int) $data['assigned_to_id'] : null,
            'created_by_id' => Auth::id(),
        ]);

        if (isset($data['labels'])) {
            $task->labels()->attach($data['labels']);
        }

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

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:task_statuses,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        $task->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'status_id' => (int) $data['status_id'],
            'assigned_to_id' => $request->filled('assigned_to_id') ? (int) $data['assigned_to_id'] : null,
        ]);

        $labels = collect($data['labels'] ?? [])->filter()->values()->all();
        $task->labels()->sync($labels);

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

<?php

namespace App\Http\Controllers;

// use App\Http\Requests\TaskRequest;
use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    // public function index(Request $request)
    // {
    //     $data = $request->validate([
    //         'filter' => 'nullable|array',
    //     ]);

    //     $filterTasks = QueryBuilder::for(Task::class)
    //         ->allowedFilters([
    //             AllowedFilter::exact('status_id'),
    //             AllowedFilter::exact('created_by_id'),
    //             AllowedFilter::exact('assigned_to_id'),
    //         ])
    //         ->with(['status', 'createdBy', 'assignedTo']);

    //     $tasks = $filterTasks->paginate();
    //     $taskStatuses = TaskStatus::all();
    //     $users = User::all();

    //     $filter = [
    //         'status_id' => $request->input('filter.status_id'),
    //         'created_by_id' => $request->input('filter.created_by_id'),
    //         'assigned_to_id' => $request->input('filter.assigned_to_id'),
    //     ];

    //     return view('tasks.index', compact('tasks', 'taskStatuses', 'users', 'filter'));
    // }

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
        // $validated = $request->validated();
        // /** @var \App\Models\User $user */
        // $user = Auth::user();
        // $task = $user->createdTasks()->create($validated);

        // if (!empty($validated['labels'])) {
        //     $task->labels()->attach(array_filter($validated['labels']));
        // }

        // flash(__('controllers.tasks_create'))->success();

        // return redirect()->route('tasks.index');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => [
                'required',
                Rule::exists('task_statuses', 'id')
            ],
            'assigned_to_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ],
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ], [
            'name.required' => __('controllers.unique_error_task'),
            'status_id.exists' => 'The selected status is invalid.',
            'assigned_to_id.exists' => 'The selected user is invalid.',
        ]);

        $data['status_id'] = (int) $data['status_id'];

        if (isset($data['assigned_to_id'])) {
            $data['assigned_to_id'] = (int) $data['assigned_to_id'];
        }

        $task = new Task();
        $task->fill($data);
        $task->created_by_id = Auth::id();
        $task->save();

        if (isset($data['labels'])) {
            $task->labels()->attach($data['labels']);
        }

        flash(__('controllers.tasks_create'))->success();

        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        $taskStatus = TaskStatus::findOrFail($task->status_id)->name;
        return view('tasks.show', compact('task', 'taskStatus'));

        // $task->load('status', 'createdBy', 'assignedTo', 'labels');
        // return view('tasks.show', compact('task'));
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
            'status_id' => [
                'required',
                Rule::exists('task_statuses', 'id')
            ],
            'assigned_to_id' => [
                'nullable',
                Rule::exists('users', 'id')
            ],
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        $data['status_id'] = (int) $data['status_id'];
        if (isset($data['assigned_to_id'])) {
            $data['assigned_to_id'] = (int) $data['assigned_to_id'];
        }

        $task->update($data);

        if (isset($data['labels'])) {
            $task->labels()->sync($data['labels']);
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

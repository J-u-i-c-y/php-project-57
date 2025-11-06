<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'filter' => "nullable|array"
        ]);
        $filter = [
            'status_id' => null,
            'creator_by_id' => null,
            'assigned_by_id' => null
        ];

        $filterTasks = QueryBuilder::for(Task::class);

        if (!empty($data['filter'])) {
            $filter = $data['filter'];
            foreach ($data['filter'] as $key => $value) {
                if (!is_null($value)) {
                    $filterTasks = $filterTasks->where($key, $value);
                }
            }
        }

        $tasks = $filterTasks->paginate();
        $taskStatuses = new TaskStatus();
        $users = new User();
        return view('tasks.index', compact('tasks', 'taskStatuses', 'users', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (Auth::guest()) {
            return abort(403);
        }
        $taskStatuses = TaskStatus::all();
        $users = User::all();
        $labels = Label::all();

        return view('tasks.create', compact('taskStatuses', 'users', 'labels'));
    }

    public function store(Request $request)
    {

        Log::info('Store method called', [
        'authenticated' => Auth::check(),
        'user_id' => Auth::id(),
        'request_data' => $request->all()
    ]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required',
            'assigned_by_id' => 'nullable',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        if (!TaskStatus::where('id', $data['status_id'])->exists()) {
            return redirect()->back()->withErrors(['status_id' => 'The selected status is invalid.']);
        }

        if (isset($data['assigned_by_id']) && !User::where('id', $data['assigned_by_id'])->exists()) {
            return redirect()->back()->withErrors(['assigned_by_id' => 'The selected user is invalid.']);
        }

        $data['status_id'] = (int) $data['status_id'];
        if (isset($data['assigned_by_id'])) {
            $data['assigned_by_id'] = (int) $data['assigned_by_id'];
        }

        $task = new Task();
        $task->fill($data);
        $task->creator_by_id = Auth::id();
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
    }

    public function edit(Task $task)
    {
        if (Auth::guest()) {
            return abort(403);
        }
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
            'status_id' => 'required',
            'assigned_by_id' => 'nullable',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        if (!TaskStatus::where('id', $data['status_id'])->exists()) {
            return redirect()->back()->withErrors(['status_id' => 'The selected status is invalid.']);
        }

        if (isset($data['assigned_by_id']) && !User::where('id', $data['assigned_by_id'])->exists()) {
            return redirect()->back()->withErrors(['assigned_by_id' => 'The selected user is invalid.']);
        }

        $data['status_id'] = (int) $data['status_id'];
        if (isset($data['assigned_by_id'])) {
            $data['assigned_by_id'] = (int) $data['assigned_by_id'];
        }

        $task->update($data);

        if (isset($data['labels'])) {
            $task->labels()->sync($data['labels']);
        } else {
            $task->labels()->detach();
        }

        flash('Task has been updated successfully!')->success();
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        if (Auth::guest()) {
            return abort(403);
        }
        if (Auth::id() === $task->creator_by_id) {
            $task->labels()->detach();
            $task->delete();
            flash(__('controllers.tasks_destroy'))->success();
        } else {
            flash(__('controllers.tasks_destroy_failed'))->error();
        }
        return redirect()->route('tasks.index');
    }
}

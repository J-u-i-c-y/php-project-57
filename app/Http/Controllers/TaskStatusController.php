<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use App\Http\Requests\TaskStatusRequest;

class TaskStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TaskStatus::class);
    }

    public function index()
    {
        $taskStatuses = TaskStatus::paginate();

        return view('task_statuses.index', compact('taskStatuses'));
    }

    public function show()
    {
        return redirect()->route('task_statuses.index');
    }

    public function create()
    {
        $this->authorize('create', TaskStatus::class);
        return view('task_statuses.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', TaskStatus::class);

        $data = $request->validate([
            'name' => 'required|unique:task_statuses',
        ], [
            'name.unique' => __('controllers.unique_error_status'),
        ]);

        $taskStatus = new TaskStatus();
        $taskStatus->fill($data);
        $taskStatus->save();
        flash(__('controllers.task_statuses_create'))->success();

        return redirect()->route('task_statuses.index');
    }

    public function edit(TaskStatus $taskStatus)
    {
        $this->authorize('update', $taskStatus);
        return view('task_statuses.edit', compact('taskStatus'));
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $this->authorize('update', $taskStatus);

        $data = $request->validate([
            'name' => "required|unique:task_statuses,name,{$taskStatus->id}",
        ]);
        $taskStatus->fill($data);
        $taskStatus->save();
        flash(__('controllers.task_statuses_update'))->success();

        return redirect()->route('task_statuses.index');
    }

    public function destroy(TaskStatus $taskStatus)
    {
        $this->authorize('delete', $taskStatus);

        if ($taskStatus->tasks()->exists()) {
            flash(__('layout.delete_error'))->error();

            return redirect()->route('task_statuses.index');
        }

        $taskStatus->delete();

        flash(__('controllers.task_statuses_destroy'))->success();

        return redirect()->route('task_statuses.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStatusStoreRequest;
use App\Http\Requests\TaskStatusUpdateRequest;
use App\Models\TaskStatus;

class TaskStatusController extends Controller
{
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
        return view('task_statuses.create');
    }

    public function store(TaskStatusStoreRequest $request)
    {
        TaskStatus::create($request->validated());
        flash(__('controllers.task_statuses_create'))->success();
        return redirect()->route('task_statuses.index');
    }

    public function edit(TaskStatus $taskStatus)
    {
        return view('task_statuses.edit', compact('taskStatus'));
    }

    public function update(TaskStatusUpdateRequest $request, TaskStatus $taskStatus)
    {
        $taskStatus->update($request->validated());
        flash(__('controllers.task_statuses_update'))->success();
        return redirect()->route('task_statuses.index');
    }

    public function destroy(TaskStatus $taskStatus)
    {
        try {
            $this->authorize('delete', $taskStatus);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            flash(__('layout.delete_error'))->error();
            return redirect()->route('task_statuses.index');
        }

        $taskStatus->delete();
        flash(__('controllers.task_statuses_destroy'))->success();
        return redirect()->route('task_statuses.index');
    }
}

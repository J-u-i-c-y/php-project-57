@extends('layouts.app')
@section('content')

    <div class="grid col-span-full">
        @include('flash::message')
        <h1 class="mb-5">{{ __('layout.tasks') }}</h1>
        <div class="w-full flex items-center">
            <div>
                <form method="GET" action="{{ route('tasks.index') }}">
                    <div class="flex">
                        <select class="rounded border-gray-300" name="filter[status_id]" id="filter[status_id]">
                            <option value selected="selected">{{ __('layout.table_task_status') }}</option>
                            @foreach ($taskStatuses->all() as $status)
                                <option value="{{ $status->id }}" {{ $status->id == $filter['status_id'] ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>


                        <select class="rounded border-gray-300" name="filter[assigned_to_id]" id="filter[assigned_to_id]">
                            <option value selected="selected">{{ __('layout.table_creater') }}</option>
                            @foreach ($users->all() as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $filter['assigned_to_id'] ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>

                        <select class="rounded border-gray-300" name="filter[assigned_to_id]" id="filter[assigned_to_id]">
                            <option value selected="selected">{{ __('layout.table_assigned') }}</option>
                            @foreach ($users->all() as $user)
                                <option value="{{ $user->id }}" {{ $user->id == $filter['assigned_to_id'] ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2" type="submit">{{ __('layout.create_apply') }}</button>
                    </div>
                </form>
            </div>
        <div class="ml-auto">
            @auth
                <a href="{{ route('tasks.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('layout.create_button_task') }}
                </a>
            @endauth
        </div>
        </div>

        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>{{ __('layout.table_id') }}</th>
                <th>{{ __('layout.table_task_status') }}</th>
                <th>{{ __('layout.table_name') }}</th>
                <th>{{ __('layout.table_creater') }}</th>
                <th>{{ __('layout.table_assigned') }}</th>
                <th>{{ __('layout.table_date_of_creation') }}</th>
                @auth
                    <th>{{ __('layout.table_actions') }}</th>
                @endauth
            </tr>
            </thead>
            @foreach ($tasks as $task)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $task->id }}</td>
                    <td>{{ $taskStatuses::find($task->status_id)->name }}</td>
                    <td><a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.show', $task->id) }}">{{ $task->name }}</a></td>
                    <td>{{ $users::find($task->creator_by_id)->name }}</td>
                    <td>{{ $users::find($task->assigned_by_id)->name ?? '' }}</td>
                    <td>{{ $task->created_at->format('Y-m-d') }}</td>
                    @auth
                        <td>
                            @if (Auth::user()->id === $task->creator_by_id)
                                <a data-confirm="{{ __('layout.table_delete_question') }}"
                                   data-method="delete"
                                   class="text-red-600 hover:text-red-900"
                                   href="{{ route('tasks.destroy', $task) }}"
                                   rel="nofollow">{{ __('layout.table_delete') }}</a>
                                @csrf
                                @method('DELETE')
                            @endif
                                <a class="text-blue-600 hover:text-blue-900" href="{{ route('tasks.edit', $task) }}">
                                    {{ __('layout.table_edit') }}</a></a>
                        </td>
                    @endauth
                </tr>
        @endforeach
    </div>
        </table>
@endsection
@section('pagination')
    {{ $tasks->links() }}
@endsection

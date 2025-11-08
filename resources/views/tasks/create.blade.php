@extends('layouts.app')
@section('content')
    <div class="grid col-span-full">
        <h1 class="mb-5">{{ __('layout.tasks_create') }}</h1>
        <form class="w-50" method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="flex flex-col">
                <div>
                    <label for="name">{{ __('layout.table_name') }}</label>
                </div>
                <div class="mt-2">
                    <input class="rounded border-gray-300 w-1/3" type="text" name="name" id="name" value="{{ old('name') }}">
                </div>
                @error('name')
                <div class="text-rose-600">{{ $message }}</div>
                @enderror
                <div class="mt-2">
                    <label for="description">{{ __('layout.table_description') }}</label>
                </div>
                <div>
                    <textarea class="rounded border-gray-300 w-1/3 h-32" name="description" id="description">{{ old('description') }}</textarea>
                </div>
                @error('description')
                <div class="text-rose-600">{{ $message }}</div>
                @enderror
                <div class="mt-2">
                    <label for="status_id">{{ __('layout.table_task_status') }}</label>
                </div>
                <div>
                    <select class="rounded border-gray-300 w-1/3" name="status_id" id="status_id">
                        <option value selected="selected"></option>
                        @foreach ($taskStatuses->all() as $status)
                            <option value="{{ $status->id }}" {{ $status->id == old('status_id') ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                   </select>
                </div>

            @error('status_id')
                <div class="text-rose-600">{{ $message }}</div>
            @enderror
            <div class="mt-2">
                <label for="assigned_to_id">{{ __('layout.table_assigned') }}</label>
            </div>
            <div>
                <select class="rounded border-gray-300 w-1/3" name="assigned_to_id" id="assigned_to_id">
                    <option value selected="selected"></option>
                    @foreach ($users->all() as $user)
                        <option value="{{ $user->id }}" {{ $user->id == old('assigned_to_id') ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                </select>
            </div>
                @error('assigned_to_id')
                    <div class="text-rose-600">{{ $message }}</div>
                @enderror
            <div class="mt-2">
                <label for="labels[]">{{ __('layout.labels_header') }}</label>
            </div>
            <div>
                <select class="rounded border-gray-300 w-1/3 h-32" name="labels[]" id="labels[]" multiple>
                    @foreach ($labels->all() as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mt-2">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">{{ __('layout.create_button') }}</button>
            </div>
        </div>
    </form>
    </div>
@endsection

@extends('layouts.app')
@section('content')

    <div class="grid col-span-full">
        @include('flash::message')
        <h1 class="mb-5">{{ __('layout.labels') }}</h1>
        <div>
            @auth
                <a href="{{ route('labels.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('layout.labels_create') }}
                </a>
            @endauth
        </div>
        <table class="mt-4">
            <thead class="border-b-2 border-solid border-black text-left">
            <tr>
                <th>{{ __('layout.table_id') }}</th>
                <th>{{ __('layout.table_name') }}</th>
                <th>{{ __('layout.table_description') }}</th>
                <th>{{ __('layout.table_date_of_creation') }}</th>
                @auth
                    <th>{{ __('layout.table_actions') }}</th>
                @endauth
            </tr>
            </thead>
            @foreach ($labels as $label)
                <tr class="border-b border-dashed text-left">
                    <td>{{ $label->id }}</td>
                    <td>{{ $label->name }}</td>
                    <td>{{ $label->description }}</td>
                    <td>{{ $label->created_at->format('Y-m-d') }}</td>
                    @auth
                        <td>
                            <form action="{{ route('labels.destroy', $label) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Вы уверены?');">
                                    {{ __('layout.table_delete') }}
                                </button>
                            </form>
                            <a class="text-blue-600 hover:text-blue-900" href="{{ route('labels.edit', $label) }}">
                                {{ __('layout.table_edit') }}</a>
                            
                        </td>
                    @endauth
                </tr>
        @endforeach
    </div>
    </section>
    </div>
    @endsection
@section('pagination')
    {{ $labels->links() }}
@endsection


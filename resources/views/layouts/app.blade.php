<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-param" content="_token" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @csrf

    <title>{{ __('layout.title') }}</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
</head>
<body>
<div id='app'>
    <header class="fixed w-full">
        <nav class="bg-white border-gray-300 py-2.5 dark:bg-gray-700 shadow-md">
            <div class="flex flex-wrap items-center justify-between max-w-screen-xl px-4 mx-auto">
                <a href="{{ route('welcome') }}" class="flex items-center">
                    <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">{{ __('layout.title') }}</span>
                </a>
                <div class="flex items-center lg:order-2">
                    @guest
                        <a href="/login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Войти</a>
                        <a href="/register" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2">Зарегистрировать</a>
                    @endguest
                    @auth
                        <a href="/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded ml-2"> {{ __('layout.logout') }} </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                    @endauth
                    
                </div>
                <div class="items-center justify-between hidden w-full lg:flex lg:w-auto lg:order-1">
                    <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-8 lg:mt-0">
                        <li>
                            <a href="{{ route('tasks.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">
                                {{ __('layout.task_header') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('task_statuses.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">
                                {{ __('layout.task_statuses_header') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('labels.index') }}" class="block py-2 pl-3 pr-4 text-gray-700 hover:text-blue-700 lg:p-0">
                                {{ __('layout.labels_header') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <section class="bg-white dark:bg-gray-700">
        <div class="grid max-w-screen-xl px-4 pt-20 pb-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12 lg:pt-28">
            @yield('content')
            <div class="mt-4">
                @yield('pagination')
            </div>
        </div>
    </section>
</div>
</body>

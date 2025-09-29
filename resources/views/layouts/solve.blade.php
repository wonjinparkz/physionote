<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'PhysioNote'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script src="{{ mix('js/app.js') }}" defer></script>

    @stack('styles')
</head>
<body class="font-sans bg-gray-light min-h-screen flex justify-center items-start p-0 m-0 antialiased">
    <div class="app-container">
        <!-- Simplified Header for Question Solving -->
        <header class="bg-white px-5 py-4 border-b border-border z-50 relative flex items-center justify-center">
            <a href="{{ route('questions.index') }}" class="absolute left-5 text-gray-700 hover:text-gray-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <span class="font-medium text-gray-900">
                @if(isset($year))
                    {{ $year }}년도 기출문제
                @else
                    문제 풀기
                @endif
            </span>
        </header>

        <main class="flex-1 pb-20 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Footer Component -->
        @php
            $active = 'questions';
        @endphp
        <x-footer :active="$active" />
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    @stack('scripts')
</body>
</html>
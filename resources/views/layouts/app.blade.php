<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PhysioNote') }}</title>

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
        <header class="bg-white px-5 py-4 border-b border-border z-50 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">PhysioNote</h1>
            @guest
            <div class="flex gap-2">
                <a href="/register" class="px-3 py-1.5 bg-primary text-white rounded-md text-sm font-medium transition-colors hover:bg-primary-dark">회원가입</a>
                <a href="/login" class="px-3 py-1.5 bg-transparent text-primary border border-primary rounded-md text-sm font-medium transition-colors hover:bg-blue-50">로그인</a>
            </div>
            @else
            <div class="flex gap-2">
                <a href="/logout" class="px-3 py-1.5 bg-transparent text-primary border border-primary rounded-md text-sm font-medium transition-colors hover:bg-blue-50"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">로그아웃</a>
            </div>
            <form id="logout-form" action="/logout" method="POST" class="hidden">
                @csrf
            </form>
            @endguest
        </header>

        <nav class="bg-white px-5 border-b border-border flex gap-5 overflow-x-auto scrollbar-hide">
            <a href="/" class="menu-item {{ request()->is('/') ? 'active' : '' }}">홈</a>
            <a href="/problems" class="menu-item {{ request()->is('problems*') ? 'active' : '' }}">문제</a>
            <a href="/clinical" class="menu-item {{ request()->is('clinical*') ? 'active' : '' }}">임상정보</a>
            <a href="#" onclick="alert('커뮤니티 기능은 준비중입니다.'); return false;" class="menu-item">커뮤니티</a>
            <a href="/mypage" class="menu-item {{ request()->is('mypage*') ? 'active' : '' }}">마이페이지</a>
        </nav>

        <main class="flex-1 pb-20 overflow-y-auto">
            @yield('content')
        </main>

        <!-- Footer Component -->
        @php
            $active = 'home';
            if (request()->is('questions*') || request()->is('problems*')) {
                $active = 'questions';
            } elseif (request()->is('contents*')) {
                $active = 'contents';
            } elseif (request()->is('mypage*')) {
                $active = 'mypage';
            }
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
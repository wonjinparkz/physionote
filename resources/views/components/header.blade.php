@props([
    'type' => 'default',
    'title' => null,
    'backAction' => 'history.back()'
])

@if($type === 'content')
    <!-- Content Header with Back Button -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="flex items-center justify-between h-14 px-4">
            <!-- Back Button -->
            <button onclick="{{ $backAction }}" class="p-2 -ml-2">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Title -->
            <h1 class="text-base font-medium text-gray-900 flex-1 text-center mx-2 truncate">
                {{ $title }}
            </h1>

            <!-- Right Action Button -->
            @yield('header_action')
        </div>
    </header>
@else
    <!-- Default Header -->
    <header class="bg-white border-b border-gray-200">
        <div class="flex items-center h-16 px-5">
            <a href="/" class="flex items-center">
                <img src="/images/logo.png" alt="PhysioNote" class="h-8 mr-2" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <span class="text-xl font-bold text-primary" style="display:none;">PhysioNote</span>
            </a>

            <div class="ml-auto flex items-center space-x-4">
                @guest
                    <a href="/register" class="px-4 py-2 text-sm text-gray-600 hover:text-primary">회원가입</a>
                    <a href="/login" class="px-4 py-2 text-sm bg-primary text-white rounded-lg hover:bg-primary-dark">로그인</a>
                @endguest
            </div>
        </div>
    </header>
@endif
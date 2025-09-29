@extends('layouts.auth')

@section('title', '로그인')
@section('header_title', '로그인')

@section('content')
<div class="px-5 py-8">
    <div class="bg-white">

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- 이메일 로그인 폼 -->
        <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">이메일</label>
                <input type="email" name="email" id="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF]"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">비밀번호</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF]">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded text-[#4056FF] focus:ring-[#4056FF]">
                    <span class="ml-2 text-sm text-gray-600">로그인 상태 유지</span>
                </label>
                <a href="#" class="text-sm text-[#4056FF] hover:text-[#3447E6]">
                    비밀번호 찾기
                </a>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 bg-[#4056FF] text-white rounded-lg hover:bg-[#3447E6] transition-colors font-medium">
                로그인
            </button>
        </form>

        <!-- 회원가입 안내 섹션 -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600 mb-3">혹시, 회원이 아니신가요?</p>
            <a href="{{ route('register') }}"
               class="inline-block w-full py-3 px-4 bg-white border-2 border-[#4056FF] text-[#4056FF] rounded-lg hover:bg-[#F4F5FF] transition-colors font-medium">
                회원가입
            </a>
        </div>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">간편 회원가입/로그인</span>
            </div>
        </div>

        <!-- 소셜 로그인 섹션 -->
        <div class="space-y-3">
            <!-- 네이버 로그인 -->
            <a href="{{ route('social.redirect', 'naver') }}"
               class="flex items-center justify-center w-full py-3 px-4 rounded-lg bg-[#02C75A] hover:bg-[#01B351] transition-colors">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="20" height="20" rx="4" fill="white"/>
                    <path d="M13.5 14.5H10.8529L7.5 9.5V14.5H5V5.5H7.64706L11 10.5V5.5H13.5V14.5Z" fill="#02C75A"/>
                </svg>
                <span class="font-medium text-white">네이버로 시작하기</span>
            </a>

            <!-- 카카오 로그인 -->
            <a href="{{ route('social.redirect', 'kakao') }}"
               class="flex items-center justify-center w-full py-3 px-4 rounded-lg bg-[#FEE500] hover:bg-[#FDD835] transition-colors">
                <svg class="w-5 h-5 mr-3" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3C5.58172 3 2 5.95885 2 9.56757C2 11.8676 3.45004 13.8743 5.64379 15.0405L4.86621 17.8649C4.80625 18.0811 5.0525 18.2568 5.24583 18.1351L8.74167 15.973C9.15417 16.027 9.57292 16.0541 10 16.0541C14.4183 16.0541 18 13.1757 18 9.56757C18 5.95885 14.4183 3 10 3Z" fill="#371C1D"/>
                </svg>
                <span class="font-medium text-[#371C1D]">카카오로 시작하기</span>
            </a>
        </div>

    </div>
</div>
@endsection
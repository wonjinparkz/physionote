@extends('layouts.content')

@section('title', '학습 기록 - PhysioNote')
@section('header_title', '학습 기록')
@section('back_action', 'window.location.href=\'/mypage\'')

@section('content')
    <!-- Placeholder for Study History -->
    <div class="bg-white px-5 py-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <p class="text-gray-500 text-sm">아직 학습 기록이 없습니다.</p>
        <a href="/questions" class="inline-block mt-4 px-5 py-2.5 bg-[#4056FF] text-white rounded-lg text-sm font-medium">
            문제 풀기 시작
        </a>
    </div>
@endsection
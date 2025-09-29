@extends('layouts.app')

@section('title', '문제 풀기')

@section('content')
<div class="container mx-auto px-5 py-8 max-w-4xl">
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($yearData as $data)
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6 flex items-center justify-between">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            {{ $data['year'] }}년도 기출문제
                        </h2>

                        <div class="flex items-center gap-6 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <span>{{ $data['total_questions'] }}문제</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>예상 {{ ceil($data['total_questions'] * 1.5) }}분</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('questions.start', $data['first_category_id']) }}"
                       class="flex-shrink-0 bg-blue-600 text-white px-6 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        문제 풀기
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-gray-400 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <p class="text-gray-500 text-lg">등록된 문제가 없습니다.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
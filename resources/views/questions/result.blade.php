@extends('layouts.app')

@section('title', '문제 풀기 결과')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <h1 class="text-3xl font-bold text-center mb-8">문제 풀기 결과</h1>

    <!-- 결과 요약 카드 -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- 점수 -->
            <div class="text-center">
                <div class="text-4xl font-bold {{ $score >= 80 ? 'text-green-600' : ($score >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $score }}점
                </div>
                <div class="text-gray-600">최종 점수</div>
            </div>

            <!-- 정답률 -->
            <div class="text-center">
                <div class="text-2xl font-semibold text-green-600">{{ $correctCount }}개</div>
                <div class="text-gray-600">정답</div>
            </div>

            <!-- 오답 -->
            <div class="text-center">
                <div class="text-2xl font-semibold text-red-600">{{ $wrongCount }}개</div>
                <div class="text-gray-600">오답</div>
            </div>

            <!-- 미답변 -->
            <div class="text-center">
                <div class="text-2xl font-semibold text-gray-600">{{ $unanswered }}개</div>
                <div class="text-gray-600">미답변</div>
            </div>
        </div>

        <!-- 추가 정보 -->
        <div class="border-t pt-4 flex justify-between items-center">
            <div>
                <span class="text-gray-600">카테고리:</span>
                <span class="font-semibold">{{ $category['name'] ?? '전체' }}</span>
            </div>
            <div>
                <span class="text-gray-600">소요 시간:</span>
                <span class="font-semibold">{{ $duration }}분</span>
            </div>
            <div>
                <span class="text-gray-600">총 문제 수:</span>
                <span class="font-semibold">{{ $totalQuestions }}개</span>
            </div>
        </div>
    </div>

    <!-- 문제별 결과 -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">문제별 정답 확인</h2>

        <div class="space-y-6">
            @foreach($questions as $index => $question)
                @php
                    $userAnswer = $userAnswers[$question->id] ?? null;
                    $isCorrect = $userAnswer == $question->answer;
                    $isUnanswered = $userAnswer === null;
                @endphp

                <div class="border rounded-lg p-4 {{ $isCorrect ? 'border-green-300 bg-green-50' : ($isUnanswered ? 'border-gray-300 bg-gray-50' : 'border-red-300 bg-red-50') }}">
                    <!-- 문제 헤더 -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold">문제 {{ $question->no }}</span>
                            @if($isCorrect)
                                <span class="text-green-600 font-semibold">✓ 정답</span>
                            @elseif($isUnanswered)
                                <span class="text-gray-600">- 미답변</span>
                            @else
                                <span class="text-red-600 font-semibold">✗ 오답</span>
                            @endif
                        </div>
                        <button onclick="toggleDetail({{ $index }})"
                                class="text-blue-600 hover:text-blue-700">
                            상세보기 ▼
                        </button>
                    </div>

                    <!-- 답안 정보 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <span class="text-gray-600">내 답안:</span>
                            <span class="font-semibold {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                {{ $userAnswer ? $userAnswer . '번' : '답변하지 않음' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">정답:</span>
                            <span class="font-semibold text-blue-600">
                                {{ $question->answer ? $question->answer . '번' : '정답 없음' }}
                            </span>
                        </div>
                    </div>

                    <!-- 상세 내용 (토글) -->
                    <div id="detail-{{ $index }}" class="hidden mt-4 pt-4 border-t">
                        <!-- 문제 -->
                        <div class="mb-4">
                            <h4 class="font-semibold mb-2">문제:</h4>
                            <div class="prose max-w-none">
                                {!! $question->question !!}
                            </div>
                        </div>

                        <!-- 보기 -->
                        <div class="mb-4">
                            <h4 class="font-semibold mb-2">보기:</h4>
                            <div class="space-y-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @php
                                        $optionField = 'option_' . $i;
                                        $isUserAnswer = $userAnswer == $i;
                                        $isCorrectAnswer = $question->answer == $i;
                                    @endphp
                                    <div class="p-2 rounded {{ $isCorrectAnswer ? 'bg-green-100 border border-green-300' : ($isUserAnswer && !$isCorrectAnswer ? 'bg-red-100 border border-red-300' : '') }}">
                                        <span class="font-semibold">{{ $i }}번</span>
                                        @if($isCorrectAnswer)
                                            <span class="text-green-600 text-sm ml-2">✓ 정답</span>
                                        @endif
                                        @if($isUserAnswer && !$isCorrectAnswer)
                                            <span class="text-red-600 text-sm ml-2">✗ 내 답안</span>
                                        @endif
                                        <div class="mt-1 prose prose-sm max-w-none">
                                            {!! $question->$optionField !!}
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- 해설 -->
                        @if($question->explanation)
                            <div class="mt-4 p-4 bg-blue-50 rounded">
                                <h4 class="font-semibold mb-2">해설:</h4>
                                <div class="prose max-w-none">
                                    {!! $question->explanation !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 액션 버튼 -->
    <div class="mt-8 flex justify-center gap-4">
        <a href="{{ route('questions.index') }}"
           class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition-colors">
            문제 목록으로
        </a>
        @if($category)
            <a href="{{ route('questions.start', $category['id']) }}"
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                다시 풀기
            </a>
        @endif
    </div>
</div>

<script>
    function toggleDetail(index) {
        const detail = document.getElementById('detail-' + index);
        detail.classList.toggle('hidden');
    }
</script>
@endsection
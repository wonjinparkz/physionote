@extends('layouts.solve')

@section('title', '문제 풀기 - ' . ($currentIndex + 1) . '번')

@section('content')
<div class="container mx-auto px-5 py-8 max-w-4xl">
    <!-- 교시 네비게이션 -->
    @if(!empty($classInfo))
    <div class="mb-6 flex gap-2 justify-center">
        @foreach($classInfo as $classTime => $info)
            <a href="{{ route('questions.jump.class', $classTime) }}"
               class="px-6 py-2 rounded-lg font-medium transition-colors
                      {{ $currentClass == $classTime ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                {{ $info['name'] }}
            </a>
        @endforeach
    </div>
    @endif

    <!-- 진행 상황 바 -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium">
                @if($year && $currentClass)
                    {{ $year }}년도 {{ $currentClass }}교시
                @else
                    {{ $category['name'] ?? '문제 풀기' }}
                @endif
            </span>
            <span class="text-sm font-medium">
                {{ $answeredInClass ?? 0 }} / {{ $classQuestionsCount }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ max(0, min(100, $progress)) }}%"></div>
        </div>
        <!-- Debug: Progress = {{ $progress }}% -->
    </div>

    <!-- 문제 카드 -->
    <div class="bg-white mb-6">
        <!-- 문제 번호 -->
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-blue-600">문제 {{ $question->no }}</h2>
        </div>

        <!-- 문제 내용 -->
        <div class="prose max-w-none mb-6">
            {!! $question->question !!}
        </div>

        <!-- 보기 -->
        <form id="questionForm" action="{{ route('questions.submit') }}" method="POST">
            @csrf
            <div class="space-y-3">
                @for($i = 1; $i <= 5; $i++)
                    @php
                        $optionField = 'option_' . $i;
                        $isSelected = isset($userAnswers[$question->id]) && $userAnswers[$question->id] == $i;
                    @endphp
                    <label class="block cursor-pointer option-label" data-option="{{ $i }}">
                        <div id="option-{{ $i }}" class="relative flex items-start p-4 border-2 rounded-lg transition-all duration-300
                                    {{ $isSelected ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                            <input type="radio"
                                   name="answer"
                                   value="{{ $i }}"
                                   {{ $isSelected ? 'checked' : '' }}
                                   class="mt-1 mr-3 text-blue-600 option-radio"
                                   onchange="selectAnswer({{ $i }})">
                            <div class="flex-1 pr-16">
                                <div class="prose prose-sm max-w-none">
                                    {!! $question->$optionField !!}
                                </div>
                            </div>
                            <span id="result-{{ $i }}" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2 font-semibold">
                                <!-- 정답/오답 표시가 여기에 들어감 -->
                            </span>
                        </div>
                    </label>
                @endfor
            </div>

            <!-- 해설 영역 (숨김 상태로 시작) -->
            <div id="explanationBox" class="hidden mt-6 p-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg">
                <h3 class="font-bold text-lg mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    해설
                </h3>
                @if($question->explanation)
                    <div class="prose max-w-none">
                        {!! $question->explanation !!}
                    </div>
                @else
                    <p class="text-gray-600">해설이 없습니다.</p>
                @endif
            </div>

            <input type="hidden" id="correctAnswer" value="{{ $question->answer }}">

            <!-- 네비게이션 버튼 -->
            <div class="flex justify-between items-center mt-8">
                @if($currentIndex > 0)
                    <a href="{{ route('questions.previous') }}"
                       class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        이전 문제
                    </a>
                @else
                    <div></div>
                @endif

                @if($currentIndex < $totalQuestions - 1)
                    <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        다음 문제
                    </button>
                @else
                    <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        제출하기
                    </button>
                @endif
            </div>
        </form>
    </div>

</div>

<script>
    let hasAnswered = false;

    function selectAnswer(selectedAnswer) {
        // 이미 답변했으면 리턴
        if (hasAnswered) return;

        hasAnswered = true;
        const correctAnswer = parseInt(document.getElementById('correctAnswer').value);

        // 답변 저장
        saveAnswer(selectedAnswer);

        // 모든 옵션 비활성화
        document.querySelectorAll('.option-radio').forEach(radio => {
            radio.disabled = true;
        });

        // 모든 옵션 라벨의 hover 효과 제거
        document.querySelectorAll('.option-label').forEach(label => {
            label.classList.remove('cursor-pointer');
        });

        // 선택한 답안 처리
        const selectedOption = document.getElementById(`option-${selectedAnswer}`);
        const selectedResult = document.getElementById(`result-${selectedAnswer}`);

        if (selectedAnswer === correctAnswer) {
            // 정답인 경우
            selectedOption.classList.remove('border-gray-200');
            selectedOption.classList.add('border-blue-500', 'bg-blue-50');
            selectedResult.innerHTML = '<span class="text-blue-600">✓ 정답</span>';
            selectedResult.classList.remove('hidden');
        } else {
            // 오답인 경우
            selectedOption.classList.remove('border-gray-200');
            selectedOption.classList.add('border-red-500', 'bg-red-50');
            selectedResult.innerHTML = '<span class="text-red-600">✗ 오답</span>';
            selectedResult.classList.remove('hidden');

            // 정답 표시
            const correctOption = document.getElementById(`option-${correctAnswer}`);
            const correctResult = document.getElementById(`result-${correctAnswer}`);
            correctOption.classList.remove('border-gray-200');
            correctOption.classList.add('border-blue-500', 'bg-blue-50');
            correctResult.innerHTML = '<span class="text-blue-600">✓ 정답</span>';
            correctResult.classList.remove('hidden');
        }

        // 해설 표시
        document.getElementById('explanationBox').classList.remove('hidden');

        // 1.5초 후 자동으로 다음 문제로 이동 (원하면 주석 처리)
        // setTimeout(() => {
        //     document.getElementById('questionForm').submit();
        // }, 1500);
    }


    // 답변 저장 함수
    function saveAnswer(answer) {
        fetch('{{ route('questions.submit') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                answer: answer,
                save_only: true
            })
        });
    }

    // 페이지 로드 시 이미 답변한 경우 처리
    window.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('.option-radio:checked');
        if (checkedRadio) {
            const answeredValue = parseInt(checkedRadio.value);
            const correctAnswer = parseInt(document.getElementById('correctAnswer').value);

            // 이미 답변한 상태 표시
            hasAnswered = true;

            // 라디오 버튼 비활성화
            document.querySelectorAll('.option-radio').forEach(radio => {
                radio.disabled = true;
            });

            // 정답/오답 표시
            if (answeredValue === correctAnswer) {
                document.getElementById(`result-${answeredValue}`).innerHTML = '<span class="text-blue-600">✓ 정답</span>';
                document.getElementById(`result-${answeredValue}`).classList.remove('hidden');
            } else {
                document.getElementById(`result-${answeredValue}`).innerHTML = '<span class="text-red-600">✗ 오답</span>';
                document.getElementById(`result-${answeredValue}`).classList.remove('hidden');

                document.getElementById(`option-${correctAnswer}`).classList.add('border-blue-500', 'bg-blue-50');
                document.getElementById(`result-${correctAnswer}`).innerHTML = '<span class="text-blue-600">✓ 정답</span>';
                document.getElementById(`result-${correctAnswer}`).classList.remove('hidden');
            }

            // 해설 표시
            document.getElementById('explanationBox').classList.remove('hidden');
        }
    });
</script>
@endsection
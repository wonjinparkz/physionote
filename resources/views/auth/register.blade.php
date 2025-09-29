@extends('layouts.auth')

@section('title', '회원가입')
@section('header_title', '회원가입')

@section('content')
<div class="px-5 py-6">
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

        <!-- 회원가입 폼 -->
        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <!-- 이메일 -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">이메일</label>
                <input type="email" name="email" id="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF] text-base"
                       value="{{ old('email') }}"
                       placeholder="example@xxx.com">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 비밀번호 -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-900 mb-2">비밀번호</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF] text-base"
                       placeholder="영문/숫자/특수문자 조합 8~20자">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 비밀번호 확인 -->
            <div>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF] text-base"
                       placeholder="비밀번호 확인">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 이름 -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">이름</label>
                <input type="text" name="name" id="name" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4056FF] focus:border-[#4056FF] text-base"
                       value="{{ old('name') }}"
                       placeholder="이름 입력">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- 약관 동의 -->
            <div class="space-y-3 pt-4">
                <!-- 전체 동의 -->
                <label class="flex items-start">
                    <input type="checkbox" id="agree_all" class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                    <span class="ml-2 text-sm font-semibold text-gray-900">전체 동의</span>
                </label>

                <div class="ml-6 text-xs text-gray-500">
                    전체 동의에는 필수 및 선택 항목이 포함되며, 선택 항목에 동의하지 않아도 서비스 이용이 가능합니다.
                </div>

                <div class="space-y-2 pt-2">
                    <!-- 만 14세 이상 -->
                    <label class="flex items-start">
                        <input type="checkbox" name="agree_age" required class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                        <span class="ml-2 text-sm text-gray-700">
                            만 14세 이상입니다. <span class="text-red-500">(필수)</span>
                        </span>
                    </label>

                    <!-- 쿠켓 이용약관 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_terms" required class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-700">
                                이용약관 <span class="text-red-500">(필수)</span>
                            </span>
                        </label>
                        <a href="#" class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- 개인정보처리방침 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_privacy" required class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-700">
                                개인정보처리방침 <span class="text-red-500">(필수)</span>
                            </span>
                        </label>
                        <a href="#" class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- 마케팅 수신 동의 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_marketing" class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-700">
                                마케팅 활용 및 광고성 정보 수신 동의 <span class="text-gray-500">(선택)</span>
                            </span>
                        </label>
                        <a href="#" class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- 야간 혜택 알림 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_night" class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-700">
                                야간 혜택 알림 수신 동의 <span class="text-gray-500">(선택)</span>
                            </span>
                        </label>
                        <a href="#" class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- 혜택/이벤트 알림 -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_event" class="mt-0.5 rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-700">
                                혜택/이벤트 알림 수신 동의 <span class="text-gray-500">(선택)</span>
                            </span>
                        </label>
                        <a href="#" class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- 수신 방법 -->
                    <div class="ml-6 flex items-center gap-4 pt-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="agree_sms" class="rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-600">문자</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="agree_push" class="rounded text-[#4056FF] focus:ring-[#4056FF]">
                            <span class="ml-2 text-sm text-gray-600">앱 푸시</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 안내 문구 -->
            <div class="text-xs text-gray-500 pt-4">
                서비스 이용을 위해 필수 개인정보는 동의 없이 수집·이용됩니다. 자세한 내용은
                <a href="#" class="text-[#4056FF] underline">개인정보처리방침</a>에서 확인하실 수 있습니다.
            </div>

            <!-- 가입하기 버튼 -->
            <button type="submit"
                    class="w-full py-4 px-4 bg-[#4056FF] text-white rounded-lg hover:bg-[#3447E6] transition-colors font-medium text-base mt-6">
                가입하기
            </button>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const agreeAll = document.getElementById('agree_all');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(#agree_all)');

    agreeAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = agreeAll.checked;
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            agreeAll.checked = allChecked;
        });
    });
});
</script>
@endsection
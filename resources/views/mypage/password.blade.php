@extends('layouts.content')

@section('title', '비밀번호 변경 - PhysioNote')
@section('header_title', '비밀번호 변경')
@section('back_action', 'window.location.href=\'/mypage\'')

@section('content')
    <div class="bg-white">
        <form method="POST" action="/mypage/password" class="px-5 py-6">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">현재 비밀번호</label>
                    <input type="password"
                           id="current_password"
                           name="current_password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4056FF] focus:border-transparent outline-none transition-all"
                           required>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">새 비밀번호</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4056FF] focus:border-transparent outline-none transition-all"
                           required>
                    <p class="mt-1 text-xs text-gray-500">비밀번호는 최소 8자 이상이어야 합니다.</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">새 비밀번호 확인</label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4056FF] focus:border-transparent outline-none transition-all"
                           required>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password Requirements -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-xs font-semibold text-gray-700 mb-2">비밀번호 요구사항</h3>
                <ul class="space-y-1 text-xs text-gray-600">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        최소 8자 이상
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        영문, 숫자 조합 권장
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-gray-400 mr-1.5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        특수문자 포함 권장
                    </li>
                </ul>
            </div>

            <!-- Submit Button -->
            <div class="mt-8">
                <button type="submit"
                        class="w-full py-3 bg-[#4056FF] text-white rounded-lg font-medium hover:bg-[#3447E6] transition-colors">
                    비밀번호 변경
                </button>
            </div>
        </form>

        <!-- Security Notice -->
        <div class="px-5 py-4 border-t border-gray-100">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-xs text-gray-600">
                    <p class="font-semibold mb-1">보안 안내</p>
                    <p>비밀번호는 정기적으로 변경하는 것이 좋으며, 다른 사이트와 동일한 비밀번호를 사용하지 마세요.</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="fixed bottom-20 left-0 right-0 px-5 z-50">
            <div class="max-w-lg mx-auto bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.bg-green-500').style.display = 'none';
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div class="fixed bottom-20 left-0 right-0 px-5 z-50">
            <div class="max-w-lg mx-auto bg-red-500 text-white px-4 py-3 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.bg-red-500').style.display = 'none';
            }, 3000);
        </script>
    @endif
@endsection
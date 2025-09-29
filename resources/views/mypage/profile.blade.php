@extends('layouts.content')

@section('title', '프로필 설정 - PhysioNote')
@section('header_title', '프로필 설정')
@section('back_action', 'window.location.href=\'/mypage\'')

@section('content')
    <form method="POST" action="/mypage/profile" enctype="multipart/form-data" class="bg-white">
        @csrf
        @method('PUT')

        <!-- Profile Image Section -->
        <div class="px-5 py-6 border-b border-gray-100">
            <div class="flex flex-col items-center">
                <div class="relative">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                        @if($user->profile_image)
                            <img src="{{ Storage::url($user->profile_image) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            <svg class="w-14 h-14 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        @endif
                    </div>
                    <label for="profile_image" class="absolute bottom-0 right-0 w-8 h-8 bg-[#4056FF] rounded-full flex items-center justify-center cursor-pointer hover:bg-[#3447E6] transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </label>
                    <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                </div>
                <p class="mt-3 text-sm text-gray-500">프로필 사진 변경</p>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="px-5 py-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">이름</label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4056FF] focus:border-transparent outline-none transition-all"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">이메일</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                       disabled>
                <p class="mt-1 text-xs text-gray-500">이메일은 변경할 수 없습니다.</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="px-5 py-6 border-t border-gray-100">
            <button type="submit"
                    class="w-full py-3 bg-[#4056FF] text-white rounded-lg font-medium hover:bg-[#3447E6] transition-colors">
                프로필 저장
            </button>
        </div>
    </form>

    <!-- Account Settings -->
    <div class="mt-2 bg-white">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">계정 관리</h3>
        </div>

        <div class="px-5 py-3">
            <a href="/mypage/password" class="flex items-center justify-between py-3 hover:bg-gray-50 -mx-3 px-3 rounded-lg transition-colors">
                <span class="text-sm font-medium text-gray-900">비밀번호 변경</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>

            <a href="/mypage/delete-account" class="flex items-center justify-between py-3 hover:bg-gray-50 -mx-3 px-3 rounded-lg transition-colors">
                <span class="text-sm font-medium text-red-600">회원 탈퇴</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
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

    <script>
        // Preview profile image
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.querySelector('.w-24.h-24');
                    container.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-full h-full object-cover">`;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
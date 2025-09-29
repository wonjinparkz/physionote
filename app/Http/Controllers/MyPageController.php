<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class MyPageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $statistics = $user->getStatistics();

        return view('mypage.index', compact('user', 'statistics'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('mypage.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return redirect()->route('mypage.profile')->with('success', '프로필이 성공적으로 업데이트되었습니다.');
    }

    public function settings()
    {
        $user = Auth::user();
        return view('mypage.settings', compact('user'));
    }

    public function showPasswordForm()
    {
        $user = Auth::user();

        // Check if user logged in via social provider
        if ($user->provider && !$user->password) {
            return redirect()->route('mypage.index')->with('error', '소셜 로그인 사용자는 비밀번호를 변경할 수 없습니다.');
        }

        return view('mypage.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => '현재 비밀번호를 입력해주세요.',
            'password.required' => '새 비밀번호를 입력해주세요.',
            'password.min' => '비밀번호는 최소 8자 이상이어야 합니다.',
            'password.confirmed' => '비밀번호 확인이 일치하지 않습니다.',
        ]);

        $user = Auth::user();

        // Check if user logged in via social provider
        if ($user->provider && !$user->password) {
            return redirect()->route('mypage.index')->with('error', '소셜 로그인 사용자는 비밀번호를 변경할 수 없습니다.');
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', '현재 비밀번호가 올바르지 않습니다.');
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('mypage.password')->with('success', '비밀번호가 성공적으로 변경되었습니다.');
    }

    public function studyHistory(Request $request)
    {
        $user = Auth::user();

        // Placeholder for study history
        // TODO: Implement study history logic

        return view('mypage.study-history');
    }

    public function studyHistoryDetail($id)
    {
        // Placeholder for study history detail
        // TODO: Implement study history detail logic

        return redirect()->route('mypage.study-history');
    }
}
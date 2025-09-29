<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirect($provider)
    {
        // Validate provider
        if (!in_array($provider, ['naver', 'kakao'])) {
            return redirect('/login')->with('error', '지원하지 않는 로그인 방식입니다.');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function callback($provider)
    {
        // Validate provider
        if (!in_array($provider, ['naver', 'kakao'])) {
            return redirect('/login')->with('error', '지원하지 않는 로그인 방식입니다.');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::where('provider', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if (!$user) {
                // Check if user exists with same email
                $existingUser = User::where('email', $socialUser->getEmail())->first();

                if ($existingUser) {
                    // Update existing user with social provider info
                    $existingUser->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_token' => $socialUser->token,
                        'provider_refresh_token' => $socialUser->refreshToken ?? null,
                        'avatar' => $socialUser->getAvatar(),
                    ]);
                    $user = $existingUser;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                        'email' => $socialUser->getEmail() ?? $socialUser->getId() . '@' . $provider . '.local',
                        'password' => bcrypt(Str::random(16)),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'provider_token' => $socialUser->token,
                        'provider_refresh_token' => $socialUser->refreshToken ?? null,
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                    ]);
                }
            } else {
                // Update token info
                $user->update([
                    'provider_token' => $socialUser->token,
                    'provider_refresh_token' => $socialUser->refreshToken ?? null,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            // Login user
            Auth::login($user, true);

            return redirect()->intended('/')->with('success', '로그인되었습니다.');

        } catch (\Exception $e) {
            \Log::error('Social login error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/login')->with('error', '로그인 중 오류가 발생했습니다. 다시 시도해주세요.');
        }
    }
}
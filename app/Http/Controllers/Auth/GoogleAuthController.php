<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::query()
            ->where('email', $googleUser->getEmail())
            ->first();

        if (!$user) {
            $user->update([
                'provider' => $user->provider ?: 'google',
                'provider_id' => $user->provider_id ?: $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $user = User::create([
                'first_name' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $googleUser->getEmail(),
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => 'business_owner',
            ]);
        }

        Auth::login($user, true);

        return match ($user->role) {
            'admin' => redirect()->intended('/admin'),
            'accountant' => redirect()->intended('/accountant/cases'),
            default => redirect()->intended('/dashboard'),
        };
    }
}

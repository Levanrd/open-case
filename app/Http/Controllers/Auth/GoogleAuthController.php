<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->with('error', 'Google login is not configured yet. Use email and password for now.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return redirect()->route('login')->with('error', 'Google login is not configured yet. Use email and password for now.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception) {
            return redirect()->route('login')->with('error', 'Google login could not be completed. Please try email and password.');
        }

        $user = User::query()
            ->where('email', $googleUser->getEmail())
            ->first();

        if ($user) {
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
                'name' => $googleUser->getName() ?: $googleUser->getEmail(),
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
            'admin' => redirect()->intended(route('admin.dashboard')),
            'accountant' => redirect()->intended(route('accountant.cases.index')),
            default => redirect()->intended(route('owner.dashboard')),
        };
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }
}

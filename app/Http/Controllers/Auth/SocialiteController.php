<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['google' => 'Google sign-in was cancelled or failed.']);
        }

        $user = User::updateOrCreate(
            ['google_id' => $googleUser->getId()],
            [
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'photo_url' => $googleUser->getAvatar(),
            ]
        );

        // Set quota reset date for new users
        if (!$user->quota_reset_date) {
            $user->update([
                'quota_reset_date' => now()->addMonth()->startOfMonth()->toDateString(),
            ]);

            // Send welcome email for brand-new users
            if ($user->wasRecentlyCreated) {
                try {
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->queue(new \App\Mail\WelcomeMail($user));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Welcome email failed', ['error' => $e->getMessage()]);
                }
            }
        }

        $user->touchActivity();

        Auth::login($user, true);

        return redirect()->route('dashboard.calendar');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}

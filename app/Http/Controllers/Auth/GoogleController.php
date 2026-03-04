<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Spatie\Permission\Models\Role;
use Throwable;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleEmail = $googleUser->getEmail();
            if (blank($googleEmail)) {
                return redirect()->route('login')->with('error', 'Google account email is not available.');
            }

            $user = User::where('email', $googleEmail)
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'Google User',
                    'email' => $googleEmail,
                    'password' => bcrypt(str()->random(16)),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]);

                $user->addPoints(
                    300,
                    'welcome_bonus',
                    null,
                    'Welcome bonus for new member registration'
                );
            } else {
                $user->forceFill([
                    'google_id' => $user->google_id ?: $googleUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?: now(),
                ])->save();
            }

            if (!$user->hasRole('user')) {
                Role::findOrCreate('user');
                $user->assignRole('user');
            }

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (InvalidStateException $e) {
            return redirect()->route('login')->with('error', 'Google login session expired. Please try again.');
        } catch (Throwable $e) {
            return redirect()->route('login')->with('error', 'Google login was cancelled or failed.');
        }
    }
}

<?php

namespace App\Repositories;

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $verificationToken = Str::random(60);
        $user->verification_token = $verificationToken;
        $user->save();

        $verificationLink = route('email.verify', ['token' => $verificationToken]);

        event(new UserRegistered($user, 'registration', $verificationLink));

        return $user;
    }

    public function findByVerificationToken($token)
    {
        return User::where('verification_token', $token)->first();
    }

    public function markEmailAsVerified(User $user)
    {
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        event(new UserRegistered($user, 'registration'));
    }
}

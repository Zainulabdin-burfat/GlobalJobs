<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(RegistrationRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->create($request->input());

            DB::commit();

            return response()->json(['message' => 'Registration Success..!, Verify email to continue.'], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json(['message' => 'Registration failed. Please try again later. '. $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function verify2(Request $request, $token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            abort(404); // Or return an error response
        }

        // Verify the email and clear the verification token
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function verify(Request $request, $token)
    {
        $user = $this->userRepository->findByVerificationToken($token);

        if (!$user) {
            abort(404); // Or return an error response
        }

        $this->userRepository->markEmailAsVerified($user);

        return response()->json(['message' => 'Email verified successfully']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a newly created user.
     */
    public function register(RegisterRequest $request)
    {

        $validated = $request->validated();
        $data = DB::transaction(function () use ($validated, $request) {

            $user = User::create($validated);
            $user->syncRoles('employee');

            $token = auth()->attempt($request->only('email', 'password'));

            $userData = [
                'token_type' => 'bearer',
                'token' => $token,
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user(),
                'roles' => auth()->user()->getRoleNames()->first(),
                'permissions' => auth()->user()->getAllPermissions(),
            ];
            // Store the data in cache, using the user's email as the unique key
            Cache::put($user->email, $userData, now()->addSeconds($userData['expires_in']));

            return $userData;
        });

        return $this->handleResponse(true, 'created new user', $data, 201);
    }

    /**
     * Login.
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        if (! $token = auth()->attempt($validated)) {
            return $this->handleResponse(false, 'Unauthorized', null, 401);
        }

        return $this->createNewToken($token);
    }

    public function refreshToken()
    {
        try {
            $token = auth()->refresh();

            return $this->createNewToken($token);
        } catch (\Exception $e) {
            return $this->handleResponse(false, 'Token refresh failed', null, 401);
        }
    }

    public function userDetail(Request $request)
    {

        $user = [
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames()->first(),
            'permissions' => auth()->user()->getAllPermissions(),
        ];

        return $this->handleResponse(true, 'User detail', $user);
    }

    protected function createNewToken($token)
    {
        $data = [
            'token_type' => 'bearer',
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
            'roles' => auth()->user()->getRoleNames()->first(),
            'permissions' => auth()->user()->getAllPermissions(),
        ];
        // Store the data in cache, using the user's email as the unique key
        Cache::put(auth()->user()->email, $data, now()->addSeconds($data['expires_in']));

        return $this->handleResponse(true, 'success', $data, 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        // Validasi input
        $validated = $request->validated();

        $user = auth()->user();

        // Periksa apakah kata sandi lama cocok
        if (! Hash::check($validated['old_password'], $user->password)) {
            return $this->handleResponse(false, 'Old password does not match', null, 400);
        }

        // Perbarui kata sandi pengguna
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return $this->handleResponse(true, 'Password changed successfully', null, 200);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        // Validasi input
        $validated = $request->validated();
        $user = auth()->user();

        $user->update($validated);

        return $this->handleResponse(true, 'Profile updated successfully', null, 200);
    }
}

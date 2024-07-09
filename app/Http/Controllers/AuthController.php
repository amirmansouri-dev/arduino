<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
//use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;
use JWTAuth;
use App\Notifications\RoleChanged;
use Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



/**
 * @group Authentication
 *
 * API for user authentication and authorization.
 */
class AuthController extends Controller
{
    /**
     * User Login
     *
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Example: secret
     * @response {
     *  "user": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "user@example.com",
     *      "email_verified_at": "2021-03-01T00:00:00.000000Z",
     *      "created_at": "2021-03-01T00:00:00.000000Z",
     *      "updated_at": "2021-03-01T00:00:00.000000Z"
     *  },
     *  "access_token": "eyJ0eXAiOiJKV1QiLCJh...",
     *  "token_type": "Bearer",
     *  "expires_at": "2021-03-01T00:00:00.000000Z"
     * }
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Enregistrer l'heure de connexion
        UserLogin::create([
            'user_id' => Auth::id(),
            'login_time' => now(),
        ]);

        // Generate a refresh token
        $refreshToken = JWTAuth::setToken($token)->refresh();

        // Get expiration times from configuration
        $accessTokenExpiration = Carbon::now()->addMinutes(Config::get('jwt.ttl'))->toDateTimeString();
        $refreshTokenExpiration = Carbon::now()->addMinutes(Config::get('jwt.refresh_ttl'))->toDateTimeString();

        // Return both access and refresh tokens along with their expiration dates
        return response()->json([
            'user' => Auth::user(),
            'access_token' => $token,
            'access_token_expires_at' => $accessTokenExpiration,
            'refresh_token' => $refreshToken,
            'refresh_token_expires_at' => $refreshTokenExpiration
        ]);
    }

    /**
     * User Registration
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The password of the user. Example: secret
     * @response 201 {
     *  "user": {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "user@example.com",
     *      "created_at": "2021-03-01T00:00:00.000000Z",
     *      "updated_at": "2021-03-01T00:00:00.000000Z"
     *  },
     *  "token": "eyJ0eXAiOiJKV1QiLCJh..."
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string',
            'permissions' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'user',
            'permissions' => $request->permissions ?? [],
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(['user' => $user, 'token' => $token], 201);
    }


    /**
     * Get Authenticated User
     *
     * @response {
     *  "id": 1,
     *  "name": "John Doe",
     *  "email": "user@example.com",
     *  "email_verified_at": "2021-03-01T00:00:00.000000Z",
     *  "created_at": "2021-03-01T00:00:00.000000Z",
     *  "updated_at": "2021-03-01T00:00:00.000000Z"
     * }
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * User Logout
     *
     * @response {
     *  "message": "Successfully logged out"
     * }
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            JWTAuth::invalidate(JWTAuth::getToken());

            // Mettre à jour l'heure de déconnexion
            UserLogin::where('user_id', $user->id)
                ->whereNull('logout_time')
                ->latest()
                ->first()
                ->update(['logout_time' => now()]);

            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not log out'], 500);
        }
    }

    /**
     * Update User Role
     *
     * @bodyParam role string required The new role of the user. Example: admin
     * @response {
     *  "message": "User role updated successfully"
     * }
     */
    public function updateRole(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'role' => ['required', Rule::in(['admin','user'])],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the authenticated user is a superadmin
        $user = Auth::user();
        if ($user->role !== 'superadmin' && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Update the user's role
        $userToUpdate = User::findOrFail($id);
        $newRole = $request->role;
        $userToUpdate->role = $newRole;
        $userToUpdate->save();

        // Send notification to the user
        $userToUpdate->notify(new RoleChanged($newRole));

        return response()->json(['message' => 'User role updated successfully']);
    }

    /**
     * Update User Permissions
     *
     * @bodyParam permissions array required The permissions of the user. Example: {"press": true, "humidity": true}
     * @response {
     *  "message": "User permissions updated successfully"
     * }
     */
    public function updatePermissions(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the authenticated user is a superadmin or admin
        $authUser = Auth::user();
        if ($authUser->role !== 'superadmin' && $authUser->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userToUpdate = User::findOrFail($id);
        $userToUpdate->permissions = $request->permissions;
        $userToUpdate->save();

        return response()->json(['message' => 'User permissions updated successfully']);
    }

    /**
     * Reset Password
     *
     * @bodyParam email string required The email of the user. Example: user@example.com
     * @bodyParam password string required The new password of the user. Example: newpassword
     * @response {
     *  "message": "Password reset successfully"
     * }
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['email' => __($status)], 400);
    }

    /**
     * Handle the reset password request.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['email' => [__($status)]], 400);
    }
    /**
     * Refresh Token
     *
     * @bodyParam refresh_token string required The refresh token of the user. Example: old_refresh_token
     * @response {
     *  "access_token": "new_access_token",
     *  "access_token_expires_at": "2021-03-01T00:00:00.000000Z"
     * }
     */
    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');

        try {
            $newToken = JWTAuth::refresh($refreshToken);
            $accessTokenExpiration = Carbon::now()->addMinutes(config('jwt.ttl'))->toDateTimeString();

            return response()->json([
                'access_token' => $newToken,
                'access_token_expires_at' => $accessTokenExpiration
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }
    }
}

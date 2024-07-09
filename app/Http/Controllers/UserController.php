<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @group User Management
 *
 * API for managing users.
 */
class UserController extends Controller
{
    /**
     * Get All Users
     *
     * @response 200 {
     *  "users": [
     *    {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com",
     *      "role": "user",
     *      "created_at": "2021-01-01T00:00:00.000000Z",
     *      "updated_at": "2021-01-01T00:00:00.000000Z"
     *    }
     *  ]
     * }
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Get User Login History
     *
     * @response 403 {
     *  "error": "Unauthorized"
     * }
     * @response 200 {
     *  "user_logins": [
     *    {
     *      "id": 1,
     *      "user_id": 1,
     *      "login_time": "2021-01-01T00:00:00.000000Z",
     *      "logout_time": "2021-01-01T01:00:00.000000Z"
     *    }
     *  ]
     * }
     */
    public function historyConnectUser(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403); // Code de statut 403 pour accès interdit
        }

        return response()->json(UserLogin::with('user')->get());
    }

    /**
     * Update User
     *
     * @bodyParam name string required The name of the user. Example: John Doe
     * @bodyParam email string required The email of the user. Example: john@example.com
     * @bodyParam role string The role of the user. Example: admin
     * @response 403 {
     *  "error": "Unauthorized"
     * }
     * @response 200 {
     *  "message": "User updated successfully"
     * }
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin' && $user->id != $id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userToUpdate = User::findOrFail($id);
        $userToUpdate->update($request->all());

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Delete User
     *
     * @response 403 {
     *  "error": "Unauthorized"
     * }
     * @response 200 {
     *  "message": "User deleted successfully"
     * }
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if ($user->role !== 'superadmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userToDelete = User::findOrFail($id);
        $userToDelete->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

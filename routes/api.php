<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdviceController;
use App\Http\Controllers\QueryController;
use App\Http\Middleware\RefreshTokenMiddleware;
use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatbotController;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->middleware(RefreshTokenMiddleware::class);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users',[UserController::class, 'index']);

    Route::get('history-connect-user', [UserController::class, 'historyConnectUser']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);

});

Route::middleware('auth:api')->post('update-role/{id}', [AuthController::class, 'updateRole']);

Route::middleware('auth:api')->post('update-permissions/{id}', [AuthController::class, 'updatePermissions']);

Route::get('advices', [AdviceController::class, 'index']);
Route::get('advices/{id}', [AdviceController::class, 'show']);
Route::post('advices', [AdviceController::class, 'store']);
Route::put('advices/{id}', [AdviceController::class, 'update']);
Route::delete('advices/{id}', [AdviceController::class, 'delete']);

Route::get('queries', [QueryController::class, 'index']);
Route::get('queries/{id}', [QueryController::class, 'show']);
Route::post('queries', [QueryController::class, 'store']);
Route::put('queries/{id}', [QueryController::class, 'update']);
Route::delete('queries/{id}', [QueryController::class, 'delete']);


Route::post('chatbot/ask', [ChatbotController::class, 'ask']);

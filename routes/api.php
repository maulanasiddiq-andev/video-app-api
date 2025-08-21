<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/users', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('/video', VideoController::class);
    Route::prefix('/video')->group(function() {
        Route::get('/{video}/comments', [VideoController::class, 'getComments']);
        Route::get('/{video}/latest-comment', [VideoController::class, 'getLatestComment']);
        Route::post('/{video}/like', [VideoController::class, 'likeVideo']);
        Route::get('/{video}/suggested-videos', [VideoController::class, 'getSuggestedVideos']);
    });
    Route::apiResource('/comment', CommentController::class);
    Route::apiResource('/history', HistoryController::class);
    Route::apiResource('/like', LikeController::class);

    Route::prefix('/profile')->group(function() {
        Route::post('/edit-profile-image', [ProfileController::class, 'editProfileImage']);
        Route::get('/get-self', [ProfileController::class, 'getSelf']);
        Route::get('/get-my-videos', [ProfileController::class, 'getMyVideos']);
        Route::get('/get-my-comments', [ProfileController::class, 'getMyComments']);
    });

    Route::prefix('/user')->group(function() {
        Route::get('/{user}/get-videos', [UserController::class, 'getUserVideos']);
        Route::get('/{user}/get-comments', [UserController::class, 'getUserComments']);
    });
});

Route::apiResource('/user', UserController::class);

Route::get('/google', function() {
    $client = new Google\Client();
    $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
    $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
    $client->setRedirectUri('http://localhost:8000/get-google-token');
    $client->addScope(Google\Service\Drive::DRIVE);
    $client->setAccessType('offline');
    $client->setPrompt('consent');

    if (!request()->has('code')) {
        return redirect()->away($client->createAuthUrl());
    } else {
        $client->authenticate(request('code'));
        return $client->getAccessToken(); // contains 'refresh_token'
    }
});

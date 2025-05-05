<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\NovelController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\ChapterController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\ReadingHistoryController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Middleware\EnsureEmailIsVerified;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Untuk send kode otp / verifikasi email

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::prefix('auth')->group(function () {
    Route::get('google', [AuthController::class, 'redirectToGoogle']);
    Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
});



// Novels
Route::get('/novels', [NovelController::class, 'index']);
Route::get('/novels/featured', [NovelController::class, 'featured']);
Route::get('/novels/search', [NovelController::class, 'search']);
Route::get('/novels/category/{categoryId}', [NovelController::class, 'byCategory']);
Route::get('/novels/author/{authorId}', [NovelController::class, 'byAuthor']);
Route::get('/novels/tag/{tagId}', [NovelController::class, 'byTag']);
Route::get('/novels/{id}', [NovelController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/categories/{id}/novels', [CategoryController::class, 'novels']);

// Authors
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);
Route::get('/authors/{id}/novels', [AuthorController::class, 'novels']);

// Tags
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);
Route::get('/tags/{id}/novels', [TagController::class, 'novels']);

// Chapters
Route::get('/novels/{novelId}/chapters', [ChapterController::class, 'index']);
Route::get('/novels/{novelId}/chapters/{chapterId}', [ChapterController::class, 'show']);

// Reviews
Route::get('/novels/{novelId}/reviews', [ReviewController::class, 'index']);
Route::get('/novels/{novelId}/reviews/{reviewId}', [ReviewController::class, 'show']);

// Comments
Route::get('/reviews/{reviewId}/comments', [CommentController::class, 'index']);
Route::get('/reviews/{reviewId}/comments/{commentId}', [CommentController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('send-otp-manually/{user}', [EmailVerificationController::class, 'sendOtpManually']); // Kirim OTP manual
    Route::post('verify-otp', [EmailVerificationController::class, 'verifyOtp']); // Verifikasi OTP
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('send-otpp', [EmailVerificationController::class, 'sendOtp']); // Kirim OTP
    Route::get('/user-activity-summary', [UserController::class, 'summary']);
});

// Protected routes
Route::middleware('auth:sanctum', EnsureEmailIsVerified::class)->group(function () {

    // Auth
    // Route::post('/logout', [AuthController::class, 'logout']);
    // Route::get('/user', [AuthController::class, 'user']);

    // Users
    Route::apiResource('users', UserController::class);

    // Novels (protected actions)
    Route::post('/novels', [NovelController::class, 'store']);
    Route::put('/novels/{id}', [NovelController::class, 'update']);
    Route::delete('/novels/{id}', [NovelController::class, 'destroy']);

    // Categories (protected actions)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // Authors (protected actions)
    Route::post('/authors', [AuthorController::class, 'store']);
    Route::put('/authors/{id}', [AuthorController::class, 'update']);
    Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);

    // Tags (protected actions)
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);

    // Chapters (protected actions)
    Route::post('/novels/{novelId}/chapters', [ChapterController::class, 'store']);
    Route::put('/novels/{novelId}/chapters/{chapterId}', [ChapterController::class, 'update']);
    Route::delete('/novels/{novelId}/chapters/{chapterId}', [ChapterController::class, 'destroy']);

    // Reviews (protected actions)
    Route::post('/novels/{novelId}/reviews', [ReviewController::class, 'store']);
    Route::put('/novels/{novelId}/reviews/{reviewId}', [ReviewController::class, 'update']);
    Route::delete('/novels/{novelId}/reviews/{reviewId}', [ReviewController::class, 'destroy']);
    Route::post('/novels/{novelId}/reviews/{reviewId}/like', [ReviewController::class, 'like']);

    // Comments (protected actions)
    Route::post('/reviews/{reviewId}/comments', [CommentController::class, 'store']);
    Route::put('/reviews/{reviewId}/comments/{commentId}', [CommentController::class, 'update']);
    Route::delete('/reviews/{reviewId}/comments/{commentId}', [CommentController::class, 'destroy']);
    Route::post('/reviews/{reviewId}/comments/{commentId}/like', [CommentController::class, 'like']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/novels/{novelId}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/novels/{novelId}/favorite', [FavoriteController::class, 'destroy']);
    Route::get('/novels/{novelId}/favorite/check', [FavoriteController::class, 'check']);

    // Reading History
    Route::get('/reading-history', [ReadingHistoryController::class, 'index']);
    Route::get('/reading-history/{novelId}', [ReadingHistoryController::class, 'show']);
    Route::post('/reading-history/{novelId}', [ReadingHistoryController::class, 'update']);
    Route::delete('/reading-history/{novelId}', [ReadingHistoryController::class, 'destroy']);
    Route::delete('/reading-history', [ReadingHistoryController::class, 'clearAll']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/notifications', [NotificationController::class, 'destroyAll']);


    // Route::get('/users', [EmailController::class, 'getAllUsers']);
    Route::post('/send-email', [EmailController::class, 'sendEmailToUsers']);
    Route::post('/send-email-all', [EmailController::class, 'sendEmailToAllUsers']);
});

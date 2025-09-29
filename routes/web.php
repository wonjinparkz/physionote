<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\MyPageController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/content/{id}', [ContentController::class, 'show'])->name('content.show');

// Authentication Routes
Route::get('/login', function() {
    return view('auth.login');
})->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.store');

// Social Authentication Routes
Route::get('/auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'callback'])->name('social.callback');

// 문제 풀기 라우트 (로그인 필요)
Route::prefix('questions')->name('questions.')->middleware('auth')->group(function () {
    Route::get('/', [QuestionController::class, 'index'])->name('index');
    Route::get('/start/{categoryId}', [QuestionController::class, 'start'])->name('start');
    Route::get('/solve', [QuestionController::class, 'solve'])->name('solve');
    Route::post('/submit', [QuestionController::class, 'submit'])->name('submit');
    Route::get('/previous', [QuestionController::class, 'previous'])->name('previous');
    Route::get('/result', [QuestionController::class, 'result'])->name('result');
    Route::get('/reset', [QuestionController::class, 'reset'])->name('reset');
    Route::get('/jump/{index}', [QuestionController::class, 'jump'])->name('jump');
    Route::get('/jump-class/{classTime}', [QuestionController::class, 'jumpToClass'])->name('jump.class');
});

// 마이페이지 라우트 (로그인 필요)
Route::prefix('mypage')->name('mypage.')->middleware('auth')->group(function () {
    Route::get('/', [MyPageController::class, 'index'])->name('index');
    Route::get('/profile', [MyPageController::class, 'profile'])->name('profile');
    Route::put('/profile', [MyPageController::class, 'updateProfile'])->name('profile.update');
    Route::get('/password', [MyPageController::class, 'showPasswordForm'])->name('password');
    Route::put('/password', [MyPageController::class, 'updatePassword'])->name('password.update');
    Route::get('/study-history', [MyPageController::class, 'studyHistory'])->name('study-history');
    Route::get('/study-history/{id}', [MyPageController::class, 'studyHistoryDetail'])->name('study-history.detail');
    Route::get('/settings', [MyPageController::class, 'settings'])->name('settings');
});

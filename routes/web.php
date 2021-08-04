<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Auth::routes();
    Auth::routes(['register' => false]);

    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'authenticate'])->name('login');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'profileData'])->name('profile');
    Route::post('/change-password', [App\Http\Controllers\Admin\ProfileController::class, 'ChangePass'])->name('change-password');
    Route::post('/change-profile-img', [App\Http\Controllers\Admin\ProfileController::class, 'ChangeProfileImg'])->name('change-profile-img');
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    Route::get('/user/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edituser');
    Route::post('/updateuser', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('updateuser');

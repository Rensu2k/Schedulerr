<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// Login
Route::get('/',      [LoginController::class, 'showLogin'])->name('login');
Route::post('/login',[LoginController::class, 'handleLogin'])->name('login.post');
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// Dashboard (protected by session check inside controller)
Route::get('/dashboard',     [DashboardController::class, 'index'])->name('dashboard');
Route::get('/schedule-list', [DashboardController::class, 'scheduleList'])->name('schedule-list');
Route::post('/export-summary', [DashboardController::class, 'exportSummary'])->name('export.summary');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/upload-picture', [App\Http\Controllers\ProfileController::class, 'uploadProfilePicture'])->name('profile.upload-picture');
Route::post('/profile/add-user', [App\Http\Controllers\ProfileController::class, 'addUser'])->name('profile.add-user');

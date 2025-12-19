<?php

use App\Http\Controllers\LabelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Routes\AuthRoutes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::resource('task_statuses', TaskStatusController::class);
Route::resource('tasks', TaskController::class);
// Route::resource('labels', LabelController::class);
Route::resource('labels', LabelController::class)->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get(PROFILE_URI, [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch(PROFILE_URI, [ProfileController::class, 'update'])->name('profile.update');
    Route::delete(PROFILE_URI, [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/logout', function () {
    Auth::logout();

    return redirect('/');
})->name('logout');

AuthRoutes::register();

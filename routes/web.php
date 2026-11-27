<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [CustomerAuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register'])->middleware('throttle:5,1');
});
Route::middleware('auth')->group(function () {
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/chat/conversations', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/conversations/{conversation}/messages', [ChatController::class, 'send'])->name('chat.send');
});
Route::get('/chat/conversations/{conversation}/messages', [ChatController::class, 'messages'])
    ->middleware('throttle:30,1')
    ->name('chat.messages');

Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:5,1');
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::post('/gifts', [AdminController::class, 'createGift'])->name('gifts.create');
    Route::patch('/gifts/{gift}', [AdminController::class, 'updateGift'])->name('gifts.update');
    Route::delete('/gifts/{gift}', [AdminController::class, 'deleteGift'])->name('gifts.delete');
    Route::post('/conversations/{conversation}/messages', [AdminController::class, 'reply'])->name('chat.reply');
    Route::post('/orders', [AdminController::class, 'createOrder'])->name('orders.create');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardListController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\BoardMemberController;
use App\Http\Controllers\CardMemberController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Routes - Redirect berdasarkan auth status
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Boards
    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
    Route::get('/boards/{board}/edit', [BoardController::class, 'edit'])->name('boards.edit');
    Route::put('/boards/{board}', [BoardController::class, 'update'])->name('boards.update');
    Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');
    
    // Board Members
    Route::post('/boards/{board}/members', [BoardMemberController::class, 'store'])->name('boards.members.store');
    Route::delete('/boards/{board}/members/{user}', [BoardMemberController::class, 'destroy'])->name('boards.members.destroy');
    
    // Lists
    Route::post('/boards/{board}/lists', [CardListController::class, 'store'])->name('lists.store');
    Route::put('/lists/{list}', [CardListController::class, 'update'])->name('lists.update');
    Route::delete('/lists/{list}', [CardListController::class, 'destroy'])->name('lists.destroy');
    Route::post('/lists/reorder', [CardListController::class, 'reorder'])->name('lists.reorder');
    
    // Cards - PERBAIKAN: Gunakan List model binding
    Route::post('/lists/{list}/cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');
    Route::post('/cards/{card}/move', [CardController::class, 'move'])->name('cards.move');
    
    // Card Members
    Route::post('/cards/{card}/members', [CardMemberController::class, 'store'])->name('cards.members.store');
    Route::delete('/cards/{card}/members/{user}', [CardMemberController::class, 'destroy'])->name('cards.members.destroy');
});

// Fallback route - harus di paling bawah
Route::fallback(function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});
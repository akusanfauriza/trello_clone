<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/trello', function () {
    return view('app');
})->where('any', '.*');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Board routes
Route::apiResource('boards', BoardController::class);
Route::post('boards/{board}/members', [BoardController::class, 'addMember']);
Route::delete('boards/{board}/members/{user}', [BoardController::class, 'removeMember']);

// List routes
Route::post('boards/{board}/lists', [ListController::class, 'store']);
Route::put('lists/{list}', [ListController::class, 'update']);
Route::delete('lists/{list}', [ListController::class, 'destroy']);
Route::post('boards/{board}/lists/reorder', [ListController::class, 'reorder']);

// Card routes
Route::post('lists/{list}/cards', [CardController::class, 'store']);
Route::get('cards/{card}', [CardController::class, 'show']);
Route::put('cards/{card}', [CardController::class, 'update']);
Route::delete('cards/{card}', [CardController::class, 'destroy']);
Route::post('cards/{card}/members', [CardController::class, 'addMember']);
Route::delete('cards/{card}/members/{user}', [CardController::class, 'removeMember']);

// Activity routes
Route::get('boards/{board}/activities', [ActivityController::class, 'index']);

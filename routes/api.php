<?php

use Pokemon\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Cards;
use App\Classes\Deck;
use App\Classes\DeckAPI;
use App\Classes\DeckGenerator;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// List of all decks
Route::get('/decks', [DeckAPI::class, 'index']);
// Generate a new deck
Route::get('/decks/generate', [DeckAPI::class, 'generate']);
// Generate a focused deck
Route::get('/decks/generate/{type}', [DeckAPI::class, 'generate']);
// Check specific deck
Route::get('/decks/{id}', [DeckAPI::class, 'detail']);

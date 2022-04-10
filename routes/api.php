<?php

use App\Models\Cards;
use Pokemon\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Classes\Deck;
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

Route::get('/generate', function (Request $request) {

    $deck = DeckGenerator::create();
    dd($deck);

    // $cards = Pokemon::Card()->all();
    $card = Pokemon::Card()->find('xy1-1');
    $supertypes = Pokemon::Supertype()->all();
    $types = Pokemon::Type()->all();
    $cards = Pokemon::Card()->where([
        'supertype' => 'Trainer',
    ])->all();

    dd($supertypes,$types,$card,$cards[100]);
    return Response::json(['test'=>'top']);
});

Route::get('/test', function (Request $request) {
    $allCarts = Cards::pluck('uid')->toArray();
    dd($allCarts);
});

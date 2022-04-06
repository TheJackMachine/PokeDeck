<?php

use Pokemon\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Classes\Deck;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', function (Request $request) {

    $deck = new Deck('Fire');
    $deck->show();
    dd('stop');

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

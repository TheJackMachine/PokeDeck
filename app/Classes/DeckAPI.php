<?php namespace App\Classes;

use App\Http\Resources\Deck as DeckResource;

use App\Classes\DeckGenerator;
use App\Models\Deck;

class DeckAPI
{

    /**
     * Return all generated decks
     */
    static public function index()
    {
        $decks = Deck::all();
        return DeckResource::collection($decks);
    }

    /**
     * Return specific deck
     * @param $deckUUID
     */
    static public function detail($deckUUID)
    {
        $deck = Deck::where('uuid', $deckUUID)->with('cards')->first();
        return new DeckResource($deck);
    }

    /**
     * Generate a new focused deck
     */
    static public function generate($focus = null)
    {
        // todo: handle exception
        $deck = DeckGenerator::create($focus);
        return new DeckResource($deck);
    }

}

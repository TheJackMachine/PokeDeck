<?php namespace App\Classes;

use Pokemon\Pokemon;
use Illuminate\Support\Str;
use App\Exceptions\InvalidType;

use App\Models\Card;
use App\Models\Deck;

class DeckGenerator
{

    protected static array $validTypes = [];
    protected static int $typeMaxRange = 16; // Min cards of that type
    protected static int $typeMinRange = 12; // Max cards of that type
    protected static int $energyNumber = 10; // Number of energy card to add

    protected static int $deckSize = 60; // Number of cards in the deck

    /**
     * Fill this deck
     * @throws InvalidType
     */
    public static function create($type = null): Deck
    {
        self::init();

        $type = self::checkType($type);

        $cards = self::generateDeck($type);

        // 4 - save cards in Database
        $uidList = self::saveCards($cards);

        // Get Cards from database
        $cardsToAssociate = Card::whereIn('uid', $uidList)->get();

        // Save Deck
        // todo: add Name ?
        $deck = Deck::create([
            'uuid' => Str::orderedUuid(),
            'focus' => $type,
        ]);

        // Associations
        $deck->cards()->saveMany($cardsToAssociate);

        // Refresh and return a new fresh deck
        return Deck::where('uuid', $deck->uuid)->with('cards')->first();
    }

    /**
     * Set some stuff before start
     */
    public static function init()
    {
        Pokemon::ApiKey(env('POKEMON_API_KEY'));
        self::initValidTypes();
    }

    /**
     * Return a type after verifying
     * @param $type
     * @return string
     */
    protected static function checkType($type): string
    {
        if (empty($type)) return self::randomType();
        return $type;
    }

    /**
     * Generate a deck following rules
     *
     * @param null $type
     * @return array
     * @throws InvalidType
     */
    protected static function generateDeck($type): array
    {
        // 0 - verify type is valid
        self::validateType($type);

        // 1 - Add 12 - 16 pokemon card of specific type
        $cards = self::addPokemonCards($type, self::numberOfPokemonCards());

        // 2 - Add 10 energy cards
        $cards = [...$cards, ...self::addEnergyCards($type, self::$energyNumber)];

        // 3 - Add training card
        return [...$cards, ...self::addTrainerCards(self::getRemainderCardsNumber($cards))];
    }

    /**
     * Save new card in database for later research
     * @param $cards
     * @return array // The uid list of all cards
     */
    protected static function saveCards($cards): array
    {
        // Check existing saved Cards
        $savedCards = Card::pluck('uid')->toArray();
        // Save cards if not exists
        $uidList = [];
        foreach ($cards as $card) {
            $uidList[] = $card->getId();
            if (!in_array($card->getId(), $savedCards)) {
                Card::create([
                    'uid' => $card->getId(),
                    'name' => $card->getName(),
                    'supertype' => $card->getSupertype(),
                    'types' => empty($card->getTypes()) ? [] : $card->getTypes(),
                ]);
            }
        }
        return $uidList;
    }

    /**
     * return number of pokemon cards to get
     * @return int
     */
    protected static function numberOfPokemonCards(): int
    {
        return rand(self::$typeMaxRange, self::$typeMinRange);
    }

    /**
     * Return number of cards to complete the deck
     */
    protected static function getRemainderCardsNumber(array $card): int
    {
        return self::$deckSize - count($card);
    }

    /**
     * Add 12 - 16 pokemon card of specific type
     */
    protected static function addPokemonCards($type, $number): array
    {
        // 1 - Get pokemon of that type
        $pokemonCards = self::fetchPokemonType($type);
        $cards = [];
        // 1a - add to this deck
        for ($n = 0; $n < $number; $n++) {
            $cards[] = self::randomCard($pokemonCards);
        }
        return $cards;
    }

    /**
     * Add 10 energy cards of specific type
     */
    protected static function addEnergyCards($type, $number): array
    {
        $energyCards = self::fetchEnergyType($type);
        $cards = [];
        for ($n = 0; $n < $number; $n++) {
            $cards[] = self::randomCard($energyCards);
        }
        return $cards;
    }

    /**
     * Add training card
     */
    protected static function addTrainerCards($number): array
    {
        $trainerCards = self::fetchTrainingCard();
        $cards = [];
        $occurrences = [];
        for ($n = 0; $n < $number; $n++) {
            // get random card and prevent more than 4 to be the same
            do {
                $selectedCard = self::randomCard($trainerCards);
            } while (array_key_exists($selectedCard->getName(), $occurrences) && $occurrences[$selectedCard->getName()] == 4);
            // track occurrence
            if (!array_key_exists($selectedCard->getName(), $occurrences)) {
                $occurrences[$selectedCard->getName()] = 1;
            } else {
                $occurrences[$selectedCard->getName()]++;
            }
            // if ok, add to selection
            $cards[] = $selectedCard;
        }
        return $cards;
    }

    /**
     * Return a random element in card array
     * @param $cards
     * @return mixed
     */
    protected static function randomCard($cards)
    {
        return $cards[rand(0, count($cards) - 1)];
    }

    /**
     * Return a random type
     * @return string
     */
    public static function randomType(): string
    {
        return self::$validTypes[rand(0, count(self::$validTypes) - 1)];
    }


    /**
     * Fetch Pokemon card by type via API
     * @param $type
     * @return array
     */
    protected static function fetchPokemonType($type): array
    {
        return Pokemon::Card()->where([
            'supertype' => 'Pok??mon',
            'types' => $type
        ])->all();
    }

    /**
     * Fetch Energy card by type via API
     * @param $type
     * @return array
     */
    protected static function fetchEnergyType($type): array
    {
        return Pokemon::Card()->where([
            'supertype' => 'Energy',
            'name' => $type
        ])->all();
    }

    /**
     * Fetch Training card by type via API
     * @return array
     */
    protected static function fetchTrainingCard(): array
    {
        return Pokemon::Card()->where([
            'supertype' => 'Trainer',
        ])->all();
    }

    /**
     * Hydrate validTypes if needed
     */
    protected static function initValidTypes()
    {
        if (empty(self::$validTypes)) {
            self::$validTypes = Pokemon::Type()->all();
        }
    }

    public static function setValidTypes(array $types = [])
    {
        self::$validTypes = $types;
    }

    public static function getValidTypes()
    {
        return self::$validTypes;
    }

    /**
     * Check if type is valid
     * @param $type
     * @throws InvalidType
     */
    protected static function validateType($type)
    {
        if (!in_array($type, self::$validTypes)) throw new InvalidType();
    }
}

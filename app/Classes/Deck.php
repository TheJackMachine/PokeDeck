<?php namespace App\Classes;

use Pokemon\Pokemon;
use Illuminate\Support\Str;
use App\Exceptions\InvalidType;

class Deck
{

    protected static $validTypes;
    protected array $cards = [];
    protected $uid;

    protected string $typeFocused; // What type of Pokemon this deck is focused ?
    protected int $typeMaxRange = 16; // Min cards of that type
    protected int $typeMinRange = 12; // Max cards of that type
    protected int $energyNumber = 10; // Number of energy card to add

    protected int $deckSize = 60; // Number of cards in the deck

    /**
     * @throws InvalidType
     */
    public function __construct($type)
    {
        $this->uid = Str::orderedUuid();
        self::initValidTypes();
        $this->create($type);
    }

    /**
     * Fill this deck
     * @throws InvalidType
     */
    protected function create($type)
    {
        // 0 - verify type is valid
        self::validateType($type);
        $this->typeFocused = $type;

        // 1 - Add 12 - 16 pokemon card of specific type
        $this->cards = $this->addPokemonCards($this->typeFocused, $this->numberOfPokemonCards());

        // 2 - Add 10 energy cards
        $this->cards = [...$this->cards, ...$this->addEnergyCards($this->typeFocused, $this->energyNumber)];

        // 3 - Add training card
        $this->cards = [...$this->cards, ...$this->addTrainerCards($this->getRemainderCardsNumber())];

    }

    /**
     * Shuffle the deck
     */
    public function shuffle()
    {
        // TODO: LMFAO Everyday I'm shuffling
    }

    /**
     * return number of pokemon cards to get
     * @return int
     */
    protected function numberOfPokemonCards(): int
    {
        return rand($this->typeMaxRange, $this->typeMinRange);
    }

    /**
     * Return number of cards to complete the deck
     */
    public function getRemainderCardsNumber()
    {
        return $this->deckSize - count($this->cards);
    }

    /**
     * Add 12 - 16 pokemon card of specific type
     */
    protected function addPokemonCards($type, $number)
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
    protected function addEnergyCards($type, $number): array
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
    protected function addTrainerCards($number)
    {
        $trainerCards = self::fetchTrainingCard();
        $cards = [];
        $occurrences = [];
        for ($n = 0; $n < $number; $n++) {
            // get random card and prevent more than 4 to be the same
            do {
                $selectedCard = self::randomCard($trainerCards);
            } while( array_key_exists($selectedCard->getName(),$occurrences) &&  $occurrences[$selectedCard->getName()] == 4 );
            // track occurrence
            if (!array_key_exists($selectedCard->getName(),$occurrences)) {
                $occurrences[$selectedCard->getName()] = 1;
            } else {
                $occurrences[$selectedCard->getName()]++;
            }
            // if ok, add to selection
            $cards[] = self::randomCard($trainerCards);
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
     * Fetch Pokemon card by type via API
     * @param $type
     * @return array
     */
    protected static function fetchPokemonType($type): array
    {
        return Pokemon::Card()->where([
            'supertype' => 'Pokémon',
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

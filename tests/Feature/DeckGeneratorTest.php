<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JetBrains\PhpStorm\NoReturn;
use Tests\TestCase;

use App\Classes\DeckGenerator;

class DeckGeneratorTest extends TestCase
{

    protected array $cards = [];

    /**
     * Run before tests
     */
    #[NoReturn] public function setUp(): void
    {
        parent::setUp();
        DeckGenerator::init();
        $method = self::getMethod('generateDeck');
        $this->cards = $method->invokeArgs(null, ['Fire']);
    }

    /**
     * Test number of card for 60.
     *
     * @return void
     */
    public function testCardNumber()
    {
        $this->assertCount(60, $this->cards);
    }

    /**
     * Test 12-16 pokemon of a given type
     *
     * @return void
     */
    public function testPokemonNumber()
    {
        $pokeNumber = 0;

        foreach ($this->cards as $card) {
            if ($card->getSupertype() == 'Pokémon') $pokeNumber++;
        }

        $this->assertTrue($pokeNumber >= 12 && $pokeNumber <= 16);
    }

    /**
     * All pokemon need to have the same type
     *
     * @return void
     */
    public function testPokemonSameType()
    {
        $wrongTypeCounter = 0;

        foreach ($this->cards as $card) {
            if ($card->getSupertype() == 'Pokémon' && !in_array('Fire',$card->getTypes()) ) $wrongTypeCounter++;
        }

        $this->assertEquals(0,$wrongTypeCounter);
    }


    /**
     * Test 12-16 pokemon of a given type
     *
     * @return void
     */
    public function testEnergyCardNumber()
    {
        $energyNumber = 0;

        foreach ($this->cards as $card) {
            if ($card->getSupertype() == 'Energy') $energyNumber++;
        }

        $this->assertTrue($energyNumber == 10);
    }


    /**
     * Test 12-16 pokemon of a given type
     *
     * @return void
     */
    public function testTrainerCardNoMoreThanFour()
    {
        $occurrences = [];
        foreach ($this->cards as $card) {
            if ($card->getSupertype() == 'Trainer') {
                // track occurrence
                if (!array_key_exists($card->getName(), $occurrences)) {
                    $occurrences[$card->getName()] = 1;
                } else {
                    $occurrences[$card->getName()]++;
                }
            }
        }

        $this->assertEmpty(array_filter($occurrences, function ($val) {
            if ($val > 4) return true;
        }));
    }

    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('App\Classes\DeckGenerator');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'name',
        'supertype',
        'types',
    ];

    /**
     * The users that belong to the role.
     */
    public function decks()
    {
        return $this->belongsToMany(Card::class,'decks_cards');
    }

}

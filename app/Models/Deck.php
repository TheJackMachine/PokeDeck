<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'focus',
    ];

    public function cards()
    {
        return $this->belongsToMany(Card::class,'decks_cards');
    }

}

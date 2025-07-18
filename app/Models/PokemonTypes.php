<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PokemonTypes extends Model
{
    protected $fillable = [
        'pokemon_id',
        'type_name'
    ];
}

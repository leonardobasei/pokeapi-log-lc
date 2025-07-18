<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $fillable = [
        'name',
        'photo',
        'height',
        'weight',
    ];

    public function types()
    {
        return $this->hasMany(PokemonTypes::class);
    }
}

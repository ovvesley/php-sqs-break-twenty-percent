<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Resposta extends Model
{
    protected $fillable = [
        'queue', 'response', 'status',
    ];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotFridgePage extends Model
{
    use HasFactory;

    protected $table = 'bot_fridge_pages';

    protected $fillable = [
        'page',
        'status'
    ];
}

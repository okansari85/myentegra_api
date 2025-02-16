<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotMedicinePages extends Model
{
    use HasFactory;

    protected $table = 'bot_medicine_pages';

    protected $fillable = [
        'page',
        'status'
    ];
}

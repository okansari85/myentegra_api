<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FridgeModel extends Model
{
    use HasFactory;

    protected $table = 'fridge_models';

    protected $fillable = [
        'model_no',
        'model',
        'production_period',
    ];
}

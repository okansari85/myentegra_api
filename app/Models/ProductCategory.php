<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Interfaces\ICategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;


class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = ['name', 'parent_id'];


    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function descendants()
    {
        return $this->parent()->with('descendants');
    }


}

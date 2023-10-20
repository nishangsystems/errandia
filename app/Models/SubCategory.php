<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_sub_categories';

    public function category()
    {
        # code...
        return $this->belongsTo(Category::class, 'category_id');
    }
}

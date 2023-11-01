<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Errand extends Model
{
    use HasFactory;

    protected $table = 'product_quote';

    public function posted_by()
    {
        # code...
        return $this->belongsTo(User::class, 'UserID');
    }

    public function location(){
        return null;
    }

    public function _categories(){
        $cats = explode($this->categories, ',');
        return SubCategory::whereIn('id', $cats);
    }
}

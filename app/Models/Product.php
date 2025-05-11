<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unique_key',
        'product_title',
        'product_description',
        'style',
        'mainframe_color',
        'size',
        'color_name',
        'piece_price',
    ];
    
    use HasFactory;
    
}

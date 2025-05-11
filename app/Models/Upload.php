<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'file_path',
        'status',
        'file_name',
        'uploaded_at',
        'processed_at',
    ];
    
    use HasFactory;
    
}

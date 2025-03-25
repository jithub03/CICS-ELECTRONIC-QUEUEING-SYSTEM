<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'full_name',
        'comments',
        'inquiry_type',
        'anonymous',
        'reaction',
    ];
}

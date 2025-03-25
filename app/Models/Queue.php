<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = [
        'full_name',
        'contact_number',
        'email',
        'inquiry_type',
        'inquiry_details',
        'notify_sms',
        'notify_email',
        'window_number',
    ];
}

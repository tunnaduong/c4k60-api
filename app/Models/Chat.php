<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chat';
    public $timestamps = false;

    protected $fillable = ['user_from', 'user_to', 'message', 'type', 'image_url', 'time', 'sent', 'received'];
}

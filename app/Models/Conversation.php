<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';
    public $timestamps = false;

    protected $fillable = ['conversation_id', 'user_1', 'user_2', 'updated_at'];
}

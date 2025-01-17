<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'thongbaolop'; // Specify the table name if it doesn't match the plural of the model name

    protected $fillable = [
        'id',
        'title',
        'content',
        'createdBy',
        'image',
        'date'
    ];
}

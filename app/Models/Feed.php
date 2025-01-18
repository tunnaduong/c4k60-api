<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    // Assuming your table is named `feeds`
    protected $table = 'tintuc_posts';

    // Define the relationship to the User model
    public function author()
    {
        return $this->belongsTo(User::class, 'username', 'username'); // Adjust column names if necessary
    }
}

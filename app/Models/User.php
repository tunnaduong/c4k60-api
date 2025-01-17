<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'c4_user';

    // Primary key
    protected $primaryKey = 'id';

    // Fillable attributes
    protected $fillable = [
        'name',
        'firstname',
        'lastname',
        'username',
        'password',
        'dayofbirth',
        'monthofbirth',
        'yearofbirth',
        'address',
        'phone_number',
        'short_name',
        'fb_link',
        'ig_link',
        'additional_info',
        'role',
        'verified',
        'avatar',
        'gender',
        'last_activity',
        'expo_push_notification_token',
    ];

    // Hidden attributes
    protected $hidden = [
        'password', // Don't expose the password
        // 'expo_push_notification_token', // Optional: hide sensitive information
    ];

    // Date attributes
    protected $dates = [
        'last_activity', // Automatically cast this field to Carbon instance
    ];

    // Cast attributes
    protected $casts = [
        'verified' => 'boolean', // Cast verified field to boolean
        'dayofbirth' => 'integer',
        'monthofbirth' => 'integer',
        'yearofbirth' => 'integer',
    ];

    // Disable automatic timestamps
    public $timestamps = false;

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    // In your User model, you do not need to hash the password
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value; // Store plain text password
    }
}

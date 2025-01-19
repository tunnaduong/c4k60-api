<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveRadio extends Model
{
    use HasFactory;

    // Set a default table name if needed
    protected $table = 'live_radio_idle_playlist';
    protected $fillable = []; // Default to an empty array
    public $timestamps = false;

    public function setTableAndFillable($table, array $fillableColumns)
    {
        $this->table = $table;
        $this->fillable = $fillableColumns;
        return $this;
    }
}

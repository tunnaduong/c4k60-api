<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    // Set a default table name if needed
    protected $table = 'album';
    protected $fillable = []; // Default to an empty array

    public function setTableAndFillable($table, array $fillableColumns)
    {
        $this->table = $table;
        $this->fillable = $fillableColumns;
        return $this;
    }
}

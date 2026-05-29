<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class Table extends Model {
    use BelongsToClub;

    protected $fillable = ['club_id', 'name', 'price_per_hour'];

    public function club() { return $this->belongsTo(Club::class); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class Service extends Model {
    use BelongsToClub;

    protected $fillable = ['club_id', 'name', 'price', 'category'];

    public function club() { return $this->belongsTo(Club::class); }
}

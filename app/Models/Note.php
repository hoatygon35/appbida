<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class Note extends Model {
    use BelongsToClub;

    protected $fillable = ['club_id', 'title', 'content', 'created_by'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}

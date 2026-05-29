<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class ActivityLog extends Model {
    use BelongsToClub;

    protected $fillable = ['user_id', 'club_id', 'action', 'description'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

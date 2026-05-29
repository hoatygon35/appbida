<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class GameSession extends Model {
    use BelongsToClub;
    
    protected $table = 'sessions';
    protected $fillable = ['club_id', 'table_id', 'opened_by', 'start_time', 'status'];
    protected $casts = ['start_time' => 'datetime'];

    public function table() { return $this->belongsTo(Table::class); }
    public function employee() { return $this->belongsTo(User::class, 'opened_by'); }
    
    public function services() {
        return $this->belongsToMany(Service::class, 'session_services', 'session_id', 'service_id')
                    ->withPivot('quantity', 'note')
                    ->withTimestamps();
    }
}

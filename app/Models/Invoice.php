<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;
use App\Models\Club;

class Invoice extends Model {
    use BelongsToClub;

    protected $fillable = [
        'club_id', 'table_id', 'session_id', 'opened_by', 
        'start_time', 'end_time', 'duration_minutes', 
        'table_fee', 'services_fee', 'total'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function table() { return $this->belongsTo(Table::class); }
    public function employee() { return $this->belongsTo(User::class, 'opened_by'); }
    public function club() { return $this->belongsTo(Club::class); }
    
    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }
}

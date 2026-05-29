<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToClub;

class Debt extends Model {
    use BelongsToClub;
    
    protected $fillable = ['club_id', 'customer_name', 'total_debt'];

    public function transactions() {
        return $this->hasMany(DebtTransaction::class)->orderBy('created_at', 'desc');
    }
}

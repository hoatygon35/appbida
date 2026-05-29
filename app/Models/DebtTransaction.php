<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtTransaction extends Model {
    protected $fillable = ['debt_id', 'amount', 'note'];

    public function debt() {
        return $this->belongsTo(Debt::class);
    }
}

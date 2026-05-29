<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model {
    protected $fillable = [
        'invoice_id', 'service_id', 'service_name', 
        'price', 'quantity', 'subtotal'
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }

    public function service() {
        return $this->belongsTo(Service::class);
    }
}

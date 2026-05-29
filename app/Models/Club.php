<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model {
    protected $fillable = ['name', 'phone', 'address', 'qr_code'];

    public function tables() { return $this->hasMany(Table::class); }
    public function services() { return $this->hasMany(Service::class); }
    public function users() { return $this->hasMany(User::class); }
}

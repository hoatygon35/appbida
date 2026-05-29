<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToClub {
    protected static function bootBelongsToClub() {
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->club_id) {
                $model->club_id = auth()->user()->club_id;
            }
        });

        static::addGlobalScope('club_scope', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role !== 'manager') {
                $builder->where('club_id', auth()->user()->club_id);
            }
        });
    }
}

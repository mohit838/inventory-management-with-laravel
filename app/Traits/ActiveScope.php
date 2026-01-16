<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait ActiveScope
{
    /**
     * Boot the ActiveScope trait.
     */
    public static function bootActiveScope(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where($builder->getModel()->getTable() . '.active', true);
        });
    }

    /**
     * Scope a query to include inactive models.
     */
    public function scopeWithInactive(Builder $query): void
    {
        $query->withoutGlobalScope('active');
    }

    /**
     * Scope a query to only include inactive models.
     */
    public function scopeInactive(Builder $query): void
    {
        $query->withoutGlobalScope('active')->where($query->getModel()->getTable() . '.active', false);
    }
}

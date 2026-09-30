<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::creating(function (Model $model): void {
            $model->setAttribute('created_by', $model->getAttribute('created_by') ?? auth()->id());
            $model->setAttribute('updated_by', $model->getAttribute('updated_by') ?? auth()->id());
        });

        static::updating(function (Model $model): void {
            $model->setAttribute('updated_by', auth()->id());
        });
    }
}

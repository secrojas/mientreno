<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Support\BusinessContext;

trait Tenantable
{
    protected static function bootTenantable(): void
    {
        static::addGlobalScope('business', function (Builder $builder) {
            $ctx = app(BusinessContext::class);
            if ($ctx->id()) {
                $builder->where($builder->getModel()->getTable().'.business_id', $ctx->id());
            }
        });

        static::creating(function ($model) {
            $ctx = app(BusinessContext::class);
            if ($ctx->id() && empty($model->business_id)) {
                $model->business_id = $ctx->id();
            }
        });
    }
}

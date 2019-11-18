<?php

namespace App\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

interface Filter
{
    /**
     * 把过滤条件附加到 builder 的实例上
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value);
}

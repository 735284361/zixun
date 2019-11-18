<?php

namespace App\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class Keyword implements Filter
{
    /**
     * @param Builder $builder
     * @param mixed $value
     * @return Builder|void
     */
    public static function apply(Builder $builder, $value)
    {
        return $builder
            ->when($value, function($query) use ($value) {
                $query->orWhere('name','like','%'.$value.'%')
                        ->orWhere('title','like','%'.$value.'%')
                        ->orWhere('background','like','%'.$value.'%')
                        ->orWhere('good_at_filed','like','%'.$value.'%')
                        ->orWhereHas('tags',function($query) use ($value) {
                            $query->where('tag','like', '%'.$value.'%');
                        });
            });
    }
}

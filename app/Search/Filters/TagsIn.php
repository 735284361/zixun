<?php

namespace App\Search\Filters;

use Illuminate\Database\Eloquent\Builder;

class TagsIn implements Filter
{
    /**
     * @param Builder $builder
     * @param mixed $value
     * @return Builder|mixed
     */
    public static function apply(Builder $builder, $value)
    {
        $when = false;
        if (is_array($value) && !in_array('',$value) && !empty($value)) {
            $when = true;
        }
        return $builder
            ->when($when, function($query) use ($value) {
                $query->whereHas('tags',function($query) use ($value) {
                    $query->whereIn('tag',$value);
                });
            });
    }
}

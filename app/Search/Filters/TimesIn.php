<?php

namespace App\Search\Filters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TimesIn implements Filter
{
    /**
     * @param Builder $builder
     * @param mixed $value
     * @return Builder|void
     */
    public static function apply(Builder $builder, $value)
    {
        $when = false;
        if (is_array($value) && !in_array('',$value) && !empty($value)) {
            $value = join(",",$value);
            $when = true;
        }
        return $builder->when($when, function($query) use ($value) {
            $query->whereHas('teacherTimes',function($query) use ($value) {
                $query->whereRaw('FROM_UNIXTIME(start_at,"%H") IN ('.$value.')')->where('date_at','>=',Carbon::today()->timestamp);
            });
        });
    }
}

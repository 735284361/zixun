<?php
namespace App\Search\Filters;
use Illuminate\Database\Eloquent\Builder;
class PriceBetween implements Filter
{
    /**
     * Apply a given search value to the builder instance.
     *
     * @param Builder $builder
     * @param mixed $value
     * @return Builder $builder
     */
    public static function apply(Builder $builder, $value)
    {
        $when = false;
        // 条件维数组 切不存在空值
        if (is_array($value) && !in_array('', $value) && !empty($value)) {
            $when = true;
        }
        return $builder->when($when, function($query) use ($value) {
            $query->whereBetween('price', [$value[0],$value[1]]);
        });
    }
}

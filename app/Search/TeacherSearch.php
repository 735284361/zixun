<?php

namespace App\Search;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TeacherSearch
{
    public static function apply(Request $filters)
    {
        DB::enableQueryLog();
        $query = static::applyDecoratorsFromRequest($filters, (new Teacher())->newQuery());
        $list = static::getResults($query);
        $sql = DB::getQueryLog();
//        dd($sql);
        return $list;
    }

    private static function applyDecoratorsFromRequest(Request $request, Builder $query)
    {
        foreach ($request->all() as $filterName => $value) {
            $decorator = static::createFilterDecorator($filterName);
            if (static::isValidDecorator($decorator)) {
                $query = $decorator::apply($query, $value);
            }
        }
        return $query;
    }

    private static function createFilterDecorator($name)
    {
        return __NAMESPACE__ . '\\Filters\\' . studly_case($name);
    }

    private static function isValidDecorator($decorator)
    {
        return class_exists($decorator);
    }

    private static function getResults(Builder $query)
    {
        return $query->where('status',Teacher::STATUS_ENABLE)->paginate(1);
    }
}

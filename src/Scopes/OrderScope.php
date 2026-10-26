<?php

namespace AndersonScherdovski\Base\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $sort = request()->get('sort', 'id desc');

        if (!$sort)
            return;

        $sortArray = explode('.', $sort);

        if (count($sortArray) > 1) {
            $table = $sortArray[0];
            $sort = $sortArray[1];

            [$column, $direction] = self::parseSort($sort);

            $builder->whereHas($table, function ($query) use ($column, $direction) {
                $query->orderBy($column, $direction);
            });
        } else {
            [$column, $direction] = self::parseSort($sort);

            $builder->orderBy($column, $direction);
        }
    }

    /**
     * Split a "column direction" expression, defaulting the direction to asc.
     *
     * @return array{0: string, 1: string}
     */
    private static function parseSort(string $sort): array
    {
        $arrayData = explode(' ', trim($sort));

        return [$arrayData[0], $arrayData[1] ?? 'asc'];
    }
}

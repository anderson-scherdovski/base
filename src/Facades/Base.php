<?php

namespace AndersonScherdovski\Base\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AndersonScherdovski\Base\Base
 */
class Base extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'base';
    }
}

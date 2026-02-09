<?php

namespace BlackpigCreatif\Sceau\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \BlackpigCreatif\Sceau\Services\SchemaStack push(array $schema)
 * @method static array all()
 * @method static void clear()
 * @method static bool isEmpty()
 * @method static int count()
 *
 * @see \BlackpigCreatif\Sceau\Services\SchemaStack
 */
class Schema extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \BlackpigCreatif\Sceau\Services\SchemaStack::class;
    }
}

<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Facades;

use Illuminate\Support\Facades\Facade;
/**
 * @method static \HTMLPurifier              getPurifier()
 * @method static array|string               clean(array|string $input)
 * @method static \Stevebauman\Purify\Purify config(string|array $driver = null)
 */
class Purify extends Facade
{
    /**
     * The facade accessor string.
     *
     * @return string
     */
    protected static function get_facade_accessor()
    {
        return 'purify';
    }
}
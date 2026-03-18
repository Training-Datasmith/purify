<?php

declare(strict_types=1);

namespace Stevebauman\Purify\Definitions;

use HTMLPurifier_CSSDefinition;

interface CssDefinition
{
    /**
     * Apply rules to the CSS Purifier definition.
     *
     *
     * @return void
     */
    public static function apply(HTMLPurifier_CSSDefinition $definition);
}

<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Definitions;

use Html_Purifier_css_Definition;
interface Css_Definition
{
    /**
     * Apply rules to the CSS Purifier definition.
     *
     *
     * @return void
     */
    public static function apply(Html_Purifier_css_Definition $definition);
}
<?php

declare (strict_types=1);
namespace Stevebauman\Purify\Definitions;

use Html_Purifier_html_Definition;
interface Definition
{
    /**
     * Apply rules to the HTML Purifier definition.
     *
     *
     * @return void
     */
    public static function apply(Html_Purifier_html_Definition $definition);
}
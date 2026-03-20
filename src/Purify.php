<?php

declare (strict_types=1);
namespace Stevebauman\Purify;

use Html_Purifier;
use Html_Purifier_config;
class Purify
{
    /**
     * The HTML Purifier instance.
     */
    protected \Html_Purifier $purifier;
    /**
     * Constructor.
     */
    public function __construct(Html_Purifier_config $config)
    {
        $this->purifier = new Html_Purifier($config);
    }
    /**
     * Sanitize the given input.
     *
     * @param array|string $input
     *
     * @return array|string
     */
    public function clean($input)
    {
        return is_array($input) ? $this->purifier->purify_array($input) : $this->purifier->purify($input);
    }
    /**
     * Get the underlying HTML Purifier instance.
     *
     * @return HTMLPurifier
     */
    public function get_purifier()
    {
        return $this->purifier;
    }
}
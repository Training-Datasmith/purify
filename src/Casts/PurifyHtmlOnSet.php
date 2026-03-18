<?php

namespace Stevebauman\Purify\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Stevebauman\Purify\Facades\Purify;

class PurifyHtmlOnSet extends Caster implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return string|array|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Purify the value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed                               $value
     *
     * @return array|string|null
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        return (new Purify())->config($this->config)->clean($value);
    }
}

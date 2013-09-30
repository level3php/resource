<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3;

abstract class Formatter
{
    const CONTENT_TYPE_ANY = '*/*';

    public abstract function toResponse(Resource $resource, $pretty = false);

    public abstract function fromRequest($string);

    public function getContentType()
    {
        return static::CONTENT_TYPE;
    }
}

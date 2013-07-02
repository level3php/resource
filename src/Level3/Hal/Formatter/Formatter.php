<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Hal\Formatter;
use Level3\Hal\Resource;

abstract class Formatter
{
    public function format(Resource $resource)
    {
        return $this->formatResource($resource, false);
    }

    public function formatPretty(Resource $resource)
    {
        return $this->formatResource($resource, true);
    }

    protected abstract function formatResource(Resource $resource, $pretty);
}

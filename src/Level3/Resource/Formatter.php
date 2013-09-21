<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Resource;
use Level3\Resource;

abstract class Formatter
{
    const CONTENT_TYPE_ANY = '*/*';

    public function format(Resource $resource)
    {
        return $this->formatResource($resource, false);
    }

    public function formatPretty(Resource $resource)
    {
        return $this->formatResource($resource, true);
    }

    protected abstract function formatResource(Resource $resource, $pretty);

    public function getContentType()
    {
        return $this->contentType;
    }
}

<?php
/*
 * This file is part of the Level3 package.
 *
 * (c) Máximo Cuadros <maximo@yunait.com>
 * (c) Ben Longden <ben@nocarrier.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Level3\Formatter;

use Level3\Formatter;
use Level3\Resource;
use Level3\Exceptions\BadRequest;

class JsonFormatter extends Formatter
{
    const CONTENT_TYPE = 'application/hal+json';

    public function fromRequest($string)
    {
        if (strlen($string) == 0) {
            return Array();
        }

        $array = json_decode($string, true);

        if (!is_array($array)) {
            throw new BadRequest();
        }

        return $array;
    }

    public function toResponse(Resource $resource, $pretty = false)
    {
        $options = 0;
        if (version_compare(PHP_VERSION, '5.4.0') >= 0 and $pretty) {
            $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        }

        return json_encode($resource->toArray(), $options);
    }
}

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

interface MapperInterface
{
    public function mapList($uri, $alias);
    public function mapGet($uri, $alias);
    public function mapPost($uri, $alias);   
    public function mapPut($uri, $alias);
    public function mapDelete($uri, $alias);
    public function getURI($alias, array $parameters = null);
}
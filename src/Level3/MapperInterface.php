<?php
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
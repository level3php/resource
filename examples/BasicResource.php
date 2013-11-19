<?php
require __DIR__ . '/../vendor/autoload.php';

use Level3\Resource\Link;
use Level3\Resource\Resource;
use Level3\Resource\Formatter\HAL;

$resource = new Resource();
$resource->setURI('/foo');
$resource->setLink('foo', new Link('/bar'));
$resource->setData([
    'foo' => 'bar',
    'baz' => 'qux'
]);

$resource->setFormatter(new HAL\JsonFormatter(true));

echo $resource;
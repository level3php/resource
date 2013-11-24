<?php
require __DIR__ . '/../vendor/autoload.php';

use Level3\Resource\Link;
use Level3\Resource\Resource;
use Level3\Resource\Format\Writer\Siren;

$resource = new Resource();
$resource->setRepositoryKey('index');
$resource->setURI('/index?page=2');
$resource->setLink('prev', new Link('/index?page=1'));
$resource->setLink('next', new Link('/index?page=3'));
$resource->addData('count', 5);

$subresource = [];
foreach (range(1, 5) as $value) {
    $subresource = new Resource();
    $subresource->addData('value', $value);

    $subresources[] = $subresource;
}

$resource->addResources('subresources', $subresources);

$writer = new Siren\JsonWriter(true);
echo $writer->execute($resource);
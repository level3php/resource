<?php
require __DIR__ . '/../vendor/autoload.php';

use Level3\Resource\Format\Reader\HAL;

$json = '{"foo":"bar","baz":"qux","_links":{"self":{"href":"/foo"},"foo":{"href":"/bar"}}}';

$reader = new HAL\JsonReader();
$resource = $reader->execute($json);
print_r($resource);
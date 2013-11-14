<?php

namespace Level3\Repository;
use Level3\Resource\Parameters;

interface Putter
{
    public function put(Parameters $attributes, Array $data);
}

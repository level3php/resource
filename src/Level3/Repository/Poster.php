<?php

namespace Level3\Repository;
use Level3\Resource\Parameters;

interface Poster
{
    public function post(Parameters $attributes, Array $data);
}

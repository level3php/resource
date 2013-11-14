<?php

namespace Level3\Repository;
use Level3\Resource\Parameters;

interface Finder
{
    public function find(Parameters $attributes, Parameters $filters);
}

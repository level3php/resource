<?php

namespace Level3\Repository;
use Level3\Messages\Parameters;

interface Finder
{
    public function find(Parameters $attributes, Parameters $filters);
}

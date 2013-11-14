<?php

namespace Level3\Repository;
use Level3\Resource\Parameters;

interface Getter
{
    public function get(Parameters $attributes);
}

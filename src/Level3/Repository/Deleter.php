<?php

namespace Level3\Repository;
use Level3\Resource\Parameters;

interface Deleter
{
    public function delete(Parameters $attributes);
}

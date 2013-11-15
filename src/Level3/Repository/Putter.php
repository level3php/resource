<?php

namespace Level3\Repository;
use Level3\Messages\Parameters;

interface Putter
{
    public function put(Parameters $attributes, Array $data);
}

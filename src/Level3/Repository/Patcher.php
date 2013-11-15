<?php

namespace Level3\Repository;
use Level3\Messages\Parameters;

interface Patcher
{
    public function patch(Parameters $attributes, Array $data);
}

<?php

namespace Level3\Repository;

use Level3\Messages\Parameters;

interface Getter
{
    public function get(Parameters $attributes);
}

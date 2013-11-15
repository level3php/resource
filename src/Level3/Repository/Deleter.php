<?php

namespace Level3\Repository;

use Level3\Messages\Parameters;

interface Deleter
{
    public function delete(Parameters $attributes);
}

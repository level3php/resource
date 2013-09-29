<?php

namespace Level3;

use Level3\Messages\Request;

interface Processor
{
    public function find(Request $request);

    public function get(Request $request);

    public function post(Request $request);

    public function put(Request $request);
    
    public function patch(Request $request);

    public function delete(Request $request);
}
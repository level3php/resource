<?php

namespace Level3\Messages\Processors;

use Level3\Messages\Request;

interface RequestProcessor
{
    public function find(Request $request);

    public function get(Request $request);

    public function post(Request $request);

    public function put(Request $request);

    public function delete(Request $request);
}
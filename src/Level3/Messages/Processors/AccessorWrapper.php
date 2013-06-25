<?php

namespace Level3\Messages\Processors;

use Level3\Accessor;
use Level3\Messages\Request;

class AccessorWrapper implements RequestProcessor
{
    protected $resourceAccessor;

    public function __construct(Accessor $resourceAccessor)
    {
        $this->resourceAccessor = $resourceAccessor;
    }

    public function find(Request $request)
    {
        $key = $request->getKey();

        return $this->resourceAccessor->find($key);
    }

    public function get(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();

        return $this->resourceAccessor->get($key, $id);
    }

    public function post(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();
        $content = $request->getContent();

        return $this->resourceAccessor->post($key, $id, $content);
    }

    public function put(Request $request)
    {
        $key = $request->getKey();
        $content = $request->getContent();

        return $this->resourceAccessor->put($key, $content);
    }

    public function delete(Request $request)
    {
        $key = $request->getKey();
        $id = $request->getId();

        return $this->resourceAccessor->delete($key, $id);
    }
}
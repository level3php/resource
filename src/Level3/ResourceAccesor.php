<?php
namespace Level3;

class ResourceAccesor
{
    private $hub;

    public function __construct(ResourceHub $hub)
    {
        $this->hub = $hub;
    }

    public function get($key, $id = null)
    {
        $resource = $this->hub[$key];
        if ( $id ) $result = $resource->getOne($id);
        else $result = $resource->get();

        if ( $result === false ) $status = StatusCode::NOT_FOUND;
        else if ( $result === null ) $status = StatusCode::NO_CONTENT;
        else $status = StatusCode::OK;


        return new Response($result, $status);
    }

    public function post($key, $id, Array $data)
    {
        $resource = $this->hub[$key];
        $result = $resource->post($id, $data);

        if ( $result === false ) $status = StatusCode::CONFLICT;
        else if ( $result === null ) $status = StatusCode::NOT_FOUND;
        else $status = StatusCode::OK;

        $value = $resource->getOne($id);
        return new Response($value, $status);
    }

    public function put($key, Array $data)
    {
        $resource = $this->hub[$key];
        $result = $resource->put($data);

        if ( !$result ) $status = StatusCode::BAD_REQUEST;
        else $status = StatusCode::CREATED;

        $value = $resource->getOne($result);
        return new Response($value, $status);
    }

    public function delete($key, $id)
    {
        $resource = $this->hub[$key];
        $result = $resource->delete($id);

        if ( $result === false ) $status = StatusCode::NOT_FOUND;
        else if ( $result === null ) $status = StatusCode::NO_CONTENT;
        else $status = StatusCode::OK;

        return new Response(null, $status);
    }
}
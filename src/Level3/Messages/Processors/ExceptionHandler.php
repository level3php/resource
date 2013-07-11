<?php

namespace Level3\Messages\Processors;

use Level3\Exceptions\BaseException;
use Level3\Hal\Formatter\FormatterFactory;
use Level3\Hal\ResourceFactory;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use Teapot\StatusCode;

class ExceptionHandler implements RequestProcessor
{
    private $requestProcessor;
    private $formatterFactory;
    private $resourceFactory;
    private $responseFactory;
    private $isDebugEnabled = false;

    public function __construct(
        RequestProcessor $processor,
        FormatterFactory $formatterFactory,
        ResourceFactory $resourceFactory,
        ResponseFactory $responseFactory
    ) {
        $this->requestProcessor = $processor;
        $this->formatterFactory = $formatterFactory;
        $this->resourceFactory = $resourceFactory;
        $this->responseFactory = $responseFactory;
    }

    public function enableDebug()
    {
        $this->isDebugEnabled = true;
    }

    public function disableDebug()
    {
        $this->isDebugEnabled = false;
    }

    public function find(Request $request)
    {
        $this->processRequest($request, 'find');
    }

    private function processRequest(Request $request, $method)
    {
        try {
            $response = $this->$method($request);
        } catch (BaseException $e) {
            $response = $this->generateExceptionResponse($request, $e, $e->getCode());
        } catch (\Exception $e) {
            $response = $this->generateExceptionResponse($request, $e, StatusCode::INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    private function generateExceptionResponse(Request $request, \Exception $exception, $code)
    {
        $data = $this->generateDataForException($exception, $code);
        $resource = $this->resourceFactory->create(null, $data);
        $formatter = $this->formatterFactory->create($request->getContentType());
        $resource->setFormatter($formatter);
        $response = $this->responseFactory->create($resource, $code);
        $response->setContentType($formatter->getContentType());
        return $response;
    }

    private function generateDataForException(\Exception $e, $statusCode = 500)
    {
        $data = array(
            'code' => $statusCode,
        );

        if ($this->isDebugEnabled) {
            $data['message'] = $e->getMessage();
            $data['trace'] = $e->getTrace();
        }

        return $data;
    }

    public function get(Request $request)
    {
        $this->processRequest($request, 'get');
    }

    public function post(Request $request)
    {
        $this->processRequest($request, 'post');
    }

    public function put(Request $request)
    {
        $this->processRequest($request, 'put');
    }

    public function delete(Request $request)
    {
        $this->processRequest($request, 'delete');
    }
}
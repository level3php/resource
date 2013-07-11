<?php

namespace Level3\Messages\Processors;

use Level3\Exceptions\BaseException;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Teapot\StatusCode;

class ExceptionHandler implements RequestProcessor
{
    private $requestProcessor;
    private $responseFactory;
    private $isDebugEnabled = false;
    private $logger;

    public function __construct(
        RequestProcessor $processor,
        ResponseFactory $responseFactory
    ) {
        $this->requestProcessor = $processor;
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

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function find(Request $request)
    {
        return $this->processRequest($request, 'find');
    }

    private function processRequest(Request $request, $method)
    {
        try {
            $response = $this->requestProcessor->$method($request);
        } catch (BaseException $e) {
            $this->logExceptionWithLevel($e, LogLevel::INFO);
            $response = $this->generateExceptionResponse($request, $e, $e->getCode());
        } catch (\Exception $e) {
            $this->logExceptionWithLevel($e, LogLevel::ALERT);
            $response = $this->generateExceptionResponse($request, $e, StatusCode::INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    private function logExceptionWithLevel(\Exception $exception, $level)
    {
        if ($this->logger !== null) {
            $this->logger->log($level, $exception->getMessage(), $exception->getTrace());
        }
    }

    private function generateExceptionResponse(Request $request, \Exception $exception, $code)
    {
        $data = $this->generateDataForException($exception, $code);
        return $this->responseFactory->createFromDataAndStatusCode($request, $data, $code);
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
        return $this->processRequest($request, 'get');
    }

    public function post(Request $request)
    {
        return $this->processRequest($request, 'post');
    }

    public function put(Request $request)
    {
        return $this->processRequest($request, 'put');
    }

    public function delete(Request $request)
    {
        return $this->processRequest($request, 'delete');
    }
}
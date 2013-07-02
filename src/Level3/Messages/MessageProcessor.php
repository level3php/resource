<?php

namespace Level3\Messages;

use Level3\Hal\Formatter\FormatterFactory;
use Level3\Hal\Resource;
use Level3\Hal\ResourceFactory;
use Level3\Messages\Exceptions\HeaderNotFound;
use Level3\Messages\Exceptions\NotAcceptable;
use Level3\Messages\Parser\ParserFactory;
use Level3\Messages\Request;
use Level3\Messages\ResponseFactory;
use Level3\Repository\Exception\BaseException;
use Teapot\StatusCode;

class MessageProcessor
{
    const HEADER_ACCEPT = 'accept';
    const HEADER_RANGE = 'range';
    const HEADER_CONTENT_TYPE = 'content-type';
    const ACCEPT_DELIMITER = ';';
    const DEFAULT_CONTENT_TYPE = FormatterFactory::CONTENT_TYPE_APPLICATION_HAL_JSON;

    private $responseFactory;
    private $parserFactory;
    private $formatterFactory;
    private $resourceFactory;
    private $isDebugEnabled = false;
    protected $supportedFormats;

    public function __construct(
        ResponseFactory $responseFactory,
        ParserFactory $parserFactory,
        FormatterFactory $formatterFactory,
        ResourceFactory $resourceFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->parserFactory = $parserFactory;
        $this->formatterFactory = $formatterFactory;
        $this->resourceFactory = $resourceFactory;
        $this->supportedFormats = array(
            FormatterFactory::CONTENT_TYPE_APPLICATION_HAL_JSON,
            FormatterFactory::CONTENT_TYPE_APPLICATION_HAL_XML
        );
    }

    public function isDebugEnabled()
    {
        return $this->isDebugEnabled;
    }

    public function enableDebug()
    {
        $this->isDebugEnabled = true;
    }

    public function disableDebug()
    {
        $this->isDebugEnabled = false;
    }

    public function generateErrorResponse(\Exception $exception)
    {
        try {
            throw $exception;
        } catch (BaseException $e) {
            $response = $this->generateSpecificExceptionResponse($e);

        } catch (\Exception $e) {
            $response = $this->generateGenericExceptionResponse($e);
        }

        return $response;
    }

    protected function generateSpecificExceptionResponse(BaseException $exception)
    {
        $data = array();

        return $this->generateResponseFromDataAndStatusCode($data, $exception->getCode());
    }

    protected function generateGenericExceptionResponse(\Exception $exception)
    {
        $exception->getTrace();
        $data = array('code' => StatusCode::INTERNAL_SERVER_ERROR);
        if ($this->isDebugEnabled) {
            $data['message'] = $exception->getMessage();
            $data['stackTrace'] = $exception->getTrace();
        }

        return $this->generateResponseFromDataAndStatusCode($data, StatusCode::INTERNAL_SERVER_ERROR);
    }

    private function generateResponseFromDataAndStatusCode(array $data, $statusCode)
    {
        $resource = $this->resourceFactory->create(null, $data);
        $formatter = $this->createDefaultFormatter();
        $resource->setFormatter($formatter);

        return $this->responseFactory->create($resource, $statusCode);
    }

    public function generateOKResponse(Request $request, Resource $resource)
    {
        try {
            $response = $this->generateResponseWithRequestForResource($request, $resource);
        } catch (HeaderNotFound $e) {
            $response = $this->generateResponseWithDefaultFormatterForResource($resource);
        } catch (NotAcceptable $e) {
            $response = $this->generateNotAcceptableResponse();
        }

        return $response;
    }

    private function generateResponseWithRequestForResource(Request $request, Resource $resource)
    {
        $formatter = $this->getFormatterForRequest($request);
        $resource->setFormatter($formatter);
        return $this->responseFactory->create($resource, StatusCode::OK);
    }

    private function getFormatterForRequest(Request $request)
    {
        $format = $this->getFirstAcceptedFormat($request);
        return $this->formatterFactory->create($format);
    }

    private function generateResponseWithDefaultFormatterForResource(Resource $resource)
    {
        $formatter = $this->createDefaultFormatter();
        $resource->setFormatter($formatter);
        return $this->responseFactory->create($resource, StatusCode::OK);
    }

    private function generateNotAcceptableResponse()
    {
        return $this->responseFactory->create(null, StatusCode::NOT_ACCEPTABLE);
    }

    protected function getDefaultAcceptedFormat()
    {
        return self::DEFAULT_CONTENT_TYPE;
    }

    protected function getFirstAcceptedFormat(Request $request)
    {
        $acceptHeader = $request->getHeader(self::HEADER_ACCEPT);
        $acceptTypes = explode(self::ACCEPT_DELIMITER, $acceptHeader);

        foreach ($acceptTypes as $acceptType) {
            $acceptType = trim($acceptType);
            if ($this->isSupported($acceptType))
                return $acceptType;
        }
        throw new NotAcceptable();
    }

    private function isSupported($contentType)
    {
        return in_array($contentType, $this->supportedFormats);
    }

    public function getRequestContentAsArray(Request $request)
    {
        $contentType = $request->getHeader(self::HEADER_CONTENT_TYPE);
        $parser = $this->parserFactory->create($contentType);

        return $parser->parse($request->getContent());
    }

    private function createDefaultFormatter()
    {
        return $this->formatterFactory->create(self::DEFAULT_CONTENT_TYPE);
    }
}

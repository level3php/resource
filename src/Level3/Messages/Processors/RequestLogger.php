<?php

namespace Level3\Messages\Processors;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Level3\Messages\Request;
use Psr\Log\LogLevel;

class RequestLogger implements RequestProcessor
{
    const FIND_METHOD_NAME = 'FIND';
    const GET_METHOD_NAME = 'GET';
    const POST_METHOD_NAME = 'POST';
    const PUT_METHOD_NAME = 'PUT';
    const DELETE_METHOD_NAME = 'DELETE';

    private $processor;
    private $logger;
    private $logLevel = LogLevel::INFO;

    private $allowdLogLevels;

    public function __construct(RequestProcessor $processor, LoggerInterface $logger)
    {
        $this->processor = $processor;
        $this->logger = $logger;

        $this->allowdLogLevels = array(
            LogLevel::INFO => true,
            LogLevel::ALERT => true,
            LogLevel::CRITICAL => true,
            LogLevel::DEBUG => true,
            LogLevel::EMERGENCY => true,
            LogLevel::ERROR => true,
            LogLevel::NOTICE => true,
            LogLevel::WARNING => true
        );
    }

    public function getLogLevel()
    {
        return $this->logLevel;
    }

    public function setLogLevel($logLevel)
    {
        $this->checkLogLevelIsValid($logLevel);
        $this->logLevel = $logLevel;
    }

    private function checkLogLevelIsValid($logLevel)
    {
        if (!isset($this->allowdLogLevels[$logLevel]) || !$this->allowdLogLevels[$logLevel]) {
            throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $logLevel));
        }
    }

    public function find(Request $request)
    {
        $message = $this->generateLogMessage($request, self::FIND_METHOD_NAME);
        $this->logger->log($this->logLevel, $message);

        return $this->processor->find($request);
    }

    public function get(Request $request)
    {
        $message = $this->generateLogMessage($request, self::GET_METHOD_NAME);
        $this->logger->log($this->logLevel, $message);

        return $this->processor->get($request);
    }

    public function post(Request $request)
    {
        $message = $this->generateLogMessage($request, self::POST_METHOD_NAME);
        $this->logger->log($this->logLevel, $message);

        return $this->processor->post($request);
    }

    public function put(Request $request)
    {
        $message = $this->generateLogMessage($request, self::PUT_METHOD_NAME);
        $this->logger->log($this->logLevel, $message);

        return $this->processor->put($request);
    }

    public function delete(Request $request)
    {
        $message = $this->generateLogMessage($request, self::DELETE_METHOD_NAME);
        $this->logger->log($this->logLevel, $message);

        return $this->processor->delete($request);
    }

    protected function generateLogMessage(Request $request, $method)
    {
        $pathInfo = $request->getPathInfo();
        $userName = $request->getUser()->getFullName();
        $apiKey = $request->getUser()->getApiKey();
        return sprintf('%s %s - %s(%s)', $method, $pathInfo, $userName, $apiKey);
    }
}
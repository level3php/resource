<?php
namespace Level3\Tests;

use Level3\Processor\Wrapper\CrossOriginResourceSharing as CORS;
use Level3\Messages\Response;
use Psr\Log\LogLevel;
use Teapot\StatusCode;
use Exception;

use Mockery as m;

class CrossOriginResourceSharingTest extends TestCase
{
    private $wrapper;

    public function createWrapper()
    {
        return new CORS();
    }

    public function testSetAllowOriginWildcard()
    {
        $wrapper = $this->createWrapper();
        $wrapper->setAllowOrigin('*');

        $this->assertSame('*', $wrapper->getAllowOrigin());

        $request = $this->createRequestMock();
        $response = new Response();

        $wrapper->get(function($request) use ($response) {
            $request->getKey();
            return $response;
        }, $request);

        $this->assertSame('*', $response->getHeader(CORS::ALLOW_ORIGIN_HEADER));
    }
}

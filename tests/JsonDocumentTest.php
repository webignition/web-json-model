<?php

namespace webignition\Tests\WebResource\JsonDocument;

use GuzzleHttp\Message\ResponseInterface;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use webignition\WebResource\Exception;
use webignition\WebResource\JsonDocument\JsonDocument;

class JsonDocumentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var JsonDocument
     */
    private $jsonDocument;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->jsonDocument = new JsonDocument();
    }

    /**
     * @dataProvider getContentArrayDataProvider
     *
     * @param ResponseInterface $httpResponse
     * @param array $expectedArrayContent
     */
    public function testGetContentArray(ResponseInterface $httpResponse, $expectedArrayContent)
    {
        $this->jsonDocument->setHttpResponse($httpResponse);
        $this->assertEquals($expectedArrayContent, $this->jsonDocument->getContentArray());
    }

    /**
     * @return array
     */
    public function getContentArrayDataProvider()
    {
        return [
            'empty object' => [
                'responseBody' => $this->createHttpResponse('{}'),
                'expectedArrayContent' => [],
            ],
            'simple object' => [
                'responseBody' => $this->createHttpResponse('{"foo": "bar"}'),
                'expectedArrayContent' => [
                    'foo' => 'bar',
                ],
            ],
            'integer' => [
                'responseBody' => $this->createHttpResponse('1'),
                'expectedArrayContent' => 1
            ],
            'string' => [
                'responseBody' => $this->createHttpResponse('"foo"'),
                'expectedArrayContent' => 'foo'
            ],
            'null' => [
                'responseBody' => $this->createHttpResponse('null'),
                'expectedArrayContent' => null
            ],
        ];
    }

    /**
     * @dataProvider getContentObjectDataProvider
     *
     * @param ResponseInterface $httpResponse
     * @param array $expectedContentObject
     */
    public function testGetContentObject(ResponseInterface $httpResponse, $expectedContentObject)
    {
        $this->jsonDocument->setHttpResponse($httpResponse);
        $this->assertEquals($expectedContentObject, $this->jsonDocument->getContentObject());
    }

    /**
     * @return array
     */
    public function getContentObjectDataProvider()
    {
        return [
            'empty object' => [
                'responseBody' => $this->createHttpResponse('{}'),
                'expectedContentObject' => (object) [],
            ],
            'simple object' => [
                'responseBody' => $this->createHttpResponse('{"foo": "bar"}'),
                'expectedContentObject' => (object) [
                    'foo' => 'bar',
                ],
            ],
            'integer' => [
                'responseBody' => $this->createHttpResponse('1'),
                'expectedContentObject' => 1
            ],
            'string' => [
                'responseBody' => $this->createHttpResponse('"foo"'),
                'expectedContentObject' => 'foo'
            ],
            'null' => [
                'responseBody' => $this->createHttpResponse('null'),
                'expectedContentObject' => null
            ],
        ];
    }

    public function testSetHttpResponseWithInvalidContentType()
    {
        $httpResponse = \Mockery::mock(ResponseInterface::class);
        $httpResponse
            ->shouldReceive('getHeader')
            ->with('content-type')
            ->andReturn('text/html');

        $this->setExpectedException(Exception::class, 'HTTP response contains invalid content type', 2);

        $this->jsonDocument->setHttpResponse($httpResponse);
    }

    /**
     * @param string $body
     *
     * @return MockInterface|ResponseInterface
     */
    private function createHttpResponse($body = null)
    {
        $httpResponse = \Mockery::mock(ResponseInterface::class);
        $httpResponse
            ->shouldReceive('getHeader')
            ->with('content-type')
            ->andReturn('application/json');

        if (!empty($body)) {
            $httpResponse
                ->shouldReceive('getBody')
                ->andReturn($body);
        }

        return $httpResponse;
    }
}

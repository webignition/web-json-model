<?php

namespace webignition\Tests\WebResource\JsonDocument;

use Mockery\Mock;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use webignition\WebResource\JsonDocument\InvalidContentTypeException;
use webignition\WebResource\JsonDocument\JsonDocument;
use webignition\WebResource\WebResource;

class JsonDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createInvalidContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedExceptionMessage
     * @param string $expectedExceptionContentType
     */
    public function testCreateInvalidContentType(
        ResponseInterface $response,
        $expectedExceptionMessage,
        $expectedExceptionContentType
    ) {
        try {
            new JsonDocument($response);
            $this->fail(InvalidContentTypeException::class. 'not thrown');
        } catch (InvalidContentTypeException $invalidContentTypeException) {
            $this->assertEquals(InvalidContentTypeException::CODE, $invalidContentTypeException->getCode());
            $this->assertEquals($expectedExceptionMessage, $invalidContentTypeException->getMessage());
            $this->assertEquals($expectedExceptionContentType, (string)$invalidContentTypeException->getContentType());
        }
    }

    /**
     * @return array
     */
    public function createInvalidContentTypeDataProvider()
    {
        return [
            'text/plain' => [
                'response' => $this->createResponse('text/plain'),
                'expectedExceptionMessage' => 'Invalid content type: "text/plain"',
                'expectedExceptionContentType' => 'text/plain',
            ],
            'text/html' => [
                'response' => $this->createResponse('text/html'),
                'expectedExceptionMessage' => 'Invalid content type: "text/html"',
                'expectedExceptionContentType' => 'text/html',
            ],
        ];
    }

    /**
     * @dataProvider createDataProvider
     *
     * @param ResponseInterface $response
     * @param null|string|int|bool|array $expectedData
     *
     * @throws InvalidContentTypeException
     */
    public function testCreate(ResponseInterface $response, $expectedData)
    {
        $jsonDocument = new JsonDocument($response);

        $this->assertEquals($expectedData, $jsonDocument->getData());
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'application/json, null data' => [
                'response' => $this->createResponse('application/json', json_encode(null)),
                'expectedData' => null,
            ],
            'application/json, integer data, 0' => [
                'response' => $this->createResponse('application/json', json_encode(0)),
                'expectedData' => 0,
            ],
            'application/json, integer data, 1' => [
                'response' => $this->createResponse('application/json', json_encode(1)),
                'expectedData' => 1,
            ],
            'application/json, float' => [
                'response' => $this->createResponse('application/json', json_encode(pi())),
                'expectedData' => pi(),
            ],
            'application/json, bool, true' => [
                'response' => $this->createResponse('application/json', json_encode(true)),
                'expectedData' => true,
            ],
            'application/json, bool, false' => [
                'response' => $this->createResponse('application/json', json_encode(false)),
                'expectedData' => false,
            ],
            'application/json, string, empty' => [
                'response' => $this->createResponse('application/json', json_encode('')),
                'expectedData' => '',
            ],
            'application/json, string, non-empty' => [
                'response' => $this->createResponse('application/json', json_encode('foo')),
                'expectedData' => 'foo',
            ],
            'application/json, object, empty' => [
                'response' => $this->createResponse('application/json', json_encode((object)[])),
                'expectedData' => [],
            ],
            'application/json, object, non-empty' => [
                'response' => $this->createResponse('application/json', json_encode((object)[
                    'foo' => 'bar',
                ])),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
            'application/json, array, empty' => [
                'response' => $this->createResponse('application/json', json_encode([])),
                'expectedData' => [],
            ],
            'application/json, array, non-empty' => [
                'response' => $this->createResponse('application/json', json_encode([
                    'foo' => 'bar',
                ])),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
            'application/ld+json, string, non-empty' => [
                'response' => $this->createResponse('application/ld+json', json_encode('foo')),
                'expectedData' => 'foo',
            ],
            'text/javascript, string, non-empty' => [
                'response' => $this->createResponse('text/javascript', json_encode('foo')),
                'expectedData' => 'foo',
            ],
        ];
    }

    /**
     * @param string $contentType
     *
     * @param string|null $content
     * @return ResponseInterface|Mock
     */
    private function createResponse($contentType, $content = null)
    {
        /* @var ResponseInterface|Mock $response */
        $response = \Mockery::mock(ResponseInterface::class);

        $response
            ->shouldReceive('getHeader')
            ->once()
            ->with(WebResource::HEADER_CONTENT_TYPE)
            ->andReturn([
                $contentType,
            ]);

        /* @var StreamInterface|Mock $streamInterface */
        $streamInterface = \Mockery::mock(StreamInterface::class);
        $streamInterface
            ->shouldReceive('__toString')
            ->andReturn($content);

        $response
            ->shouldReceive('getBody')
            ->andReturn($streamInterface);

        return $response;
    }
}

<?php

namespace webignition\WebResource\JsonDocument\Tests;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\JsonDocument\JsonDocument;
use webignition\WebResource\WebResourceProperties;

class JsonDocumentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContentWithInvalidContentType()
    {
        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/png"');

        new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT_TYPE => new InternetMediaType('image', 'png'),
        ]));
    }

    /**
     * @dataProvider createFromContentDataProvider
     *
     * @param InternetMediaTypeInterface|null $contentType
     * @param string $expectedContentTypeString
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateFromContent(?InternetMediaTypeInterface $contentType, string $expectedContentTypeString)
    {
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'json document content';

        $jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT_TYPE => $contentType,
            WebResourceProperties::ARG_CONTENT => $content,
        ]));

        $this->assertInstanceOf(JsonDocument::class, $jsonDocument);
        $this->assertEquals($uri, $jsonDocument->getUri());
        $this->assertEquals($content, $jsonDocument->getContent());
        $this->assertEquals($expectedContentTypeString, (string)$jsonDocument->getContentType());
        $this->assertNull($jsonDocument->getResponse());
    }

    public function createFromContentDataProvider(): array
    {
        return [
            'no content type' => [
                'contentType' => null,
                'expectedContentTypeString' => 'application/json',
            ],
            'application/json content type' => [
                'contentType' => new InternetMediaType('application', 'json'),
                'expectedContentTypeString' => 'application/json',
            ],
            'text/javascript content type' => [
                'contentType' => new InternetMediaType('text', 'javascript'),
                'expectedContentTypeString' => 'text/javascript',
            ],
            'text/json content type' => [
                'contentType' => new InternetMediaType('text', 'json'),
                'expectedContentTypeString' => 'text/json',
            ],
            'application/ld+json content type' => [
                'contentType' => new InternetMediaType('application', 'ld+json'),
                'expectedContentTypeString' => 'application/ld+json',
            ],
        ];
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testCreateFromResponseWithInvalidContentType()
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('image/jpg');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/jpg"');

        new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));
    }

    /**
     * @dataProvider createFromResponseDataProvider
     *
     * @param string $responseContentTypeHeader
     * @param string $expectedContentTypeString
     *
     * @throws InvalidContentTypeException
     */
    public function testCreateFromResponse(string $responseContentTypeHeader, string $expectedContentTypeString)
    {
        $uri = \Mockery::mock(UriInterface::class);
        $content = 'web page content';

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($content);

        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn($responseContentTypeHeader);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_RESPONSE => $response,
        ]));

        $this->assertInstanceOf(JsonDocument::class, $jsonDocument);
        $this->assertEquals($uri, $jsonDocument->getUri());
        $this->assertEquals($content, $jsonDocument->getContent());
        $this->assertEquals($expectedContentTypeString, (string)$jsonDocument->getContentType());
        $this->assertEquals($response, $jsonDocument->getResponse());
    }

    public function createFromResponseDataProvider(): array
    {
        return [
            'application/json content type' => [
                'responseContentTypeHeader' => 'application/json',
                'expectedContentTypeString' => 'application/json',
            ],
            'text/javascript content type' => [
                'responseContentTypeHeader' => 'text/javascript',
                'expectedContentTypeString' => 'text/javascript',
            ],
            'text/json content type' => [
                'responseContentTypeHeader' => 'text/json',
                'expectedContentTypeString' => 'text/json',
            ],
            'application/ld+json content type' => [
                'responseContentTypeHeader' => 'application/ld+json',
                'expectedContentTypeString' => 'application/ld+json',
            ],
        ];
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentTypeInvalidContentType()
    {
        $uri = \Mockery::mock(UriInterface::class);

        $jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $uri,
            WebResourceProperties::ARG_CONTENT => 'content',
        ]));

        $this->assertEquals('application/json', (string)$jsonDocument->getContentType());

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "application/octetstream"');

        $jsonDocument->setContentType(new InternetMediaType('application', 'octetstream'));
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetResponseWithInvalidContentType()
    {
        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn('');

        $currentResponse = \Mockery::mock(ResponseInterface::class);
        $currentResponse
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('application/json');

        $currentResponse
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $newResponse = \Mockery::mock(ResponseInterface::class);
        $newResponse
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('image/jpg');

        $jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_RESPONSE => $currentResponse,
        ]));

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/jpg"');

        $jsonDocument->setResponse($newResponse);
    }

    /**
     * @dataProvider getDataDataProvider
     *
     * @param mixed $content
     * @param null|string|int|bool|array $expectedData
     *
     * @throws InvalidContentTypeException
     */
    public function testGetData($content, $expectedData)
    {
        /* @var JsonDocument $jsonDocument */
        $jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $content,
        ]));

        $this->assertEquals($expectedData, $jsonDocument->getData());
    }

    /**
     * @return array
     */
    public function getDataDataProvider()
    {
        return [
            'null data' => [
                'content' =>  json_encode(null),
                'expectedData' => null,
            ],
            'integer data, 0' => [
                'content' =>  json_encode(0),
                'expectedData' => 0,
            ],
            'integer data, 1' => [
                'content' =>  json_encode(1),
                'expectedData' => 1,
            ],
            'float' => [
                'content' =>  json_encode(pi()),
                'expectedData' => pi(),
            ],
            'bool, true' => [
                'content' =>  json_encode(true),
                'expectedData' => true,
            ],
            'bool, false' => [
                'content' =>  json_encode(false),
                'expectedData' => false,
            ],
            'string, empty' => [
                'content' =>  json_encode(''),
                'expectedData' => '',
            ],
            'string, non-empty' => [
                'content' =>  json_encode('foo'),
                'expectedData' => 'foo',
            ],
            'object, empty' => [
                'content' =>  json_encode((object)[]),
                'expectedData' => [],
            ],
            'object, non-empty' => [
                'content' =>  json_encode((object)[
                    'foo' => 'bar',
                ]),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
            'array, empty' => [
                'content' =>  json_encode([]),
                'expectedData' => [],
            ],
            'array, non-empty' => [
                'content' =>  json_encode([
                    'foo' => 'bar',
                ]),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
        ];
    }
}

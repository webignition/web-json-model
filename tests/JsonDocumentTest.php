<?php

namespace webignition\WebResource\JsonDocument\Tests;

use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\JsonDocument\JsonDocument;

class JsonDocumentTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateFromContentWithInvalidContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $contentType = new InternetMediaType();
        $contentType->setType('image');
        $contentType->setSubtype('png');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/png"');

        JsonDocument::createFromContent($uri, 'content', $contentType);
    }

    /**
     * @dataProvider createFromContentDataProvider
     *
     * @param InternetMediaTypeInterface|null $contentType
     * @param string $expectedContentTypeString
     */
    public function testCreateFromContent(?InternetMediaTypeInterface $contentType, string $expectedContentTypeString)
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $content = 'json document content';

        $jsonDocument = JsonDocument::createFromContent($uri, $content, $contentType);

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
                'contentType' => $this->createContentType('application', 'json'),
                'expectedContentTypeString' => 'application/json',
            ],
            'text/javascript content type' => [
                'contentType' => $this->createContentType('text', 'javascript'),
                'expectedContentTypeString' => 'text/javascript',
            ],
            'text/json content type' => [
                'contentType' => $this->createContentType('text', 'json'),
                'expectedContentTypeString' => 'text/json',
            ],
            'application/ld+json content type' => [
                'contentType' => $this->createContentType('application', 'ld+json'),
                'expectedContentTypeString' => 'application/ld+json',
            ],
        ];
    }

    public function testCreateFromResponseWithInvalidContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('image/jpg');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/jpg"');

        JsonDocument::createFromResponse($uri, $response);
    }

    /**
     * @dataProvider createFromResponseDataProvider
     *
     * @param string $responseContentTypeHeader
     * @param string $expectedContentTypeString
     */
    public function testCreateFromResponse(string $responseContentTypeHeader, string $expectedContentTypeString)
    {
        $content = 'web page content';

        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var StreamInterface|MockInterface $responseBody */
        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn($content);

        /* @var ResponseInterface|MockInterface $response */
        $response = \Mockery::mock(ResponseInterface::class);
        $response
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn($responseContentTypeHeader);

        $response
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        $jsonDocument = JsonDocument::createFromResponse($uri, $response);

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

    public function testSetUri()
    {
        /* @var UriInterface|MockInterface $currentUri */
        $currentUri = \Mockery::mock(UriInterface::class);

        /* @var UriInterface|MockInterface $newUri */
        $newUri = \Mockery::mock(UriInterface::class);

        $jsonDocument = JsonDocument::createFromContent($currentUri, '');

        $this->assertEquals($currentUri, $jsonDocument->getUri());

        $updatedWebPage = $jsonDocument->setUri($newUri);

        $this->assertInstanceOf(JsonDocument::class, $updatedWebPage);
        $this->assertEquals($newUri, $updatedWebPage->getUri());
        $this->assertNotEquals(spl_object_hash($jsonDocument), spl_object_hash($updatedWebPage));
    }

    public function testSetContentTypeInvalidContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $jsonDocument = JsonDocument::createFromContent($uri, 'web page content');

        $this->assertEquals('application/json', (string)$jsonDocument->getContentType());

        $contentType = $this->createContentType('application', 'octetstream');

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "application/octetstream"');

        $jsonDocument->setContentType($contentType);
    }

    public function testSetContentTypeValidContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $jsonDocument = JsonDocument::createFromContent($uri, 'web page content');

        $this->assertEquals('application/json', (string)$jsonDocument->getContentType());

        $contentType = $this->createContentType('application', 'ld+json');

        $updatedWebPage = $jsonDocument->setContentType($contentType);

        $this->assertEquals('application/ld+json', (string)$updatedWebPage->getContentType());
    }

    public function testSetContent()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $currentContent = 'current content';
        $newContent = 'new content';

        $jsonDocument = JsonDocument::createFromContent($uri, $currentContent);

        $this->assertEquals($currentContent, $jsonDocument->getContent());

        $updatedWebPage = $jsonDocument->setContent($newContent);

        $this->assertInstanceOf(JsonDocument::class, $updatedWebPage);
        $this->assertEquals($newContent, $updatedWebPage->getContent());
        $this->assertNotEquals(spl_object_hash($jsonDocument), spl_object_hash($updatedWebPage));
    }

    public function testSetResponseWithInvalidContentType()
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        $responseBody = \Mockery::mock(StreamInterface::class);
        $responseBody
            ->shouldReceive('__toString')
            ->andReturn('');

        /* @var ResponseInterface|MockInterface $currentResponse */
        $currentResponse = \Mockery::mock(ResponseInterface::class);
        $currentResponse
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('application/json');

        $currentResponse
            ->shouldReceive('getBody')
            ->andReturn($responseBody);

        /* @var ResponseInterface|MockInterface $newResponse */
        $newResponse = \Mockery::mock(ResponseInterface::class);
        $newResponse
            ->shouldReceive('getHeaderLine')
            ->with(JsonDocument::HEADER_CONTENT_TYPE)
            ->andReturn('image/jpg');

        $jsonDocument = JsonDocument::createFromResponse($uri, $currentResponse);

        $this->expectException(InvalidContentTypeException::class);
        $this->expectExceptionMessage('Invalid content type "image/jpg"');

        $jsonDocument->setResponse($newResponse);
    }

    /**
     * @dataProvider getDataDataProvider
     *
     * @param mixed $content
     * @param null|string|int|bool|array $expectedData
     */
    public function testGetData($content, $expectedData)
    {
        /* @var UriInterface|MockInterface $uri */
        $uri = \Mockery::mock(UriInterface::class);

        /* @var JsonDocument $jsonDocument */
        $jsonDocument = JsonDocument::createFromContent($uri, $content);

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

    private function createContentType(string $type, string $subtype): InternetMediaTypeInterface
    {
        $contentType = new InternetMediaType();
        $contentType->setType($type);
        $contentType->setSubtype($subtype);

        return $contentType;
    }
}

<?php

namespace webignition\Tests\WebResource;

use Psr\Http\Message\ResponseInterface;
use webignition\InternetMediaType\Parser\ParseException as InternetMediaTypeParseException;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\JsonDocument;
use webignition\WebResource\TestingTools\ResponseFactory;

class JsonDocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createInvalidContentTypeDataProvider
     *
     * @param ResponseInterface $response
     * @param string $expectedExceptionMessage
     * @param string $expectedExceptionContentType
     *
     * @throws InternetMediaTypeParseException
     */
    public function testCreateInvalidContentType(
        ResponseInterface $response,
        $expectedExceptionMessage,
        $expectedExceptionContentType
    ) {
        try {
            new JsonDocument($response);
            $this->fail(InvalidContentTypeException::class . ' not thrown');
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
                'response' => ResponseFactory::create('text/plain'),
                'expectedExceptionMessage' => 'Invalid content type "text/plain"',
                'expectedExceptionContentType' => 'text/plain',
            ],
            'text/html' => [
                'response' => ResponseFactory::create('text/html'),
                'expectedExceptionMessage' => 'Invalid content type "text/html"',
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
     * @throws InternetMediaTypeParseException
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
                'response' => ResponseFactory::create('application/json', json_encode(null)),
                'expectedData' => null,
            ],
            'application/json, integer data, 0' => [
                'response' => ResponseFactory::create('application/json', json_encode(0)),
                'expectedData' => 0,
            ],
            'application/json, integer data, 1' => [
                'response' => ResponseFactory::create('application/json', json_encode(1)),
                'expectedData' => 1,
            ],
            'application/json, float' => [
                'response' => ResponseFactory::create('application/json', json_encode(pi())),
                'expectedData' => pi(),
            ],
            'application/json, bool, true' => [
                'response' => ResponseFactory::create('application/json', json_encode(true)),
                'expectedData' => true,
            ],
            'application/json, bool, false' => [
                'response' => ResponseFactory::create('application/json', json_encode(false)),
                'expectedData' => false,
            ],
            'application/json, string, empty' => [
                'response' => ResponseFactory::create('application/json', json_encode('')),
                'expectedData' => '',
            ],
            'application/json, string, non-empty' => [
                'response' => ResponseFactory::create('application/json', json_encode('foo')),
                'expectedData' => 'foo',
            ],
            'application/json, object, empty' => [
                'response' => ResponseFactory::create('application/json', json_encode((object)[])),
                'expectedData' => [],
            ],
            'application/json, object, non-empty' => [
                'response' => ResponseFactory::create('application/json', json_encode((object)[
                    'foo' => 'bar',
                ])),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
            'application/json, array, empty' => [
                'response' => ResponseFactory::create('application/json', json_encode([])),
                'expectedData' => [],
            ],
            'application/json, array, non-empty' => [
                'response' => ResponseFactory::create('application/json', json_encode([
                    'foo' => 'bar',
                ])),
                'expectedData' => [
                    'foo' => 'bar',
                ],
            ],
            'application/ld+json, string, non-empty' => [
                'response' => ResponseFactory::create('application/ld+json', json_encode('foo')),
                'expectedData' => 'foo',
            ],
            'text/javascript, string, non-empty' => [
                'response' => ResponseFactory::create('text/javascript', json_encode('foo')),
                'expectedData' => 'foo',
            ],
        ];
    }

    public function testGetModelledContentTypeStrings()
    {
        $this->assertEquals(
            [
                JsonDocument::APPLICATION_JSON_CONTENT_TYPE,
                JsonDocument::TEXT_JAVASCRIPT_CONTENT_TYPE,
                JsonDocument::APPLICATION_LD_PLUS_JSON_CONTENT_TYPE,
            ],
            JsonDocument::getModelledContentTypeStrings()
        );
    }
}

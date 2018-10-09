<?php

namespace webignition\WebResource\JsonDocument\Tests;

use Psr\Http\Message\UriInterface;
use webignition\InternetMediaType\InternetMediaType;
use webignition\WebResource\Exception\InvalidContentTypeException;
use webignition\WebResource\Exception\ReadOnlyResponseException;
use webignition\WebResource\Exception\UnseekableResponseException;
use webignition\WebResource\JsonDocument\JsonDocument;
use webignition\WebResource\WebResourceProperties;

class JsonDocumentMutationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var JsonDocument
     */
    private $jsonDocument;

    /**
     * @var JsonDocument
     */
    private $updatedJsonDocument;

    protected function assertPostConditions()
    {
        parent::assertPostConditions();

        $this->assertInstanceOf(JsonDocument::class, $this->jsonDocument);
        $this->assertInstanceOf(JsonDocument::class, $this->updatedJsonDocument);
        $this->assertNotEquals(spl_object_hash($this->jsonDocument), spl_object_hash($this->updatedJsonDocument));
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetUri()
    {
        $currentUri = \Mockery::mock(UriInterface::class);
        $newUri = \Mockery::mock(UriInterface::class);

        $this->jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_URI => $currentUri,
        ]));

        $this->assertEquals($currentUri, $this->jsonDocument->getUri());

        $this->updatedJsonDocument = $this->jsonDocument->setUri($newUri);

        $this->assertEquals($currentUri, $this->jsonDocument->getUri());
        $this->assertEquals($newUri, $this->updatedJsonDocument->getUri());
    }

    /**
     * @throws InvalidContentTypeException
     */
    public function testSetContentTypeValidContentType()
    {
        $this->jsonDocument = new JsonDocument(WebResourceProperties::create([]));

        $this->assertEquals('application/json', (string)$this->jsonDocument->getContentType());

        $contentType = new InternetMediaType('application', 'ld+json');

        $this->updatedJsonDocument = $this->jsonDocument->setContentType($contentType);

        $this->assertEquals('application/json', (string)$this->jsonDocument->getContentType());
        $this->assertEquals('application/ld+json', (string)$this->updatedJsonDocument->getContentType());
    }

    /**
     * @throws InvalidContentTypeException
     * @throws ReadOnlyResponseException
     * @throws UnseekableResponseException
     */
    public function testSetContent()
    {
        $currentContent = 'current content';
        $newContent = 'new content';

        $this->jsonDocument = new JsonDocument(WebResourceProperties::create([
            WebResourceProperties::ARG_CONTENT => $currentContent,
        ]));

        $this->assertEquals($currentContent, $this->jsonDocument->getContent());

        $this->updatedJsonDocument = $this->jsonDocument->setContent($newContent);

        $this->assertEquals($currentContent, $this->jsonDocument->getContent());
        $this->assertEquals($newContent, $this->updatedJsonDocument->getContent());
    }
}

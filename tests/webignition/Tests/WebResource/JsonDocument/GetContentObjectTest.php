<?php

namespace webignition\Tests\WebResource\JsonDocument;

class GetContentObjectTest extends BaseTest {

    public function testGetAsStdClass() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:application/json\n\n{}");
        $this->jsonDocument->setHttpResponse($response);

        $this->assertInstanceOf('\stdClass', $this->jsonDocument->getContentObject());
    }

    public function testGetAsArray() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:application/json\n\n{}");
        $this->jsonDocument->setHttpResponse($response);

        $object = $this->jsonDocument->getContentArray();

        $this->assertTrue(is_array($object));
        $this->assertEquals([], $object);
    }
}
<?php

namespace webignition\Tests\WebResource\JsonDocument;

class GetContentObjectTest extends BaseTest {

    public function testGetContentObject() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:application/json\n\n{}");        
        $this->jsonDocument->setHttpResponse($response);
        
        $this->assertInstanceOf('\stdClass', $this->jsonDocument->getContentObject());
    }
}
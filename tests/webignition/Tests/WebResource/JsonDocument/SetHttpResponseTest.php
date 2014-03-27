<?php

namespace webignition\Tests\WebResource\JsonDocument;

class SetHttpResponseTest extends BaseTest {

    public function testSetResponseWithValidContentType() {
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:application/json\n\n{}");        
        $this->jsonDocument->setHttpResponse($response);
    }
    
    public function testSetResponseWithInvalidContentType() {
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid content type', 2);
        
        $response = \Guzzle\Http\Message\Response::fromMessage("HTTP/1.0 200 OK\nContent-Type:application/xml\n\{}");        
        $this->jsonDocument->setHttpResponse($response);
    }    
}
<?php

namespace webignition\Tests\WebResource\JsonDocument;

class SetHttpResponseTest extends BaseTest {

    public function testSetResponseWithValidContentType() {
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-Type:application/json\n\n{}");
        $this->jsonDocument->setHttpResponse($response);
    }
    
    public function testSetResponseWithInvalidContentType() {
        $this->setExpectedException('webignition\WebResource\Exception', 'HTTP response contains invalid content type', 2);
        
        $response = $this->getHttpResponseFromMessage("HTTP/1.0 200 OK\nContent-Type:application/xml\n\{}");
        $this->jsonDocument->setHttpResponse($response);
    }    
}
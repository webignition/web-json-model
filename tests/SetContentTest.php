<?php

use webignition\WebResource\JsonDocument\JsonDocument;

class SetContentTypeTest extends BaseTest {

    public function testSetValidContentType() {
        $contentTypeStrings = array(
            'application/json'
        );
        
        $jsonDocument = new JsonDocument();
        
        foreach ($contentTypeStrings as $contentTypeString) {
            $jsonDocument->setContentType($contentTypeString);
            $this->assertEquals($contentTypeString, (string)$jsonDocument->getContentType());
        }
    }   
    
    public function testSetInvalidContentType() {
        $contentTypeStrings = array(
            'image/png',
            'text/css',
            'text/javascript'
        );
        
        $jsonDocument = new JsonDocument();
        
        foreach ($contentTypeStrings as $contentTypeString) {
            try {
                $jsonDocument->setContentType($contentTypeString);
                $this->fail('Invalid content type exception not thrown for "'.$contentTypeString.'"');
            } catch (\webignition\WebResource\Exception $exception) {
                $this->assertEquals(1, $exception->getCode());
            }
        }
    } 
    
}
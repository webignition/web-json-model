<?php

use webignition\WebResource\JsonDocument\JsonDocument;

class SetContentTest extends BaseTest {

    public function testSetContent() {
        $jsonDocument = new JsonDocument();
        $jsonDocument->setContentType('application/json');
        $jsonDocument->setContent($this->getFixtureContent(__FUNCTION__, 'content.json'));        
        $contentObject = $jsonDocument->getContentObject();
        
        $this->assertEquals($this->getFixtureContent(__FUNCTION__, 'content.json'), $jsonDocument->getContent());
        $this->assertInstanceOf('\stdClass', $contentObject);
        $this->assertEquals('example glossary', $contentObject->glossary->title);
    }    
}
<?php

namespace webignition\Tests\WebResource\JsonDocument;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {
    
const FIXTURES_DATA_RELATIVE_PATH = '/Fixtures';      

    /**
     *
     * @var \webignition\WebResource\JsonDocument\JsonDocument 
     */
    protected $jsonDocument;

    public function setUp() {
        parent::setUp();
        $this->jsonDocument = new \webignition\WebResource\JsonDocument\JsonDocument();
    }
   
}
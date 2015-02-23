<?php

namespace webignition\Tests\WebResource\JsonDocument;

use GuzzleHttp\Message\MessageFactory as HttpMessageFactory;
use GuzzleHttp\Message\ResponseInterface as HttpResponse;
use webignition\WebResource\JsonDocument\JsonDocument;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {
    
const FIXTURES_DATA_RELATIVE_PATH = '/Fixtures';      

    /**
     *
     * @var JsonDocument
     */
    protected $jsonDocument;

    public function setUp() {
        parent::setUp();
        $this->jsonDocument = new JsonDocument();
    }


    /**
     * @param $message
     * @return HttpResponse
     */
    protected function getHttpResponseFromMessage($message) {
        $factory = new HttpMessageFactory();
        return $factory->fromMessage($message);
    }
   
}
<?php
namespace webignition\WebResource\JsonDocument;

use webignition\WebResource\WebResource;
use webignition\InternetMediaType\Parser\Parser as InternetMediaTypeParser;

/**
 *
 */
class JsonDocument extends WebResource
{
    public function __construct()
    {
        $validContentTypes = array(
            'application/json'
        );

        foreach ($validContentTypes as $validContentTypeString) {
            $mediaTypeParser = new InternetMediaTypeParser();
            $this->addValidContentType($mediaTypeParser->parse($validContentTypeString));
        }
    }

    /**
     * @return \stdClass
     */
    public function getContentObject()
    {
        return $this->getDecodedContent();
    }

    /**
     * @return array
     */
    public function getContentArray()
    {
        return $this->getDecodedContent(true);
    }

    /**
     * @param bool $asArray
     *
     * @return array|\stdClass
     */
    private function getDecodedContent($asArray = false)
    {
        return json_decode($this->getContent(), $asArray);
    }
}

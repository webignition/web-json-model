<?php

namespace webignition\WebResource\JsonDocument;

use Psr\Http\Message\ResponseInterface;
use webignition\WebResource\WebResource;

class JsonDocument extends WebResource
{
    const APPLICATION_JSON_CONTENT_TYPE = 'application/json';
    const TEXT_JAVASCRIPT_CONTENT_TYPE = 'text/javascript';
    const APPLICATION_JSON_SUB_CONTENT_TYPE_PATTERN = '/application\/[a-z]+\+json/';
    const TEXT_JSON_CONTENT_TYPE = 'text/json';

    /**
     * @param ResponseInterface $response
     * @param string $url
     *
     * @throws InvalidContentTypeException
     */
    public function __construct(ResponseInterface $response, $url)
    {
        parent::__construct($response, $url);

        $contentType = $this->getContentType();
        $contentTypeSubtypeString = $contentType->getTypeSubtypeString();

        $hasApplicationJsonContentType = self::APPLICATION_JSON_CONTENT_TYPE === $contentTypeSubtypeString;
        $hasTextJavascriptContentType = self::TEXT_JAVASCRIPT_CONTENT_TYPE === $contentTypeSubtypeString;

        if (!$hasApplicationJsonContentType && !$hasTextJavascriptContentType) {
            if (0 === preg_match(self::APPLICATION_JSON_SUB_CONTENT_TYPE_PATTERN, $contentTypeSubtypeString)) {
                throw new InvalidContentTypeException($contentType);
            }
        }
    }

    /**
     * @return null|bool|string|int|array
     */
    public function getData()
    {
        return json_decode($this->getContent(), true);
    }
}

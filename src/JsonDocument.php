<?php

namespace webignition\WebResource;

class JsonDocument extends SpecificContentTypeWebResource
{
    const APPLICATION_JSON_CONTENT_TYPE = 'application/json';
    const APPLICATION_LD_PLUS_JSON_CONTENT_TYPE = 'application/ld+json';
    const TEXT_JAVASCRIPT_CONTENT_TYPE = 'text/javascript';
    const APPLICATION_JSON_SUB_CONTENT_TYPE_PATTERN = '/application\/[a-z]+\+json/';
    const TEXT_JSON_CONTENT_TYPE = 'text/json';

    /**
     * @return null|bool|string|int|array
     */
    public function getData()
    {
        return json_decode($this->getContent(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowedContentTypeStrings()
    {
        return self::getModelledContentTypeStrings();
    }

    /**
     * {@inheritdoc}
     */
    protected static function getAllowedContentTypePatterns()
    {
        return [
            self::APPLICATION_JSON_SUB_CONTENT_TYPE_PATTERN,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getModelledContentTypeStrings()
    {
        return [
            self::APPLICATION_JSON_CONTENT_TYPE,
            self::TEXT_JAVASCRIPT_CONTENT_TYPE,
            self::APPLICATION_LD_PLUS_JSON_CONTENT_TYPE,
        ];
    }
}

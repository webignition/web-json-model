<?php

namespace webignition\WebResource\JsonDocument;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\WebResourceInterfaces\JsonDocumentInterface;
use webignition\WebResource\WebResource;

class JsonDocument extends WebResource implements JsonDocumentInterface
{
    const DEFAULT_CONTENT_TYPE_TYPE = 'application';
    const DEFAULT_CONTENT_TYPE_SUBTYPE = 'json';

    /**
     * @return null|bool|string|int|array
     */
    public function getData()
    {
        return json_decode($this->getContent(), true);
    }

    public static function getDefaultContentType(): InternetMediaType
    {
        $contentType = new InternetMediaType();
        $contentType->setType(self::DEFAULT_CONTENT_TYPE_TYPE);
        $contentType->setSubtype(self::DEFAULT_CONTENT_TYPE_SUBTYPE);

        return $contentType;
    }

    public static function models(InternetMediaTypeInterface $internetMediaType): bool
    {
        $contentTypeSubtype = $internetMediaType->getTypeSubtypeString();

        if (in_array($contentTypeSubtype, self::getModelledContentTypeStrings())) {
            return true;
        }

        if (preg_match(ContentTypes::CONTENT_TYPE_APPLICATION_JSON_SUB_TYPE, $contentTypeSubtype)) {
            return true;
        }

        return false;
    }

    public static function getModelledContentTypeStrings(): array
    {
        return [
            ContentTypes::CONTENT_TYPE_APPLICATION_JSON,
            ContentTypes::CONTENT_TYPE_TEXT_JAVASCRIPT,
            ContentTypes::CONTENT_TYPE_TEXT_JSON,
        ];
    }
}

<?php

namespace TickTackk\Seeder\Job\Exception;

use Throwable;

class InvalidContentTypePluralProvidedException extends \InvalidArgumentException
{
    protected $contentTypePlural;

    public function __construct(?string $contentTypePlural, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid content type plural provided.', $code, $previous);

        $this->contentTypePlural = $contentTypePlural;
    }

    public function getContentTypePlural() :? string
    {
        return $this->contentTypePlural;
    }
}
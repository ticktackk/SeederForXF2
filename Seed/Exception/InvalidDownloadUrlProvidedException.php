<?php

namespace TickTackk\Seeder\Seed\Exception;

use Throwable;

class InvalidDownloadUrlProvidedException extends \InvalidArgumentException
{
    protected $downloadUrl;

    public function __construct(string $downloadUrl, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid download URL provided.', $code, $previous);

        $this->downloadUrl = $downloadUrl;
    }

    public function getDownloadUrl() : string
    {
        return $this->downloadUrl;
    }
}
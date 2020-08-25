<?php

namespace TickTackk\Seeder\Seed\Exception;

use Throwable;

class DownloadUrlDidNotReturnOkResponseException extends \RuntimeException
{
    protected $downloadUrl;

    protected $responseCode;

    public function __construct(string $downloadUrl, int $responseCode = null, $code = 0, Throwable $previous = null)
    {
        parent::__construct('Download url did not return OK response.', $code, $previous);

        $this->downloadUrl = $downloadUrl;
        $this->responseCode = $responseCode;
    }

    public function getDownloadUrl() : string
    {
        return $this->downloadUrl;
    }

    public function getResponseCode() :? int
    {
        return $this->responseCode;
    }
}
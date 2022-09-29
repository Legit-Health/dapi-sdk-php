<?php

namespace LegitHealth\Dapi;

use Exception;

class MediaAnalyzerException extends Exception
{
    public function __construct(
        string $message,
        private ?int $statusCode = null
    ) {
        parent::__construct($message);
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}

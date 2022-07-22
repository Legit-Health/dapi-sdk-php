<?php

namespace LegitHealth\Dapi;

use Throwable;

class MediaAnalyzerException extends \Exception
{
    public const AI_CLIENT = 1;
    public const UPLOADING_IMAGE = 2;
    public const SAVING = 3;
    public const INVALID_IMAGE = 4;
    public const CANNOT_CALCULATE_MALIGNANCY = 5;

    private ?Throwable $originalException;

    /** @var mixed $data */
    private mixed $data;

    public function __construct(
        string $message,
        int $code,
        Throwable $originalException = null,
        mixed $data = null
    ) {
        parent::__construct($message, $code);
        $this->originalException = $originalException;
        $this->data = $data;
    }

    public function getOriginalException(): ?Throwable
    {
        return $this->originalException;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}

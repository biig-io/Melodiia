<?php

namespace Biig\Melodiia\Response;

abstract class AbstractApiResponse implements ApiResponse
{
    /** @var string */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}

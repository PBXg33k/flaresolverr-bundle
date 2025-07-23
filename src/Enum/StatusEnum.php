<?php

namespace Pbxg33k\FlareSolverrBundle\Enum;

enum StatusEnum: string
{
    case OK = 'ok';
    case ERROR = 'error';

    public function isOk(): bool
    {
        return $this === self::OK;
    }

    public function isError(): bool
    {
        return $this === self::ERROR;
    }
}

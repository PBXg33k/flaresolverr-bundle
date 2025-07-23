<?php

namespace Pbxg33k\FlareSolverrBundle\Response;

class IndexResponse
{
    public function __construct(
        protected(set) string $msg,
        protected(set) string $version,
        protected(set) string $userAgent,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            msg: $data['msg'] ?? '',
            version: $data['version'] ?? '',
            userAgent: $data['userAgent'] ?? '',
        );
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

<?php

namespace Pbxg33k\FlareSolverrBundle\Response;

class ChallengeResolutionResult
{
    public function __construct(
        public ?string $url = null,
        public ?string $status = null,
        public ?array $headers = null,
        public ?string $response = null,
        public ?array $cookies = null,
        public ?string $userAgent = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            status: $data['status'] ?? null,
            headers: $data['headers'] ?? null,
            response: $data['response'] ?? null,
            cookies: $data['cookies'] ?? null,
            userAgent: $data['userAgent'] ?? null,
        );
    }
}

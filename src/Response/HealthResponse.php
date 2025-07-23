<?php

namespace Pbxg33k\FlareSolverrBundle\Response;

use Pbxg33k\FlareSolverrBundle\Enum\StatusEnum;

class HealthResponse
{
    public function __construct(
        protected(set) StatusEnum $status {
            get {
                return $this->status;
            }
        },
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            status: StatusEnum::from($data['status'] ?? 'error'),
        );
    }

}

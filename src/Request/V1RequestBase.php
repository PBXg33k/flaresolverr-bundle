<?php

namespace Pbxg33k\FlareSolverrBundle\Request;

use Pbxg33k\FlareSolverrBundle\Enum\CommandEnum;

class V1RequestBase
{
    public function __construct(
        public ?string $url,
        public ?array $postData = null,
        public ?string $userAgent = null,
        public ?array $headers = null,
        public ?array $cookies = null,
        public bool $returnOnlyCookies = false,
        public int $maxTimeout = 60000, // Default to 60 seconds
        public ?array $proxy = null,
        public ?string $session = null,
        public int $sessionTtlTimeout = 0, // Default to no session timeout
    )
    {
    }

    public function toCurlJsonOptionArray(CommandEnum $cmd): array
    {
        $optionArray = [
            'cmd' => $cmd->value
        ];

        $props = get_object_vars($this);

        // Remove properties that are not set or null
        foreach ($props as $key => $value) {
            if ($value === null || (is_array($value) && empty($value))) {
                unset($props[$key]);
            }
        }

        // Merge the properties into the option array
        // Return the complete array
        return array_merge($optionArray, $props);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}

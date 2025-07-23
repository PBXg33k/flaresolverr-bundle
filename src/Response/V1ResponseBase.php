<?php

namespace Pbxg33k\FlareSolverrBundle\Response;

use Dom\HTMLDocument;
use Pbxg33k\FlareSolverrBundle\Enum\StatusEnum;

class V1ResponseBase
{
    private(set) ?HTMLDocument $HTMLDocument = null;

    public function __construct(
        public StatusEnum $status,
        public ?string $message = null,
        public ?string $session = null,
        public ?array $sessions = null,
        public ?int $startTimestamp = null,
        public ?int $endTimestamp = null,
        public ?string $version = null,
        public ?ChallengeResolutionResult $solution = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $instance = new self(
            status: $data['status'] ? StatusEnum::from($data['status']) : StatusEnum::ERROR,
            message: $data['message'] ?? null,
            session: $data['session'] ?? null,
            sessions: $data['sessions'] ?? null,
            startTimestamp: $data['startTimestamp'] ?? null,
            endTimestamp: $data['endTimestamp'] ?? null,
            version: $data['version'] ?? null,
        );

        if(isset($data['solution']) && is_array($data['solution'])) {
            $instance->solution = ChallengeResolutionResult::fromArray($data['solution']);
        } else {
            $instance->solution = null;
        }

        // If the timestamps are present, convert them to integers
        if (isset($data['startTimestamp']) && is_string($data['startTimestamp'])) {
            $instance->startTimestamp = (int)$data['startTimestamp'];
        }
        if (isset($data['endTimestamp']) && is_string($data['endTimestamp'])) {
            $instance->endTimestamp = (int)$data['endTimestamp'];
        }
        return $instance;
    }

    public function getTimeDiff(): ?int
    {
        return $this->endTimestamp && $this->startTimestamp
            ? $this->endTimestamp - $this->startTimestamp
            : null;
    }

    public function getResponseContent(): ?string
    {
        if ($this->solution && $this->solution->response) {
            return $this->solution->response;
        }

        return null;
    }

    /**
     * Prepares the response for caching by resetting the HTMLDocument.
     * This is useful to avoid caching the HTMLDocument itself, which cannot be serialized.
     */
    public function prepareForCache(): void
    {
        $this->HTMLDocument = null; // Reset HTMLDocument to avoid caching it
    }

    public function getResponseContentAsHTMLDocument(): ?HTMLDocument
    {
        $htmlContent = $this->getResponseContent();
        if ($htmlContent) {
            if (!$this->HTMLDocument) {
                $this->HTMLDocument = HTMLDocument::createFromString(
                    $htmlContent,
                    LIBXML_NOERROR
                );
            }

            return $this->HTMLDocument;
        }

        return null;
    }
}

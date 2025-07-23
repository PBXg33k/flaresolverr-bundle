<?php

namespace Pbxg33k\FlareSolverrBundle\Client;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Pbxg33k\FlareSolverrBundle\{Enum\CommandEnum,
    Enum\StatusEnum,
    Request\V1RequestBase,
    Response\HealthResponse,
    Response\IndexResponse,
    Response\V1ResponseBase};

class FlareSolverrClient
{
    public function __construct(
        protected HttpClientInterface      $httpClient,
        protected LoggerInterface          $logger,
        protected EventDispatcherInterface $dispatcher,
        protected string                   $flareSolverrRootUrl,
    )
    {
    }

    public function index(): IndexResponse
    {
        $response = $this->httpClient->request('GET', $this->flareSolverrRootUrl . '/');

        return IndexResponse::fromArray($response->toArray());
    }

    public function checkHealth(): bool
    {
        try {
            $response = $this->httpClient->request('GET', $this->flareSolverrRootUrl . '/health');
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $statusResponse = HealthResponse::fromArray($response->toArray());
                // Parse the response to check the health status
                // It MUST be a JSON response with a 'status' field with value 'ok
                return $statusResponse->getStatus() === StatusEnum::OK;
            } else {
                $this->logger->warning('FlareSolverr health check failed with status code: ' . $statusCode);
                return false;
            }
        } catch (\Exception $e) {
            $this->logger->error('FlareSolverr health check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function requestGet(string $url, ?string $sessionID = null): V1ResponseBase
    {
        $request = new V1RequestBase(
            url: $url,
            session: $sessionID,
        );

        return $this->sendV1Request(CommandEnum::REQUEST_GET, $request);
    }

    public function sessionCreate(?string $sessionID = null)
    {
        $request = new V1RequestBase(
            url: null,
            session: $sessionID,
        );

        return $this->sendV1Request(CommandEnum::SESSION_CREATE, $request);
    }

    public function sessionList()
    {
        $request = new V1RequestBase(url: null);

        return $this->sendV1Request(CommandEnum::SESSION_LIST, $request);
    }

    public function sessionDestroy(string $sessionID)
    {
        $request = new V1RequestBase(
            url: null,
            session: $sessionID,
        );

        return $this->sendV1Request(CommandEnum::SESSION_DESTROY, $request);
    }

    protected function sendV1Request(CommandEnum $command, V1RequestBase $request): V1ResponseBase
    {
        $options = [
            'json' => $request->toCurlJsonOptionArray($command),
        ];

        $this->logger->debug('Sending FlareSolverr request', [
            'command' => $command->value,
            'request' => $request->toArray(),
            'options' => $options,
        ]);

        try {
            $response = $this->httpClient->request('POST', $this->flareSolverrRootUrl . '/v1', $options);

            // Check for HTTP errors
            if ($response->getStatusCode() !== 200) {
                throw new \RuntimeException(
                    'FlareSolverr request failed with status code: ' . $response->getStatusCode()
                );
            }

            // Log the response for debugging
            $this->logger->debug('FlareSolverr response received', [
                'command' => $command->value,
                'response' => $response->toArray(),
            ]);

            return V1ResponseBase::fromArray($response->toArray());
            //return $response->toArray();
        } catch (\Exception $e) {
            throw new \RuntimeException('FlareSolverr request failed:' . $e->getMessage(), 0, $e);
        }
    }
}

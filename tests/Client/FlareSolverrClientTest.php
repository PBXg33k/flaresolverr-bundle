<?php

namespace Pbxg33k\Test\FlareSolverrBundle\Client;

use Pbxg33k\FlareSolverrBundle\Client\FlareSolverrClient;
use Pbxg33k\FlareSolverrBundle\Enum\StatusEnum;
use Pbxg33k\FlareSolverrBundle\Response\IndexResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FlareSolverrClientTest extends TestCase
{
    private MockObject|HttpClientInterface $httpClient;
    private MockObject|LoggerInterface $logger;
    private MockObject|EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
    }

    public function testIndex()
    {
        $flareSolverrUrl = 'http://example.com/';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $flareSolverrUrl)
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"msg":"FlareSolverr is ready!","version":"1.0.0","userAgent":"FlareSolverr"}',
                    arrayContent: [
                        'msg' => 'FlareSolverr is ready!',
                        'version' => '1.0.0',
                        'userAgent' => 'FlareSolverr',
                    ],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $response = $client->index();
        $this->assertInstanceOf(IndexResponse::class, $response);
        $this->assertEquals('FlareSolverr is ready!', $response->msg);
        $this->assertEquals('1.0.0', $response->version);
        $this->assertEquals('FlareSolverr', $response->userAgent);
    }

    public function testCheckHealth()
    {
        $flareSolverrUrl = 'http://example.com/health';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $flareSolverrUrl)
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"ok"}',
                    arrayContent: ['status' => 'ok'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $result = $client->checkHealth();
        $this->assertTrue($result);
    }

    public function testCheckHealthInvalidResponse()
    {
        $flareSolverrUrl = 'http://example.com/health';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $flareSolverrUrl)
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"error"}',
                    arrayContent: ['status' => 'error'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $result = $client->checkHealth();
        $this->assertFalse($result);
    }

    public function testCheckHealthNon200Response()
    {
        $flareSolverrUrl = 'http://example.com/health';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $flareSolverrUrl)
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 500,
                    content: '{"status":"error"}',
                    arrayContent: ['status' => 'error'],
                )
            );

        $this->logger->expects($this->once())
            ->method('warning')
            ->with('FlareSolverr health check failed with status code: 500');

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $result = $client->checkHealth();
        $this->assertFalse($result);
    }

    public function testCheckHealthException()
    {
        $flareSolverrUrl = 'http://example.com/health';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('GET', $flareSolverrUrl)
            ->willThrowException(new \Exception('Network error'));

        $this->logger->expects($this->once())
            ->method('error')
            ->with('FlareSolverr health check failed: Network error');

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $result = $client->checkHealth();
        $this->assertFalse($result);
    }

    public function testRequestGet()
    {
        $url = 'http://example.com';
        $sessionID = 'test-session';

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com/v1', [
                'json' => [
                    'cmd' => 'request.get',
                    'url' => $url,
                    'session' => $sessionID,
                    'returnOnlyCookies' => false, // Default to false
                    'maxTimeout' => 60000, // Default timeout
                    'sessionTtlTimeout' => 0, // Default session TTL
                ],
            ])
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"ok"}',
                    arrayContent: ['status' => 'ok'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $response = $client->requestGet($url, $sessionID);
        $this->assertEquals(StatusEnum::OK, $response->status);
    }

    public function testSessionCreate()
    {
        $sessionID = 'new-session';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com/v1', [
                'json' => [
                    'cmd' => 'sessions.create',
                    'session' => $sessionID,
                    'returnOnlyCookies' => false, // Default to false
                    'maxTimeout' => 60000, // Default timeout
                    'sessionTtlTimeout' => 0, // Default session TTL
                ],
            ])
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"ok"}',
                    arrayContent: ['status' => 'ok'],
                )
            );
        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );
        $response = $client->sessionCreate($sessionID);
        $this->assertEquals(StatusEnum::OK, $response->status);
    }

    public function testSessionList()
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com/v1', [
                'json' => [
                    'cmd' => 'sessions.list',
                    'returnOnlyCookies' => false, // Default to false
                    'maxTimeout' => 60000, // Default timeout
                    'sessionTtlTimeout' => 0, // Default session TTL
                ],
            ])
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"ok"}',
                    arrayContent: ['status' => 'ok'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $response = $client->sessionList();
        $this->assertEquals(StatusEnum::OK, $response->status);
    }

    public function testSessionDestroy()
    {
        $sessionID = 'test-session';
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with('POST', 'http://example.com/v1', [
                'json' => [
                    'cmd' => 'sessions.destroy',
                    'session' => $sessionID,
                    'returnOnlyCookies' => false, // Default to false
                    'maxTimeout' => 60000, // Default timeout
                    'sessionTtlTimeout' => 0, // Default session TTL
                ],
            ])
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 200,
                    content: '{"status":"ok"}',
                    arrayContent: ['status' => 'ok'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $response = $client->sessionDestroy($sessionID);
        $this->assertEquals(StatusEnum::OK, $response->status);
    }

    public function testSendV1RequestThrowsRuntimeExceptionOnNon200Response()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('FlareSolverr request failed with status code: 500');

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willReturn(
                $this->createMockResponse(
                    statusCode: 500,
                    content: '{"status":"error"}',
                    arrayContent: ['status' => 'error'],
                )
            );

        $client = new FlareSolverrClient(
            httpClient: $this->httpClient,
            logger: $this->logger,
            dispatcher: $this->eventDispatcher,
            flareSolverrRootUrl: 'http://example.com',
        );

        $client->requestGet('http://example.com');
    }

    private function createMockResponse(
        int $statusCode = 200,
        ?string $content = null,
        ?array $headers = null,
        array $arrayContent = []
    ): MockObject
    {
        $response = $this->createMock(\Symfony\Contracts\HttpClient\ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);

        $response->method('getContent')->willReturn($content);
        $response->method('toArray')->willReturn($arrayContent);

        $response->method('getHeaders')->willReturn($headers ?? []);

        return $response;
    }
}

<?php


namespace Pbxg33k\Test\FlareSolverrBundle\Response;

use Pbxg33k\FlareSolverrBundle\Enum\StatusEnum;
use PHPUnit\Framework\TestCase;
use Pbxg33k\FlareSolverrBundle\Response\V1ResponseBase;

class V1ResponseBaseTest extends TestCase
{
    public function testFromArray()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'message' => 'Success',
            'session' => 'abc123',
            'sessions' => ['session1', 'session2'],
            'startTimestamp' => 1633072800,
            'endTimestamp' => 1633076400,
            'version' => '1.0.0',
            'solution' => [
                'response' => 'Response content',
                'cookies' => ['cookie1=value1', 'cookie2=value2'],
                // Add other fields as necessary
            ],
        ];

        $response = V1ResponseBase::fromArray($data);

        $this->assertInstanceOf(V1ResponseBase::class, $response);
        $this->assertEquals(StatusEnum::OK, $response->status);
        $this->assertEquals('Success', $response->message);
        $this->assertEquals('abc123', $response->session);
        $this->assertEquals(['session1', 'session2'], $response->sessions);
        $this->assertEquals(1633072800, $response->startTimestamp);
        $this->assertEquals(1633076400, $response->endTimestamp);
        $this->assertEquals('1.0.0', $response->version);
        $this->assertNotNull($response->solution);
        $this->assertEquals('Response content', $response->solution->response);
    }

    public function testFromArrayWithMissingStatusGivesStatusEnumErrorOnStatus()
    {
        $data = [
            'message' => 'Missing status',
            'session' => 'abc123',
            'startTimestamp' => 1633072800,
            'endTimestamp' => 1633076400,
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertInstanceOf(V1ResponseBase::class, $response);
        $this->assertEquals(StatusEnum::ERROR, $response->status);
        $this->assertEquals('Missing status', $response->message);
        $this->assertEquals('abc123', $response->session);
        $this->assertNull($response->solution);
    }

    public function testGetTimeDiff()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'startTimestamp' => 1633072800,
            'endTimestamp' => 1633076400,
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertEquals(3600, $response->getTimeDiff());

        // Test with missing timestamps
        $response = new V1ResponseBase(status: StatusEnum::OK);
        $this->assertNull($response->getTimeDiff());
    }

    public function testTimeStampsAreIntegers()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'startTimestamp' => '1633072800',
            'endTimestamp' => '1633076400',
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertIsInt($response->startTimestamp);
        $this->assertIsInt($response->endTimestamp);
    }

    public function testGetResponseContent()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'solution' => [
                'response' => 'Response content',
            ],
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertEquals('Response content', $response->getResponseContent());

        // Test with no solution
        $response = new V1ResponseBase(status: StatusEnum::OK);
        $this->assertNull($response->getResponseContent());
    }
    
    public function testGetResponseContentAsHTMLDocument()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'solution' => [
                'response' => '<html lang="en"><body>Test</body></html>',
            ],
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertInstanceOf(\Dom\HTMLDocument::class, $response->getResponseContentAsHTMLDocument());
        $this->assertEquals('Test', $response->getResponseContentAsHTMLDocument()->body->textContent);
    }

    public function testHtmlDocumentIsNullWhenPrepareForCacheIsCalled()
    {
        $data = [
            'status' => StatusEnum::OK->value,
            'solution' => [
                'response' => '<html lang="en"><body>Test</body></html>',
            ],
        ];

        $response = V1ResponseBase::fromArray($data);
        $this->assertInstanceOf(\Dom\HTMLDocument::class, $response->getResponseContentAsHTMLDocument());
        $this->assertInstanceOf(\Dom\HTMLDocument::class, $response->HTMLDocument);
        $response->prepareForCache();

        $this->assertNull($response->HTMLDocument);
    }

    public function testGetResponseContentAsHTMLDocumentReturnsNullWhenNoResponse()
    {
        $response = new V1ResponseBase(status: StatusEnum::OK);
        $this->assertNull($response->getResponseContentAsHTMLDocument());
    }
}

<?php

namespace Pbxg33k\Test\FlareSolverrBundle\Response;

use PHPUnit\Framework\TestCase;

class IndexResponseTest extends TestCase
{
    public function testFromArray()
    {
        $data = [
            'msg' => 'Test message',
            'version' => '1.0.0',
            'userAgent' => 'TestUserAgent/1.0',
        ];

        $response = \Pbxg33k\FlareSolverrBundle\Response\IndexResponse::fromArray($data);

        $this->assertInstanceOf(\Pbxg33k\FlareSolverrBundle\Response\IndexResponse::class, $response);
        $this->assertEquals('Test message', $response->msg);
        $this->assertEquals('1.0.0', $response->version);
        $this->assertEquals('TestUserAgent/1.0', $response->userAgent);
    }

    public function testToArray()
    {
        $response = new \Pbxg33k\FlareSolverrBundle\Response\IndexResponse(
            msg: 'Test message',
            version: '1.0.0',
            userAgent: 'TestUserAgent/1.0'
        );

        $array = $response->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Test message', $array['msg']);
        $this->assertEquals('1.0.0', $array['version']);
        $this->assertEquals('TestUserAgent/1.0', $array['userAgent']);
    }
}

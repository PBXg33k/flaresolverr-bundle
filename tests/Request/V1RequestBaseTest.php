<?php

namespace Pbxg33k\Test\FlareSolverrBundle\Request;

use Pbxg33k\FlareSolverrBundle\Enum\CommandEnum;
use Pbxg33k\FlareSolverrBundle\Request\V1RequestBase;
use PHPUnit\Framework\TestCase;

class V1RequestBaseTest extends TestCase
{

    public function test__construct()
    {
        $request = $this->createV1RequestBase();

        $this->assertInstanceOf(V1RequestBase::class, $request);
        $this->assertEquals('http://example.com', $request->url);
        $this->assertEquals(['key' => 'value'], $request->postData);
        $this->assertEquals('TestUserAgent', $request->userAgent);
        $this->assertEquals(['Content-Type' => 'application/json'], $request->headers);
        $this->assertEquals(['session' => 'abc123'], $request->cookies);
        $this->assertTrue($request->returnOnlyCookies);
        $this->assertEquals(30000, $request->maxTimeout);
        $this->assertEquals(['http' => 'http://proxy.example.com'], $request->proxy);
        $this->assertEquals('testSession', $request->session);
        $this->assertEquals(120, $request->sessionTtlTimeout);
    }

    public function testToArray()
    {
        $request = $this->createV1RequestBase();
        $array = $request->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('url', $array);
        $this->assertArrayHasKey('postData', $array);
        $this->assertArrayHasKey('userAgent', $array);
        $this->assertArrayHasKey('headers', $array);
        $this->assertArrayHasKey('cookies', $array);
        $this->assertArrayHasKey('returnOnlyCookies', $array);
        $this->assertArrayHasKey('maxTimeout', $array);
        $this->assertArrayHasKey('proxy', $array);
        $this->assertArrayHasKey('session', $array);
        $this->assertArrayHasKey('sessionTtlTimeout', $array);

        $this->assertEquals('http://example.com', $array['url']);
        $this->assertEquals(['key' => 'value'], $array['postData']);
        $this->assertEquals('TestUserAgent', $array['userAgent']);
        $this->assertEquals(['Content-Type' => 'application/json'], $array['headers']);
        $this->assertEquals(['session' => 'abc123'], $array['cookies']);
        $this->assertTrue($array['returnOnlyCookies']);
        $this->assertEquals(30000, $array['maxTimeout']);
        $this->assertEquals(['http' => 'http://proxy.example.com'], $array['proxy']);
        $this->assertEquals('testSession', $array['session']);
        $this->assertEquals(120, $array['sessionTtlTimeout']);
    }

    public function testToCurlJsonOptionArray()
    {
        $request = $this->createV1RequestBase();
        $commandEnum = CommandEnum::REQUEST_GET;

        $options = $request->toCurlJsonOptionArray($commandEnum);

        $this->assertIsArray($options);
        $this->assertArrayHasKey('cmd', $options);
        $this->assertEquals(CommandEnum::REQUEST_GET->value, $options['cmd']);
        $this->assertArrayHasKey('url', $options);
        $this->assertEquals('http://example.com', $options['url']);
        // Check other properties as needed
    }

    public function testToCurlJsonOptionArrayWithEmptyProperties()
    {
        $request = new V1RequestBase(
            url: null,
            postData: null,
            userAgent: null,
            headers: null,
            cookies: null,
            returnOnlyCookies: false,
            maxTimeout: 0,
            proxy: null,
            session: null,
            sessionTtlTimeout: 0
        );
        $commandEnum = CommandEnum::REQUEST_GET;

        $options = $request->toCurlJsonOptionArray($commandEnum);

        $this->assertIsArray($options);
        $this->assertArrayHasKey('cmd', $options);
        $this->assertEquals(CommandEnum::REQUEST_GET->value, $options['cmd']);
        // Ensure no other properties are present
        // Only the following keys should be present
        // 'cmd','returnOnlyCookies', 'maxTimeout', 'sessionTtlTimeout'
        $this->assertCount(4, $options);
        $this->assertArrayHasKey('returnOnlyCookies', $options);
        $this->assertFalse($options['returnOnlyCookies']);
        $this->assertArrayHasKey('maxTimeout', $options);
        $this->assertEquals(0, $options['maxTimeout']);
        $this->assertArrayHasKey('sessionTtlTimeout', $options);
        $this->assertEquals(0, $options['sessionTtlTimeout']);

        $this->assertArrayNotHasKey('session', $options);
        $this->assertArrayNotHasKey('url', $options);
        $this->assertArrayNotHasKey('postData', $options);
        $this->assertArrayNotHasKey('userAgent', $options);
        $this->assertArrayNotHasKey('headers', $options);
        $this->assertArrayNotHasKey('cookies', $options);
        $this->assertArrayNotHasKey('proxy', $options);
    }

    private function createV1RequestBase(): V1RequestBase
    {
        return new V1RequestBase(
            url: 'http://example.com',
            postData: ['key' => 'value'],
            userAgent: 'TestUserAgent',
            headers: ['Content-Type' => 'application/json'],
            cookies: ['session' => 'abc123'],
            returnOnlyCookies: true,
            maxTimeout: 30000,
            proxy: ['http' => 'http://proxy.example.com'],
            session: 'testSession',
            sessionTtlTimeout: 120,
        );
    }
}

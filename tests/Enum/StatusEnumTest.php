<?php
namespace Pbxg33k\Test\FlareSolverrBundle\Enum;

use Pbxg33k\FlareSolverrBundle\Enum\StatusEnum;
use PHPUnit\Framework\TestCase;

class StatusEnumTest Extends TestCase
{
    public function testIsOk()
    {
        $status = StatusEnum::OK;
        $this->assertTrue($status->isOk());
        $this->assertFalse($status->isError());
    }

    public function testIsError()
    {
        $status = StatusEnum::ERROR;
        $this->assertFalse($status->isOk());
        $this->assertTrue($status->isError());
    }
}

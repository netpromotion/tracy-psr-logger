<?php

namespace Netpromotion\TracyPsrLogger\Test;

use Netpromotion\TracyPsrLogger\TracyPsrLogger;
use Tracy\ILogger;

class TracyPsrLoggerTest extends \PHPUnit_Framework_TestCase
{
    private function getLogger(&$level)
    {
        $logger = $this->getMock("Tracy\\ILogger");
        /** @noinspection PhpUnusedParameterInspection */
        $logger
            ->expects($this->once())
            ->method("log")
            ->willReturnCallback(function ($m, $l) use (&$level) {$level = $l;});
        return new TracyPsrLogger($logger);
    }

    /**
     * @dataProvider dataWrappingWorks
     * @param $method
     * @param $expectedLevel
     */
    public function testWrappingWorks($method, $expectedLevel)
    {
        call_user_func([$this->getLogger($level), $method], "");
        $this->assertEquals($expectedLevel, $level);
    }

    public function dataWrappingWorks()
    {
        return [
            ["emergency", ILogger::EXCEPTION],
            ["alert", ILogger::WARNING],
            ["critical", ILogger::CRITICAL],
            ["error", ILogger::ERROR],
            ["warning", ILogger::WARNING],
            ["notice", ILogger::WARNING],
            ["info", ILogger::INFO],
            ["debug", ILogger::DEBUG]
        ];
    }
}

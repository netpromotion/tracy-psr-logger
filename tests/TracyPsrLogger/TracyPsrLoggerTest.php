<?php

namespace Netpromotion\TracyPsrLogger\Test;

use Netpromotion\TracyPsrLogger\TracyPsrLogger;
use Tracy\ILogger;

class TracyPsrLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataWrappingWorks
     * @param $method
     * @param $expectedLevel
     */
    public function testWrappingWorks($method, $expectedLevel)
    {
        $logger = $this->getMock("Tracy\\ILogger");
        /** @noinspection PhpUnusedParameterInspection */
        $logger
            ->expects($this->once())
            ->method("log")
            ->willReturnCallback(function ($m, $l) use (&$level) {$level = $l;});

        call_user_func([new TracyPsrLogger($logger), $method], "");
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

    /**
     * @dataProvider dataContextIsLogged
     * @param mixed[] $context
     * @param string $expectedPartOfMessage
     */
    public function testContextIsLogged(array $context, $expectedPartOfMessage)
    {
        $logger = $this->getMock("Tracy\\ILogger");
        $logger
            ->expects($this->once())
            ->method("log")
            ->willReturnCallback(function ($m) use (&$message) {$message = $m;});

        call_user_func([new TracyPsrLogger($logger), "log"], "test", "message", $context);
        $this->assertStringMatchesFormat($expectedPartOfMessage, $message);
    }

    public function dataContextIsLogged()
    {
        return [
            [["null" => null], '%s{"null":null}'],
            [["bool" => true], '%s{"bool":true}'],
            [["int" => 1], '%s{"int":1}'],
            [["float" => 1.00], '%s{"float":1}'],
            [["string" => "string"], '%s{"string":"string"}'],
            [["object" => new \stdClass()], '%s{"object":{}}'],
            [["exception" => new \Exception("message", 1)], '%s{"exception":{"message":"message","code":1,"trace":%s}}'],
        ];
    }
}

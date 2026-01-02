<?php

namespace Tests\Unit\Model;

use IamLab\Model\ErrorLog;
use PHPUnit\Framework\TestCase;

class ErrorLogTest extends TestCase
{
    public function testSettersAndGettersWork(): void
    {
        $log = new ErrorLog();
        $log->setLevel('warning')
            ->setMessage('Something happened')
            ->setUrl('https://example.com/page')
            ->setUserAgent('UnitTest/1.0')
            ->setIp('127.0.0.1')
            ->setUserId(42)
            ->setContext(['a' => 1, 'b' => 'x']);

        $this->assertSame('warning', $log->getLevel());
        $this->assertSame('Something happened', $log->getMessage());
        $this->assertSame('https://example.com/page', $log->getUrl());
        $this->assertSame('UnitTest/1.0', $log->getUserAgent());
        $this->assertSame('127.0.0.1', $log->getIp());
        $this->assertSame(42, $log->getUserId());
        $this->assertSame(['a' => 1, 'b' => 'x'], $log->getContext());
        $this->assertJson($log->getContextJson());
    }

    public function testSetContextJsonRoundTrip(): void
    {
        $json = '{"alpha":true,"beta":"yes"}';
        $log = new ErrorLog();
        $log->setContextJson($json);
        $this->assertSame($json, $log->getContextJson());
        $this->assertSame(['alpha' => true, 'beta' => 'yes'], $log->getContext());
    }

    public function testBeforeValidationOnCreateSetsCreatedAt(): void
    {
        $log = new ErrorLog();
        $this->assertNull($log->getCreatedAt());
        // Call lifecycle hook directly to avoid DB need
        $log->beforeValidationOnCreate();
        $this->assertNotEmpty($log->getCreatedAt());
    }
}

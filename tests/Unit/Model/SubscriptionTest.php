<?php

namespace Tests\Unit\Model;

use IamLab\Model\Subscription;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    public function testSettersAndGettersWork(): void
    {
        $sub = new Subscription();
        $sub->setUserId(1)
            ->setPaymentMethod('stripe')
            ->setSubscriptionId('sub_123')
            ->setPlanId('premium_monthly')
            ->setStatus('active')
            ->setStartsAt('2026-01-01 00:00:00')
            ->setEndsAt('2026-02-01 00:00:00')
            ->setTrialEndsAt('2026-01-07 00:00:00')
            ->setCanceledAt('2026-01-15 00:00:00')
            ->setPayload('{"info":"test"}');

        $this->assertSame(1, $sub->getUserId());
        $this->assertSame('stripe', $sub->getPaymentMethod());
        $this->assertSame('sub_123', $sub->getSubscriptionId());
        $this->assertSame('premium_monthly', $sub->getPlanId());
        $this->assertSame('active', $sub->getStatus());
        $this->assertSame('2026-01-01 00:00:00', $sub->getStartsAt());
        $this->assertSame('2026-02-01 00:00:00', $sub->getEndsAt());
        $this->assertSame('2026-01-07 00:00:00', $sub->getTrialEndsAt());
        $this->assertSame('2026-01-15 00:00:00', $sub->getCanceledAt());
        $this->assertSame('{"info":"test"}', $sub->getPayload());
    }

    public function testBeforeValidationOnCreateSetsTimestamps(): void
    {
        $sub = new Subscription();
        $this->assertNull($sub->getCreatedAt());
        $this->assertNull($sub->getUpdatedAt());
        
        $sub->beforeValidationOnCreate();
        
        $this->assertNotEmpty($sub->getCreatedAt());
        $this->assertNotEmpty($sub->getUpdatedAt());
    }
}

<?php

namespace Tests\Unit\Model;

use IamLab\Model\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    public function testSettersAndGettersWork(): void
    {
        $payment = new Payment();
        $payment->setUserId(1)
            ->setPaymentMethod('stripe')
            ->setTransactionId('tx_123')
            ->setAmount(99.99)
            ->setCurrency('EUR')
            ->setStatus('completed')
            ->setType('single')
            ->setPayload('{"info":"test"}');

        $this->assertSame(1, $payment->getUserId());
        $this->assertSame('stripe', $payment->getPaymentMethod());
        $this->assertSame('tx_123', $payment->getTransactionId());
        $this->assertSame(99.99, $payment->getAmount());
        $this->assertSame('EUR', $payment->getCurrency());
        $this->assertSame('completed', $payment->getStatus());
        $this->assertSame('single', $payment->getType());
        $this->assertSame('{"info":"test"}', $payment->getPayload());
    }

    public function testBeforeValidationOnCreateSetsTimestamps(): void
    {
        $payment = new Payment();
        $this->assertNull($payment->getCreatedAt());
        $this->assertNull($payment->getUpdatedAt());
        
        $payment->beforeValidationOnCreate();
        
        $this->assertNotEmpty($payment->getCreatedAt());
        $this->assertNotEmpty($payment->getUpdatedAt());
    }

    public function testBeforeValidationOnUpdateSetsUpdatedAt(): void
    {
        $payment = new Payment();
        $payment->beforeValidationOnUpdate();
        $this->assertNotEmpty($payment->getUpdatedAt());
    }
}

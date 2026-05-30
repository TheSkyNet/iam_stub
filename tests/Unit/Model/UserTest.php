<?php

namespace Tests\Unit\Model;

use IamLab\Model\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSettersAndGettersWork(): void
    {
        $user = new User();
        $user->setName('John Doe')
            ->setEmail('john@example.com')
            ->setPassword('hashedpassword')
            ->setStatus('active');

        $this->assertSame('John Doe', $user->getName());
        $this->assertSame('john@example.com', $user->getEmail());
        $this->assertSame('hashedpassword', $user->getPassword());
        $this->assertSame('active', $user->getStatus());
    }

    public function testCanBeInstantiated(): void
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }
}

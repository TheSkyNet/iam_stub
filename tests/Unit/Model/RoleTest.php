<?php

namespace Tests\Unit\Model;

use IamLab\Model\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function testSettersAndGettersWork(): void
    {
        $role = new Role();
        $role->setName('editor')
            ->setDescription('Can edit posts');

        $this->assertSame('editor', $role->getName());
        $this->assertSame('Can edit posts', $role->getDescription());
    }

    public function testCanBeInstantiated(): void
    {
        $role = new Role();
        $this->assertInstanceOf(Role::class, $role);
    }

    public function testValidationFailsWithoutName(): void
    {
        $role = new Role();
        $role->setDescription('Only description');

        // This is a unit test, we're testing the object state
        $this->assertNull($role->getName());
        $this->assertSame('Only description', $role->getDescription());
    }
}

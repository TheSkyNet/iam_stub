<?php

namespace IamLab\Migrations\Seeders;

use IamLab\Model\Role;
use IamLab\Model\User;
use IamLab\Service\RolesService;
use Phalcon\Migrations\Mvc\Model\Migration;

class RolesSeeder extends Migration
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrator with full system access and management capabilities'
            ],
            [
                'name' => 'editor',
                'description' => 'Editor with content management permissions and publishing rights'
            ],
            [
                'name' => 'member',
                'description' => 'Regular member with standard user access and basic features'
            ],
            [
                'name' => 'guest',
                'description' => 'Guest user with limited access to public content only'
            ]
        ];

        foreach ($roles as $roleData) {
            // Check if role already exists
            $existingRole = Role::findFirstByName($roleData['name']);
            if ($existingRole) {
                echo "Role '{$roleData['name']}' already exists, skipping...\n";
                continue;
            }

            $role = new Role();
            $role->setName($roleData['name']);
            $role->setDescription($roleData['description']);
            $role->setCreatedAt(date('Y-m-d H:i:s'));
            $role->setUpdatedAt(date('Y-m-d H:i:s'));

            if (!$role->save()) {
                echo "Failed to save role: " . $role->getName() . "\n";
                foreach ($role->getMessages() as $message) {
                    echo $message->getMessage() . "\n";
                }
            } else {
                echo "Successfully created role: " . $role->getName() . "\n";
            }
        }
    }

    /**
     * Assign a role to a user by email
     *
     * @param string $email
     * @param string $roleName
     * @return bool
     */
    public function assignRoleToUser(string $email, string $roleName): bool
    {
        $user = User::findFirstByEmail($email);
        if (!$user) {
            echo "User with email '{$email}' not found\n";
            return false;
        }

        $rolesService = new RolesService();
        if ($rolesService->addRole($user, $roleName)) {
            echo "Successfully assigned role '{$roleName}' to user '{$email}'\n";
            return true;
        } else {
            echo "Failed to assign role '{$roleName}' to user '{$email}'\n";
            return false;
        }
    }

    /**
     * Assign default admin role to the first user created
     *
     * @param string $email
     * @return bool
     */
    public function assignAdminRole(string $email): bool
    {
        return $this->assignRoleToUser($email, 'admin');
    }
}
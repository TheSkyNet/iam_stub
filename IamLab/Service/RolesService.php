<?php

namespace IamLab\Service;

use IamLab\Model\Role;
use IamLab\Model\User;
use IamLab\Model\UserRole;
use Phalcon\Di\Injectable;
use function App\Core\Helpers\dd;

/**
 * RolesService - Service for managing user roles and permissions
 * 
 * This service provides methods to:
 * - Check if a user has a specific role
 * - Add roles to users
 * - Remove roles from users
 * - Get all roles for a user
 * - Manage role assignments
 */
class RolesService extends Injectable
{
    /**
     * Check if a user has a specific role
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    public function hasRole(User $user, string $roleName): bool
    {
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            return false;
        }

        $userRole = UserRole::findFirst([
            'conditions' => 'user_id = :user_id: AND role_id = :role_id:',
            'bind' => [
                'user_id' => $user->getId(),
                'role_id' => $role->getId()
            ]
        ]);

        return $userRole !== NULL;
    }

    /**
     * Add a role to a user
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    public function addRole(User $user, string $roleName): bool
    {
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            return false;
        }

        // Check if user already has this role
        if ($this->hasRole($user, $roleName)) {
            return true; // Already has the role
        }

        $userRole = new UserRole();
        $userRole->setUserId($user->getId());
        $userRole->setRoleId($role->getId());
        $userRole->setCreatedAt(date('Y-m-d H:i:s'));
        $userRole->setUpdatedAt(date('Y-m-d H:i:s'));

        return $userRole->save();
    }

    public function listRoles($user): array
    {
        $return =[];
        $userRoles = UserRole::find([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $user->getId()]
        ]);
        foreach ($userRoles as $userRole) {
            $role = Role::findFirstById($userRole->getRoleId());
            if ($role) {
                $return[] = $role->getName();
            }
        }

        return $return;

    }

    /**
     * Remove a role from a user
     *
     * @param User $user
     * @param string $roleName
     * @return bool
     */
    public function removeRole(User $user, string $roleName): bool
    {
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            return false;
        }

        $userRole = UserRole::findFirst([
            'conditions' => 'user_id = :user_id: AND role_id = :role_id:',
            'bind' => [
                'user_id' => $user->getId(),
                'role_id' => $role->getId()
            ]
        ]);

        if ($userRole) {
            return $userRole->delete();
        }

        return true; // Role wasn't assigned anyway
    }

    /**
     * Get all roles for a user
     *
     * @param User $user
     * @return array
     */
    public function getUserRoles(User $user): array
    {
        $userRoles = UserRole::find([
            'conditions' => 'user_id = :user_id:',
            'bind' => ['user_id' => $user->getId()]
        ]);

        $roles = [];
        foreach ($userRoles as $userRole) {
            $role = Role::findFirstById($userRole->getRoleId());
            if ($role) {
                $roles[] = $role->getName();
            }
        }

        return $roles;
    }

    /**
     * Get all available roles
     *
     * @return Role[]
     */
    public function getAllRoles(): array
    {
        return Role::find()->toArray();
    }

    /**
     * Create a new role
     *
     * @param string $name
     * @param string $description
     * @return bool
     */
    public function createRole(string $name, string $description = ''): bool
    {
        // Check if role already exists
        $existingRole = Role::findFirstByName($name);
        if ($existingRole) {
            return false;
        }

        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);
        $role->setCreatedAt(date('Y-m-d H:i:s'));
        $role->setUpdatedAt(date('Y-m-d H:i:s'));

        return $role->save();
    }

    /**
     * Delete a role and all its assignments
     *
     * @param string $roleName
     * @return bool
     */
    public function deleteRole(string $roleName): bool
    {
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            return false;
        }

        // Delete all user role assignments first
        $userRoles = UserRole::find([
            'conditions' => 'role_id = :role_id:',
            'bind' => ['role_id' => $role->getId()]
        ]);

        foreach ($userRoles as $userRole) {
            $userRole->delete();
        }

        // Delete the role itself
        return $role->delete();
    }

    /**
     * Get users with a specific role
     *
     * @param string $roleName
     * @return User[]
     */
    public function getUsersWithRole(string $roleName): array
    {
        $role = Role::findFirstByName($roleName);
        if (!$role) {
            return [];
        }

        $userRoles = UserRole::find([
            'conditions' => 'role_id = :role_id:',
            'bind' => ['role_id' => $role->getId()]
        ]);

        $users = [];
        foreach ($userRoles as $userRole) {
            $user = User::findFirstById($userRole->getUserId());
            if ($user) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Check if a user has any of the specified roles
     *
     * @param User $user
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole(User $user, array $roleNames): bool
    {
        foreach ($roleNames as $roleName) {
            if ($this->hasRole($user, $roleName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a user has all of the specified roles
     *
     * @param User $user
     * @param array $roleNames
     * @return bool
     */
    public function hasAllRoles(User $user, array $roleNames): bool
    {
        foreach ($roleNames as $roleName) {
            if (!$this->hasRole($user, $roleName)) {
                return false;
            }
        }
        return true;
    }
}
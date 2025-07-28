<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\User;

class UsersApi extends aAPI
{
    /**
     * Get all users
     * GET /api/users
     */
    public function indexAction(): void
    {
        $this->requireAdmin();
        
        try {
            $users = User::find();
            
            // Include roles for each user
            $usersData = [];
            foreach ($users as $user) {
                $userData = $user->toArray();
                $userData['roles'] = $user->getRoles();
                $usersData[] = $userData;
            }

            $this->dispatch([
                'success' => true,
                'data' => $usersData
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user by ID
     * GET /api/users/:id
     */
    public function showAction(): void
    {
        $this->requireAdmin();
        
        try {
            $id = $this->getRouteParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $user = User::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $id]
            ]);
            if (!$user) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                return;
            }

            $userData = $user->toArray();
            $userData['roles'] = $user->getRoles();

            $this->dispatch([
                'success' => true,
                'data' => $userData
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create new user
     * POST /api/users
     */
    public function createAction(): void
    {
        $this->requireAdmin();
        
        try {
            // Validate required fields
            $name = $this->getParam('name');
            $email = $this->getParam('email');
            $password = $this->getParam('password');
            
            if (!$name || !$email || !$password) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Name, email, and password are required'
                ]);
                return;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Invalid email format'
                ]);
                return;
            }
            
            // Check if email already exists
            $existingUser = User::findFirstByEmail($email);
            if ($existingUser) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Email already exists'
                ]);
                return;
            }

            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setKey(bin2hex(random_bytes(32)));
            $user->setEmailVerified($this->getParam('email_verified', false));
            
            if ($user->save()) {
                // Assign roles if provided
                $roles = $this->getParam('roles', []);
                if (is_array($roles)) {
                    foreach ($roles as $roleName) {
                        $user->addRole($roleName);
                    }
                }
                
                $userData = $user->toArray();
                $userData['roles'] = $user->getRoles();
                
                $this->dispatch([
                    'success' => true,
                    'data' => $userData,
                    'message' => 'User created successfully'
                ]);
            } else {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Failed to create user',
                    'errors' => $user->getMessages()
                ]);
            }
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update user
     * PUT /api/users/:id
     */
    public function updateAction(): void
    {
        $this->requireAdmin();
        
        try {
            $id = $this->getRouteParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $user = User::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $id]
            ]);
            if (!$user) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                return;
            }

            // Update basic properties
            $name = $this->getParam('name');
            if ($name !== null) {
                $user->setName($name);
            }

            $email = $this->getParam('email');
            if ($email !== null) {
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->dispatchError([
                        'success' => false,
                        'message' => 'Invalid email format'
                    ]);
                    return;
                }
                
                // Check if email already exists (excluding current user)
                $existingUser = User::findFirst([
                    'conditions' => 'email = :email: AND id != :id:',
                    'bind' => ['email' => $email, 'id' => $id]
                ]);
                if ($existingUser) {
                    $this->dispatchError([
                        'success' => false,
                        'message' => 'Email already exists'
                    ]);
                    return;
                }
                
                $user->setEmail($email);
            }

            // Update password if provided
            $password = $this->getParam('password');
            if ($password !== null && !empty($password)) {
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            }

            // Update email verification status
            if ($this->hasParam('email_verified')) {
                $user->setEmailVerified($this->getParam('email_verified'));
            }

            if ($user->save()) {
                // Update roles if provided
                $roles = $this->getParam('roles');
                if (is_array($roles)) {
                    // Get current roles
                    $currentRoles = $user->getRoles();
                    
                    // Remove roles that are not in the new list
                    foreach ($currentRoles as $currentRole) {
                        if (!in_array($currentRole, $roles)) {
                            $user->removeRole($currentRole);
                        }
                    }
                    
                    // Add new roles
                    foreach ($roles as $roleName) {
                        if (!in_array($roleName, $currentRoles)) {
                            $user->addRole($roleName);
                        }
                    }
                }
                
                $userData = $user->toArray();
                $userData['roles'] = $user->getRoles();
                
                $this->dispatch([
                    'success' => true,
                    'data' => $userData,
                    'message' => 'User updated successfully'
                ]);
            } else {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Failed to update user',
                    'errors' => $user->getMessages()
                ]);
            }
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete user
     * DELETE /api/users/:id
     */
    public function deleteAction(): void
    {
        $this->requireAdmin();
        
        try {
            $id = $this->getRouteParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $user = User::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $id]
            ]);
            if (!$user) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'User not found'
                ]);
                return;
            }

            // Prevent deletion of current admin user
            $currentUser = $this->getCurrentUser();
            if ($currentUser && $currentUser->getId() == $user->getId()) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Cannot delete your own account'
                ]);
                return;
            }

            if ($user->delete()) {
                $this->dispatch([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Failed to delete user',
                    'errors' => $user->getMessages()
                ]);
            }
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Search users
     * GET /api/users/search
     */
    public function searchAction(): void
    {
        $this->requireAdmin();
        
        try {
            $query = $this->getParam('q', '');
            if (empty($query)) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
                return;
            }

            // Search by name or email
            $users = User::find([
                "name LIKE :query: OR email LIKE :query:",
                'bind' => ['query' => "%$query%"]
            ]);

            // Include roles for each user
            $usersData = [];
            foreach ($users as $user) {
                $userData = $user->toArray();
                $userData['roles'] = $user->getRoles();
                $usersData[] = $userData;
            }

            $this->dispatch([
                'success' => true,
                'data' => $usersData,
                'query' => $query
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
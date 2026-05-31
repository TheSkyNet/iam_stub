<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\Role;

class RolesApi extends aAPI
{
    /**
     * Get all roles
     * GET /api/roles
     */
    public function indexAction(): void
    {
        $this->requireAdmin();

        try {
            $roles = Role::find();

            $this->dispatch([
                'success' => true,
                'data' => $roles->toArray()
            ]);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Get role by ID
     * GET /api/roles/:id
     */
    public function showAction($id): void
    {
        $this->requireAdmin();

        try {
            $role = Role::findFirst($id);
            if (!$role) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Role not found'
                ]);
            }

            $this->dispatch([
                'success' => true,
                'data' => $role->toArray()
            ]);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve role',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Create new role
     * POST /api/roles
     */
    public function createAction(): void
    {
        $this->requireAdmin();

        try {
            $role = new Role();

            // Set properties from request data
            $name = $this->getParam('name');
            if ($name) {
                $role->setName($name);
            }

            $description = $this->getParam('description');
            if ($description) {
                $role->setDescription($description);
            }

            // Set timestamps
            $role->setCreatedAt(date('Y-m-d H:i:s'));
            $role->setUpdatedAt(date('Y-m-d H:i:s'));

            $this->save($role);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to create role',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Update role
     * PUT /api/roles/:id
     */
    public function updateAction($id): void
    {
        $this->requireAdmin();

        try {
            $role = Role::findFirst($id);
            if (!$role) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Role not found'
                ]);
            }

            // Update properties from request data
            $name = $this->getParam('name');
            if ($name !== null) {
                $role->setName($name);
            }

            $description = $this->getParam('description');
            if ($description !== null) {
                $role->setDescription($description);
            }

            // Update timestamp
            $role->setUpdatedAt(date('Y-m-d H:i:s'));

            $this->save($role);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Delete role
     * DELETE /api/roles/:id
     */
    public function deleteAction($id): void
    {
        $this->requireAdmin();

        try {
            $role = Role::findFirst($id);
            if (!$role) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Role not found'
                ]);
            }

            $this->delete($role);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to delete role',
                'error' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Search roles
     * GET /api/roles/search
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
            }

            // Implement search logic based on your model structure
            $roles = Role::find([
                "name LIKE :query: OR description LIKE :query:",
                'bind' => ['query' => sprintf('%%%s%%', $query)]
            ]);

            $this->dispatch([
                'success' => true,
                'data' => $roles->toArray(),
                'query' => $query
            ]);
        } catch (Exception $exception) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Search failed',
                'error' => $exception->getMessage()
            ]);
        }
    }
}

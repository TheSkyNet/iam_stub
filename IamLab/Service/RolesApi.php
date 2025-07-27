<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\Roles;

class RolesApi extends aAPI
{
    /**
     * Get all roless
     * GET /api/roles
     */
    public function indexAction(): void
    {
        try {
            $roless = Roles::find();

            $this->dispatch([
                'success' => true,
                'data' => $roless->toArray()
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve roless',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get roles by ID
     * GET /api/roles/:id
     */
    public function showAction(): void
    {
        try {
            $id = $this->getParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $roles = Roles::findFirst($id);
            if (!$roles) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Roles not found'
                ]);
                return;
            }

            $this->dispatch([
                'success' => true,
                'data' => $roles->toArray()
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create new roles
     * POST /api/roles
     */
    public function createAction(): void
    {
        try {
            $roles = new Roles();

            // Set properties from request data
            $name = $this->getParam('name');
            if ($name) {
                $roles->setName($name);
            }

            // Add more property assignments as needed
            // $roles->setDescription($this->getParam('description'));
            // $roles->setStatus($this->getParam('status', 'active'));

            $this->save($roles);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to create roles',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update roles
     * PUT /api/roles/:id
     */
    public function updateAction(): void
    {
        try {
            $id = $this->getParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $roles = Roles::findFirst($id);
            if (!$roles) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Roles not found'
                ]);
                return;
            }

            // Update properties from request data
            $name = $this->getParam('name');
            if ($name !== null) {
                $roles->setName($name);
            }

            // Add more property updates as needed
            // if ($this->hasParam('description')) {
            //     $roles->setDescription($this->getParam('description'));
            // }

            $this->save($roles);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to update roles',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete roles
     * DELETE /api/roles/:id
     */
    public function deleteAction(): void
    {
        try {
            $id = $this->getParam('id');
            if (!$id) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'ID parameter is required'
                ]);
                return;
            }

            $roles = Roles::findFirst($id);
            if (!$roles) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Roles not found'
                ]);
                return;
            }

            $this->delete($roles);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to delete roles',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Search roless
     * GET /api/roles/search
     */
    public function searchAction(): void
    {
        try {
            $query = $this->getParam('q', '');
            if (empty($query)) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
                return;
            }

            // Implement search logic based on your model structure
            $roless = Roles::find([
                "name LIKE :query:",
                'bind' => ['query' => "%$query%"]
            ]);

            $this->dispatch([
                'success' => true,
                'data' => $roless->toArray(),
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
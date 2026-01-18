<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\SiteSetting;

class SettingsApi extends aAPI
{
    /**
     * GET /api/settings
     * List all settings
     */
    public function indexAction(): void
    {
        $this->requireAdmin();

        $settings = SiteSetting::find([
            'order' => 'key ASC'
        ]);

        $this->dispatch([
            'success' => true,
            'data' => $settings->toArray()
        ]);
    }

    /**
     * PUT /api/settings/{id}
     * Update a setting
     */
    public function updateAction(): void
    {
        $this->requireAdmin();
        $id = (int)$this->getRouteParam('id', 0, 'int');
        
        $setting = SiteSetting::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id]
        ]);

        if (!$setting) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Setting not found'
            ], 404);
            return;
        }

        $data = $this->getData();
        if (isset($data['value'])) {
            $setting->setValue($data['value']);
        }
        if (isset($data['description'])) {
            $setting->setDescription($data['description']);
        }

        if (!$setting->save()) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to update setting',
                'errors' => $setting->getMessages()
            ], 500);
            return;
        }

        $this->dispatch([
            'success' => true,
            'message' => 'Setting updated successfully',
            'data' => $setting->toArray()
        ]);
    }

    /**
     * POST /api/settings
     * Create a new setting
     */
    public function createAction(): void
    {
        $this->requireAdmin();
        $data = $this->getData();

        if (!isset($data['key']) || !isset($data['type'])) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Key and type are required'
            ], 422);
            return;
        }

        $setting = new SiteSetting();
        $setting->setKey($data['key']);
        $setting->setType($data['type']);
        $setting->setValue($data['value'] ?? '');
        $setting->setDescription($data['description'] ?? '');

        if (!$setting->save()) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to create setting',
                'errors' => $setting->getMessages()
            ], 500);
            return;
        }

        $this->dispatch([
            'success' => true,
            'message' => 'Setting created successfully',
            'data' => $setting->toArray()
        ], 201);
    }
}

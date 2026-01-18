<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Service\LMS\LMSService;

class LMSApi extends aAPI
{
    protected LMSService $lmsService;

    public function initialize()
    {
        $this->lmsService = new LMSService();
        $this->lmsService->initialize();
    }

    /**
     * GET /api/lms/status
     * Get status of all LMS integrations
     */
    public function statusAction(): void
    {
        $this->requireAdmin();

        try {
            $status = $this->lmsService->getIntegrationStatus();
            $stats = $this->lmsService->getStatistics();

            $this->dispatch([
                'success' => true,
                'data' => [
                    'integrations' => $status,
                    'statistics' => $stats
                ]
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to get LMS status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/lms/refresh
     * Refresh health status of all integrations
     */
    public function refreshAction(): void
    {
        $this->requireAdmin();

        try {
            $this->lmsService->refreshHealthStatus();
            $this->dispatch([
                'success' => true,
                'message' => 'LMS health status refreshed successfully'
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to refresh LMS status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/lms/test
     * Test an integration
     */
    public function testAction(): void
    {
        $this->requireAdmin();
        $data = $this->getData();
        $integration = $data['integration'] ?? 'ollama';
        $prompt = $data['prompt'] ?? 'Say hello!';

        try {
            $result = $this->lmsService->generateContent($prompt, $integration);
            $this->dispatch([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'LMS test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

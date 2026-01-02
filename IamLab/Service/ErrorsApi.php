<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\ErrorLog;

class ErrorsApi extends aAPI
{
    /**
     * POST /api/errors
     * Public endpoint to record a frontend or client error
     */
    public function createAction(): void
    {
        $data = $this->getData();

        $message = (string)($data['message'] ?? '');
        if ($message === '') {
            $this->dispatch([
                'success' => false,
                'message' => 'Message is required',
                'error' => 'VALIDATION_ERROR'
            ], 422);
        }

        $level = (string)($data['level'] ?? 'error');
        $url = (string)($data['url'] ?? $this->request->getURI());
        $userAgent = (string)($data['user_agent'] ?? $this->request->getUserAgent());
        $ip = (string)($data['ip'] ?? $this->request->getClientAddress());
        $context = $data['context'] ?? null; // array or string

        $userId = null;
        try {
            $identity = (new \IamLab\Service\Auth\AuthService())->getIdentity();
            if ($identity && isset($identity['user_id'])) {
                $userId = (int)$identity['user_id'];
            }
        } catch (Exception) {
            // ignore auth errors
        }

        $log = new ErrorLog();
        $log->setLevel($level)
            ->setMessage($message)
            ->setUrl($url)
            ->setUserAgent($userAgent)
            ->setIp($ip)
            ->setUserId($userId);

        if (is_array($context)) {
            $log->setContext($context);
        } elseif (is_string($context)) {
            $log->setContextJson($context);
        }

        if (!$log->save()) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to save error log',
                'errors' => $log->getMessages()
            ], 500);
        }

        // Also write to app logger if available
        if (isset($this->logger)) {
            /** @var \Phalcon\Logger\LoggerInterface $logger */
            $logger = $this->logger;
            $logger->error('[FE] ' . $message . ' | url=' . $url);
        }

        $this->dispatch([
            'success' => true,
            'data' => [
                'id' => $log->getId(),
            ],
        ], 201);
    }

    /**
     * GET /api/errors
     * Admin: list error logs with filters
     */
    public function indexAction(): void
    {
        $this->requireAdmin();

        $level = $this->getParam('level', null, 'string');
        $q = $this->getParam('q', null, 'string');
        $limit = (int)$this->getParam('limit', 20, 'int');
        $offset = (int)$this->getParam('offset', 0, 'int');
        $since = $this->getParam('since', null, 'string');

        $conditions = [];
        $bind = [];
        if ($level) { $conditions[] = 'level = :level:'; $bind['level'] = $level; }
        if ($since) { $conditions[] = 'created_at >= :since:'; $bind['since'] = $since; }
        if ($q) {
            $conditions[] = '(message LIKE :q: OR url LIKE :q:)';
            $bind['q'] = '%' . $q . '%';
        }
        $where = $conditions ? implode(' AND ', $conditions) : null;

        $total = ErrorLog::count([
            'conditions' => $where,
            'bind' => $bind,
        ]);

        $items = ErrorLog::find([
            'conditions' => $where,
            'bind' => $bind,
            'order' => 'created_at DESC',
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $list = [];
        foreach ($items as $item) {
            $list[] = [
                'id' => $item->getId(),
                'level' => $item->getLevel(),
                'message' => $item->getMessage(),
                'context' => $item->getContext(),
                'url' => $item->getUrl(),
                'user_agent' => $item->getUserAgent(),
                'ip' => $item->getIp(),
                'user_id' => $item->getUserId(),
                'created_at' => $item->getCreatedAt(),
            ];
        }

        $this->dispatch([
            'success' => true,
            'data' => [
                'items' => $list,
                'total' => (int)$total,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ], 200);
    }

    /**
     * GET /api/errors/{id}
     */
    public function showAction(): void
    {
        $this->requireAdmin();
        $id = (int)$this->getRouteParam('id', 0, 'int');
        $log = ErrorLog::findFirst([ 'conditions' => 'id = :id:', 'bind' => ['id' => $id] ]);
        if (!$log) {
            $this->dispatch([
                'success' => false,
                'message' => 'Error log not found'
            ], 404);
        }
        $this->dispatch([
            'success' => true,
            'data' => [
                'id' => $log->getId(),
                'level' => $log->getLevel(),
                'message' => $log->getMessage(),
                'context' => $log->getContext(),
                'url' => $log->getUrl(),
                'user_agent' => $log->getUserAgent(),
                'ip' => $log->getIp(),
                'user_id' => $log->getUserId(),
                'created_at' => $log->getCreatedAt(),
            ],
        ], 200);
    }

    /**
     * DELETE /api/errors/{id}
     */
    public function deleteAction(): void
    {
        $this->requireAdmin();
        $id = (int)$this->getRouteParam('id', 0, 'int');
        $log = ErrorLog::findFirst([ 'conditions' => 'id = :id:', 'bind' => ['id' => $id] ]);
        if (!$log) {
            $this->dispatch([
                'success' => false,
                'message' => 'Error log not found'
            ], 404);
        }
        $log->delete();
        $this->dispatch(['success' => true], 200);
    }

    /**
     * POST /api/errors/cleanup { days: int }
     */
    public function cleanupAction(): void
    {
        $this->requireAdmin();
        $days = (int)($this->getParam('days', 30, 'int'));
        $threshold = date('Y-m-d H:i:s', time() - ($days * 86400));
        $connection = $this->getDI()->get('db');
        $sql = 'DELETE FROM error_logs WHERE created_at < :th';
        $stmt = $connection->prepare($sql);
        $result = $connection->executePrepared($stmt, ['th' => $threshold], ['th' => \Phalcon\Db\Column::BIND_PARAM_STR]);
        $this->dispatch([
            'success' => true,
            'data' => [ 'deleted' => (int)$connection->affectedRows() ]
        ], 200);
    }
}

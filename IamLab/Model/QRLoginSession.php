<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

class QRLoginSession extends Model
{
    public $id;
    public $session_token;
    public $user_id;
    public $status; // 'pending', 'authenticated', 'expired'
    public $created_at;
    public $expires_at;
    public $authenticated_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('qr_login_sessions');

        // Set up relationships
        $this->belongsTo('user_id', User::class, 'id');
    }

    /**
     * Generate a unique session token
     */
    public static function generateSessionToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create a new QR login session
     */
    public static function createSession(int $expirationMinutes = 5): QRLoginSession
    {
        $session = new self();
        $session->session_token = self::generateSessionToken();
        $session->status = 'pending';
        $session->created_at = date('Y-m-d H:i:s');
        $session->expires_at = date('Y-m-d H:i:s', strtotime("+{$expirationMinutes} minutes"));

        return $session;
    }

    /**
     * Find session by token
     */
    public static function findByToken(string $token): ?QRLoginSession
    {
        return self::findFirst([
            'conditions' => 'session_token = :token:',
            'bind' => ['token' => $token]
        ]);
    }

    /**
     * Check if session is valid (not expired and in a pending state)
     */
    public function isValid(): bool
    {
        return ($this->status === 'pending' || $this->status === 'pending_mobile_auth') && 
               strtotime($this->expires_at) > time();
    }

    /**
     * Authenticate the session with a user
     */
    public function authenticate(int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $this->user_id = $userId;
        $this->status = 'authenticated';
        $this->authenticated_at = date('Y-m-d H:i:s');

        return $this->save();
    }

    /**
     * Mark session as expired
     */
    public function expire(): bool
    {
        $this->status = 'expired';
        return $this->save();
    }

    /**
     * Clean up expired sessions
     */
    public static function cleanupExpired(): void
    {
        $expiredSessions = self::find([
            'conditions' => 'expires_at < :now: OR status = :expired:',
            'bind' => [
                'now' => date('Y-m-d H:i:s'),
                'expired' => 'expired'
            ]
        ]);

        foreach ($expiredSessions as $session) {
            $session->delete();
        }
    }

    /**
     * Get session token
     */
    public function getSessionToken(): string
    {
        return $this->session_token;
    }

    /**
     * Get user ID
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * Get status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Check if authenticated
     */
    public function isAuthenticated(): bool
    {
        return $this->status === 'authenticated';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     */
    public static function findFirst($parameters = null): mixed
    {
        return parent::findFirst($parameters);
    }
}

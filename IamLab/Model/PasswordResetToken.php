<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use DateTime;

/**
 * @method static findFirstByToken(string $token)
 * @method static find($parameters = null)
 */
class PasswordResetToken extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=32, nullable=false)
     */
    protected $id;

    /**
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    protected $user_id;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $token;

    /**
     * @var string
     * @Column(type="timestamp", nullable=false)
     */
    protected $expires_at;

    /**
     * @var string
     * @Column(type="timestamp", nullable=false)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(type="timestamp", nullable=true)
     */
    protected $used_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("password_reset_tokens");
        
        // Define relationship with User model
        $this->belongsTo('user_id', User::class, 'id', [
            'alias' => 'user'
        ]);
    }

    /**
     * Generate a secure reset token
     *
     * @return string
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create a new password reset token for a user
     *
     * @param User $user
     * @param int $expirationHours
     * @return PasswordResetToken|false
     */
    public static function createForUser(User $user, int $expirationHours = 1): PasswordResetToken|false
    {
        // Clean up any existing tokens for this user
        self::cleanupExpiredTokens($user->getId());

        $token = new self();
        $token->setUserId($user->getId());
        $token->setToken(self::generateToken());
        $token->setExpiresAt(date('Y-m-d H:i:s', strtotime("+{$expirationHours} hours")));
        $token->setCreatedAt(date('Y-m-d H:i:s'));

        if ($token->save()) {
            return $token;
        }

        return false;
    }

    /**
     * Clean up expired tokens for a user
     *
     * @param int $userId
     * @return void
     */
    public static function cleanupExpiredTokens(int $userId): void
    {
        $expiredTokens = self::find([
            'conditions' => 'user_id = :userId: AND (expires_at < NOW() OR used_at IS NOT NULL)',
            'bind' => ['userId' => $userId]
        ]);

        foreach ($expiredTokens as $expiredToken) {
            $expiredToken->delete();
        }
    }

    /**
     * Check if token is valid and not expired
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if ($this->used_at !== null) {
            return false;
        }

        $now = new DateTime();
        $expiresAt = new DateTime($this->expires_at);

        return $now <= $expiresAt;
    }

    /**
     * Mark token as used
     *
     * @return bool
     */
    public function markAsUsed(): bool
    {
        $this->used_at = date('Y-m-d H:i:s');
        return $this->save();
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getExpiresAt(): string
    {
        return $this->expires_at;
    }

    public function setExpiresAt(string $expires_at): self
    {
        $this->expires_at = $expires_at;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUsedAt(): ?string
    {
        return $this->used_at;
    }

    public function setUsedAt(?string $used_at): self
    {
        $this->used_at = $used_at;
        return $this;
    }

    /**
     * Get the associated user
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->getRelated('user');
    }
}
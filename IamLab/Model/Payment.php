<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;

class Payment extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_id;

    /**
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    protected $payment_method;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $transaction_id;

    /**
     * @var double
     * @Column(type="decimal", size=10, scale=2, nullable=false)
     */
    protected $amount;

    /**
     * @var string
     * @Column(type="string", length=3, nullable=false)
     */
    protected $currency;

    /**
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    protected $status;

    /**
     * @var string
     * @Column(type="string", length=30, nullable=false)
     */
    protected $type;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $payload;

    /**
     * @var string
     * @Column(type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(type="datetime", nullable=false)
     */
    protected $updated_at;

    public function initialize(): void
    {
        $this->setSource('payment');
        $this->belongsTo('user_id', User::class, 'id', ['alias' => 'user']);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id): static
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    public function setPaymentMethod($payment_method): static
    {
        $this->payment_method = $payment_method;
        return $this;
    }

    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    public function setTransactionId($transaction_id): static
    {
        $this->transaction_id = $transaction_id;
        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function setPayload($payload): static
    {
        $this->payload = $payload;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function beforeValidationOnCreate(): void
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function beforeValidationOnUpdate(): void
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }
}

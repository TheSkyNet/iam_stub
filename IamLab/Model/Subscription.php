<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;

class Subscription extends Model
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
    protected $subscription_id;

    /**
     * @var string
     * @Column(type="string", length=100, nullable=false)
     */
    protected $plan_id;

    /**
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    protected $status;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $starts_at;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $ends_at;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $trial_ends_at;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $canceled_at;

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

    public function initialize()
    {
        $this->setSource('subscription');
        $this->belongsTo('user_id', User::class, 'id', ['alias' => 'user']);
    }

    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; return $this; }

    public function getUserId() { return $this->user_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; return $this; }

    public function getPaymentMethod() { return $this->payment_method; }
    public function setPaymentMethod($payment_method) { $this->payment_method = $payment_method; return $this; }

    public function getSubscriptionId() { return $this->subscription_id; }
    public function setSubscriptionId($subscription_id) { $this->subscription_id = $subscription_id; return $this; }

    public function getPlanId() { return $this->plan_id; }
    public function setPlanId($plan_id) { $this->plan_id = $plan_id; return $this; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; return $this; }

    public function getStartsAt() { return $this->starts_at; }
    public function setStartsAt($starts_at) { $this->starts_at = $starts_at; return $this; }

    public function getEndsAt() { return $this->ends_at; }
    public function setEndsAt($ends_at) { $this->ends_at = $ends_at; return $this; }

    public function getTrialEndsAt() { return $this->trial_ends_at; }
    public function setTrialEndsAt($trial_ends_at) { $this->trial_ends_at = $trial_ends_at; return $this; }

    public function getCanceledAt() { return $this->canceled_at; }
    public function setCanceledAt($canceled_at) { $this->canceled_at = $canceled_at; return $this; }

    public function getPayload() { return $this->payload; }
    public function setPayload($payload) { $this->payload = $payload; return $this; }

    public function getCreatedAt() { return $this->created_at; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; return $this; }

    public function getUpdatedAt() { return $this->updated_at; }
    public function setUpdatedAt($updated_at) { $this->updated_at = $updated_at; return $this; }

    public function beforeValidationOnCreate()
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function beforeValidationOnUpdate()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }
}

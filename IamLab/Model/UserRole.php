<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * UserRole pivot model for many-to-many relationship between User and Role
 */
class UserRole extends Model
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
     * @var integer
     * @Column(type="integer", length=32, nullable=false)
     */
    protected $role_id;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $created_at;

    /**
     * @var string
     * @Column(type="datetime", nullable=true)
     */
    protected $updated_at;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     *
     * @return $this
     */
    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * Method to set the value of field role_id
     *
     * @param integer $role_id
     *
     * @return $this
     */
    public function setRoleId(int $role_id): static
    {
        $this->role_id = $role_id;
        return $this;
    }

    /**
     * Method to set the value of field created_at
     *
     * @param string $created_at
     *
     * @return $this
     */
    public function setCreatedAt(string $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Method to set the value of field updated_at
     *
     * @param string $updated_at
     *
     * @return $this
     */
    public function setUpdatedAt(string $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field role_id
     *
     * @return integer
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * Returns the value of field created_at
     *
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * Returns the value of field updated_at
     *
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('user_roles');
        
        // Define relationships
        $this->belongsTo('user_id', User::class, 'id', ['alias' => 'user']);
        $this->belongsTo('role_id', Role::class, 'id', ['alias' => 'role']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return UserRole[]|Model\ResultsetInterface
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return UserRole|Model
     */
    public static function findFirst($parameters = null): mixed
    {
        return parent::findFirst($parameters);
    }
}
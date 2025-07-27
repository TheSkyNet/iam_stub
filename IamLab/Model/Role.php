<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * @method static findFirstByName(string $name)
 * @method static findFirstById(mixed $role_id)
 */
class Role extends Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=32, nullable=false)
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="text", nullable=true)
     */
    protected $description;

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
     * Method to set the value of field name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Method to set the value of field description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;
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
     * Returns the value of field name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the value of field description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
        $this->setSource('roles');
        
        // Define many-to-many relationship with User through user_roles
        $this->hasManyToMany(
            'id',
            UserRole::class,
            'role_id',
            'user_id',
            User::class,
            'id',
            ['alias' => 'users']
        );
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return Role[]|Model\ResultsetInterface
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
     * @return Role|Model
     */
    public static function findFirst($parameters = null): mixed
    {
        return parent::findFirst($parameters);
    }
}
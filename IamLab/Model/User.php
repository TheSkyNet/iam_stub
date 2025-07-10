<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\Model\Validator\Email as Email;

/**
 * @method static findFirstByEmail(string $getEmail)
 */
class User extends Model
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
     * @Column(type="string", length=50, nullable=false)
     */

    protected $email;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $password;
    /**
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $key;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @var string
     * @Column(type="string", length=50, nullable=true)
     */
    protected $oauth_provider;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $oauth_id;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false, default=false)
     */
    protected $email_verified;

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

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): User
    {
        $this->key = $key;
        return $this;
    }

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
     * Method to set the value of a field name
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
     * Method to set the value of field email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Method to set the value of field avatar
     *
     * @param string $avatar
     *
     * @return $this
     */
    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * Returns the value of field avatar
     *
     * @return string
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * Method to set the value of field oauth_provider
     *
     * @param string $oauth_provider
     *
     * @return $this
     */
    public function setOauthProvider(?string $oauth_provider): static
    {
        $this->oauth_provider = $oauth_provider;
        return $this;
    }

    /**
     * Returns the value of field oauth_provider
     *
     * @return string
     */
    public function getOauthProvider(): ?string
    {
        return $this->oauth_provider;
    }

    /**
     * Method to set the value of field oauth_id
     *
     * @param string $oauth_id
     *
     * @return $this
     */
    public function setOauthId(?string $oauth_id): static
    {
        $this->oauth_id = $oauth_id;
        return $this;
    }

    /**
     * Returns the value of field oauth_id
     *
     * @return string
     */
    public function getOauthId(): ?string
    {
        return $this->oauth_id;
    }

    /**
     * Method to set the value of field email_verified
     *
     * @param bool $email_verified
     *
     * @return $this
     */
    public function setEmailVerified(bool $email_verified): static
    {
        $this->email_verified = $email_verified;
        return $this;
    }

    /**
     * Returns the value of field email_verified
     *
     * @return bool
     */
    public function getEmailVerified(): bool
    {
        return (bool)$this->email_verified;
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
     * Returns the value of field created_at
     *
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
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
     * Returns the value of field updated_at
     *
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation()
    {
        return true;
        $this->validate(
            new Email(
                [
                    'field' => 'email',
                    'required' => true,
                ]
            )
        );

        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {

        $this->setSource('user');
    }


    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return User[]|Model\ResultsetInterface
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
     * @return User|Model
     */
    public static function findFirst($parameters = null):mixed
    {
        return parent::findFirst($parameters);
    }

}

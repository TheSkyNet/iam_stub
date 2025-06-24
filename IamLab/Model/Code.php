<?php

namespace IamLab\Model;
use IamLab\Core\API\Entity;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Code extends Entity
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $img;

    /**
     *
     * @var string
     */
    public $body;

    /**
     *
     * @var string
     */
    public $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Code
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Code
    {
        $this->title = $title;
        return $this;
    }

    public function getImg(): string
    {
        return $this->img;
    }

    public function setImg(string $img): Code
    {
        $this->img = $img;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): Code
    {
        $this->body = $body;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Code
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phalcons");
        $this->setSource("code");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Code[]|Code|ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Code|ResultInterface|ModelInterface|null
     */
    public static function findFirst($parameters = null): ?ModelInterface
    {
        return parent::findFirst($parameters);
    }

}

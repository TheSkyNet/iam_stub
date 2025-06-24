<?php
namespace IamLab\Model;
use IamLab\Core\API\Entity;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;
use Phalcon\Mvc\ModelInterface;

class Filepond extends Entity
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
    public $filename;

    /**
     *
     * @var string
     */
    public $filepath;

    /**
     *
     * @var string
     */
    public $extension;

    /**
     *
     * @var string
     */
    public $mimetypes;

    /**
     *
     * @var string
     */
    public $expires_at;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phalcons");
        $this->setSource("filepond");
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Filepond[]|Filepond|ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Filepond|ResultInterface|ModelInterface|null
     */
    public static function findFirst($parameters = null): ?ModelInterface
    {
        return parent::findFirst($parameters);
    }

}

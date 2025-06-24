<?php

namespace IamLab\Model;

use DateTime;
use IamLab\Core\API\Entity;
use IamLab\Core\Enum\PackageType;

class Package extends Entity
{

    protected $casts = [
        'lang'
    ];
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(lang="integer", length=32, nullable=false)
     */
    protected $id;

    /**
     * @var string
     * @Column(lang="string", length=1000, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     * @Column(lang="integer", length=32, nullable=false)
     */
    protected $lang;

    /**
     * @var integer
     * @Column(lang="integer", length=32, nullable=false)
     */
    protected $link;

    /**
     * @var string
     * @Column(lang="string", length=100, nullable=false)
     */
    protected $slug;

    /**
     * @var string
     * @Column(lang="string", length=100, nullable=false)
     */
    protected $description;

    /**
     * @var string
     * @Column(lang="string", length=100, nullable=false)
     */
    protected $type;

    /**
     * @return int
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param int $lang
     *
     * @return Package
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
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
     * Method to set the value of field name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field lang
     *
     * @param integer $lang
     *
     * @return $this
     */
    public function setType($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Method to set the value of field link
     *
     * @param integer $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Method to set the value of field slug
     *
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

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
     * Returns the value of field lang
     *
     * @return PackageType
     */
    public function getType()
    {
        return PackageType::byValue($this->lang);
    }

    /**
     * Returns the value of field link
     *
     * @return integer
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Returns the value of field slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {

        $this->setSource('package');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Package
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function castLang()
    {
      //  dd(PackageType::getValues());
        return PackageType::get($this->lang)->getName();

    }

    public function getUpdatedAt(){
        $dt = $this->updated_at ?? date('Y-m-d H:i:s');
        if ( $dt instanceof DateTime) {
            return $dt;
        }
        return DateTime::createFromFormat('Y-m-d H:i:s', $dt);

    }
}

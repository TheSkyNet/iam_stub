<?php

namespace IamLab\Model;
use DateTime;
use Phalcon\Mvc\Model;
class Project extends Model
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
   * @Column(type="string", length=1000, nullable=false)
   */
  protected $img;

  /**
   * @var string
   * @Column(type="string", length=1000, nullable=false)
   */
  protected $title;

  /**
   * @var string
   * @Column(type="string", length=1000, nullable=false)
   */
  protected $body;
  /**
   * @var string
   * @Column(lang="string", length=100, nullable=false)
   */
  protected $type;

  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * @param string $type
   *
   * @return Post
   */
  public function setType($type)
  {
    $this->type = $type;
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
   * Method to set the value of field img
   *
   * @param string $img
   *
   * @return $this
   */
  public function setImg($img)
  {
    $this->img = $img;

    return $this;
  }

  /**
   * Method to set the value of field title
   *
   * @param string $title
   *
   * @return $this
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Method to set the value of field body
   *
   * @param string $body
   *
   * @return $this
   */
  public function setBody($body)
  {
    $this->body = $body;

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
   * Returns the value of field img
   *
   * @return string
   */
  public function getImg()
  {
    return $this->img;
  }

  /**
   * Returns the value of field title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Returns the value of field body
   *
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * Initialize method for model.
   */
  public function initialize()
  {

    $this->setSource('project');
  }

  public function getUpdatedAt(){
      $dt = $this->updated_at ?? date('Y-m-d H:i:s');
      if ( $dt instanceof DateTime) {
          return $dt;
      }
      return DateTime::createFromFormat('Y-m-d H:i:s', $dt);

  }




}

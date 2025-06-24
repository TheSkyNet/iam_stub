<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;

class SiteSetting extends Model
{
    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'integer';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOL = 'boolean';
    public const TYPE_ARRAY = 'array';
    public const TYPE_JSON = 'json';

    protected $id;
    protected $key;
    protected $value;
    protected $type;
    protected $description;
    protected $created_at;
    protected $updated_at;

    public function initialize()
    {
        $this->setSource('site_settings');
    }

    public function beforeSave()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function getValue()
    {
        switch ($this->type) {
            case self::TYPE_INT:
                return (int) $this->value;
            case self::TYPE_FLOAT:
                return (float) $this->value;
            case self::TYPE_BOOL:
                return (bool) $this->value;
            case self::TYPE_ARRAY:
            case self::TYPE_JSON:
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    public function setValue($value)
    {
        if (in_array($this->type, [self::TYPE_ARRAY, self::TYPE_JSON], true) && !is_string($value)) {
            $this->value = json_encode($value, JSON_THROW_ON_ERROR);
        } else {
            $this->value = (string) $value;
        }
        return $this;
    }

    // Getters and setters
    public function getId() { return $this->id; }
    public function getKey() { return $this->key; }
    public function getType() { return $this->type; }
    public function getDescription() { return $this->description; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }

    public function setKey($key) { $this->key = $key; return $this; }
    public function setType($type) { $this->type = $type; return $this; }
    public function setDescription($description) { $this->description = $description; return $this; }
}
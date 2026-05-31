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

    public function initialize(): void
    {
        $this->setSource('site_settings');
    }

    public function beforeSave(): void
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }

    public function getValue()
    {
        return match ($this->type) {
            self::TYPE_INT => (int) $this->value,
            self::TYPE_FLOAT => (float) $this->value,
            self::TYPE_BOOL => (bool) $this->value,
            self::TYPE_ARRAY, self::TYPE_JSON => json_decode((string) $this->value, true),
            default => $this->value,
        };
    }

    public function setValue($value): static
    {
        if (in_array($this->type, [self::TYPE_ARRAY, self::TYPE_JSON], true) && !is_string($value)) {
            $this->value = json_encode($value, JSON_THROW_ON_ERROR);
        } else {
            $this->value = (string) $value;
        }

        return $this;
    }

    // Getters and setters
    public function getId()
    {
        return $this->id;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setKey($key): static
    {
        $this->key = $key;
        return $this;
    }

    public function setType($type): static
    {
        $this->type = $type;
        return $this;
    }

    public function setDescription($description): static
    {
        $this->description = $description;
        return $this;
    }
}

<?php
namespace IamLab\Core\Enum;

use Exception;
use MabeEnum\Enum;

class PackageType extends Enum
{
  const COMPOSER = 2;
  const NPM      = 1;
  const UNKNOWN  = 0;
  const framework  = 'framework';
  const module  = 'module';
  const tool  = 'tool';
  const enumeration  = 'enumeration';
  const library  = 'library';
  const client  = 'api-client';

  public function toString()
  {
      return match ($this->getValue()) {
          2, 0 => 'COMPOSER',
          1 => 'NPM',
          default => ''
      };
  }
}
<?php

namespace App\Core\Helpers;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Exception;
use SodiumException;

/**
 * @throws Exception
 */
function crypt(string $message): string
{
    $key = Key::loadFromAsciiSafeString(config('app.encryption_key'));
    $plaintext = Crypto::encrypt($message, $key);
    return $plaintext;
}

/**
 * Decrypt a message
 *
 * @param string $encrypted - message encrypted with safeEncrypt()
 * @return string
 * @throws BadFormatException
 * @throws EnvironmentIsBrokenException
 * @throws WrongKeyOrModifiedCiphertextException
 */
function decrypt(string $encrypted ): string
{
    $key = Key::loadFromAsciiSafeString(config('app.encryption_key'));
    $plaintext = Crypto::decrypt($encrypted, $key);
    return $plaintext;
}

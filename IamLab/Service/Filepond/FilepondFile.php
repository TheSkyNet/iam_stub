<?php

namespace IamLab\Service\Filepond;

use RuntimeException;
use SplFileInfo;
use const PATHINFO_FILENAME;

class FilepondFile extends SplFileInfo
{


    /**
     * @param string $file The file name
     */
    public function __construct(string $file)
    {
        parent::__construct($file);
    }


    public function getFilenameWithoutExtension(): string
    {
        $filename = $this->getFilename();

        return pathinfo($filename, PATHINFO_FILENAME);
    }

    /**
     * Returns the contents of the file.
     *
     * @throws RuntimeException
     */
    public function getContents(): string
    {
        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });
        try {
            $content = file_get_contents($this->getPathname());
        } finally {
            restore_error_handler();
        }
        if (false === $content) {
            throw new RuntimeException($error);
        }

        return $content;
    }
}
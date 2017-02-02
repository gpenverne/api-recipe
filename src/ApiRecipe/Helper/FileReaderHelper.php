<?php

namespace ApiRecipe\Helper;

class FileReaderHelper
{
    /**
     * @param  string $filename
     *
     * @return string
     */
    public function readFile($filename)
    {
        return file_get_contents($filename);
    }
}

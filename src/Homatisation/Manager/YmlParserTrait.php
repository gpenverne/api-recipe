<?php

namespace Homatisation\Manager;

trait YmlParserTrait
{
    /**
     * @param string $file
     *
     * @return mixed
     */
    protected function parseYmlFile($file)
    {
        if (!is_file($file)) {
            throw new \Exception(sprintf('Unable to parse %s yml file: file not found', $file));
        }

        $fileContent = file_get_contents($file);

        return Yml::parse($fileContent);
    }
}

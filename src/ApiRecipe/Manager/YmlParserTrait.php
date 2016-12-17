<?php

namespace ApiRecipe\Manager;

use Symfony\Component\Yaml\Yaml;

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

        $array = Yaml::parse($fileContent);

        return $array;
    }
}

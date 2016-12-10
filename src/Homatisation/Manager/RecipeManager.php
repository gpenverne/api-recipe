<?php

namespace Homatisation\Manager;

class RecipeManager
{
    const STATE_ON = 'on';

    const STATE_OFF = 'off';

    /**
     * @var array
     */
    private $infos = [];

    /**
     * @param string $recipeName
     */
    public function __construct($recipeName)
    {
        $this->recipeName = $recipeName;
        $this->importRecipe();
    }

    public function exec($state = null)
    {
    }

    /**
     * @return $this
     */
    protected function importRecipe()
    {
        $expectedFile = sprintf('%s/../../recipes/%s.yml', __DIR__, $this->recipeName);

        $this->infos = $this->parseYmlFile($expectedFile);

        return $this;
    }
}

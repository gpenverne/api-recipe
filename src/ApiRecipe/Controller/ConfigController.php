<?php

namespace ApiRecipe\Controller;

class ConfigController extends Controller
{
    /**
     * @return string
     */
    public function indexAction()
    {
        $this->setResponseFormat('json');

        $keys = ['voices', 'server'];
        $config = [];
        foreach ($keys as $key) {
            $config[$key] = $this->getConfig($key);
        }

        return $config;
    }
}

<?php

namespace Homatisation\Provider;

interface ProviderInterface
{
    /**
     * @param array
     */
    public function __construct($array = []);

    /**
     * @return array
     */
    public function getActions();
}

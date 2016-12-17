<?php

namespace ApiRecipe\Provider;

interface ProviderInterface
{
    /**
     * @param array
     */
    public function __construct($array = []);
}

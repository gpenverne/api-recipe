<?php

namespace ApiRecipe\Collector;

interface CollectorInterface
{
    public function __construct($args = []);

    public function setup();

    public function collect($recipe);
}

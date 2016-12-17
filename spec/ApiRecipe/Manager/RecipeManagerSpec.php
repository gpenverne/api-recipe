<?php

namespace spec\ApiRecipe\Manager;

use ApiRecipe\Manager\RecipeManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipeManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RecipeManager::class);
    }
}

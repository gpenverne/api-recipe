<?php

namespace spec\Homatisation\Manager;

use Homatisation\Manager\RecipeManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipeManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RecipeManager::class);
    }
}

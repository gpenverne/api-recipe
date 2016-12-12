<?php

namespace spec\Homatisation\Controller;

use Homatisation\Controller\RecipesController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipesControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RecipesController::class);
    }
}

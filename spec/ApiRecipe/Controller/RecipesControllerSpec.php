<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\RecipesController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RecipesControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RecipesController::class);
    }
}

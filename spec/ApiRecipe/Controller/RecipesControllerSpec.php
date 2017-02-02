<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\RecipesController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class RecipesControllerSpec extends ObjectBehavior
{
    public function let(Request $request)
    {
        $this->beConstructedWith($request);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(RecipesController::class);
    }
}

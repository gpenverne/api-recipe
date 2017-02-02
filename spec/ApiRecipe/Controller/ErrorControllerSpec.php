<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\ErrorController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class ErrorControllerSpec extends ObjectBehavior
{
    public function let(Request $request)
    {
        $this->beConstructedWith($request);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ErrorController::class);
    }
}

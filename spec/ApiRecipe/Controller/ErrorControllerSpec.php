<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\ErrorController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorController::class);
    }
}

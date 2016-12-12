<?php

namespace spec\Homatisation\Controller;

use Homatisation\Controller\ErrorController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ErrorControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorController::class);
    }
}

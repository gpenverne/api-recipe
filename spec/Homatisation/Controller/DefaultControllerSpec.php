<?php

namespace spec\Homatisation\Controller;

use Homatisation\Controller\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DefaultControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }
}

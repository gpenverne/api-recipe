<?php

namespace spec\Homatisation\Manager;

use Homatisation\Manager\RequestManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RequestManager::class);
    }
}

<?php

namespace spec\ApiRecipe\Manager;

use ApiRecipe\Manager\RoutingManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RoutingManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RoutingManager::class);
    }
}

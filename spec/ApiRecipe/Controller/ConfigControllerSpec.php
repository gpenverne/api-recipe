<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\ConfigController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;

class ConfigControllerSpec extends ObjectBehavior
{
    public function let(Container $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigController::class);
    }
}

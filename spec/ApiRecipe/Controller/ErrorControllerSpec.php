<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\ErrorController;
use ApiRecipe\Helper\HeaderHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;

class ErrorControllerSpec extends ObjectBehavior
{
    public function let(Container $container, HeaderHelper $headerHelper)
    {
        $container->get('helper.header')->willReturn($headerHelper);
        $headerHelper->setHeader('HTTP/1.1 404 Not Found')->willReturn(null);

        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ErrorController::class);
    }
}

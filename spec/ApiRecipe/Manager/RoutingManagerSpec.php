<?php

namespace spec\ApiRecipe\Manager;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use ApiRecipe\Manager\RoutingManager;
use ApiRecipe\Helper\HeaderHelper;
use ApiRecipe\Helper\FileReaderHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RoutingManagerSpec extends ObjectBehavior
{
    public function let(Container $container, FileReaderHelper $fileReaderHelper)
    {
        $container->get('helper.file_reader')->willReturn($fileReaderHelper);
        
        $this->beConstructedWith($container);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(RoutingManager::class);
    }

    public function it_injects_services_to_container($container)
    {
        $container->set('helper.file_reader', Argument::Type(FileReaderHelper::class))->shouldBeCalled();
        $container->set('helper.header', Argument::Type(HeaderHelper::class))->shouldBeCalled();
        $container->set('request', Argument::Type(Request::class))->shouldBeCalled();

        $this->injectServices($container)->shouldReturn($container);
    }
}

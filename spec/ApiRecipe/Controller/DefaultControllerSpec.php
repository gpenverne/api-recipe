<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\DefaultController;
use ApiRecipe\Helper\FileReaderHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Container;

class DefaultControllerSpec extends ObjectBehavior
{
    public function let(Container $container, FileReaderHelper $fileReader)
    {
        $container->get('helper.file_reader')->willReturn($fileReader);

        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }

    public function it_returns_a_static_home_page($fileReader, $container)
    {
        $fileReader->readFile(Argument::Any())->shouldBeCalled();

        $this->indexAction();
    }
}

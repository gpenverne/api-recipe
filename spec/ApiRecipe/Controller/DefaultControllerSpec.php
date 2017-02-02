<?php

namespace spec\ApiRecipe\Controller;

use ApiRecipe\Controller\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerSpec extends ObjectBehavior
{
    public function let(Request $request)
    {
        $this->beConstructedWith($request);
    }
    
    public function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }
}

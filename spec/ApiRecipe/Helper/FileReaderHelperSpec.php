<?php

namespace spec\ApiRecipe\Helper;

use ApiRecipe\Helper\FileReaderHelper;
use PhpSpec\ObjectBehavior;

class FileReaderHelperSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(FileReaderHelper::class);
    }
}

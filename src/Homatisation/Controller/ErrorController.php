<?php

namespace Homatisation\Controller;

class ErrorController implements ControllerInterface
{
    public function __call($method, $args)
    {
        return call_user_func([$this, 'notFoundAction'], $args);
    }

    public function notFoundAction()
    {
        die('not found');
    }
}

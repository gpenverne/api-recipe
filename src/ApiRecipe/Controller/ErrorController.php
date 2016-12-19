<?php

namespace ApiRecipe\Controller;

class ErrorController extends Controller
{
    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func([$this, 'notFoundAction'], $args);
    }

    public function notFoundAction()
    {
        die('not found');
    }
}

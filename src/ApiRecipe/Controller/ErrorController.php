<?php

namespace ApiRecipe\Controller;

class ErrorController extends Controller
{
    const ERROR_HEADER = 'HTTP/1.1 404 Not Found';

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args = [])
    {
        return call_user_func([$this, 'notFoundAction'], $args);
    }

    public function notFoundAction()
    {
        $this->get('helper.header')->setHeader(self::ERROR_HEADER);

        return 'not found';
    }
}

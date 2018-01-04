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
        return $this->notFoundAction();
    }

    public function notFoundAction()
    {
        $this->container->get('helper.header')->setHeader(self::ERROR_HEADER);

        return 'not found';
    }
}

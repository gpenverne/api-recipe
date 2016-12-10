<?php

namespace Homatisation\Manager;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestManager implements ManagerInterface
{
    /**
     * @return Client
     */
    protected function getClient()
    {
        return new Client();
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function request($url)
    {
        $res = $this->getClient()->request(Request::METHOD_GET, $url);
        if (Response::HTTP_OK === $res->getStatusCode()) {
            return true;
        }

        return false;
    }
}

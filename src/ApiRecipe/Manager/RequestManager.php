<?php

namespace ApiRecipe\Manager;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestManager implements ManagerInterface
{
    /**
     * @param string $url
     * @param bool $async
     *
     * @return bool
     */
    public function request($url, $async = false)
    {
        if ($async) {
            $res = $this->asyncRequest(Request::METHOD_GET, $url);
        } else {
            $res = $this->syncRequest(Request::METHOD_GET, $url);
        }

        if (Response::HTTP_OK === $res->getStatusCode()) {
            return true;
        }

        return false;
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        return new Client();
    }

    private function syncRequest($method, $url)
    {
        return $this->getClient()->request(Request::METHOD_GET, $url);
    }

    private function asyncRequest($method, $url)
    {
        return $this->getClient()->getAsync($url);
    }
}

<?php

namespace Homatisation\Provider;

use Homatisation\Manager\RequestManager;

class ApiProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $baseUrl;

    /**
     * @param string $key
     *
     * @return bool
     */
    public function endPoint($endPoint)
    {
        $url = $this->getUrl($endPoint);

        return $this->request($url);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    protected function request($url)
    {
        $requestManager = new RequestManager();

        return $requestManager->request($url);
    }
    /**
     * @param string $key
     *
     * @return string
     */
    protected function getUrl($endPoint)
    {
        return sprintf('%s/%s', $this->baseUrl, $endPoint);
    }
}

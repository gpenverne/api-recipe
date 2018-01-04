<?php

namespace ApiRecipe\Provider;

use ApiRecipe\Manager\RequestManager;

class ApiProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $baseUrl;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'async',
            'sync',
        ];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function sync($endPoint)
    {
        $url = $this->getUrl($endPoint);

        return $this->request($url);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function async($endPoint)
    {
        $consolePath = realpath(sprintf('%s/../../bin/console', __DIR__));
        $command = sprintf('%s actions:exec "%s:sync:%s"', $consolePath, $this->getProviderName(), $endPoint);
        shell_exec(sprintf('%s > /dev/null 2>/dev/null &', $command));
    }

    /**
     * @param string $url
     * @param bool $async
     *
     * @return bool
     */
    protected function request($url, $async = false)
    {
        $requestManager = new RequestManager();

        return $requestManager->request($url, $async);
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

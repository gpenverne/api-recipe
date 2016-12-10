<?php

namespace Homatisation\Manager;

use Doctrine\Common\Inflector\Inflector;
use Homatisation\Provider\ProviderInterface;

class ProviderManager implements ManagerInterface
{
    use YmlParserTrait;

    /**
     * @param string $providerName
     *
     * @return ProviderInterface
     */
    public function getProvider($providerName)
    {
        $providerName = Inflector::Camelize($providerName);
        $expectedClass = sprintf('Homatisation\\Provider\\%sProvider', ucfirst($providerName));
        $config = $this->getConfig($providerName);

        return new $expectedClass($config);
    }

    /**
     * @return $this
     */
    protected function getConfig($providerName)
    {
        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);

        foreach ($config['providers'] as $key => $providerConfig) {
            if (Inflector::Camelize($key) === $providerName) {
                return $providerConfig;
            }
        }

        return [];
    }
}

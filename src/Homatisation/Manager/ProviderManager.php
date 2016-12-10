<?php

namespace Homatisation\Manager;

use Doctrine\Common\Inflector\Inflector;
use Homatisation\Provider\ProviderInterface;

class ProviderManager
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
        $expectedClass = sprintf('Homatisation\\Provider\\%s', $providerName);
        $config = $this->getConfig($providerName);

        return new $expectedClass($config);
    }

    /**
     * @return $this
     */
    protected function getConfig($providerName)
    {
        $expectedFile = sprintf('%s/../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);

        foreach ($config as $key => $providerConfig) {
            if (Inflector::Camelize($key) === $this->$providerName) {
                return $providerConfig;
            }
        }

        return [];
    }
}

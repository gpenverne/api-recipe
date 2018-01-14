<?php

namespace ApiRecipe\Provider;

class KodiProvider implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @var string;
     */
    protected $binary;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'action',
            'number'
        ];
    }

    /**
     * @param string $command
     *
     * @return bool
     */
    public function action($action)
    {
        $fullCommand = sprintf('%s --host=%s --action="%s"', $this->binary, $this->host, $action);

        return (bool) exec($fullCommand);
    }

    public function number($number)
    {
        foreach (str_split($number) as $char) {
            $this->action(sprintf('Number%s', $char));
        }
    }
}

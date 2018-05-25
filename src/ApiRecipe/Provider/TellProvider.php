<?php

namespace ApiRecipe\Provider;

class TellProvider  implements ProviderInterface
{
    use HydratorTrait;

    /**
     * @return array
     */
    public function getActions()
    {
        return [
            'sentence',
        ];
    }

    public function sentence($sentence)
    {
        file_put_contents('/tmp/to-tell', $sentence);

        return $sentence;
    }
}

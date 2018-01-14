<?php

namespace ApiRecipe\Provider;

class LockerProvider implements ProviderInterface
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
            'doNotRepeat',
        ];
    }

    public function doNotRepeat($action)
    {
        $lastActionFile = '/tmp/last-recipe';
        try {
            $f = file_get_contents($lastActionFile);
            if ($action === $f) {
                die('Action locked by file');
            }
        } catch (\Exception $e) {
        }

        file_put_contents($lastActionFile, $action);

        return true;
    }
}

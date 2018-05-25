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
            'lock',
            'unlock',
            'wait'
        ];
    }

    public function wait($lockname)
    {
        $lockFile = $this->getLockFile($lockname);
        while (!is_file($lockFile)) {
            sleep(5);
        }
        return true;
    }

    public function doNotRepeat($action)
    {
        $lastActionFile = '/tmp/last-recipe';
        $lockFile = $this->getLockFile($action);
        if (is_file($lockFile)) {
            die('Action locked by lock file');
        }
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

    public function lock($action)
    {
        $lockFile = $this->getLockFile($action);
        if (!is_file($lockFile)) {
            return (bool) touch($lockFile);
        } else {
            die('locked '.$action);
        }

        return false;
    }

    public function unlock($action)
    {
        $lockFile = $this->getLockFile($action);
        if (is_file($lockFile)) {
            return (bool) unlink($lockFile);
        }

        return false;
    }

    private function getLockFile($action)
    {
        return sprintf('/tmp/lock-%s', $action);
        ;
    }
}

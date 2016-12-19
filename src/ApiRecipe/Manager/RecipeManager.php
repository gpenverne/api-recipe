<?php

namespace ApiRecipe\Manager;

use ApiRecipe\Converter\ArrayToStdClassConverter;

class RecipeManager implements ManagerInterface
{
    use YmlParserTrait;

    private $fileName;

    /**
     * @var int
     */
    private $probability = 0;

    /**
     * @var array
     */
    private $infos = [
        'title' => null,
        'description' => null,
        'picture' => null,
        'actions' => [
            StateManager::STATE_ON => [],
            StateManager::STATE_OFF => [],
            StateManager::STATE_EACH_TIME => [],
        ],
    ];

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @param null|string $recipeName
     */
    public function __construct($recipeName = null)
    {
        if (null !== $recipeName) {
            $this->recipeName = $recipeName;
            $this->infos = $this->loadConfig($recipeName);
        }
    }

    /**
     * @return array
     */
    public function getAll($recipeName = null)
    {
        $recipes = [];
        $recipesFolder = sprintf('%s/../../../recipes', __DIR__);
        $cursor = opendir($recipesFolder);

        while ($f = readdir($cursor)) {
            if ('.' !== $f && '..' !== $f) {
                $recipe = str_replace('.yml', '', $f);
                $recipeInfos = $this->loadConfig($recipe);

                if ($recipe === $recipeName) {
                    return $recipeInfos;
                }

                $recipes[] = $recipeInfos;
            }
        }

        return $recipes;
    }

    /**
     * @param string $recipeName
     *
     * @return \stdClass
     */
    public function get($recipeName)
    {
        return $this->getAll($recipeName);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->infos->title;
    }

    /**
     * @param string $state
     *
     * @return array
     */
    public function exec($state = null)
    {
        $result = [];

        if ($state === null) {
            if ($this->getStateManager()->isOn($this->recipeName)) {
                $state = StateManager::STATE_OFF;
            } else {
                $state = StateManager::STATE_ON;
            }
        }

        $actions = $this->getActions($state);

        foreach ($actions as $action) {
            $actionParameters = explode(':', $action);
            if (2 === count($actionParameters)) {
                list($provider, $method) = $actionParameters;
                $result[$action] = $this->execAction($provider, $method, $state);
            } else {
                list($provider, $method, $argument) = $actionParameters;
                $result[$action] = $this->execAction($provider, $method, $state, $argument);
            }
        }

        $this->getStateManager()->toggleRecipeState($this->recipeName);

        $this->collect();

        return [
            'actions' => $result,
        ];
    }

    public function collect()
    {
        $expectedFile = sprintf('%s/../../../app/config/config.yml', __DIR__);
        $config = $this->parseYmlFile($expectedFile);
        $collectors = isset($config['collectors']) ? $config['collectors'] : [];

        foreach ($collectors as $collector) {
            $collectorClass = $collector['class'];
            $arguments = $collector['arguments'];
            $collector = new $collectorClass($arguments);
            $collector->collect($this->getTitle());
        }
    }

    /**
     * @param int $probability
     */
    public function setProbability($probability)
    {
        $this->probability = $probability;

        return $this;
    }

    /**
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * @param string $provider
     * @param string $method
     * @param string $argument
     *
     * @return bool
     */
    protected function execAction($provider, $method, $state = null, $argument = null)
    {
        $action = new ActionManager($provider, $method, $state, $argument);

        return $action->exec();
    }

    /**
     * @param string $state
     *
     * @return array
     */
    public function getActions($state = null)
    {
        $actions = $this->infos->actions[StateManager::STATE_EACH_TIME];

        if (StateManager::STATE_ON === $state) {
            $actions = array_merge($actions, $this->infos->actions[StateManager::STATE_ON]);
        } elseif (StateManager::STATE_OFF === $state) {
            $actions = array_merge($actions, $this->infos->actions[StateManager::STATE_OFF]);
        }

        return $actions;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        $f = explode('.', $this->fileName);

        return $f[0];
    }

    /**
     * @param string $recipeName
     *
     * @return $this
     */
    protected function loadConfig($recipeName)
    {
        $expectedFile = sprintf('%s/../../../recipes/%s.yml', __DIR__, $recipeName);
        if (!is_file($expectedFile)) {
            $infos = $this->loadConfigFromTitle($recipeName);
        } else {
            $this->fileName = $recipeName;
            $infos = $this->parseYmlFile($expectedFile);
        }

        $infos = array_merge($this->infos, $infos);
        $infos = ArrayToStdClassConverter::convert($infos);

        if (!isset($infos->actions[StateManager::STATE_ON])) {
            $infos->actions[StateManager::STATE_ON] = [];
        }
        if (!isset($infos->actions[StateManager::STATE_OFF])) {
            $infos->actions[StateManager::STATE_OFF] = [];
        }
        if (!isset($infos->actions[StateManager::STATE_EACH_TIME])) {
            $infos->actions[StateManager::STATE_EACH_TIME] = [];
        }

        $infos->state = $this->getStateManager()->getRecipeState($recipeName);
        $infos->url = sprintf('/recipes/exec/%s?format=json&origin=%s', $recipeName, isset($_GET['origin']) ? $_GET['origin'] : 'unknown');
        $infos->visible = true;
        $infos->icon = isset($infos->picture) ? $this->getIconFromPicture($infos->picture) : null;

        return $infos;
    }

    /**
     * @param string $picture
     *
     * @return string
     */
    protected function getIconFromPicture($picture = null)
    {
        $localPicturePath = $this->getLocalCacheImage($picture);
        $rawData = file_get_contents($localPicturePath);

        return base64_encode($rawData);
    }

    /**
     * @param string $picture
     *
     * @return string
     */
    protected function getLocalCacheImage($picture)
    {
        $width = 128;
        $height = 128;

        $localPicturePath = sprintf('%s/../../../web/images/%s', __DIR__, $picture);
        if (!is_file($localPicturePath)) {
            return;
        }
        $targetCacheFile = sprintf('%s/../../../var/cache/%s-%dx%d-png', __DIR__, $picture, $width, $height);
        if (is_file($targetCacheFile)) {
            return $targetCacheFile;
        }
        $extension = explode('.', $localPicturePath);
        $extension = strtolower(end($extension));
        $newImg = imagecreatetruecolor($width, $height);
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
        imagefilledrectangle($newImg, 0, 0, $width, $height, $transparent);

        switch ($extension) {
            case 'png':
                $img = imagecreatefrompng($localPicturePath);
            break;
            case 'jpg':
                $img = imagecreatefromjpeg($localPicturePath);
                break;
            case 'jpeg':
                $img = imagecreatefromjpeg($localPicturePath);
                break;
        }
        $imgInfo = getimagesize($localPicturePath);

        imagecopyresampled($newImg, $img, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

        imagepng($newImg, $targetCacheFile);

        return $targetCacheFile;
    }

    /**
     * @return StateManager
     */
    protected function getStateManager()
    {
        if (null === $this->stateManager) {
            $this->stateManager = new StateManager();
        }

        return $this->stateManager;
    }

    /**
     * @param string $title
     *
     * @return string
     */
    protected function loadConfigFromTitle($title)
    {
        $recipesDir = sprintf('%s/../../../recipes', __DIR__);
        $d = opendir($recipesDir);
        while ($f = readdir($d)) {
            $expectedFile = sprintf('%s/%s', $recipesDir, $f);
            if ('.' !== $f && '..' != $f && is_file($expectedFile)) {
                $infos = $this->parseYmlFile($expectedFile);
                if ($infos['title'] == $title) {
                    $this->fileName = $f;

                    return $infos;
                }
            }
        }

        return null;
    }
}

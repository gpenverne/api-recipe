<?php

namespace Homatisation\Controller;

class DefaultController implements ControllerInterface
{
    public function indexAction()
    {
        $indexHtmlFile = sprintf('%s/../../../web/index.html', __DIR__);

        return file_get_contents($indexHtmlFile);
    }
}

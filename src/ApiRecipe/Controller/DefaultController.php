<?php

namespace ApiRecipe\Controller;

class DefaultController extends Controller
{
    /**
     * @return string
     */
    public function indexAction()
    {
        $indexHtmlFile = sprintf('%s/../../../web/index.html', __DIR__);

        return $this->get('helper.file_reader')->readFile($indexHtmlFile);
    }
}

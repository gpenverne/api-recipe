<?php

namespace ApiRecipe\Helper;

class HeaderHelper
{
    /**
     * @param string $rawHeader
     */
    public function setHeader($rawHeader)
    {
        header($rawHeader);
    }
}

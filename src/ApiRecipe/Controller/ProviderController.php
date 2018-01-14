<?php

namespace ApiRecipe\Controller;

class ProviderController extends Controller
{
    public function execAction($providerName = null)
    {
        $this->setResponseFormat('json');
        $provider = $this->getProviderManager()->getProvider($providerName);
        $command = $this->get('request')->get('command');
        $args = explode(',', $this->get('request')->get('args'));
        call_user_func_array([$provider, $command], $args);
    }
}

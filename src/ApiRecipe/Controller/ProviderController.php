<?php

namespace ApiRecipe\Controller;

class ProviderController extends Controller
{
    public function execAction($providerName = null)
    {
        $this->setResponseFormat('json');
        $provider = $this->getProviderManager()->getProvider($providerName);
        $command = $this->get('request')->get('command');
        $args = $this->get('request')->get('args');
        $d = json_decode(file_get_contents('php://input'), true);

        if (null !== $d) {
            if (isset($d['args'])) {
                $args = trim($d['args']);
            }
        }
        $args = explode(',', $args);

        return call_user_func_array([$provider, $command], $args);
    }
}

<?php

namespace ApiRecipe\Manager;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use ApiRecipe\Controller\ErrorController;

class RoutingManager implements ManagerInterface
{
    const FORMAT_JSON = 'json';

    const FORMAT_HTML = 'html';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string|ControllerInterface
     */
    protected $controller;

    public function __construct()
    {
        ini_set('display_errors', E_ALL);
        $this->request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($this->request);

        $routeInfos = explode('/', $context->getPathInfo());
        $routeInfos = array_slice($routeInfos, 1);

        @list($this->controller, $method, $arg) = $routeInfos;

        if (null === $method) {
            $method = 'index';
        }

        $method = sprintf('%sAction', $method);
        $this->controller = $this->getController();

        if (!method_exists($this->controller, $method)) {
            $this->controller = 'error';
            $this->controller = $this->getController();
        }

        $result = $this->controller->$method($arg);

        $this->sendResponse($result);
    }

    /**
     * @param string $response
     *
     * @return string
     */
    protected function sendResponse($response)
    {
        $request = $this->request;
        if (!isset($_SERVER ['HTTP_USER_AGENT'])) {
            return;
        }
        if (null === $request->query->get('format')) {
            $request->setRequestFormat(self::FORMAT_HTML);
        } else {
            $request->setRequestFormat($request->query->get('format'));
        }

        switch ($request->getRequestFormat()) {
            case self::FORMAT_HTML:
                header('Content-type: text/html');
                echo $response;
            break;
            case self::FORMAT_JSON:
                header('Content-type: application/json');
                echo json_encode($response);
            break;
            default:
                die('This format is not supported. Supported formats are json, html.');
            break;
        }
    }

    /**
     * @return ControllerInterface
     */
    protected function getController()
    {
        if (!$this->controller) {
            $this->controller = 'default';
        }
        $className = sprintf('ApiRecipe\\Controller\\%sController', ucfirst($this->controller));

        if (!class_exists($className)) {
            $className = ErrorController::class;
        }

        return new $className();
    }
}

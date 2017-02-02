<?php

namespace ApiRecipe\Manager;

use ApiRecipe\Controller\ErrorController;
use ApiRecipe\Helper\HeaderHelper;
use ApiRecipe\Helper\FileReaderHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\DependencyInjection\Container;

class RoutingManager implements ManagerInterface
{
    const FORMAT_JSON = 'json';

    const FORMAT_HTML = 'html';

    const FORMAT_JS = 'javascript';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string|ControllerInterface
     */
    protected $controller;

    public function __construct()
    {
        ini_set('display_errors', E_ALL);

        $this->container = new Container();
        $this->request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($this->request);

        $routeInfos = explode('/', $context->getPathInfo());
        $routeInfos = array_slice($routeInfos, 1);

        $this->injectServices($this->container);

        @list($this->controller, $method, $arg) = $routeInfos;

        if (null === $method) {
            $method = 'index';
        }

        $method = sprintf('%sAction', $method);
        $this->controller = $this->getController();

        if (!method_exists($this->controller, $method)) {
            $this->controller = 'error';
            $this->controller = $this->getController($this->request);
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
                header('Content-type: text/html; charset=utf-8');
                echo $response;
            break;
            case self::FORMAT_JSON:
                header('Content-type: application/json; charset=utf-8');
                echo json_encode($response);
            break;
            case self::FORMAT_JAVASCRIPT:
                header('Content-type: text/javascript; charset=utf-8');
                echo $response;
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

        $controller = new $className($this->container);

        return $controller;
    }

    /**
     * @param  Container $container
     *
     * @return Container
     */
    protected function injectServices(Container $container)
    {
        $services = [
            'helper.file_reader' => new FileReaderHelper(),
            'helper.header' => new HeaderHelper,
            'request' => $this->request,
        ];

        foreach ($services as $serviceName => $serviceInstance) {
            $container->set($serviceName, $serviceInstance);
        }

        return $container;
    }
}

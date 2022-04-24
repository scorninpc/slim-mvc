<?php

namespace Slim\Mvc;

class Bootstrap
{
	private $container;
	private $request;
	private $response;
	private $args;
	private $view;
	private $controller;


	/**
	 * 
	 */
	public function __construct(\DI\Container $container, \Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, $args)
	{
		// Store application parameters
		$this->container = $container;
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;

		// Retrieve routes
		$routes = $container->get("routes");

		// Retrieve route name
		$routeContext = \Slim\Routing\RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$name = $route->getName();

		// Retrieve the route infos
		if(!isset($routes[$name])) {
			throw new \Slim\Exception\HttpNotFoundException($request, "Rota não encontrada");
		}
		$route = $routes[$name];

		// Verifica if there is a variable on URI or we need to add the default value
		foreach($route['defaults'] as $arg_name => $arg_default) {
			if(!isset($this->args[$arg_name])) {
				$this->args[$arg_name] = $arg_default;
			}
		}

		// Start the view
		$this->view = new \Slim\Mvc\View();

		// Verify if there is a module variable
		$module = "";
		if(isset($this->args['module'])) {
			$module =  ucfirst($this->args['module']) . "\\";
		}

		// Verify if controller exists with and without module (because the module can be passed as simple arg, and not as module)
		$controllerName = "\\Application\\" . $module . "Controllers\\" . strtolower($this->args['controller']) . "Controller";
		if(!class_exists($controllerName)) {
			die($controllerName);
			$controllerName = "\\Application\\Controllers\\" . strtolower($this->args['controller']) . "Controller";

			// Create controller object
			if(!class_exists($controllerName)) {
				throw new \Slim\Exception\HttpNotFoundException($request, "Controlador não encontrado");
			}
		}

		$this->controller = new $controllerName($this->view, $this->container, $this->request, $this->response, $this->args);

		// Create and call action
		$action = $this->args['action'] . "Action";
		if(!is_callable([$this->controller, $action])) {
			throw new \Slim\Exception\HttpNotFoundException($request, "Action não encontrada");
		}
		$ret = $this->controller->$action();
	}

	/**
	 * 
	 */
	public function getResponse() : \Psr\Http\Message\ResponseInterface
	{
		// Parse all the view
		return $this->controller->run();
	}
}
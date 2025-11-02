<?php

namespace Slim\Mvc;

/**
 * custom request, para adicionar uns metodos mais amigaveis
 */
class Request extends \Slim\Psr7\Request
{
	/**
	 * armazena os parametros
	 */
	protected $params = [];

	/**
	 * original request
	 */
	protected $original_request;

	/**
	 * recria o request para usar o custom
	 */
	public static function fromRequest(\Slim\Psr7\Request $request, \DI\Container $container, $args)
	{
		// recria a interface de headers
		$headers = new \Slim\Psr7\Headers();
		foreach ($request->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				$headers->addHeader($name, $value);
			}
		}

		// retorna o novo request
		$object = new self(
			$request->getMethod(),
			$request->getUri(),
			$headers,
			$request->getCookieParams(),
			$request->getServerParams(),
			$request->getBody(),
			$request->getUploadedFiles(),
		);

		// armazena o original request (alguns metodos não funcionam, como getParsedBody)
		$object->setOriginalRequest($request);

		// retorna as rotas
		$routes = $container->get("routes");

		// recupera o nome da rota
		$routeContext = \Slim\Routing\RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$name = $route->getName();

		// recupera as informações da rota
		if(!isset($routes[$name])) {
			throw new \Slim\Exception\HttpNotFoundException($request, "Rota não encontrada");
		}
		$route = $routes[$name];

		// recupera os dados do post, se houver (como executado primeiro, se tiver o mesmo parametro no GET, o GET sobreescreve)
		$params = (array)$request->getParsedBody();
		$args = array_merge($params, $args);

		// seta os parametros padrões da rota
		foreach($route['defaults'] as $arg_name => $arg_default) {
			if(!isset($args[$arg_name])) {
				$args[$arg_name] = $arg_default;
			}
		}

		// recupera os parametros da url
		$params = $args['params'];
		if($params) {
			unset($args['params']);
			$params_parts = explode("/", $params);
			for($i=0; $i<count($params_parts); $i+=2) {
				$args[$params_parts[$i]] = $params_parts[$i+1];
			}
		}

		// armazena os parametros
		$object->setParams($args);

		// retorna o objeto criado
		return $object;
	}
	
	/**
	 * seta o request original
	 */
	public function setOriginalRequest($request) 
	{
		$this->original_request = $request;
	}

	/**
	 * seta os parametros
	 */
	public function setParams($params)
	{
		$this->params = $params;
	}

	/**
	 * recupera os parametros
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * recupera o parametro do post
	 */
	public function getPostParam($name, $default=NULL)
	{
		$params = (array)$this->original_request->getParsedBody();
		if(!isset($params[$name])) {
			return $default;
		}

		return $params[$name];
	}

	/**
	 * verifica se a requisição é um post
	 */
	public function isPost()
	{
		$method = parent::getMethod();
		return $method == "POST";
	}

	/**
	 * verifica se a requisição é um put
	 */
	public function isPut()
	{
		$method = parent::getMethod();
		return $method == "PUT";
	}

	/**
	 * verifica se a requisição é um get
	 */
	public function isGet()
	{
		$method = parent::getMethod();
		return $method == "GET";
	}

	/**
	 * verifica se a requisição é um option
	 */
	public function isOption()
	{
		$method = parent::getMethod();
		return $method == "OPTION";
	}

	/**
	 * verifica se a requisição é um delete
	 */
	public function isDelete()
	{
		$method = parent::getMethod();
		return $method == "DELETE";
	}

	/**
	 * verifica se é uma requisição ajax
	 */
	public function isAjax()
	{
		return strtolower(parent::getHeaderLine("X-Requested-With")) === "xmlhttprequest";
	}
}
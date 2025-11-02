<?php

namespace Slim\Mvc;

class Controller
{
	public $view;
	protected $args;
	protected $request;
	protected $response;
	protected $container;

	/**
	 * @todo parametrize template.tpl
	 */
	public function __construct($view, $container, $request, $response, $args)
	{
		$this->container = $container;
		$this->response = $response;
		$this->request = $request;
		$this->view = $view;
		
		$params = $args['params'];
		if($params) {
			unset($args['params']);
			$params_parts = explode("/", $params);
			for($i=0; $i<count($params_parts); $i+=2) {
				$args[$params_parts[$i]] = $params_parts[$i+1];
			}
		}
		$this->args = $args;
	}

	/**
	 * get container
	 */
	public function getContainer()
	{
		return $this->container;
	}
	
	/**
	 * get request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * 
	 */
	public function run()
	{
		// Get the template file set
		$templateFile = $this->view->getTemplateFile();
		if(!isset($templateFile)) {

			// Verify if there is a module variable
			$module = "";
			$module_name = $this->getParam("module");
			if($module_name !== NULL) {
				// Get config
				$config = $this->container->get("config");
				$module =  realpath($config['application']['modules_location']) . "/" . ucfirst($this->getParam("module"));
			}
			else {
				$module = APPLICATION_PATH;
			}

			// If not manual set, look if it is disabled, to get action template
			if($this->view->isTemplateDisabled()) {
				$templateFile = $module . "/Views/" . $this->getParam("controller") . "/" . $this->getParam("action") . ".tpl";
			}

			// If its not disabled, get defined template file
			else {
				$templateFile = $module . "/Views/layouts/template.tpl";

				// Render content template, and assign to template file
				$contentFile = $module . "/Views/" . $this->getParam("controller") . "/" . $this->getParam("action") . ".tpl";
				if(!file_exists($contentFile)) {
					throw new \Exception("Arquivo " . $contentFile . " não encontrado", 500);
				}
				$content = $this->container->get("view")->fetch($contentFile, $this->view->getVars());
				$this->view->assign("layout_content", $content);
			}
		}

		if(!file_exists($templateFile)) {
			throw new \Exception("Arquivo " . $templateFile . " não encontrado", 500);
		}

		return $this->container->get("view")->render($this->response, $templateFile, $this->view->getVars());
	}

	/**
	 * 
	 */
	public function getParam($name, $default=NULL)
	{
		// Verifica o get
		if(!isset($this->args[$name])) {

			// Verifica o post
			$post_params = (array)$this->request->getParsedBody();
			if(!isset($post_params[$name])) {
				// Se nao tem, da o explode

				return $default;
			}


			return $post_params[$name];
		}

		return $this->args[$name];
	}

	/**
	 * 
	 */
	public function getPost($name, $default=NULL)
	{
		$params = (array)$this->request->getParsedBody();

		if(!isset($params[$name])) {
			return $default;
		}

		return $params[$name];
	}
}

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
		$this->request = $request;
		$this->response = $response;
		$this->args = $args;
		$this->view = $view;
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
				$module =  "/" . ucfirst($this->getParam("module"));
			}

			// If not manual set, look if it is disabled, to get action template
			if($this->view->isTemplateDisabled()) {
				$templateFile = APPLICATION_PATH . $module . "/Views/" . $this->getParam("controller") . "/" . $this->getParam("action") . ".tpl";
			}

			// If its not disabled, get defined template file
			else {
				$templateFile = APPLICATION_PATH . $module . "/Views/layouts/template.tpl";

				// Render content template, and assign to template file
				$contentFile = APPLICATION_PATH . $module . "/Views/" . $this->getParam("controller") . "/" . $this->getParam("action") . ".tpl";
				if(!file_exists($contentFile)) {
					throw new \Slim\Exception\HttpNotFoundException($this->request, "Arquivo " . $contentFile . " não encontrado");
				}
				$content = $this->container->get("view")->fetch($contentFile, $this->view->getVars());
				$this->view->assign("layout_content", $content);
			}
		}

		if(!file_exists($templateFile)) {
			throw new \Slim\Exception\HttpNotFoundException($this->request, "Arquivo " . $templateFile . " não encontrado");
		}

		return $this->container->get("view")->render($this->response, $templateFile, $this->view->getVars());
	}

	/**
	 * 
	 */
	public function getParam($name, $default=NULL)
	{
		if(!isset($this->args[$name])) {
			return $default;
		}

		return $this->args[$name];
	}
}
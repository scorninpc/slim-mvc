<?php

namespace Slim\Mvc;

class ViewHelpers
{
	protected $request;

	public function __construct($request)
	{
		$this->request = $request;
	}

	public function __call($name, $args)
	{
		// se houver modulo
		$module = \Slim\Mvc\Factory::get("requestModuleName");
		
		// recupera o helper do projeto com o modulo
		$helperName = "\\Application\\" . $module . "Helpers\\View\\" . ucfirst($name);
		if(!class_exists($helperName)) {

			// recupera o helper do projeto sem o modulo
			$helperName = "\\Application\\Helpers\\View\\" . ucfirst($name);
			if(!class_exists($helperName)) {
				throw new \Exception("View helper \"" . $name . "\" not found");
			}
		}
		
		// cria o objeto
		$config = \Slim\Mvc\Factory::get("config");
		$helper = new $helperName($config);

		// e chama
		return call_user_func_array([$helper, "call"], $args);
	}
}

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
		// Recupera o helper do projeto
		$helperName = "\\Application\\Helpers\\View\\" . ucfirst($name);
		
		if(!class_exists($helperName)) {
			throw new \Exception("Helper not found");
		}
		
		// Cria o objeto
		$helper = new $helperName($config);

		// E chama
		return call_user_func_array([$helper, "call"], $args);
	}
}

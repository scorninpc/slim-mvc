<?php

namespace Slim\Mvc;

/**
 * custom request, para adicionar uns metodos mais amigaveis
 */
class Request extends \Slim\Psr7\Request
{
	/**
	 * recria o request para usar o custom
	 */
	public static function fromRequest(\Slim\Psr7\Request $request)
	{

		// recria a interface de headers
		$headers = new \Slim\Psr7\Headers();
		foreach ($request->getHeaders() as $name => $values) {
			foreach ($values as $value) {
				$headers->addHeader($name, $value);
			}
		}

		// retorna o novo request
		return new self(
			$request->getMethod(),
			$request->getUri(),
			$headers,
			$request->getCookieParams(),
			$request->getServerParams(),
			$request->getBody(),
			$request->getUploadedFiles(),
		);
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
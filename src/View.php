<?php

namespace Slim\Mvc;

class View
{
	protected $vars;
	protected $template_file;
	protected $content_file;
	protected $view;
	protected $template_disabled = FALSE;

	/**
	 *
	 */
	public function disableTemplate()
	{
		$this->template_disabled = TRUE;
	}

	/**
	 *
	 */
	public function isTemplateDisabled()
	{
		return $this->template_disabled;
	}

	/**
	 * Recupera caminho do template
	 */
	public function getTemplateFile()
	{
		return $this->template_file;
	}

	/**
	 * Seta caminho do template
	 */
	public function setTemplateFile($template)
	{
		$this->template_file = $template;
	}

	/**
	 * Assina uma variavel pro template
	 */
	public function assign($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Assina uma variavel pro template
	 */
	public function __set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	/**
	 * Recupera uma variavel assinada
	 */
	public function __get($name)
	{
		return $this->vars[$name];
	}

	/**
	 * Recupera as variaveis assinadas
	 */
	public function getVars()
	{
		return $this->vars;
	}
}
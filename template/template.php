<?php

/**
 * Riff PHP Library
 *
 * This source file is part of Riff, a stand-alone PHP library.
 *
 * @package     Riff
 * @subpackage  RiffTemplate
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

/**
 * The templating engine
 *
 * @package     Riff
 * @subpackage  RiffTemplate
 *
 * @author      Pieter Beulque <pieterbeulque@gmail.com>
 * @since       0.1.1
 *
 */

class RiffTemplate
{

	private $variables;

	public function __construct()
	{
		$this->variables = array();

	}

	public function assign($key, $value)
	{
		$this->variables[$key] = (string) $value;
	}

	public function display($template)
	{

	}

}
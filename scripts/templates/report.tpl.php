<?php

class {{ controller_name }} extends {{ base_controller_name }}
{
	/**
	 *
	 */
	public function indexAction()
	{
		parent::show('{{ lower(module_name) }}/index', $data);
	}

	/**
	 *
	 */
	public function exportAction()
	{
		// TODO: download csv
	}

	/**
	 *
	 */
	private function binds()
	{
		return array();
	}
}




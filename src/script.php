<?php

defined('_JEXEC') or die;

class PlgSystemTemplateCompanionInstallerScript
{
	/**
	 * @var	$db		JDatabase
	 */
	public $db              = null;

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	public function postflight($route, $parent)
	{
		if ($route === 'install') {
			$this->db      = JFactory::getDbo();

			$query = $this->db->getQuery(true)
				->update('#__extensions')
				->set($this->db->quoteName('enabled') . ' = 1')
				->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
				->where($this->db->quoteName('element') . ' = ' . $this->db->quote('templatecompanion'))
				->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('system'));
				$this->db->setQuery($query);
				$this->db->execute();
		}
	}
}

<?php
/**
 * @package		System Plugin - Less Template Companion, an automatic Less compiler for developers and users
 * @version		0.1.0-alpha.6
 * @author		Gijs Lamon
 * @copyright	(C) 2017 Gijs Lamon
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Based on the works of Andreas Tasch (https://github.com/ndeet/plg_system_less) and Thomas Hunziker (https://github.com/Bakual/Allrounder)
 */

// no direct access
defined('_JEXEC') or die();

/**
 * Plugin checks and compiles updated .less files on page load and on template style save.
 * Give your users the ability to set variables as template parameter and removing the need to manually compile .less files ever again.
 *
 * JLess compiler uses lessphp; see http://leafo.net/lessphp/
 *
 * @since  1.0
 */
class plgSystemLessTemplateCompanion extends JPlugin
{
	/**
	 * @var $app
	 */
	protected $app;
	protected $lessFile		= '';
	protected $cssFile		= '';
	protected $frontend		= false;
	protected $backend		= false;
	protected $templatePath	= '';

	/**
	 * override constructor to load classes as soon as possible
	 * @param $subject
	 * @param $config
	 */
	public function __construct(&$subject, $config)
	{
		// trigger parent constructor first so params get set
		parent::__construct($subject, $config);

		// set app
		$this->app = JFactory::getApplication();
	}

	/**
	 * Compile .less files on change
	 */
	function onBeforeRender()
	{
		$table		= $this->app->getTemplate(true);

		// 0 = frontend only
		// 1 = backend only
		// 2 = front + backend
		$mode = $this->params->get('mode', 0);

		// Convert the template params to an object.
		// TODO: load template params
		if (is_string($table->params))
		{
			$registry = new \Joomla\Registry\Registry;
			$registry->loadString($table->params);
			$table->params = $registry;
		}

		//only execute frontend
		if ($this->app->isSite() && ($mode == 0 || $mode == 2))
		{
			$this->templatePath = JPATH_BASE . DIRECTORY_SEPARATOR . 'templates/' . $this->app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$this->lessFile = $this->templatePath . $this->params->get('lessfile', 'less/template.less');

			//destination .css file, default css/template.css
			$this->cssFile = $this->templatePath . $this->params->get('cssfile', 'css/template.css');

		}

		//execute backend
		if ($this->app->isAdmin() && ($mode == 1 || $mode == 2))
		{
			$this->templatePath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'templates/' . $this->app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$this->lessFile = $this->templatePath . $this->params->get('admin_lessfile', 'less/template.less');

			//destination .css file, default css/template.css
			$this->cssFile = $this->templatePath . $this->params->get('admin_cssfile', 'css/template.css');

		}

		//check if .less file exists and is readable
		if (is_readable($this->lessFile))
		{
			//initialise less compiler
			try
			{
				$this->compileLess($table);
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(JText::_($e->getMessage(), 'error'));
			}
		}

		return false;
	}

	/**
	 * Compile .less files on template style change
	 *
	 * @param   string  $context  Context of the data
	 * @param   object  $table    Table object
	 * @param   bool    $isNew    New entry or edit
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onExtensionAfterSave($context, $table, $isNew)
	{
		if ($context != 'com_templates.style' && $context != 'com_advancedtemplates.style')
		{
			return;
		}

		// Convert the params to an object.
		if (is_string($table->params))
		{
			$registry = new \Joomla\Registry\Registry;
			$registry->loadString($table->params);
			$table->params = $registry;
		}

		// Check if parameter "useLESS" is set
		if (!$table->params->get('useLESS'))
		{
			return;
		}

		// Path to less file
		$client       		= ($table->client_id) ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$this->templatePath = $client . '/templates/' . $table->template;
		$this->lessFile     = $this->templatePath . '/less/template.less';
		$this->cssFile 		= $this->templatePath . '/css/template' . $table->id . '.css';

		// Check if .less file exists and is readable
		if (is_readable($this->lessFile))
		{
			$this->compileLess($table, $this->templatePath);
		}
	}
	
	protected function compileLess($table)
	{
		$less = new JLess;

		if ($table->params->get('cssCompress', 0))
		{
			$less->setFormatter('compressed');
		}
		else
		{
			// Joomla way
			$formatter = new JLessFormatterJoomla;
			$less->setFormatter($formatter);
		}

		$params_array = $table->params->toArray();

		// Unset the some parameter as it breaks the compiler if it starts with a dot (.) or hash (#).
		$unsets = array(
					'customCssCode',
					'textLogo',
					'slogan',
					'copyText',
				);

		foreach ($unsets as $unset)
		{
			if (array_key_exists($unset, $params_array))
			{
				unset($params_array[$unset]);
			}
		}

		// Sanitising params for LESS
		foreach ($params_array as &$value)
		{
			// Trim whitespaces
			$value = trim($value);

			// Adding quotes around variable so it's threaten as string if a slash is in it.
			if (strpos($value, '/') !== false)
			{
				$value = '"' . $value . '"';
			}

			// Quoting empty values as they break the compiler
			if ($value == '')
			{
				$value = '""';
			}
		}

		$less->setVariables($params_array);

		$less->setImportDir(array($templatePath . '/less/'));
		$lessString = file_get_contents($this->lessFile);

		// Check for custom files
		$lessString = $this->checkCustomFiles($lessString);

		try
		{
			$cssString = $less->compile($lessString);
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage('lessphp error: ' . $e->getMessage(), 'warning');
		}

		JFile::write($this->cssFile, $cssString);

		$this->loadLanguage();
		$this->app->enqueueMessage(JText::sprintf('PLG_SYSTEM_LESSALLROUNDER_SUCCESS', $this->cssFile), 'message');
	}
	
	protected function checkCustomFiles($lessString)
	{
		if (is_readable($this->templatePath . '/less/custom.less'))
		{
			$lessString .= file_get_contents($this->templatePath . '/less/custom.less');
		}

		if (is_readable($this->templatePath . '/css/custom.css'))
		{
			$lessString .= file_get_contents($this->templatePath . '/css/custom.css');
		}
		
		return $lessString;
	}
}

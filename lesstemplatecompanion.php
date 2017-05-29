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
		//path to less file
		$lessFile 	= '';
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
			$templatePath = JPATH_BASE . DIRECTORY_SEPARATOR . 'templates/' . $this->app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$lessFile = $templatePath . $this->params->get('lessfile', 'less/template.less');

			//destination .css file, default css/template.css
			$cssFile = $templatePath . $this->params->get('cssfile', 'css/template.css');

		}

		//execute backend
		if ($this->app->isAdmin() && ($mode == 1 || $mode == 2))
		{
			$templatePath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'templates/' . $this->app->getTemplate() . DIRECTORY_SEPARATOR;

			//entrypoint for main .less file, default is less/template.less
			$lessFile = $templatePath . $this->params->get('admin_lessfile', 'less/template.less');

			//destination .css file, default css/template.css
			$cssFile = $templatePath . $this->params->get('admin_cssfile', 'css/template.css');

		}

		//check if .less file exists and is readable
		if (is_readable($lessFile))
		{
			//initialise less compiler
			try
			{
				$this->compileLess($table, $templatePath, $lessFile, $cssFile);
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(JText::_($e->getMessage(), 'error'));
			}
		}

		return false;
	}

	/**
	 * Remove template.css from document html
	 * Stylesheet href may include query string, ie template.css?1234567890123
	 * @author   piotr-cz
	 *
	 * @return   void
	 */
	public function removeCss()
	{
		// Initialise variables
		$doc = JFactory::getDocument();
		$body = JResponse::getBody();

		// Get Uri to template stylesheet file
		$templateUri = JUri::base(true) . '/templates/' . $doc->template . '/';
		$cssUri = $templateUri . $this->params->get('cssfile', 'css/template.css');

		// Replace line with link element and path to stylesheet file
		$replaced = preg_replace( '~(\s*?<link.* href=".*?' . preg_quote($cssUri) . '(?:\?.*)?".*/>)~', '', $body, -1, $count);

		if ($count)
		{
			JResponse::setBody($replaced);
		}

		return;
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
		$client       = ($table->client_id) ? JPATH_ADMINISTRATOR : JPATH_SITE;
		$templatePath = $client . '/templates/' . $table->template;
		$lessFile     = $templatePath . '/less/template.less';
		$cssFile      = $templatePath . '/css/template' . $table->id . '.css';

		// Check if .less file exists and is readable
		if (is_readable($lessFile))
		{
			$this->compileLess($table, $templatePath, $lessFile, $cssFile);
		}
	}
	
	public function compileLess($table, $templatePath, $lessFile, $cssFile)
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
		$lessString = file_get_contents($lessFile);

		// Check for custom files
		if (is_readable($templatePath . '/less/custom.less'))
		{
			$lessString .= file_get_contents($templatePath . '/less/custom.less');
		}

		if (is_readable($templatePath . '/css/custom.css'))
		{
			$lessString .= file_get_contents($templatePath . '/css/custom.css');
		}

		try
		{
			$cssString = $less->compile($lessString);
		}
		catch (Exception $e)
		{
			$this->app->enqueueMessage('lessphp error: ' . $e->getMessage(), 'warning');
		}

		JFile::write($cssFile, $cssString);

		$this->loadLanguage();
		$this->app->enqueueMessage(JText::sprintf('PLG_SYSTEM_LESSALLROUNDER_SUCCESS', $cssFile), 'message');
	}
}

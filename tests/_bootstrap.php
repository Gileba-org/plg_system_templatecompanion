<?php
/**
 * Prepares a minimalist framework for unit testing.
 *
 * Joomla is assumed to include the /unittest/ directory.
 * eg, /path/to/joomla/unittest/
 *
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://www.phpunit.de/manual/current/en/installation.html
 */

/**
 * Mock for the global application exit.
 *
 * @param   mixed  $message  Exit code or string. Defaults to zero.
 *
 * @return  void
 */
function jexit($message = 0)
{
}

define('_JEXEC', 1);

// Fix magic quotes.
ini_set('magic_quotes_runtime', 0);

// Maximise error reporting.
ini_set('zend.ze1_compatibility_mode', '0');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set fixed precision value to avoid round related issues
ini_set('precision', 14);

/*
 * Ensure that required path constants are defined.  These can be overridden within the phpunit.xml file
 * if you chose to create a custom version of that file.
 */
if (!defined('JPATH_TESTS'))
{
	define('JPATH_TESTS', realpath(dirname(dirname(__DIR__)) . '/joomla-cms/tests/unit'));
}

if (!defined('JPATH_TEST_DATABASE'))
{
	define('JPATH_TEST_DATABASE', JPATH_TESTS . '/stubs/database');
}

if (!defined('JPATH_TEST_STUBS'))
{
	define('JPATH_TEST_STUBS', JPATH_TESTS . '/stubs');
}

if (!defined('JPATH_PLATFORM'))
{
	define('JPATH_PLATFORM', realpath(dirname(dirname(__DIR__)) . '/joomla-cms/libraries'));
}

if (!defined('JPATH_LIBRARIES'))
{
	define('JPATH_LIBRARIES', realpath(dirname(dirname(__DIR__)) . '/joomla-cms/libraries'));
}

if (!defined('JPATH_BASE'))
{
	define('JPATH_BASE', realpath(dirname(dirname(__DIR__)) . '/joomla-cms'));
}

if (!defined('JPATH_ROOT'))
{
	define('JPATH_ROOT', realpath(JPATH_BASE));
}

if (!defined('JPATH_CACHE'))
{
	define('JPATH_CACHE', JPATH_BASE . '/cache');
}

if (!defined('JPATH_CONFIGURATION'))
{
	define('JPATH_CONFIGURATION', JPATH_BASE);
}

if (!defined('JPATH_SITE'))
{
	define('JPATH_SITE', JPATH_ROOT);
}

if (!defined('JPATH_ADMINISTRATOR'))
{
	define('JPATH_ADMINISTRATOR', JPATH_ROOT . '/administrator');
}

if (!defined('JPATH_INSTALLATION'))
{
	define('JPATH_INSTALLATION', JPATH_ROOT . '/installation');
}

if (!defined('JPATH_MANIFESTS'))
{
	define('JPATH_MANIFESTS', JPATH_ADMINISTRATOR . '/manifests');
}

if (!defined('JPATH_PLUGINS'))
{
	define('JPATH_PLUGINS', JPATH_BASE . '/plugins');
}

if (!defined('JPATH_THEMES'))
{
	define('JPATH_THEMES', JPATH_BASE . '/templates');
}

if (!defined('JDEBUG'))
{
	define('JDEBUG', false);
}

@include realpath(dirname(__DIR__) . '/vendor/autoload.php');

// Import the platform in legacy mode.
require_once JPATH_PLATFORM . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Register the core Joomla test classes.
JLoader::registerPrefix('Test', JPATH_TESTS . '/core');

// Register the deprecation handler
TestHelper::registerDeprecationHandler();

// Register the deprecation logger
TestHelper::registerDeprecationLogger();

// Register the logger if enabled
TestHelper::registerLogger();

<?php
use PHPUnit\Framework\TestCase;

require_once('plg_system_templatecompanion/src/templatecompanion.php');

class PlgSystemTemplateCompanionTest extends TestCaseDatabase
{
	protected $class;
	
    public function setUp() {
		JFactory::$application = $this->getMockCmsApp();
		JFactory::$session = $this->getMockSession();

		/**
		 * Create a mock dispatcher instance
		 *
		 * @var $dispatcher Mock_JEventDispatcher_f5646d4b e.g
		 */
		$dispatcher = TestCaseDatabase::getMockDispatcher();

		$plugin = array(
			'name'   => 'templatecompanion',
			'type'   => 'System',
			'params' => new \JRegistry
		);

		$this->class = new PlgSystemTemplateCompanion($dispatcher, $plugin);
	}
	
	// Test correct behavior of setLessVariable on an array with a single correct element
	public function testParseVariable()
	{
		$testArray = array('tc_main-color' => '#123456');
		$resultArray = $this->class->setLessVariables($testArray);
		$this->assertEquals($resultArray , array('main-color' => '#123456'));
	}

	// Test correct behavior of setLessVariable on an array with a single incorrect element
	public function testDoNotParseVariable()
	{
		$testArray = array('main-color' => '#123456', 'test' => 'empty');
		$resultArray = $this->class->setLessVariables($testArray);
		$this->assertEquals($resultArray , array());
	}
	
	// Test correct behavior of setLessVariable on an array with different elements, including slashes
	public function testQuoteSlash() {
		$testArray = array('tc_slash' => 'Joomla/Test');
		$resultArray = $this->class->setLessVariables($testArray);		
		$this->assertEquals($resultArray , array('slash' => '"Joomla/Test"'));
	}
	
	// Test correct behavior of setLessVariable on an array with different elements, including slashes
	public function testQuoteEmptyValue() {
		$testArray = array('tc_empty' => '');
		$resultArray = $this->class->setLessVariables($testArray);		
		$this->assertEquals($resultArray , array('empty' => '""'));
	}
	
	// Test correct behavior of setLessVariable on an array with different elements
	public function testOnlyParseValidVariables() {
		$testArray = array('tc_main-color' => '#123456', 'test' => 'empty', 'tc_slash' => 'Joomla/Test', 'tc_empty' => '');
		$resultArray = $this->class->setLessVariables($testArray);		
		$this->assertEquals($resultArray , array('main-color' => '#123456', 'slash' => '"Joomla/Test"', 'empty' => '""'));
	}
	
	// Test correct behavior of onBeforeRender
	public function testOnBeforeRenderWithoutWriteAccess() {
		// Joomla standard testcase doesn't have a template set as default, so onBeforeRender cannot find the less source file
		$this->assertEquals('unreadable', $this->class->onBeforeRender());
		
		/*
		 * TODO
		 *  1. Make sure test does get access to a less file
		 *  2. Switch modes and check versus client/admin
		 *  3. Check if CompileLess succeeds
		 *  4. Make sure CompileLess fails
		 *
		 */
		
	}
}
?>
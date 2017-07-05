<?php
use PHPUnit\Framework\TestCase;

require_once('plg_system_lesstemplatecompanion/src/lesstemplatecompanion.php');

class plgSystemlessTemplateCompanionTest extends TestCaseDatabase
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
			'name'   => 'lesstemplatecompanion',
			'type'   => 'System',
			'params' => new \JRegistry
		);

		$this->class = new PlgSystemLessTemplateCompanion($dispatcher, $plugin);
	}
	
	// Test correct behavior of setLessVariable on an array with a single correct element
	public function testParseVariable()
	{
		$test_array = array('ltc_main-color' => '#123456');
		$result_array = $this->class->setLessVariables($test_array);
		$this->assertEquals($result_array , array('main-color' => '#123456'));
	}

	// Test correct behavior of setLessVariable on an array with a single incorrect element
	public function testDoNotParseVariable()
	{
		$test_array = array('main-color' => '#123456', 'test' => 'empty');
		$result_array = $this->class->setLessVariables($test_array);
		$this->assertEquals($result_array , array());
	}
	
	// Test correct behavior of setLessVariable on an array with different elements
	public function testOnlyParseValidVariables() {
		$test_array = array('ltc_main-color' => '#123456', 'test' => 'empty');
		$result_array = $this->class->setLessVariables($test_array);		
		$this->assertEquals($result_array , array('main-color' => '#123456'));
	}
	
	// Test correct behavior of onBeforeRender
	public function testOnBeforeRender() {
		$this->assertFalse($this->class->onBeforeRender());
	}


}
?>
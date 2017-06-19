<?php
use PHPUnit\Framework\TestCase;

require_once('plg_system_less_template_companion/src/plgSystemLessTemplateCompanion.php');

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
	
	public function testSetLessVariables()
	{
		$test_array = array('ltc_main-color' => '#123456', 'test' => 'empty');
		
		$result_array = $this->class->setLessVariables($test_array);
		
		$this->assertEquals($result_array , array('main-color' => '#123456'));
	}
}
?>
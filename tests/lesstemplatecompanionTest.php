<?php
use PHPUnit\Framework\TestCase;

require_once('./src/lesstemplatecompanion.php');

class plgSystemLessTemplateCompanionTest extends TestCase
{
	protected $plugin;
	
	public function setup() {
		$this->plugin = new plgSystemLessTemplateCompanion;
		fwrite(STDERR, "Testing");
	}
	
	public function testSetLessVariables()
	{
		$test_array = array(
			'ltc_color_main' => '#123456',
			'color_link' => '#654321'
		);

		$result_array = $this->plugin->setLessVariables($test_array);

		$this->assertThat($result_array, $this->EqualTo(array('color_main' => '#123457')));
	}
}
?>
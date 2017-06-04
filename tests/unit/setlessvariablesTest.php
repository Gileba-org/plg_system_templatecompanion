<?php
use PHPUnit\Framework\TestCase;

require_once('./lesstemplatecompanion.php');

class plgSystemLessTemplateCompanionTest extends TestCase
{
    public function testSetLessVariables()
    {
        $test_array = array(
	        'ltc_color_main' => '#123456',
	        'color_link' => '#654321'
        );
        
        $result_array = 'SetLessVariables';
        $this->assertThat($result_array, $this->EqualTo(array('color_main' => '#123456')));
    }
}
?>
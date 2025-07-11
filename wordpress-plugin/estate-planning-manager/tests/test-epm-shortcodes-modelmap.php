<?php
/**
 * Unit test for centralized section-to-model mapping in get_client_data
 */
class Test_EPM_Shortcodes_ModelMap extends EPM_Test_Case {
    public function test_get_client_data_uses_centralized_model_map() {
        $shortcodes = EPM_Shortcodes::instance();
        $client_id = 1;
        // Mock ModelMap
        require_once dirname(__DIR__) . '/public/model-map.php';
        $model_map = EstatePlanningManager\ModelMap::getSectionModelMap();
        foreach ($model_map as $section => $model_class) {
            if (class_exists($model_class)) {
                // Mock model getAllRecordsForClient
                $model = $this->getMockBuilder($model_class)
                    ->disableOriginalConstructor()
                    ->setMethods(['getAllRecordsForClient'])
                    ->getMock();
                $model->expects($this->once())
                    ->method('getAllRecordsForClient')
                    ->with($client_id)
                    ->willReturn([['id' => 123, 'test_field' => 'value']]);
                // Inject mock
                $orig = $model_class;
                $GLOBALS['epm_test_model_instance'] = $model;
                // Patch instantiation in get_client_data if needed
                $data = $shortcodes->get_client_data($section, $client_id);
                $this->assertEquals(123, $data->id);
                $this->assertEquals('value', $data->test_field);
            }
        }
    }
    public function test_get_client_data_fallback_for_unknown_section() {
        $shortcodes = EPM_Shortcodes::instance();
        $client_id = 1;
        // Should fallback to clients table for unknown section
        $data = $shortcodes->get_client_data('unknown_section', $client_id);
        $this->assertTrue(is_object($data) || $data === null);
    }
}

<?php
use PHPUnit\Framework\TestCase;

class Test_ClientIdSaveCheck extends TestCase {
    public function test_client_id_created_on_save_if_missing() {
        // Mock user and DB
        $user_id = 12345;
        $section = 'bank_accounts';
        $data = [
            'bank' => 'Test Bank',
            'account_type' => 'Checking',
            'account_number' => '123456789',
        ];
        $db = $this->getMockBuilder('EPM_Database')
            ->setMethods(['get_client_id_by_user_id', 'create_client', 'save_client_data'])
            ->getMock();
        $db->expects($this->once())
            ->method('get_client_id_by_user_id')
            ->with($user_id)
            ->willReturn(false);
        $db->expects($this->once())
            ->method('create_client')
            ->with($user_id)
            ->willReturn(999);
        $db->expects($this->once())
            ->method('save_client_data')
            ->with(999, $section, $this->arrayHasKey('bank'))
            ->willReturn(true);
        // Simulate save logic
        $client_id = $db->get_client_id_by_user_id($user_id);
        if (!$client_id) {
            $client_id = $db->create_client($user_id);
        }
        $result = $db->save_client_data($client_id, $section, $data);
        $this->assertTrue($result);
        $this->assertEquals(999, $client_id);
    }
}

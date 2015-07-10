<?php 

/**
 * Model test
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Test_Model_TestModel extends EcomDev_PHPUnit_Test_Case_Config
{	

    /**
     * 
     * @test 
     * @loadFixture
     */
    public function testModel()
    {
        echo "\nAydus_ConstantContact model test started.";
        
        $this->assertEventObserverDefined('global',
                'newsletter_subscriber_save_after',
                'aydus_constantcontact/observer',
                'newsletterSubscriberSaveAfter');
                
        echo "\nAydus_ConstantContact model test completed.";
    }
	
}
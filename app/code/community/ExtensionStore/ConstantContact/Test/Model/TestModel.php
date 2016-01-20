<?php 

/**
 * Model test
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Test_Model_TestModel extends EcomDev_PHPUnit_Test_Case_Config
{	

    /**
     * 
     * @test 
     * @loadFixture
     */
    public function testModel()
    {
        echo "\nExtensionsStore_ConstantContact model test started.";
        
        $this->assertEventObserverDefined('global',
                'newsletter_subscriber_save_after',
                'extensions_store_constantcontact/observer',
                'newsletterSubscriberSaveAfter');
                
        echo "\nExtensionsStore_ConstantContact model test completed.";
    }
	
}
<?php 

/**
 * Controller test
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Test_Controller_TestController extends EcomDev_PHPUnit_Test_Case_Controller
{	

    /**
     * 
     * @test 
     * @loadFixture
     */
    public function testController()
    {
        echo "\nExtensionsStore_ConstantContact controller test started.";
        
        //subscribe to newsletter
        $email = 'admin@extensions-store.com';
                
        $this->getRequest()->setMethod('POST')->setPost(array('email' => $email));
        $this->dispatch('newsletter/subscriber/new');
        
        $this->assertRequestRoute('newsletter/subscriber/new');  
        $this->assertEventDispatched('newsletter_subscriber_save_after');
        
        $subscriber = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
        $subscriberId = $subscriber->getId();
        $subscribed = ($subscriberId) ? true : false;
        
        $this->assertTrue($subscribed);
        
        $model = Mage::getSingleton('extensions_store_constantcontact/constantcontact');
        
        $contact = $model->getContactByEmail($email);
        
        $this->assertTrue(is_object($contact));
        $contactId = (int)$contact->id;
        
        $this->assertGreaterThan(0, $contactId);
        
        $subscriberContact = Mage::getModel('extensions_store_constantcontact/subscribercontact')->load($subscriberId, 'subscriber_id');
        $subscriberContactId = $subscriberContact->getContactId();
        
        $this->assertGreaterThan(0, $subscriberContactId);
        $this->assertEquals($contactId, $subscriberContactId);
        
        $helper = Mage::helper('extensions_store_constantcontact');
        $generalListId = $helper->getGeneralListId();
        
        $lists = $contact->lists;
        
        $inGeneralList = false;
        
        foreach ($lists as $list){
            
            if ($list->id = $generalListId){
                $inGeneralList = true;
            }
            
        }
        
        $this->assertTrue($inGeneralList);
        
        echo "\nExtensionsStore_ConstantContact controller test completed.";
    }
	
}
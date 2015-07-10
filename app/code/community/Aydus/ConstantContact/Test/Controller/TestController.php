<?php 

/**
 * Controller test
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Test_Controller_TestController extends EcomDev_PHPUnit_Test_Case_Controller
{	

    /**
     * 
     * @test 
     * @loadFixture
     */
    public function testController()
    {
        echo "\nAydus_ConstantContact controller test started.";
        
        //subscribe to newsletter
        $email = 'davidt@aydus.com';
                
        $this->getRequest()->setMethod('POST')->setPost(array('email' => $email));
        $this->dispatch('newsletter/subscriber/new');
        
        $this->assertRequestRoute('newsletter/subscriber/new');  
        $this->assertEventDispatched('newsletter_subscriber_save_after');
        
        $subscriber = Mage::getModel('newsletter/subscriber')->load($email, 'subscriber_email');
        $subscriberId = $subscriber->getId();
        $subscribed = ($subscriberId) ? true : false;
        
        $this->assertTrue($subscribed);
        
        $model = Mage::getSingleton('aydus_constantcontact/constantcontact');
        
        $contact = $model->getContactByEmail($email);
        
        $this->assertTrue(is_object($contact));
        $contactId = (int)$contact->id;
        
        $this->assertGreaterThan(0, $contactId);
        
        $subscriberContact = Mage::getModel('aydus_constantcontact/subscribercontact')->load($subscriberId, 'subscriber_id');
        $subscriberContactId = $subscriberContact->getContactId();
        
        $this->assertGreaterThan(0, $subscriberContactId);
        $this->assertEquals($contactId, $subscriberContactId);
        
        $helper = Mage::helper('aydus_constantcontact');
        $generalListId = $helper->getGeneralListId();
        
        $lists = $contact->lists;
        
        $inGeneralList = false;
        
        foreach ($lists as $list){
            
            if ($list->id = $generalListId){
                $inGeneralList = true;
            }
            
        }
        
        $this->assertTrue($inGeneralList);
        
        echo "\nAydus_ConstantContact controller test completed.";
    }
	
}
<?php

/**
 * ConstantContact observer
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Observer
{
    /**
     * Subscribe/unsubscribe user/customer to general list
     * 
     * @see newsletter_subscriber_save_after
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function newsletterSubscriberSaveAfter($observer)
    {
        $model = Mage::getSingleton('aydus_constantcontact/constantcontact');
        
        if ($model->isReady()){
            
            $subscriber = $observer->getSubscriber();
            $subscriberId = $subscriber->getId();
            $customerId = $subscriber->getCustomerId();
            
            if ($customerId){
            
                $customer = Mage::getModel('customer/customer');
                $customer->load($customerId);
            }
                        
            $listId = Mage::helper('aydus_constantcontact')->getGeneralListId();
            
            if ($subscriber->getSubscriberStatus() == 1){
                
                $data = array();
                $data['subscriber_id'] = $subscriberId;
                $data['email'] = $subscriber->getSubscriberEmail();
                $data['list'] = $listId;
                
                if ($customerId && $customer->getId()){
                
                    $data['firstname'] = $customer->getFirstname();
                    $data['lastname'] = $customer->getLastname();
                }
                
                $result = $model->addUpdateContact($data);
                                
            } else if ($subscriber->getSubscriberStatus() == 3){
                
                if ($customerId && $customer->getId()){
                    
                    $contactId = $customer->getContactId();
                    
                    if (!$contactId){
                        
                        $subscriberContact = Mage::getSingleton('aydus_constantcontact/subscribercontact');
                        $subscriberContact->load($subscriberId, 'subscriber_id');
                        $contactId = $subscriberContact->getContactId();
                    }
                    
                    if ($contactId){
                        
                        $result = $model->unsubscribe($contactId, $listId, $subscriberId);
                    }
                    
                }    
                                 
            }
            
            if ($result && $result['error'] === true){
            
                Mage::log($result['data'], null, 'aydus_constantcontact.log');
            
            } 
            
        }
        
        return $observer;
    }
    
    /**
     * Unsubscribe subscriber from general list
     * 
     * @see newsletter_subscriber_delete_after
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function newsletterUnsubscribe($observer)
    {
        $model = Mage::getSingleton('aydus_constantcontact/constantcontact');
        
        if ($model->isReady()){
            
            $subscriber = $observer->getSubscriber();
            $subscriberId = $subscriber->getId();
            $listId = Mage::helper('aydus_constantcontact')->getGeneralListId();
            $customerId = $subscriber->getCustomerId();
            $contactId = false;
            
            if ($customerId){
            
                $customer = Mage::getModel('customer/customer');
                $customer->load($customerId);
            
                $contactId = $customer->getContactId();
            } 
            
            if (!$contactId){
                
            }
            
            $result = $model->unsubscribe($contactId, $listId, $subscriberId);
            
            if ($result['error'] === true){
            
                Mage::log($result['data'], null, 'aydus_constantcontact.log');
            }
            
        }
        
        return $observer;        
    }
    
    public function updateCustomer($observer)
    {
        
    }
    
}
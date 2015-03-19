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
        $subscriber = $observer->getSubscriber();
        
        $result = Mage::getModel('aydus_constantcontact/constantcontact')->updateSubscriber($subscriber);
        
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
        $subscriber = $observer->getSubscriber();
            
        $result = Mage::getModel('aydus_constantcontact/constantcontact')->updateSubscriber($subscriber);
            
        return $observer;        
    }
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer
     */
    public function updateCustomer($observer)
    {
        $subscriber = $observer->getSubscriber();
        
        return $observer;        
    }
    
}
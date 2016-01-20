<?php

/**
 * ConstantContact observer
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Model_Observer
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
        
        $result = Mage::getSingleton('extensions_store_constantcontact/constantcontact')->updateSubscriber($subscriber);
        
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
            
        $result = Mage::getSingleton('extensions_store_constantcontact/constantcontact')->updateSubscriber($subscriber);
            
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
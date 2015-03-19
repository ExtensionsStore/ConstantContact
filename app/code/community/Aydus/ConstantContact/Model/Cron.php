<?php

/**
 * ConstantContact cron 
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Cron
{

    /**
     * 
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function syncContacts($schedule)
    {
        $subscribers = Mage::getModel('newsletter/subscriber')->getCollection();
            
        if ($subscribers->getSize()>0){
            
            foreach ($subscribers as $subscriber){
                
                Mage::getModel('aydus_constantcontact/constantcontact')->updateSubscriber($subscriber);
            }
            
        }

    }
    
}
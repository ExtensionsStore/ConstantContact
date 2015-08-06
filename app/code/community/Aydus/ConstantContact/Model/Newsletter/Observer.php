<?php

/**
 * ConstantContact newsletter observer override
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Newsletter_Observer extends Mage_Newsletter_Model_Observer
{
    /**
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledSend($schedule)
    {
        if (Mage::getStoreConfig('aydus_constantcontact/configuration/send_newsletters')){
            
            return Mage::getSingleton('aydus_constantcontact/constantcontact')->sendNewsletters();
            
        } else {
            
            parent::scheduledSend($schedule);
        }

    }
    
}

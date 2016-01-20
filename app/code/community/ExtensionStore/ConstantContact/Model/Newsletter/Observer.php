<?php

/**
 * ConstantContact newsletter observer override
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Model_Newsletter_Observer extends Mage_Newsletter_Model_Observer
{
    /**
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledSend($schedule)
    {
        if (Mage::getStoreConfig('extensions_store_constantcontact/configuration/send_newsletters')){
            
            return Mage::getSingleton('extensions_store_constantcontact/constantcontact')->sendNewsletters();
            
        } else {
            
            parent::scheduledSend($schedule);
        }

    }
    
}

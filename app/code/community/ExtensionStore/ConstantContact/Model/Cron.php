<?php

/**
 * ConstantContact cron 
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Model_Cron
{

    /**
     * 
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function syncSubscribersContacts($schedule)
    {
        if (!Mage::getStoreConfig('extensions_store_constantcontact/configuration/sync_subscribers')){
            return 'Sync disabled in system configuration.';
        }
        
        return Mage::getSingleton('extensions_store_constantcontact/constantcontact')->syncSubscribersContacts();
    }
    
}
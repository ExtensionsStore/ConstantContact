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
    public function syncSubscribersContacts($schedule)
    {
        return Mage::getSingleton('aydus_constantcontact/constantcontact')->syncSubscribersContacts();
    }
    
}
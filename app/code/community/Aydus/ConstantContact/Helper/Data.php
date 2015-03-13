<?php

/**
 * ConstantContact Helper
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ConstantContact_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEnabled()
    {
        $enabled = Mage::getStoreConfig('aydus_constantcontact/configuration/enabled');
        
        return $enabled;
    }    
    
    public function getKey()
    {
        $key = Mage::getStoreConfig('aydus_constantcontact/configuration/key');
        $key = Mage::helper('core')->decrypt($key);
        
        return $key;
    }
    
    public function getToken()
    {
        $token = Mage::getStoreConfig('aydus_constantcontact/configuration/token');
        $token = Mage::helper('core')->decrypt($token);
        
        return $token;
    }    
    
    /**
     * The main list id
     * @return int
     */
    public function getGeneralListId()
    {
        $generalListId = Mage::getStoreConfig('aydus_constantcontact/configuration/list');
        
        return $generalListId;
    }
    
    /**
     * Array of list ids selected in system configuration
     * @return array
     */
    public function getSelectedListIds()
    {
        $selectedLists = Mage::getStoreConfig('aydus_constantcontact/configuration/lists');
        $selectedListIds = explode(',',$selectedLists);
        
        return $selectedListIds;
    }
    
    /**
     * Main and selected list ids
     * @return multitype:
     */
    public function getValidListIds()
    {
        $generalListId = $this->getGeneralListId();
        $selectedListIds = $this->getSelectedListIds();
        $validListIds = array_merge(array($generalListId), $selectedListIds);
        
        return $validListIds;
    }
        

	
}
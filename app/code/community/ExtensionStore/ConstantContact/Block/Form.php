<?php

/**
 * ConstantContact form
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Block_Form extends Mage_Core_Block_Template
{
    
    public function getFormAction()
    {
        return $this->getUrl('constantcontact/form/subscribePost');        
    }

    public function getLists()
    {
        $lists = Mage::getSingleton('extensions_store_constantcontact/constantcontact')->getLists();
        
        return $lists;
    }
    
    public function getCustomer()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getModel('customer/customer');
        }
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        
        return $customer;        
    }    
    
    public function getAddress()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getModel('customer/address');
        }
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $billingAddress = $customer->getDefaultBillingAddress();
        
        if (!$billingAddress){
            $billingAddress = Mage::getModel('customer/address');
        }
        
        return $billingAddress;        
    }
    
    public function getRegions()
    {
        $collection = Mage::getModel('directory/region')->getCollection();
        
        return $collection->toOptionArray();
    }
    
    public function getCountries()
    {
        $collection = Mage::getModel('directory/country')->getCollection();
        
        return $collection->toOptionArray();        
    }

}
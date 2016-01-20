<?php

/**
 * Current transactions system config field renderer
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Block_Adminhtml_System_Config_Form_Field_Currenttransactions extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $element->setReadonly(true, true);
        $currentTransactions = (int)Mage::helper('extensions_store_constantcontact')->getCurrentTransactions();
        $element->setValue($currentTransactions);
        
        return parent::_getElementHtml($element);
    }
}

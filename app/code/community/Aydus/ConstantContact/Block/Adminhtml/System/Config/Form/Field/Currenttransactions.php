<?php

/**
 * Current transactions system config field renderer
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Block_Adminhtml_System_Config_Form_Field_Currenttransactions extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * (non-PHPdoc)
     * @see Mage_Adminhtml_Block_System_Config_Form_Field::_getElementHtml()
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) 
    {
        $element->setReadonly(true, true);
        $currentTransactions = (int)Mage::helper('aydus_constantcontact')->getCurrentTransactions();
        $element->setValue($currentTransactions);
        
        return parent::_getElementHtml($element);
    }
}

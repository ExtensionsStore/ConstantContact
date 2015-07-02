<?php

/**
 * ConstantContact Abstract model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $datetime = date('Y-m-d H:i:s');
        if (!$this->hasData('created_at')){
            $this->setData('created_at', $datetime);
        }
        $this->setData('updated_at', $datetime);
        
        return parent::_beforeSave();
    }
	
}
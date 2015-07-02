<?php

/**
 * ConstantContact Config collection model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */
	
class Aydus_ConstantContact_Model_Resource_Config_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{

	protected function _construct()
	{
        parent::_construct();
		$this->_init('aydus_constantcontact/config');
	}
	
}
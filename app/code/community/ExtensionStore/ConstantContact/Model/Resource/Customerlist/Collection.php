<?php

/**
 * ConstantContact Customerlist collection model
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */
	
class ExtensionsStore_ConstantContact_Model_Resource_Customerlist_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract 
{

	protected function _construct()
	{
        parent::_construct();
		$this->_init('extensions_store_constantcontact/customerlist');
	}
	
}
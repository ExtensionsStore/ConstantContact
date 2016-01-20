<?php

/**
 * ConstantContact Subscribercontact resource model
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Model_Resource_Subscribercontact extends Mage_Core_Model_Resource_Db_Abstract
{
	
	protected function _construct()
	{
		$this->_init('extensions_store_constantcontact/subscribercontact', 'id');
	}
	
}


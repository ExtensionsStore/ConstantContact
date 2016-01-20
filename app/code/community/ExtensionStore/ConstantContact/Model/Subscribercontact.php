<?php

/**
 * ConstantContact Subscribercontact model
 *
 * @category    ExtensionsStore
 * @package     ExtensionsStore_ConstantContact
 * @author      Extensions Store <admin@extensions-store.com>
 */

class ExtensionsStore_ConstantContact_Model_Subscribercontact extends ExtensionsStore_ConstantContact_Model_Abstract
{
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
        parent::_construct();
        
		$this->_init('extensions_store_constantcontact/subscribercontact');
	}	
	
}
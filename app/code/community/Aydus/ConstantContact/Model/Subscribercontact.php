<?php

/**
 * ConstantContact Subscribercontact model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Subscribercontact extends Mage_Core_Model_Abstract
{
	/**
	 * Initialize resource model
	 */
	protected function _construct()
	{
        parent::_construct();
        
		$this->_init('aydus_constantcontact/subscribercontact');
	}	
	
}
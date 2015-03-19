<?php

/**
 * ConstantContact Customerlist resource model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_Resource_Customerlist extends Mage_Core_Model_Resource_Db_Abstract
{
	
	protected function _construct()
	{
		$this->_init('aydus_constantcontact/customerlist', 'id');
	}
	
}


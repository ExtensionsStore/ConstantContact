<?php

/**
 * ConstantContact source list
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

class Aydus_ConstantContact_Model_System_Config_Source_List extends Varien_Object
{

	protected $_lists   = null;

	protected function _construct()
	{
		if( is_null($this->_lists) ){
		    
			$this->_lists = Mage::getSingleton('aydus_constantcontact/constantcontact')->getAllLists();
		}
	}

    /**
     * Options array of lists
     *
     * @return array
     */
    public function toOptionArray($multiselect = false)
    {
    	$lists = array();

    	if(is_array($this->_lists)){
    	    
    	    $selectLabel = '--- Select List' .(($multiselect) ? '(s)' : '') . ' ---' ;

    		$lists []= array('value' => '', 'label' => Mage::helper('aydus_constantcontact')->__($selectLabel));
    	    
    	    foreach($this->_lists as $list){
    			$lists []= array('value' => $list->id, 'label' => $list->name);
    		}

    	}else{
    	    
    		$lists []= array('value' => '', 'label' => Mage::helper('aydus_constantcontact')->__('--- No data (enter key and token first) ---'));
    	}

        return $lists;
    }

}

<?php

/**
 * ConstantContact Helper
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

class Aydus_ConstantContact_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEnabled()
    {
        $enabled = Mage::getStoreConfig('aydus_constantcontact/configuration/enabled');
        
        return $enabled;
    }    
    
    public function getKey()
    {
        $key = Mage::getStoreConfig('aydus_constantcontact/configuration/key');
        $key = Mage::helper('core')->decrypt($key);
        
        return $key;
    }
    
    public function getToken()
    {
        $token = Mage::getStoreConfig('aydus_constantcontact/configuration/token');
        $token = Mage::helper('core')->decrypt($token);
        
        return $token;
    }    
    
    /**
     * The main list id
     * @return int
     */
    public function getGeneralListId()
    {
        $generalListId = Mage::getStoreConfig('aydus_constantcontact/configuration/list');
        
        return $generalListId;
    }
    
    /**
     * Array of list ids selected in system configuration
     * @return array
     */
    public function getSelectedListIds()
    {
        $selectedLists = Mage::getStoreConfig('aydus_constantcontact/configuration/lists');
        $selectedListIds = explode(',',$selectedLists);
        
        return $selectedListIds;
    }
    
    /**
     * Main and selected list ids
     * @return multitype:
     */
    public function getValidListIds()
    {
        $generalListId = $this->getGeneralListId();
        $selectedListIds = $this->getSelectedListIds();
        $validListIds = array_merge(array($generalListId), $selectedListIds);
        
        return $validListIds;
    }
        	
    /**
     * http://stackoverflow.com/questions/1462503/sort-array-by-object-property-in-php
     * 
     * @param array $array
     * @param string $key
     */
    public function quickSort( &$array, $key )
    {
        $cur = 1;
        $stack[1]['l'] = 0;
        $stack[1]['r'] = count($array)-1;
    
        do
        {
            $l = $stack[$cur]['l'];
            $r = $stack[$cur]['r'];
            $cur--;
    
            do
            {
                $i = $l;
                $j = $r;
                $tmp = $array[(int)( ($l+$r)/2 )];
    
                // partion the array in two parts.
                // left from $tmp are with smaller values,
                // right from $tmp are with bigger ones
                do
                {
                    while( $array[$i]->{$key} < $tmp->{$key} )
                        $i++;
    
                    while( $tmp->{$key} < $array[$j]->{$key} )
                        $j--;
    
                    // swap elements from the two sides
                    if( $i <= $j)
                    {
                        $w = $array[$i];
                        $array[$i] = $array[$j];
                        $array[$j] = $w;
    
                        $i++;
                        $j--;
                    }
    
                }while( $i <= $j );
    
                if( $i < $r )
                {
                    $cur++;
                    $stack[$cur]['l'] = $i;
                    $stack[$cur]['r'] = $r;
                }
                $r = $j;
    
            }while( $l < $r );
    
        }while( $cur != 0 );
    
    
    }
    
}
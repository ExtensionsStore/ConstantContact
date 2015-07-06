<?php

/**
 * ConstantContact Helper
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author     	Aydus <davidt@aydus.com>
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
    
    public function getRateLimitPerDay()
    {
        $rateLimitPerDay = Mage::getStoreConfig('aydus_constantcontact/configuration/rate_limit_per_day');
    
        return $rateLimitPerDay;
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
     *
     * @return Aydus_ConstantContact_Model_Config
     */
    public function getCurrentTransactionsConfig()
    {
        $currentTransactionsConfig = Mage::getModel('aydus_constantcontact/config');
        $collection = $currentTransactionsConfig->getCollection();
        $collection->addFieldToFilter('config_key','current_transactions');
    
        if ($collection->getSize()>0){
    
            $currentTransactionsConfig = $collection->getFirstItem();
    
        } else {
    
            try {
                 
                $datetime = date('Y-m-d H:i:s');
    
                $currentTransactionsConfig->setConfigKey('current_transactions')
                ->setConfigValue(0)
                ->setDateCreated($datetime)
                ->setDateUpdated($datetime);
    
                $currentTransactionsConfig->save();
    
                 
            } catch(Exception $e){
    
                Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
            }
    
        }
         
        return $currentTransactionsConfig;
    }
    
    /**
     * Get current transactions for limit
     */
    public function getCurrentTransactions()
    {
        $currentTransactions = 0;
        $currentTransactionsConfig = $this->getCurrentTransactionsConfig();
    
        $today = date('Y-m-d 00:00:01');
        $today = strtotime($today);
        $updatedAt = $currentTransactionsConfig->getUpdatedAt();
        $updatedAt = strtotime($updatedAt);
    
        if ($updatedAt < $today){
    
            try {
                 
                $datetime = date('Y-m-d H:i:s');
    
                $currentTransactionsConfig->setConfigValue(0)
                ->setUpdatedAt($datetime);
    
                $currentTransactionsConfig->save();
    
                 
            } catch(Exception $e){
    
                Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
            }
    
        }
    
        $currentTransactions = $currentTransactionsConfig->getConfigValue();
         
        return $currentTransactions;
    }
    
    /**
     * Increment number of transactions
     *
     * @param int $calls
     * @return boolean
     */
    public function incrementTransactions($calls = 1)
    {
        if ($calls > 0){
             
            try {
                 
                $datetime = date('Y-m-d H:i:s');
                $currentTransactionsConfig = $this->getCurrentTransactionsConfig();
                $currentTransactions = $this->getCurrentTransactions() + $calls;
                 
                $currentTransactionsConfig->setConfigValue($currentTransactions)
                ->setUpdatedAt($datetime);
    
                $currentTransactionsConfig->save();
    
            } catch(Exception $e){
                 
                Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
                return false;
            }
        }
         
        return true;
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
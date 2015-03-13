<?php

/**
 * ConstantContact index controller
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */
class Aydus_ConstantContact_IndexController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Get singleton model
     * @return Aydus_ConstantContact_Model_Constantcontact
     */
    protected function _getModel()
    {
        $model = Mage::getSingleton('aydus_constantcontact/constantcontact');
    
        return $model;
    }
        
    /**
     * Get available lists
     */
    public function listsAction()
    {
        $result = array();
        
        if ($this->_getModel()->isReady()){
        
            $result['error'] = false;
            $result['data'] =  $this->_getModel()->getLists();
        
        } else {
        
            $result['error'] = true;
            $result['data'] =  'API is not available at this time. Please try again later.';
        }        
        
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true)->setBody(Mage::helper('core')->jsonEncode($result));
    }
    
    /**
     * Subscribe to list
     */
    public function subscribeAction()
    {
        if ($this->_getModel()->isReady()){
            
            if ($data = $this->getRequest()->getPost()){
                
                try {
                
                    //on success, result contains contact id in data value
                    $result = $this->_getModel()->addUpdateContact($data);
                
                } catch(Exception $e){
                
                    $result['error'] = true;
                    $result['data'] =  $e->getMessage();
                }                
                
            } else {
                
                $result['error'] = true;
                $result['data'] =  'No post data';
            }
        
        } else {
        
            $result['error'] = true;
            $result['data'] =  'API is not available at this time. Please try again later.';
        }        
        
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json',true)->setBody(Mage::helper('core')->jsonEncode($result));
        
    }

}

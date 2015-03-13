<?php

/**
 * ConstantContact form controller
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */
class Aydus_ConstantContact_FormController extends Mage_Core_Controller_Front_Action 
{
    protected function _initLayout()
    {
        $this->_initLayoutMessages('customer/session');   

        return $this;
    }
    
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
     * Subscribe page
     */
    public function subscribeAction()
    {
        if ($this->_getModel()->isReady()){
            
            $this->loadLayout()->_initLayout()->renderLayout();
            
        } else {
            
            $this->norouteAction();
        }
    }
    
    /**
     *  Subscribe form action 
     * 
     */
    public function subscribePostAction()
    {
        if (!$this->_validateFormKey()) {
            $this->_redirect('*/*/subscribe');
            return;
        }
                
        if ($this->_getModel()->isReady() && $this->getRequest()->isPost()) {
            
            $data = $this->getRequest()->getPost();
            
            $result = $this->_getModel()->addUpdateContact($data);
            
            if ($result['error'] === false){
                
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('aydus_constantcontact')->__('We have received your information. Thank you for subscribing.'));
                
            } else {
                
                Mage::getSingleton('customer/session')->addError(Mage::helper('aydus_constantcontact')->__('There was an error processing your request. Please, try again later'));
            }
            
        } else {
            
            Mage::getSingleton('customer/session')->addError(Mage::helper('aydus_constantcontact')->__('There was an error processing your request. Please, try again later'));
        }
        
        $this->_redirect('*/*/subscribe');

    } 

}

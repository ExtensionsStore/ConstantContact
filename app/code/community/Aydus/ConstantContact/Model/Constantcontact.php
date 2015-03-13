<?php

/**
 * ConstantContact model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

require 'vendor/autoload.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Address;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class Aydus_ConstantContact_Model_Constantcontact extends Mage_Core_Model_Abstract
{
    protected $_cc;
    protected $_lists;

    protected function _construct()
    {
        $enabled = Mage::helper('aydus_constantcontact')->getEnabled();
        
        if ($enabled){
            
            $apiKey = Mage::helper('aydus_constantcontact')->getKey();
            $accessToken = Mage::helper('aydus_constantcontact')->getToken();
            
            if ($apiKey && $accessToken){
            
                define("APIKEY", $apiKey);
                define("ACCESS_TOKEN", $accessToken);
            
                try {
                    
                    $this->_cc = new ConstantContact(APIKEY);
                    
                } catch(CtctException $ex){
            
                    Mage::log($ex->getErrors(), null, 'aydus_constantcontact.log');
                }
                
            }   
                     
        }
        
    }
    
    /**
     * API is ready
     * @return boolean
     */
    public function isReady()
    {
        return is_object($this->_cc);
    }
    
    /**
     * Get all lists from API
     * @return array
     */
    public function getAllLists()
    {
        if ($this->isReady()){
            
            try {
            
                $lists = $this->_cc->getLists(ACCESS_TOKEN);
                return $lists;
                
            } catch(CtctException $ex){
            
                Mage::log($ex->getErrors(), null, 'aydus_constantcontact.log');
            }
            
        } 
        
    }
    
    /**
     * Get selected lists from configuration
     */
    public function getLists()
    {
        if( !$this->_lists){
        
            $lists = $this->getAllLists();
            
            if (is_array($lists) && count($lists) > 0){
                
                $validListIds = Mage::helper('aydus_constantcontact')->getValidListIds();
                
                foreach ($lists as $list){
                
                    if (in_array($list->id, $validListIds)){
                        $this->_lists[] = $list;
                
                    }
                
                }                
            } 
            
        }        
        
        return $this->_lists;
    }
    
    /**
     * Add contact to list
     * 
     * @param unknown $data
     */
    public function addUpdateContact($data)
    {
        $result = array();
        
        try {

            $listId = $data['list'];
            
            if ($listId){
                
                $response = $this->_cc->getContactByEmail(ACCESS_TOKEN, $data['email']);
                
                //contact is new
                if (empty($response->results)) {
                
                    $contact = new Contact();
                    $contact->addEmail($data['email']);
                    $contact->addList($listId);
                    $contact = $this->_setContactData($contact, $data);
                
                    $contact = $this->_cc->addContact(ACCESS_TOKEN, $contact, true);
                    $result['error'] = false;
                    $result['data'] = $contact->id;

                //update contact
                } else if (count($response->results) == 1) {
                
                    $contact = $response->results[0];
                    $contact->addList($listId);
                    $contact = $this->_setContactData($contact, $data);
                                    
                    $contact = $this->_cc->updateContact(ACCESS_TOKEN, $contact, true);
                    $result['error'] = false;
                    $result['data'] = $contact->id;
                
                //some thing wrong
                } else {
                
                    $result['error'] = true;
                    $result['data'] = 'An error occurred. There were more than one contact for the email.';
                }
                
                //update table of subscriber contacts
                if ($contact->id && $data['subscriber_id']){
                    $subscriberId = $data['subscriber_id'];
                    $contactId = $contact->id;
                    $this->_updateSubscriberContact($subscriberId, $contactId);
                }
                
            } else {
                
                $result['error'] = true;
                $result['data'] = 'No list id was submitted.';
            }
            
                        
        } catch (CtctException $ex) {

            Mage::log($ex->getErrors(), null, 'aydus_constantcontact.log');
            $result['error'] = true;
            $result['data'] = $ex->getMessage();
        }
        
        return $result;
    }
    
    protected function _setContactData($contact, $data)
    {
        $contact->prefix_name = $data['prefix'];
        $contact->first_name = $data['firstname'];
        $contact->middle_name = $data['middlename'];
        $contact->last_name = $data['lastname'];
        $contact->company_name = $data['company'];
        if ($contact->company){
            $contact->work_phone = $data['telephone'];
        } else {
            $contact->home_phone = $data['telephone'];
        }
        
        if (is_array($data['street']) || $data['city'] || $data['region'] || $data['postcode']){
            
            $addresses = $contact->addresses;
            
            if (is_array($addresses) && count($addresses)>0){
            
            } else {
            
                $address = new Address();
            }
            
            $state = '';
            $stateCode = '';
            
            if ((int)$data['region_id']){
            
                $regionId = (int)$data['region_id'];
                $region = Mage::getModel('directory/region')->load($regionId);
            
                if ($region->getId()){
                    $state = $region->getDefaultName();
                    $stateCode = $region->getCode();
                }
            
            } else if ($data['region']) {
                if (strlen($data['region']) == 2){
                    $stateCode = $data['region'];
            
                } else {
                    $state = $data['region'];
                }
            
            }
            
            $addressData = array(
                    'line1' => $data['street'][0],
                    'line2' => $data['street'][0],
                    'city' =>  $data['city'],
                    'state' =>  $state,
                    'state_code' => $stateCode,
                    'country_code' =>  $data['country_id'],
                    'postal_code' => $data['postcode'],
            );
            
            $address->create($addressData);    
                
            $contact->addAddress($address);    
        }

        return $contact;
    }
    
    /**
     * Subscribe contact to list
     * 
     * @param int $contactId
     * @param int $listId
     * @param int $subscriberId
     * @return array
     */
    public function subscribe($contactId, $listId, $subscriberId = null)
    {
        $result = array();
        
        try {
            
            $contact = $this->_cc->getContact(ACCESS_TOKEN, $contactId);
            $contact->addList($listId);
            
            $resultData = $this->_cc->updateContact(ACCESS_TOKEN, $contact, true);
            $result['error'] = false;
            $result['data'] = $contact->id;     

            if ($subscriberId){
                $this->_updateSubscriberContact($subscriberId, $contactId);
            }            
        
        } catch(CtctException $ex) {
        
            $result['error'] = true;
            $errors = $ex->getErrors();
            $message = (is_array($errors)) ? $errors[0]['error_message'] : $ex->getMessage();
            $result['data'] = $message;
        }
        
        return $result;        
    }
    
    /**
     * Unsubscribe contact from list
     * 
     * @param int $contactId
     * @param int $listId
     * @param int $subscriberId
     * @return array
     */
    public function unsubscribe($contactId, $listId, $subscriberId = null)
    {
        $result = array();
                        
        try {
            
            $resultData = $this->_cc->deleteContactFromList(ACCESS_TOKEN, $contactId, $listId);
            
            if ($resultData){
                
                $result['error'] = false;
                $result['data'] = 'List has been removed from contact.';
                
            } else {
                
                $result['error'] = true;
                $result['data'] = $resultData;
            }
            
            if ($subscriberId){
                $this->_updateSubscriberContact($subscriberId, $contactId);
            }            
            
        } catch(CtctException $ex) {

            $result['error'] = false;//sdk throws exception if user is not in list
            $errors = $ex->getErrors();
            $message = (is_array($errors)) ? $errors[0]['error_message'] : $ex->getMessage(); 
            $result['data'] = $message;
        }
        
        return $result;
    }
    
    /**
     * Update table aydus_constantcontact_subscriber with subscriber_id and contact_id 
     * 
     * @param int $subscriberId
     * @param int $contactId
     */
    protected function _updateSubscriberContact($subscriberId, $contactId = false)
    {
        try {
            
            $subscriberContact = Mage::getSingleton('aydus_constantcontact/subscribercontact');
            $subscriberContact->load($subscriberId, 'subscriber_id');
            
            if ($contactId){
                
                $datetime = date('Y-m-d H:i:s');
                
                if (!$subscriberContact->getId()){
                    $subscriberContact->setCreatedAt($datetime);
                }
                
                $subscriberContact->setSubscriberId($subscriberId)
                    ->setContactId($contactId)
                    ->setUpdatedAt($datetime)
                    ->save();
                
            } else {
                
                $subscriberContact->delete();
            }            
            
        } catch (Exception $e){
            Mage::log($e->getMessage(), null, 'aydus_constantcontact.log');
            
        }
                
    }
    
}
<?php

/**
 * ConstantContact model
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

if (file_exists('vendor'.DS.'autoload.php') && file_exists('vendor'.DS.'constantcontact')){
    
    require 'vendor'.DS.'autoload.php';
    
} else {
    
    require Mage::getBaseDir('lib').DS.'ConstantContact'.DS. 'vendor'.DS.'autoload.php';
}

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Address;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

class Aydus_ConstantContact_Model_Constantcontact extends Mage_Core_Model_Abstract
{
    protected $_cc;
    protected $_allLists;
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
     * Check if API is available
     * 
     * @param int $calls
     * @return boolean
     */
    public function isReady($calls = 1)
    {
        $helper = Mage::helper('aydus_constantcontact');
        $rateLimitPerDay = (int)$helper->getRateLimitPerDay();
        
        if ($rateLimitPerDay > 0){
            
            $currentTransactions = (int)$helper->getCurrentTransactions();
            
            $ready = ($currentTransactions < $rateLimitPerDay && is_object($this->_cc)) ? true : false;
            
            if ($ready){
                
                $helper->incrementTransactions($calls);
            }
            
        } else {
            
            Mage::log('No limit set in configuration.', null, 'aydus_constantcontact.log');
            
            $ready = false;
        }
        
        
        return $ready;
    }
        
    /**
     * Get all lists from API
     * @return array
     */
    public function getAllLists()
    {
        try {
            
            if (!$this->_allLists){
                
                $cache = Mage::app()->getCache();
                $cacheKey = strtoupper(get_class($this)).'_LIST';
                
                $lists = unserialize($cache->load($cacheKey));
                
                if (!$lists){
                    
                    if ($this->isReady()){
                        
                        $lists = $this->_cc->getLists(ACCESS_TOKEN);
                        Mage::helper('aydus_constantcontact')->quickSort($lists, 'name');
                        
                        $cache->save(serialize($lists), $cacheKey, array('COLLECTION_DATA'), 86400);
                    }
                    
                }
                
                $this->_allLists = $lists;
            }
            
            return $this->_allLists;
            
        } catch(CtctException $ex){
        
            Mage::log($ex->getErrors(), null, 'aydus_constantcontact.log');
        }
        
    } 
    
    /**
     * Get selected lists from configuration
     */
    public function getLists($general = true)
    {
        if( !$this->_lists){
        
            $lists = $this->getAllLists();
            
            if (is_array($lists) && count($lists) > 0){
                
                $helper = Mage::helper('aydus_constantcontact');
                $validListIds = $helper->getValidListIds();
                
                if (!$general){
                    $generalListId = $helper->getGeneralListId();
                    $key = array_search($generalListId, $validListIds);
                    unset($validListIds[$key]);
                }
                
                foreach ($lists as $list){
                
                    if (in_array($list->id, $validListIds)){
                        
                        $list->isSubscribed = $this->getIsSubscribed($list->id);
                        
                        $this->_lists[] = $list;
                
                    }
                
                }    
                            
            } 
            
        }   
        
        return $this->_lists;
    }
    
    /**
     * Check if current customer is subscribed to list
     * 
     * @param int $listId
     * @return boolean
     */
    public function getIsSubscribed($listId)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId();
        
        if ($customerId){
            
            $customerList = Mage::getModel('aydus_constantcontact/customerlist');
            $collection = $customerList->getCollection();
            $collection->addFieldToFilter('customer_id',$customerId);
            $collection->addFieldToFilter('list_id',$listId);
                        
            if ($collection->getSize() > 0){
                
                return true;
            }
            
        }
        
        return false;
    }
    
    /**
     * Get contact by email
     * 
     * @param string $email
     * @return Contact|boolean
     */
    public function getContactByEmail($email)
    {
        if ($this->isReady()){
            
            $response = $this->_cc->getContactByEmail(ACCESS_TOKEN, $email);
            
            if (count($response->results) == 1) {
            
                $contact = $response->results[0];
            
                return $contact;
            }            
        }
    
        return false;        
    }
    
    /**
     * 
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return array
     */
    public function updateSubscriber($subscriber)
    {
        $result = array();
    
        if ($this->isReady()){
    
            try {
                
                $subscriberId = $subscriber->getId();
                $customerId = $subscriber->getCustomerId();
                $subscriberEmail = $subscriber->getSubscriberEmail();
                $contactId = $this->getContactId($subscriber);
                
                if ($customerId){
                
                    $customer = Mage::getModel('customer/customer');
                    $customer->load($customerId);
                }
                
                $listId = Mage::helper('aydus_constantcontact')->getGeneralListId();
                
                if ($subscriber->getSubscriberStatus() == 1){
                
                    $data = array();
                    $data['customer_id'] = $customerId;
                    $data['subscriber_id'] = $subscriberId;
                    $data['email'] = $subscriberEmail;
                    $data['list'] = $listId;
                
                    if ($customerId && $customer->getId()){
                
                        $data['firstname'] = $customer->getFirstname();
                        $data['lastname'] = $customer->getLastname();
                    }
                
                    $result = $this->addUpdateContact($data);
                
                } else if ($subscriber->getSubscriberStatus() == 3){
                 
                    if ($contactId){
                
                        $result = $this->unsubscribe($contactId, $listId, $subscriberId, $customerId);
                    }
                }
                
                //additional lists
                $lists = $this->getLists(false);
                
                if ($contactId && is_array($lists) && count($lists)>0){
                    
                    $subscribeLists = (array)Mage::app()->getRequest()->getParam('subscribed_lists');
                    $helper = Mage::helper('aydus_constantcontact');
                    
                    foreach ($lists as $list){
                        
                        $listId = $list->id;
                        
                        if (in_array($listId, array_keys($subscribeLists))){
                        
                            $this->subscribe($contactId, $listId, $subscriberId, $customerId);
                        
                        } else {
                        
                            $this->unsubscribe($contactId, $listId, $subscriberId, $customerId);
                        
                        }                            
                        
                    }
              
                }
                                
            }catch (Exception $e){
                Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
            }
    
        } else {
    
            $result['error'] = true;
            $result['data'] = 'API is not available.';
        }
    
        return $result;
    } 

    /**
     * Get contact id 
     * 
     * @param Mage_Newsletter_Model_Subscriber $subscriber
     * @return int
     */
    public function getContactId($subscriber)
    {
        $subscriberId = $subscriber->getId();
        $customerId = $subscriber->getCustomerId();
        $subscriberEmail = $subscriber->getSubscriberEmail();
        
        if ($customerId){
        
            $customer = Mage::getModel('customer/customer');
            $customer->load($customerId);
        }        
        
        if ($customerId && $customer->getId()){
        
            $contactId = $customer->getContactId();
        
            if (!$contactId){
        
                $subscriberContact = Mage::getSingleton('aydus_constantcontact/subscribercontact');
                $subscriberContact->load($subscriberId, 'subscriber_id');
                $contactId = $subscriberContact->getContactId();
            }
        
        }
        
        //get from CC
        if (!$contactId){
            $contact = $this->getContactByEmail($subscriberEmail);
            if ($contact && isset($contact->id)){
                $contactId = $contact->id;
            }
        }        
        
        return $contactId;
    }
    
    /**
     *  Synchronized Newsletter Subscribers with General List
     */
    public function syncSubscribersContacts()
    {
        if (!Mage::getStoreConfig('aydus_constantcontact/configuration/sync_subscribers')){
            return 'Sync disabled in system configuration.';
        }
        
        $subscribers = Mage::getModel('newsletter/subscriber')->getCollection();
        $subscribeAr = array();
        $unsubscribeAr = array();
        
        if ($subscribers->getSize()>0){
        
            foreach ($subscribers as $subscriber){
                    
                $row = array();
                $row[] = $subscriber->getSubscriberEmail();
                
                $customerId = (int)$subscriber->getCustomerId();
                
                if ($customerId){
                    
                    $customer = Mage::getModel('customer/customer')->load($customerId);
                    
                    if ($subscriber->getSubscriberStatus() == 1 && $customer && $customer->getId()){
                        
                        $row[] = $customer->getFirstname();
                        $row[] = $customer->getLastname();
                    }
                }
                
                if ($subscriber->getSubscriberStatus() == 1){
                    
                    $subscribeAr[] = $row;
                
                } else if ($subscriber->getSubscriberStatus() == 3){
                    
                    $unsubscribeAr[] = $row;
                }
                
            }
            
            $listId = Mage::helper('aydus_constantcontact')->getGeneralListId();
            
            if (!file_exists('var/export/aydus/constantcontact')){
                mkdir('var/export/aydus/constantcontact',0755,true);
            }
            
            if (count($subscribeAr)>0){
                
                if ($this->isReady()){
                    
                    try {
                        $subscribeRows = "Email,First Name,Last Name\n";
                    
                        foreach ($subscribeAr as $row){
                            $subscribeRows .= implode(',', $row)."\n";
                        }
                    
                        if (!file_exists('var/export/aydus/constantcontact')){
                            mkdir('var/export/aydus/constantcontact',0755,true);
                        }
                    
                        file_put_contents('var/export/aydus/constantcontact/subscribe.csv', $subscribeRows);
                        $result = $this->_cc->addCreateContactsActivityFromFile(
                                ACCESS_TOKEN,
                                'subscribe.csv',
                                file_get_contents('var/export/aydus/constantcontact/subscribe.csv'),
                                $listId
                        );
                        $message = (isset($result->contact_count)) ? 'Added contacts: '.$result->contact_count : $result;
                        Mage::log($message,null,'aydus_constantcontact.log');
                        unlink('var/export/aydus/constantcontact/subscribe.csv');
                    
                    } catch (Exception $e){
                    
                        Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
                    } 
                                   
                }
                 
            }
            
            if (count($unsubscribeAr)>0){
            
                if ($this->isReady()){
                    
                    try {
                        $unsubscribeRows =  "Email\n";
                    
                        foreach ($unsubscribeAr as $row){
                            $unsubscribeRows .= $row[0]."\n";
                        }
                    
                        file_put_contents('var/export/aydus/constantcontact/unsubscribe.csv', $unsubscribeRows);
                        $result = $this->_cc->addRemoveContactsFromListsActivityFromFile(
                                ACCESS_TOKEN,
                                'unsubscribe.csv',
                                file_get_contents('var/export/aydus/constantcontact/unsubscribe.csv'),
                                $listId
                        );
                        $message = (isset($result->contact_count)) ? 'Removed contacts: '.$result->contact_count : $result;
                        Mage::log($message,null,'aydus_constantcontact.log');
                        unlink('var/export/aydus/constantcontact/unsubscribe.csv');
                    
                    } catch (Exception $e){
                    
                        Mage::log($e->getMessage(),null,'aydus_constantcontact.log');
                    }      
                                  
                }
                
            }            
        
        }
        
        $numSynced = count($subscribeAr) + count($unsubscribeAr);
        
        return 'Sync complete. Number synced: '. $numSynced;

    }
    
    /**
     * Add contact to list
     * 
     * @param unknown $data
     */
    public function addUpdateContact($data)
    {
        $result = array();
        
        if ($this->isReady(2)){
        
            try {
    
                $listId = @$data['list'];
                if (!$listId){
                    $listId = Mage::helper('aydus_constantcontact')->getGeneralListId();
                }
                
                $email = @$data['email'];
                
                if ($email){
                    
                    $response = $this->_cc->getContactByEmail(ACCESS_TOKEN, $email);
                    
                    //contact is new
                    if (empty($response->results)) {
                    
                        $contact = new Contact();
                        $contact->addEmail($email);
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
                    $subscriberId = (isset($data['subscriber_id']) && (int)$data['subscriber_id']) ? (int)$data['subscriber_id'] : null;
                    $contactId = $contact->id;
                    if ($subscriberId && $contactId){
                        $this->_updateSubscriberContact($subscriberId, $contactId);
                    }
                    
                    $customerId = (isset($data['customer_id']) && (int)$data['customer_id']) ? (int)$data['customer_id'] : null;
                    if ($customerId){
                        $this->_updateCustomerList(false, $customerId, $listId);
                    }       
                    
                } else {
                    
                    $result['error'] = true;
                    $result['data'] = 'Missing required email.';
                }         
                                            
            } catch (CtctException $ex) {
    
                Mage::log($ex->getErrors(), null, 'aydus_constantcontact.log');
                $result['error'] = true;
                $result['data'] = $ex->getMessage();
            }
        } else {
            
            $result['error'] = true;
            $result['data'] = 'API is not available.';
        }
        
        return $result;
    }
    
    protected function _setContactData($contact, $data)
    {
        $contact->prefix_name = (isset($data['prefix'])) ? $data['prefix'] : '';
        $contact->first_name = (isset($data['firstname'])) ? $data['firstname'] : '';
        $contact->middle_name = (isset($data['middlename'])) ? $data['middlename'] : '';
        $contact->last_name = (isset($data['lastname'])) ? $data['lastname'] : '';
        $contact->company_name = (isset($data['company'])) ? $data['company'] : '';
        if (isset($contact->company_name) && $contact->company_name){
            $contact->work_phone = (isset($data['telephone'])) ? $data['telephone'] : '';
        } else {
            $contact->home_phone = (isset($data['telephone'])) ? $data['telephone'] : '';
        }
        
        if (is_array(@$data['street']) || isset($data['city']) || isset($data['region']) || isset($data['postcode'])){
            
            $addresses = $contact->addresses;
            
            if (is_array($addresses) && count($addresses)>0){
            
            } else {
            
                $address = new Address();
            }
            
            $state = '';
            $stateCode = '';
            
            if (isset($data['region_id']) && (int)$data['region_id']){
            
                $regionId = (int)$data['region_id'];
                $region = Mage::getModel('directory/region')->load($regionId);
            
                if ($region->getId()){
                    $state = $region->getDefaultName();
                    $stateCode = $region->getCode();
                }
            
            } else if (isset($data['region']) && $data['region']) {
                if (strlen($data['region']) == 2){
                    $stateCode = $data['region'];
            
                } else {
                    $state = $data['region'];
                }
            
            }
            
            $line1 = (is_array($data['street']) && isset($data['street'][0])) ? $data['street'][0] : '';
            $line2 = (is_array($data['street']) && isset($data['street'][1])) ? $data['street'][1] : '';
            $city = (isset($data['city'])) ? $data['city'] : '';
            $countryCode = (isset($data['country_id'])) ? $data['country_id'] : '';
            $postalCode = (isset($data['postcode'])) ? $data['postcode'] : '';
            
            $addressData = array(
                    'line1' => $line1,
                    'line2' => $line2,
                    'city' =>  $city,
                    'state' =>  $state,
                    'state_code' => $stateCode,
                    'country_code' =>  $countryCode,
                    'postal_code' => $postalCode,
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
    public function subscribe($contactId, $listId, $subscriberId = null, $customerId = null)
    {
        $result = array();
        
        if ($this->isReady(2)){
            
            try {
            
                $contact = $this->_cc->getContact(ACCESS_TOKEN, $contactId);
                $contact->addList($listId);
            
                $resultData = $this->_cc->updateContact(ACCESS_TOKEN, $contact, true);
                $result['error'] = false;
                $result['data'] = $contact->id;
            
                if ($subscriberId){
                    $this->_updateSubscriberContact($subscriberId, $contactId);
                }
                if ($customerId){
                    $this->_updateCustomerList(false, $customerId, $listId);
                }
            
            } catch(CtctException $ex) {
            
                $result['error'] = true;
                $errors = $ex->getErrors();
                $message = (is_array($errors)) ? $errors[0]['error_message'] : $ex->getMessage();
                $result['data'] = $message;
            }  
                      
        } else {
            
            $result['error'] = true;
            $result['data'] = 'API is not available.';
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
    public function unsubscribe($contactId, $listId, $subscriberId = null, $customerId = null)
    {
        $result = array();
        
        if ($this->isReady()){
        
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
                if ($customerId){
                    $this->_updateCustomerList(true, $customerId, $listId);
                }
                
            } catch(CtctException $ex) {
    
                $result['error'] = false;//sdk throws exception if user is not in list
                $errors = $ex->getErrors();
                $message = (is_array($errors)) ? $errors[0]['error_message'] : $ex->getMessage(); 
                $result['data'] = $message;
            }
            
        } else {
        
            $result['error'] = true;
            $result['data'] = 'API is not available.';
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
            
            $subscriberContact = Mage::getModel('aydus_constantcontact/subscribercontact');
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
    
    /**
     * 
     * @param bool $remove
     * @param int $customerId
     * @param int $listId
     */
    protected function _updateCustomerList($remove, $customerId, $listId)
    {
        try {
        
            $customerList = Mage::getModel('aydus_constantcontact/customerlist');
            $collection = $customerList->getCollection();
            $collection->addFieldToFilter('customer_id',$customerId);
            $collection->addFieldToFilter('list_id',$listId);
            
            if ($collection->getSize() > 0){
                $customerList = $collection->getFirstItem();
            }
                    
            if (!$remove){
        
                $datetime = date('Y-m-d H:i:s');
        
                if (!$customerList->getId()){
                    $customerList->setCreatedAt($datetime);
                }
        
                $customerList->setCustomerId($customerId)
                ->setListId($listId)
                ->setUpdatedAt($datetime)
                ->save();
        
            } else {
                
                if ($customerList->getId()){
                    $customerList->delete();
                }
        
            }
        
        } catch (Exception $e){
            Mage::log($e->getMessage(), null, 'aydus_constantcontact.log');
        
        }
                
    }
    
}
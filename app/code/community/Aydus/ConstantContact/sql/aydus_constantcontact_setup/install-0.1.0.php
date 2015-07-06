<?php

/**
 * ConstantContact setup
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

$this->startSetup();

try {

    //create contact_id customer attribute
    $customerEntityTypeId = $this->getEntityTypeId('customer');
    $attributeCode = 'contact_id';
    $attributeId = $this->getAttributeId($customerEntityTypeId, $attributeCode);
    
    if (!$attributeId){
    
        $frontendLabel = array('Constant Contact ID');
    
        $this->addAttribute('customer', $attributeCode, array(
                'type' => 'int',
                'input' => 'text',
                'label' => $frontendLabel[0],
                'source' => '',
                'backend' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'visible_on_front' => false,
                'note' => 'Constant Contact ID'
        ));
        
        //assign to forms
        $usedInForms = array(
                'adminhtml_customer',
        );    
    
        $attributeId = $this->getAttributeId($customerEntityTypeId, $attributeCode);
        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
        $attribute->load($attribute->getId());
        $attribute->setUsedInForms($usedInForms)->setIsSystem(0)->setSortOrder(150)->setIsUsedForCustomerSegment(1);
        $attribute->save();
    }
    
    //create table of newsletter subscriber contact ids
    $this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_constantcontact_subscriber_contact')} (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `subscriber_id` INT(11) NOT NULL,
      `contact_id` BIGINT(20) NOT NULL,
      `created_at` DATETIME NOT NULL,
      `updated_at` DATETIME NOT NULL,
      PRIMARY KEY ( `id` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    
    //create table to hold customer lists
    $this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_constantcontact_customer_list')} (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) NOT NULL,
    `list_id` BIGINT(20) NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY ( `id` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    
    //create table to hold configuration data
    $this->run("CREATE TABLE IF NOT EXISTS {$this->getTable('aydus_constantcontact_config')} (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `config_key` VARCHAR(255) NOT NULL,
    `config_value` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY ( `id` ),
    UNIQUE KEY( `config_key` )
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $datetime = date('Y-m-d H:i:s');
    
    $insertSql = "INSERT IGNORE INTO {$this->getTable('aydus_constantcontact_config')} 
    (`config_key`, `config_value`, `created_at`, `updated_at`) 
    VALUES ('current_transactions', '0', '$datetime', '$datetime');";

    $this->run($insertSql);
    
} catch(Exception $e){
    
    Mage::log($e->getMessage(), null, 'aydus_constantcontact.log');
}

$this->endSetup();
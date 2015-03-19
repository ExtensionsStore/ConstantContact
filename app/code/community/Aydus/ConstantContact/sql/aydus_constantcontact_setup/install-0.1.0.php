<?php

/**
 * ConstantContact setup
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author     	Aydus Consulting <davidt@aydus.com>
 */

$this->startSetup();
echo 'ConstantContact setup started...<br />';

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

echo 'ConstantContact setup ended.<br />';

$this->endSetup();
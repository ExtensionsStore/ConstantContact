<?php

/**
 * ConstantContact shell
 *
 * @category    Aydus
 * @package     Aydus_ConstantContact
 * @author      Aydus <davidt@aydus.com>
 */

require_once 'abstract.php';

class Aydus_ConstantContact_Shell_Sync extends Mage_Shell_Abstract {

	/**
	 * Run script
	 * 
	 * @return void
	 */
	public function run() {
        $action = $this->getArg('action');
        if (empty($action)) {
            echo $this->usageHelp();
        } else {
            $actionMethodName = $action . 'Action';
            if (method_exists($this, $actionMethodName)) {
                $this->$actionMethodName();
            } else {
                echo "Action $action not found!\n";
                echo $this->usageHelp();
                exit(1);
            }
        }
	}
	
	public function runAllAction()
	{
	    $this->syncAction();
	}
	
	public function syncAction()
	{
        $limit = (int)$this->getArg('limit');
	    $model = Mage::getSingleton('aydus_constantcontact/constantcontact');
	    	
	    echo $model->syncSubscribersContacts($limit) . "\n";
	}	
	
	/**
	 * Retrieve Usage Help Message
	 *
	 * @return string
	 */
	public function usageHelp() {
		$help = 'Available actions: ' . "\n";
		$methods = get_class_methods($this);
		foreach ($methods as $method) {
			if (substr($method, -6) == 'Action') {
				$help .= '    -action ' . substr($method, 0, -6) . ' -limit 0';
				$helpMethod = $method.'Help';
				if (method_exists($this, $helpMethod)) {
					$help .= $this->$helpMethod();
				}
				$help .= "\n";
			}
		}
		return $help;
	}



}

$shell = new Aydus_ConstantContact_Shell_Sync();
$shell->run();
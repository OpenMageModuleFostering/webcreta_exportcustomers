<?php

class Webcreta_Exportcustomers_Adminhtml_ExportcustomersController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

	public function exportcustomersAction() {
		$data = $this->getRequest()->getPost();
		try{
			if(isset($data['filename'])){
				$filename=$data['filename'];
			}
			else{
				$filename="exportcustomers";
			}
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');
			if($data['customers_type']=="all"){
				$query = 'SELECT * FROM ' . $resource->getTableName('sales/order');
				$results = $readConnection->fetchAll($query);
				$email_arr=array();
				$i=0;$j=0;
				$filename=$_POST['filename'];
				
				foreach($results as $r){
					$email_arr[$i]=array($r['customer_email'],$r['customer_firstname'],$r['customer_middlename'],$r['customer_lastname'],$r['customer_suffix'],$r['customer_prefix'],$r['customer_dob'],$r['customer_gender'],Mage::getModel('customer/group')->load($r['customer_group_id'])->getCustomerGroupCode());
					$i++;
				}
				$query2 = 'SELECT * FROM customer_entity';
				$email_arr2=array();
				$results2 = $readConnection->fetchAll($query2);
				foreach($results2 as $r2){
					
					$customer = Mage::getModel("customer/customer"); 
					$customer->setWebsiteId(Mage::app()->getWebsite('admin')->getId()); 
					$customer->load($r2['entity_id']);
					$email_arr2[$j]=array($customer->getEmail(),$customer->getFirstname(),$customer->getMiddlename(),$customer->getLastname(),$customer->getSuffix(),$customer->getPrefix(),$customer->getDob(),$customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender')),Mage::getModel('customer/group')->load($customer->getGroupId())->getCustomerGroupCode());
					$j++;
					
				}
				$array_both = array_merge($email_arr,$email_arr2);
				$array_both = array_map("unserialize", array_unique(array_map("serialize", $array_both)));
				$content="Email,First name,Middle name,Last name,Suffix,Prefix,Date of birth,Gender,Group"."\n";
				foreach($array_both as $em){
					$content.=$em[0];
					$content.=",";
					$content.=$em[1];
					$content.=",";
					$content.=$em[2];
					$content.=",";
					$content.=$em[3];
					$content.=",";
					$content.=$em[4];
					$content.=",";
					$content.=$em[5];
					$content.=",";
					$content.=$em[6];
					$content.=",";
					$content.=$em[7];
					$content.=",";
					$content.=$em[8];
					$content.=",";
					$content.=$em[9];
					$content.="\n";
				}
			}
			//echo $content;
			if($data['customers_type']=="registered"){
				$query2 = 'SELECT * FROM customer_entity';
				$email_arr2=array();
				$j=0;
				$results2 = $readConnection->fetchAll($query2);
				foreach($results2 as $r2){
					$customer = Mage::getModel("customer/customer"); 
					$customer->setWebsiteId(Mage::app()->getWebsite('admin')->getId()); 
					$customer->load($r2['entity_id']);
					$email_arr2[$j]=array($customer->getEmail(),$customer->getFirstname(),$customer->getMiddlename(),$customer->getLastname(),$customer->getSuffix(),$customer->getPrefix(),$customer->getDob(),$customer->getResource()->getAttribute('gender')->getSource()->getOptionText($customer->getData('gender')),Mage::getModel('customer/group')->load($customer->getGroupId())->getCustomerGroupCode());
					$j++;
				}
				$content="Email,First name,Middle name,Last name,Suffix,Prefix,Date of birth,Gender,Group"."\n";
				foreach($email_arr2 as $em){
					$content.=$em[0];
					$content.=",";
					$content.=$em[1];
					$content.=",";
					$content.=$em[2];
					$content.=",";
					$content.=$em[3];
					$content.=",";
					$content.=$em[4];
					$content.=",";
					$content.=$em[5];
					$content.=",";
					$content.=$em[6];
					$content.=",";
					$content.=$em[7];
					$content.=",";
					$content.=$em[8];
					$content.=",";
					$content.=$em[9];
					$content.="\n";
				}
			}
			if($data['customers_type']=="guests"){
				$query = 'SELECT * FROM ' . $resource->getTableName('sales/order');
				$results = $readConnection->fetchAll($query);
				$email_arr=array();
				$i=0;
				$filename=$_POST['filename'];
				foreach($results as $r){
					$email_arr[$i]=array($r['customer_email'],$r['customer_firstname'],$r['customer_middlename'],$r['customer_lastname'],$r['customer_suffix'],$r['customer_prefix'],$r['customer_dob'],$r['customer_gender'],Mage::getModel('customer/group')->load($r['customer_group_id'])->getCustomerGroupCode());
					$i++;
				}
				$email_unique = array_map("unserialize", array_unique(array_map("serialize", $email_arr)));
				$content="Email,First name,Middle name,Last name,Suffix,Prefix,Date of birth,Gender,Group"."\n";
				foreach($email_unique as $em){
					$customer = Mage::getModel('customer/customer');
					$customer->setWebsiteId(Mage::app()->getWebsite('admin')->getId());
					$customer->loadByEmail(trim($em));
					if (!$customer->getId()) {
						$content.=$em[0];
						$content.=",";
						$content.=$em[1];
						$content.=",";
						$content.=$em[2];
						$content.=",";
						$content.=$em[3];
						$content.=",";
						$content.=$em[4];
						$content.=",";
						$content.=$em[5];
						$content.=",";
						$content.=$em[6];
						$content.=",";
						$content.=$em[7];
						$content.=",";
						$content.=$em[8];
						$content.=",";
						$content.=$em[9];
						$content.="\n";
					}
				}
			}
			$fp = fopen(Mage::getBaseDir('media'). "/exportcustomers/".$filename.".csv","wb");
			fwrite($fp,$content);
			fclose($fp);
			Mage::getSingleton('adminhtml/session')->addSuccess('Customers are successfully exported. Click <a href="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'media/exportcustomers/'.$filename.'.csv" download>here</a> to download');
		}catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		$this->_redirect('*/*/');
	}	
}
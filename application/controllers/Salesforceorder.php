<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Salesforceorder extends CI_Controller { 

	public function __construct()
	{

	parent::__construct();

	  $config = array();  
        $config['protocol'] = 'smtp';  
        $config['smtp_host'] = 'smtpout.secureserver.net';  
        $config['smtp_user'] = 'mitr@prestomitr.com';  
        $config['smtp_pass'] = 'Presto@123!@#';   
        $config['smtp_port'] = 587;  
        $this->email->initialize($config);  
        $this->email->set_newline("\r\n");  
        $this->load->library('email', $config);
		


	}


function getsalesforceneworderid()
{
$uriseg=$this->uri->segment(3);
$flag=$this->uri->segment(4);

/** ADD DATA **/
$newdata=array('runningdate'=>date('Y-m-d H:i:s'),'runningtime'=>date('H:i:s'));
$this->db->insert('salesforcecronstatus',$newdata);

/** END **/

	/**$subjectname = "MITR Login Credential";
					$this->email->set_mailtype("html");
					$this->email->to('sdsrbh5@gmail.com');
					$this->email->bcc('mangleshup@gmail.com,sdsrbh5@gmail.com');
					$this->email->from('mitr@prestomitr.com');
    				$this->email->subject("MITR THINGS");
    				$this->email->message("HELLO MY NAME IS SAURABH");
    				$result11=$this->email->send(); **/



$post = [
    'username' => 'gaurav@prestogroup.in',
    'password' => 'customer@2020',
    'grant_type'   => 'password',
    'client_id'=>'3MVG9Y6d_Btp4xp4S10slvMAduKdtgZQSQHCtfSzx3tl1wgyumCAXZ5bauqfwVO5v3yE1ANqVgZLVp7JOVvLh',
    'client_secret'=>'4FE70F9317CE78F0D7731B6B7BED0F3B13950A7284D1AA998BDAE67970AE40B4'
];


$cURLConnection = curl_init('https://login.salesforce.com/services/oauth2/token');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $post);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
curl_close($cURLConnection);
$result=json_decode($apiResponse,true);
if(count($result)>0)
{

$acctoken=$result['access_token'];	
//echo $acctoken; exit;

if($acctoken<>'')
{


/** CHECK LAST SALESFORCE ORDER NO **/

if($uriseg=='')
{
$sforde=$this->db->select('internal_order_no')->from('salesforce_orders')->order_by('internal_order_no','DESC')->limit('1')->get();
if($sforde->num_rows()>0)
{
	foreach($sforde->result() as $sforde1);
$greaterfrom=$sforde1->internal_order_no;
}else
{
	$greaterfrom="11842";
}

}else
{

$greaterfrom=$uriseg;
}


/** END **/


/** ALL ITEM HERE **/
if($uriseg=='')
{

 $qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Freight__c,Packing_Type__c,Opportunity__c,IO_Ref_No__c,GST_No__c,Remarks__c,PO_Uploaded__c+FROM+Internal_Factory_Order__c+WHERE+IORefNumber__c>".$greaterfrom;
 
 }else
 {
  $qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Freight__c,Packing_Type__c,Opportunity__c,IO_Ref_No__c,GST_No__c,Remarks__c,PO_Uploaded__c+FROM+Internal_Factory_Order__c+WHERE+IORefNumber__c=".$greaterfrom;
 }

    $request_headers = array("Authorization:Bearer ".$acctoken);
 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qustatement); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    $season_data = curl_exec($ch);

    if (curl_errno($ch)) {
        print "Error: " . curl_error($ch);
        exit();
    }	

  
   curl_close($ch);
   
 
		$iodata=json_decode($season_data,true);
//echo "<pre>"; print_r($iodata); exit;
		if(count($iodata)>0)
		{

		if (array_key_exists('totalSize', $iodata)) {


		$datasize=$iodata['totalSize'];

		if($datasize>0)
		{
		for($i=0;$i<$datasize;$i++)
		{

		$internalid=$iodata['records'][$i]['Id'];
		$freight=$iodata['records'][$i]['Freight__c'];
		$packing=$iodata['records'][$i]['Packing_Type__c'];
		$opportunity=$iodata['records'][$i]['Opportunity__c'];
		$iorefno=$iodata['records'][$i]['IO_Ref_No__c'];
		$remarks=$iodata['records'][$i]['Remarks__c'];
		$pouploadedname=$iodata['records'][$i]['PO_Uploaded__c'];
		$gst=$iodata['records'][$i]['GST_No__c'];
		

		$dd=sfpo.$iorefno.'.pdf';


		$myfile = fopen($dd, "w") or die("Unable to open file!");
		
		$this->insertfirstbasicdata($freight,$packing,$opportunity,$iorefno,$internalid,$remarks,$acctoken,$pouploadedname,$gst);

		$this->getopportunitydataandinsert($opportunity,$acctoken);

		$this->opporoductdata($acctoken,$opportunity);

	


		}


		
			

		/** END DATA SIZE **/



		/** GET PRODUCTS **/




		/** END **/

		}


		/** END ARRAY KEY EXIST **/
		}
		/** END IODATA COUNT **/   	
		}
   


/** GET PRODUCTS ***/




   /** END**/



/** END **/



/** HERE **/


}




}



if($uriseg<>'')
{

$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">ORDER FETCHED</div><br/>');
		redirect(page_url.'FMS/salesforceorder/'.$flag);

}

	
}

function insertfirstbasicdata($freight,$packing,$opportunity,$iono,$internalid,$remarks,$acctoken,$pouploadedname,$gst)
{

	if($opportunity<>'' && $iono<>'')
	{
	$svail=$this->db->select('order_id')->from('salesforce_orders')->where('internal_order_no',$iono)->get();
	if($svail->num_rows()==0)
	{
		$data=array('internal_order_no'=>$iono,'packing_type'=>$packing,'freight_type'=>$freight,'opportunityid'=>$opportunity,'added_on'=>date('Y-m-d H:i:s'),'order_status'=>'1','sforderno'=>$internalid,'remarks'=>$remarks,'gstno'=>$gst);
		$this->db->insert('salesforce_orders',$data);

		$this->getattachmentformorderid($internalid,$acctoken,$opportunity,$pouploadedname,$iono);

	}



	}

	return true;

}



function getopportunitydataandinsert($oppid,$acctoken)
{


$qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Type__c,Owner_code__c,Name,purchase_order_Ref_No__c,Primary_Contact_del__c,Primary_Contact_Email__c,Primary_Contact_Mobile_No__c,Discount_Percent__c,Amount,Advance_Amount__c,Pr_Contact_Name__c,Pin_Code__c,Payment_Term__c,i__c,Installation_Tax_Charges__c,Installation_Charges__c,Billing_Address__c,Finsys_SO_No__c+FROM+Opportunity+WHERE+Id='".$oppid."'";

    $request_headers = array("Authorization:Bearer ".$acctoken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qustatement); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    $season_data1 = curl_exec($ch);

    if (curl_errno($ch)) {
        print "Error: " . curl_error($ch);
        exit();
    }

  
   curl_close($ch);
   
  // echo $season_data1; exit;
   $iodata1=json_decode($season_data1,true);
//echo "<pre>"; print_r($iodata1); exit;
	if(count($iodata1)>0)
	{
		if (array_key_exists('totalSize', $iodata1)) 
		{
			$datasize=$iodata1['totalSize'];
			if($datasize>0)
			{

			for($i=0;$i<$datasize;$i++)
			{
				$odtype=$iodata1['records'][$i]['Type__c'];
				$sono=$iodata1['records'][$i]['Finsys_SO_No__c'];

				if(strpos($odtype, 'sales') !== false){

				$odstype="SALE";
				}else if(strpos($odtype, 'Export') !== false){

				$odstype="SALE";
				}else
				{
					$odstype="SERVICE";
				}
				if($iodata1['records'][$i]['i__c']=='Required')
				{
					$ir=1;
					if(($iodata1['records'][$i]['Installation_Tax_Charges__c']=='') ||($iodata1['records'][$i]['Installation_Tax_Charges__c']=='0')) 
					{
						$instterm='Free INSTALLATION';
						$insch='0';
					}else
					{
						$instterm="CHARGABLE";
						$insch='1';
					}
					$insamount=$iodata1['records'][$i]['Installation_Tax_Charges__c'];
					
				}else
				{
					$ir=0;
					$instterm='';
					$insamount='0';
					$insch='0';
				}

				$ocode=$this->getuseridandsaleszone($iodata1['records'][$i]['Owner_Code__c']);
				if(count($ocode)>0)
				{
					$saleszone=$ocode['saleszone'];
					$user_id=$ocode['user_id'];

				}else
				{
					$saleszone=0;
					$user_id=0;

				}


				
				$dataopp=array('order_type'=>$odstype,'company_name'=>$iodata1['records'][$i]['Name'],'po_number'=>$iodata1['records'][$i]['purchase_order_Ref_No__c'],'contact_person'=>$iodata1['records'][$i]['Pr_Contact_Name__c'],'email'=>$iodata1['records'][$i]['Primary_Contact_Email__c'],'mobile_number'=>$iodata1['records'][$i]['Primary_Contact_Mobile_No__c'],'discount'=>$iodata1['records'][$i]['Discount_Percent__c'],'order_value_after_discount'=>$iodata1['records'][$i]['Amount'],'advance_amount'=>$iodata1['records'][$i]['Advance_Amount__c'],'payment_terms'=>$iodata1['records'][$i]['Payment_Term__c'],'installation_type'=>$iodata1['records'][$i]['i__c'],'installation_charges'=>$insch,'installation_amount'=>$insamount,'updated_on'=>date('Y-m-d H:i:s'),'marketing_person'=>$user_id,'address'=>$iodata1['records'][$i]['Billing_Address__c'],'pincode'=>$iodata1['records'][$i]['Pin_Code__c'],'sono'=>$sono);


					$this->updatecommondata('salesforce_orders',$dataopp,$oppid,'opportunityid');

			}


			}



		}


	}




}



function updatecommondata($table,$data,$oppid,$condition)
{
		$this->db->where($condition,$oppid);
		$this->db->update($table,$data);

		return true;
}


function getuseridandsaleszone($ownercode)
{
$arya=array();
$Restye=$this->db->select('business_location,user_id')->from('system_users')->where('salesforce_code',$ownercode)->get();
if($Restye->num_rows()>0)
{
	foreach($Restye->result() as $Restye1);

	$arya['saleszone']=$Restye1->business_location;
	$arya['user_id']=$Restye1->user_id;



}


return $arya;

}


function getproductidbycode($code,$uniqcode)
{

$Rest=$this->db->select('id')->from('presto_instruments')->where('product_unique_code',trim($uniqcode))->get();
if($Rest->num_rows()>0)
{

foreach($Rest->result() as $Rest1);

return $Rest1->id;

}else
{

	return 0;
}

}



function insertjobcards($totaljobcard,$insid,$oppoid,$litemid,$qty)
{


$odid=$this->getbasicinfofororder($oppoid);

$data=array('order_id'=>$odid,'item_id'=>$insid,'qty'=>$qty,'lineitemid'=>$litemid);

$this->db->insert('salesforce_order_instruments',$data);

return true;



}


function getbasicinfofororder($oppoid)
{
	$rrsyte=$this->db->select('order_id')->from('salesforce_orders')->where('opportunityid',$oppoid)->get();
	if($rrsyte->num_rows()>0)
	{
		foreach($rrsyte->result() as $rrsyte1);

		return $rrsyte1->order_id;
	}else
	{

		return 0;
	}


}


function opporoductdata($acctoken,$opportunity)
{


		 /** TO GET OPPORTUNITY PRODUCT DATA **/
   
	$qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Name,(SELECT+Id,Quantity,UnitPrice,ProductCode,Product_Unique_Code__c,TotalPrice,PricebookEntry.Name,PricebookEntry.Product2.Family+FROM+OpportunityLineItems)+FROM+opportunity+WHERE+Id='".$opportunity."'";
	$request_headers = array("Authorization:Bearer ".$acctoken);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $qustatement); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

	$season_data = curl_exec($ch);

	if (curl_errno($ch)) {
	print "Error: " . curl_error($ch);
	exit();
	}


	curl_close($ch);

	$iodata2=json_decode($season_data,true);
	if(count($iodata2)>0)
		{

		if (array_key_exists('totalSize', $iodata2)) {


		$datasize=$iodata2['totalSize'];

		if($datasize>0)
		{
		for($k=0;$k<$datasize;$k++)
		{

			if($iodata2['records'][$k]['OpportunityLineItems']['totalSize']>0)
			{

				$datasize1=$iodata2['records'][$k]['OpportunityLineItems']['totalSize'];
				if($datasize1>0)
				{


				for($j=0;$j<$datasize1;$j++)
				{

				$lineitemid= $iodata2['records'][$k]['OpportunityLineItems']['records'][$j]['Id'];

				$prdcode=$iodata2['records'][$k]['OpportunityLineItems']['records'][$j]['ProductCode'];

				$prduniquecode=$iodata2['records'][$k]['OpportunityLineItems']['records'][$j]['Product_Unique_Code__c'];
				
				$lineitemqty=$iodata2['records'][$k]['OpportunityLineItems']['records'][$j]['Quantity'];

				$insid=$this->getproductidbycode($prdcode,$prduniquecode);

				$this->insertjobcards($datasize1,$insid,$opportunity,$lineitemid,$lineitemqty);
				}
			}

			}


		
		}

		/** END DATA SIZE **/

		}


		/** END ARRAY KEY EXIST **/
		}
		/** END IODATA COUNT **/   	
		}
   

}



function getattachmentformorderid($sforderid,$acctoken,$opportunity,$pouploadedname,$iono)
{

$popname=str_replace(' ','+',$pouploadedname);

 $qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Name,Description+FROM+Attachment+WHERE+ParentId='".$opportunity."'+AND+Name='".$popname."'";

    $request_headers = array("Authorization:Bearer ".$acctoken);
 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $qustatement); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    $season_data = curl_exec($ch);
    //echo "<pre>"; print_r($season_data); exit;

    if (curl_errno($ch)) {
        print "Error: " . curl_error($ch);
        exit();
    }	

$a=json_decode($season_data,true);

//echo "<pre>"; print_r($a); exit;
if(count($a)>0)
{

if (array_key_exists('totalSize', $a)) {


$datasize=$a['totalSize'];

if($datasize>0)
{
	$attchmentid=$a['records']['0']['Id'];

	$this->makepdfforpo($attchmentid,$acctoken,$popname,$iono);

}


}

}



}


function makepdfforpo($attachment,$acctoken,$popname,$iono)
{


	$qustatement="https://presto.my.salesforce.com/services/data/v50.0/sobjects/Attachment/".$attachment."/body";


	$request_headers = array("Authorization:Bearer ".$acctoken);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $qustatement); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

	$season_data = curl_exec($ch);

	if (curl_errno($ch)) {
	print "Error: " . curl_error($ch);
	exit();
	}	


	
	curl_close($ch);


	$dd=sfpo.$iono.'.pdf';
	

	$myfile = fopen($dd, "w") or die("Unable to open file!");
	
	fwrite($myfile, $season_data) or die('unable to write');
	
	fclose($myfile);


} 


function fetchmissingorder()
{

$iono=trim($this->input->post('iono'));
$seg=$this->input->post('uri');

$Resty=$this->db->select('order_id,company_name')->from('salesforce_orders')->where('internal_order_no',$iono)->where('order_status','0')->get();
if($Resty->num_rows()>0)
{
	foreach($Resty->result() as $Resty1)
	{
		if($Resty1->company_name=='')
		{

		/** DELETE INCOMPLETE ORDER **/
		$this->db->where('order_id',$Resty1->order_id);
		$this->db->delete('salesforce_orders');

		$this->db->where('order_id',$Resty1->order_id);
		$this->db->delete('salesforce_order_instruments');
		/** END **/


		redirect(page_url.'Salesforceorder/getsalesforceneworderid/'.$iono.'/'.$seg);

		}else
		{

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">CANNOT FETCH! ORDER IS ALREADY SYNCED OR HAS BEEN MOVED</div><br/>');
		redirect(page_url.'FMS/salesforceorder/'.$seg);

		}
			
		
	}

}else
{

redirect(page_url.'Salesforceorder/getsalesforceneworderid/'.$iono.'/'.$seg);

}


}


function fetchservicemissingorder()
{

$iono=trim($this->uri->segment(3));
$seg=$this->uri->segment(4);

$Resty=$this->db->select('order_id,company_name')->from('salesforce_orders')->where('internal_order_no',$iono)->where('order_status','0')->get();
if($Resty->num_rows()>0)
{
	foreach($Resty->result() as $Resty1)
	{
		if($Resty1->company_name=='')
		{

		/** DELETE INCOMPLETE ORDER **/
		$this->db->where('order_id',$Resty1->order_id);
		$this->db->delete('salesforce_orders');

		$this->db->where('order_id',$Resty1->order_id);
		$this->db->delete('salesforce_order_instruments');
		/** END **/


		redirect(page_url.'Salesforceorder/getsalesforceneworderid/'.$iono.'/'.$seg);

		}else
		{

		$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">CANNOT FETCH! ORDER IS ALREADY SYNCED OR HAS BEEN MOVED</div><br/>');
		redirect(page_url.'FMS/salesforceorder/'.$seg);

		}
			
		
	}

}else
{

redirect(page_url.'Salesforceorder/getsalesforceneworderid/'.$iono.'/'.$seg);

}


}

}
	

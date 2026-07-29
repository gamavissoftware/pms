<?php

/** GET AUTHENTICATION TOKEN FROM SALESFORCE **/
$post = [
    'username' => 'gaurav@prestogroup.in.sb',
    'password' => 'revive@1234',
    'grant_type'   => 'password',
    'client_id'=>'3MVG9e2mBbZnmM6n6v3xRX1w8LGCPIIl08mqYxtmCuuOSzQWffpE_BnslD1bqGORVKXI.Ow1379u_F1UIzUUs',
    'client_secret'=>'FE7780D37391ED5B3556423CD23A540A909D5E019824EBB417980B532EB45DAA'
];



$cURLConnection = curl_init('https://test.salesforce.com/services/oauth2/token');
curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $post);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

$apiResponse = curl_exec($cURLConnection);
curl_close($cURLConnection);
$result=json_decode($apiResponse,true);
if(count($result)>0)
{

$acctoken=$result['access_token'];	

if($acctoken<>'')
{
/** CURL get opportunity**/

//$qustatement="https://presto--sb.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Account__c,IO_Ref_No__c,Name+FROM+Internal_Factory_Order__c";

/** TO GET OPPORTUNITY DATA **/
$qustatement="https://presto--sb.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Type__c,Name,purchase_order_Ref_No__c,Primary_Contact_del__c,Primary_Contact_Email__c,Primary_Contact_Mobile_No__c,Discount_Percent__c,Amount,Advance_Amount__c,Payment_Term__c,i__c,Installation_Tax_Charges__c+FROM+Opportunity";

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
   
   $iodata=json_decode($season_data);
      
   /*** END **/
   
   
   /** TO GET OPPORTUNITY PRODUCT DATA **/
   
	$qustatement="https://presto--sb.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Name,(SELECT+Id,Quantity,UnitPrice,ProductCode,TotalPrice,PricebookEntry.Name,PricebookEntry.Product2.Family+FROM+OpportunityLineItems)+FROM+opportunity+WHERE+Id ='006N000000H0qTgIAJ'";
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
   
   $iodata=json_decode($season_data);
   
   //echo "<pre>"; print_r($iodata); exit;
   
   /** END**/
   
   
   //productlineitemcode 00kN0000007bCPJIA2
     /** TO GET ACCOUNT DATA **/
   
	$qustatement="https://presto--sb.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Account.BillingAddress+FROM+Opportunity+WHERE+Id ='006N000000H0qTgIAJ'";
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
   
   $iodata=json_decode($season_data);
   
   //echo "<pre>"; print_r($iodata); exit;
   
   /** END**/
   
   
   /** TO GET INTERNAL ORDER DATA **/
   
   $qustatement="https://presto--sb.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Freight__c,Packing_Type__c+FROM+Internal_Factory_Order__c+WHERE+Opportunity__c ='006N000000H0qTgIAJ'";
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
   
   $iodata=json_decode($season_data);
   
  // echo "<pre>"; print_r($iodata); exit;
   
   /** END **/
   
   }else
   {
   
   echo "ACCESS TOKEN NOT FOUND"; exit;
   }
    
    
	
/** END **/





}


/** END **/





?>

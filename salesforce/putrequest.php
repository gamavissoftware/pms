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
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://presto--sb.my.salesforce.com/services/data/v50.0/sobjects/Opportunity/006N000000H0qTg/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

$post = array(
    'file' => 	realpath('f.json')
);

curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"Production_Ready_Status__c\":\"false\"}");

$headers = array();
$headers[] = 'Authorization: Bearer '.$acctoken;
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
//echo $result; exit;
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);



/*** LINE ITEM UPDATE CODE **/

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://presto--sb.my.salesforce.com/services/data/v50.0/sobjects/OpportunityLineItem/00kN0000007bCPJIA2/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');

$post = array(
    'file' => 	realpath('f.json')
);

// FOR DATE YYYY-MM-DDThh:mm:ssZ

//$data=array("Serial_No__c"=>"123444","Machine_No__c"=>"12322");
//$esjson=addslashes('"'.json_encode($data).'"');
curl_setopt($ch, CURLOPT_POSTFIELDS,"{\"Serial_No__c\":\"123444\",\"Machine_No__c\":\"12322\"}");

//{\"Serial_No__c\":\"123444\",\"Machine_No__c\":\"12322\"}


$headers = array();
$headers[] = 'Authorization: Bearer '.$acctoken;
$headers[] = 'Content-Type: application/json';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
echo $result; exit;
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

/** end **/


}

}


?>

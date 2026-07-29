<?php


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


//00P6F00003ERyXGUA1

$qustatement="https://presto.my.salesforce.com/services/data/v50.0/sobjects/Attachment/00P6F00003FVPNfUAP/body";


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

  
 // echo $season_data; exit;
   curl_close($ch);



$dd=$_SERVER['DOCUMENT_ROOT'].'/a.pdf';
//echo $dd; exit;

$myfile = fopen($dd, "w") or die("Unable to open file!");
fwrite($myfile, $season_data);
fclose($myfile);



 $qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Name,Description+FROM+Attachment+WHERE+ParentId='0066F000017WROr'";

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

$a=json_decode($season_data,true);
//echo "<pre>"; print_r($a); 
  
 // echo $season_data; exit;
   curl_close($ch);




   $qustatement="https://presto.my.salesforce.com/services/data/v37.0/query/?q=SELECT+Id,Name,Description+FROM+Attachment+WHERE+ParentId='a0B6F00001hnlaC'+AND+Name='Internal+Order.pdf'";

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

$a=json_decode($season_data,true);
echo "<pre>"; print_r($a); exit;
  
 // echo $season_data; exit;
   curl_close($ch);




}



}
























?>
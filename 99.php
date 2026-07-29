<?php 
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://www.99acres.com/99api/v1/getmy99Response/OeAuXClO43hwseaXEQ/uid/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_USERAGENT=>"Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0",
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => array("xml"=>"<?xml version='1.0'?><query><user_name>SRISRI2022</user_name><pswd>99Acres@123</pswd><start_date>2023-05-17 00:00:00</start_date><end_date>2023-05-18 00:00:00</end_date></query>"),
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "content-type: application/xml",
    "postman-token: 1b2aa05b-5511-2108-4aec-fb99852624fb"
  ),

));

$response = curl_exec($curl);
echo "<pre>"; print_r($response); exit;
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}


?>
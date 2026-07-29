<?php
include 'phpqrcode/qrlib.php';
	$value = "https://easemysale.com/hpcl/Open_leads/open_lead/MQ==";
	//QR code content  
	$errorCorrectionLevel ='H';//Fault tolerance level  
	$matrixPointSize = 6;//Generate image size  
	//Generate QR code image
	$name=time().".png";
	$path = SITE_ROOT.'qrimages/';

	$filename =$path.$name;
	$file1=page_url.'qrimages/'.$name;
	QRcode::png($value,$filename, $errorCorrectionLevel, $matrixPointSize, 2);  
	
	$logo =SITE_ROOT.'qrimages/Whstapp.png';

					;//Prepared logo image   
	$QR = $filename;//Original QR code image that has been generated  

	if (file_exists($logo)) {  
		$QR = imagecreatefromstring(file_get_contents($QR));//Target image connection resource.
		$logo = imagecreatefromstring(file_get_contents($logo));//Source image connection resource.
		$QR_width = imagesx($QR);//QR code image width   
		$QR_height = imagesy($QR);//The height of the QR code image   
		$logo_width = imagesx($logo);//logo image width   
		$logo_height = imagesy($logo);//logo image height   
		$logo_qr_width = $QR_width/4;//The width of the logo after the combination (accounting for 1/5 of the QR code)
		$scale = $logo_width/$logo_qr_width;//The width scaling ratio of the logo (its own width/combined width)
		$logo_qr_height = $logo_height/$scale;//The height of the logo after the combination
		$from_width = ($QR_width-$logo_qr_width)/2;//The coordinate point of the upper left corner of the logo after the combination
		
		//Regroup the picture and resize it
		/*
		 * imagecopyresampled() copies a square area in an image (source image) to another image
		 */
		imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height); 
	}   
  
	//Output picture  
	imagepng($QR,time()."png");  
	imagedestroy($QR);
	imagedestroy($logo);
	$file1=page_url.'qrimages/'.$name;
	echo '<img src="'.$file1.'" alt="Use WeChat to scan and pay">';   

?>
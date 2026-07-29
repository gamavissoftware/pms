<?php
defined('SITE_ROOT') OR define ('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
$size = $_FILES['audio_data']['size']; //the size in bytes
$input = $_FILES['audio_data']['tmp_name']; //temporary name that PHP gave to the uploaded file
$output = time().".mp3"; //letting the client control the filename is a rather bad idea
//move the file from temp name to local folder using $output name
move_uploaded_file($input,SITE_ROOT.'audio_files/'.$output);
echo $output;
?>
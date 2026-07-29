<!--
<?php 
$uri1=$this->uri->segment(1);
$uri2=$this->uri->segment(2);
$uri3=$this->uri->segment(3);

if($uri3 == '') {
  $totalUri = $uri1.'/'.$uri2; 
} else {
  $totalUri = $uri1.'/'.$uri2.'/'.$uri3;
}


$quer=$this->db->select('*')->from('system_reports')->where('url',$totalUri)->get();

if($quer->num_rows() >0)
{
   // echo "<pre>";print_r($quer->result());exit;
    foreach($quer->result() as $row);
    $img=sfdocument.'Reportingimage/'.$row->icon;
    $title= $row->title;
    $description=$row->description;
   // $video_url = $row->video;
}else
{
   $img='';
   $title='A';
   $description='B';
  // $video_url = '';
}

if($quer->num_rows() >0)
{
    if($image != '' || $title != '') {

?>
<div class="container-fluid profile-img">
<div class="row">


<div class="col-sm-2">
    <img src="<?php echo $img;?>">
</div>
<div class="col-sm-8">
<h1><?php echo $title;?></h1></br>

<?php echo $description;?>



			              </div>

                    <div class="col-sm-2">
                    <div class="text-center">
                    <a href="javascript:;" target="_blank"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
                    </div>
                    </div>
			</div>
     
            </div>

<?php
}
}
?>

</div>



</div>-->
<?php
$CI =& get_instance();
$CI->load->model('Fms_mismodel');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title>FMS MIS</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-weight:bold;
			}
				.backgroundcolor{
				background-color:#FF6961 !important; 
				color:white;
				font-weight:bold;
			}
			
			.greenbackground
			{
				background-color:yellow !important; 
				color:#fff;
				font-weight:bold;
			}
			
			.tracksheet th,td
{
	text-align:center;
	font-size:12px;
	width:0.05%;
	padding-right: 10px;
    padding-left: 10px;
	
}

 table{
	 display: block;
	  table-layout: fixed; 
    overflow-x: scroll;
	
 }

</style>
<style type="text/css" media="print">

 @page { 
        size: landscape;
    }

	table { page-break-inside:auto }
tr    { page-break-inside:avoid; page-break-after:auto }
thead { display:table-header-group }
tfoot { display:table-footer-group }

 

  .grnhead
{
    display:block;
}
td {font-size:12px;text-align:center;}
th {font-size:13px;text-align:center;color:#F3F3F3;}


.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    
    padding: 3px;
    text-align:center;
}

.col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12 {
        float: left;
   }
   .col-sm-12 {
        width: 100%;
   }
   .col-sm-11 {
        width: 91.66666667%;
   }
   .col-sm-10 {
        width: 83.33333333%;
   }
   .col-sm-9 {
        width: 75%;
   }
   .col-sm-8 {
        width: 66.66666667%;
   }
   .col-sm-7 {
        width: 58.33333333%;
   }
   .col-sm-6 {
        width: 50%;
   }
   .col-sm-5 {
        width: 41.66666667%;
   }
   .col-sm-4 {
        width: 33.33333333%;
   }
   .col-sm-3 {
        width: 25%;
   }
   .col-sm-2 {
        width: 16.66666667%;
   }
   .col-sm-1 {
        width: 8.33333333%;
   }
    html, body {
        height: 99%;    
    }
}

.whatsapp
	{
		display:none;
	}

body{
	font-family: 'Roboto', sans-serif;
}
</style>


    </head>
 

    <body>


        <!-- Navigation Bar-->
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">

                <!-- Page-Title -->
                <div class="row printhide">
                   <div class="col-sm-12">
                        <div class="page-title-box">
					<div class="col-md-4">
                           
                            <h4 class="page-title">FMS MIS</h4>
							</div>
							<div class="col-md-4"></div>
							<div class="col-md-4"><span class="btn btn-success pull-right" onclick="printdiv();">Print Me </span>
                        </div>
                    </div>
                </div>
                </div>
                <!-- end page title end breadcrumb -->
	<span  class="printhide" style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
		<div class="row card-box">
                    <div class="col-sm-12">
                     
					 <style>
.tracksheet th,td
{
	text-align:center;
	font-size:14px;
	text-transform:uppercase;
	width:0.05%;
	padding-right: 10px;
    padding-left: 10px;
	
}

 table{
	 display: block;
	 /**table-layout: fixed; **/
    overflow-x: scroll;
 }
 
.vertical
{
/**transform:rotate(-45deg);	 **/
}

.getbold
{
/**font-weight:bold;**/	
text-align:left;
}

</style>

<?php
$alluser= $CI->Fms_mismodel->getallfmsuser();
if(count($alluser)>0)
{
foreach($alluser as $allusers)
{
	$username= $CI->Fms_mismodel->getsusername($allusers);
	foreach($username as $usernames);
?>
<table border="1" width="100%" class="tracksheet" id="user<?php echo $allusers;?>">
 <thead>
      <tr>
	           <th colspan="3" rowspan="2"  style="background-color:yellow; font-weight:bold;"><div class="col-sm-3 pull-left whatsapp"><button class="btn btn-success button<?php echo $allusers;?> " onclick="getscreenshot(<?php echo $allusers;?>);">Whatsapp</button></div> <div class="col-sm-9">MIS</div></th>
			  <th style="background-color:yellow; font-weight:bold; text-align:center;">Planned</th>
			  <th style="background-color:yellow; font-weight:bold; text-align:center;">Actual</th>
			  <th  rowspan="2" style="background-color:yellow; font-weight:bold; text-align:center;">Diff</th>
			  <th  rowspan="2" style="background-color:yellow; font-weight:bold; text-align:center;">% Score</th>
			  <th rowspan="2" style="background-color:yellow; font-weight:bold; text-align:center;">Total</th>
               
       	  
		
		
      </tr>
	  <?php
	
	$lastWeek = array();
$prevMon = abs(strtotime("this monday"));
$currentDate = abs(strtotime("today"));
$seconds = 86400; //86400 seconds in a day
$dayDiff = ceil( ($currentDate-$prevMon)/$seconds );
if( $dayDiff < 7 )
{
$dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
$prevMon = strtotime( "previous monday", strtotime("-$dayDiff day") );
}
$prevMon = date("Y-m-d",$prevMon);

// create the dates from Monday to Sunday
for($i=0; $i<7; $i++)
{
$d = date("Y-m-d", strtotime( $prevMon." + $i day") );
$lastWeek[]=$d;
}
$current_monday = $lastWeek[0];
$current_saturday = $lastWeek[5];
	
	  $startdate=$current_monday;
	  $enddate=$current_saturday;
	  $flowid= $CI->Fms_mismodel->getsuserwisefms($allusers);
	  
	  ?>
	    <tr>
	          <th style="background-color:yellow; font-weight:bold; text-align:center;"><?php echo $startdate;?></th>
			  <th style="background-color:yellow; font-weight:bold; text-align:center;"><?php echo $enddate;?></th>
			  
               
       	  
		
		
      </tr>
    </thead>
    <tbody>
	<?php
	$userwisescore=array();
	foreach($flowid as $flowids)
	  {
		  $flowname= $CI->Fms_mismodel->getfmsname($flowids);
		  
		  $worknotdonemisfortotaltaskoverall=$CI->Fms_mismodel->worknotdonemisfortotaltaskoverall($startdate,$enddate,$flowids,$allusers);
		  $worknotdonemisfortotaltaskweekly=$CI->Fms_mismodel->worknotdonemisfortotaltaskweekly($startdate,$enddate,$flowids,$allusers);
		  $worknotdonemisfordonetask=$CI->Fms_mismodel->worknotdonemisfordonetask($startdate,$enddate,$flowids,$allusers);
		  $worknotdonemisfornotdonetaskdetails=$CI->Fms_mismodel->worknotdonemisdetailsfordonenotdone($startdate,$enddate,$flowids,$allusers);
		  $diff=$worknotdonemisfordonetask-$worknotdonemisfortotaltaskoverall;
			$worknotdonemisscore=$CI->Fms_mismodel->worknotdonemisscore($worknotdonemisfordonetask,$worknotdonemisfortotaltaskoverall);
			
			/* START SECOND PART **/
		 
		   $weeklyoveralldonetask=$CI->Fms_mismodel->weeklyoveralldonetask($startdate,$enddate,$flowids,$allusers);
		  $weeklytasknotdoneontime=$CI->Fms_mismodel->weeklytasknotdoneontime($startdate,$enddate,$flowids,$allusers);
		if($weeklytasknotdoneontime!=0)
		{
		  $diff2=$weeklytasknotdoneontime-$weeklyoveralldonetask;
		}else
		{
			$diff2=0;
		}
		  $worknotdoneontimemisscore=$CI->Fms_mismodel->worknotdoneontimemisscore($weeklytasknotdoneontime,$weeklyoveralldonetask);
		  $weeklytasknotdoneontimedetails=$CI->Fms_mismodel->weeklytasknotdoneontimedetails($startdate,$enddate,$flowids,$allusers);
		  
		  /** START THIRD PART **/
		  $weeklydifferenceintimedetailsmiscore=$CI->Fms_mismodel->weeklydifferenceintimedetails($startdate,$enddate,$flowids,$allusers);
	
	$totalmisforuser=($worknotdonemisscore)+($worknotdoneontimemisscore)+($weeklydifferenceintimedetailsmiscore);
		 
		?>

      <tr>
	  	  <td rowspan="5" class="vertical"><div class="vertical"><?php echo $usernames;?></div></td>
	  		 
		  <td rowspan="5"><?php echo $flowname;?></td>
        <td class="getbold">% of Work Not Done</td>
               <td><?php echo $worknotdonemisfortotaltaskoverall;?></td>
        <td><?php echo $worknotdonemisfordonetask;?></td>
		
               <td><?php echo $diff;?></td>
			  
			    <td><?php echo $worknotdonemisscore;?></td>
				 <td rowspan="5" style="background-color:red;color:white;font-weight:bold;"><?php echo $totalmisforuser;?> %</td>
       </tr>
	
	  	 <tr>
	  	  <td class="getbold">Detail Description</td>
	  		 
		         <td colspan="3" style="text-align:left;"><?php echo $worknotdonemisfornotdonetaskdetails;?> </td>
        
        <td></td>
               
       
              
     
             
				 
		 </tr>
		
		 <tr>
	  	  <td class="getbold">% of Work not done on time</td>
	  		 
		         <td><?php echo $weeklyoveralldonetask;?></td>
        <td><?php echo $weeklytasknotdoneontime;?></td>
               <td><?php echo $diff2;?></td>
        <td><?php echo $worknotdoneontimemisscore;?></td>
               
        
             
     
             
				 
		 </tr>
		 <tr>
	  	  <td class="getbold">Detail Description</td>
	  		 
		         <td colspan="3" style="text-align:left;"><?php echo $weeklytasknotdoneontimedetails;?></td>
       
        <td></td>
              
       
              
     
             
				 
		 </tr>
		
		 <tr style="border-bottom: 3px dashed #767c10;">
	  	  <td class="getbold">% of Delay in time</td>
	  		 
		         <td></td>
        <td></td>
               <td></td>
        <td><?php echo $weeklydifferenceintimedetailsmiscore;?></td>
              
        
               
     
             
				 
		 </tr>
		
		 <?php
	  }
	  ?>
          </tbody>
</table>
<div style="clear:both;height:15px;"></div>
<?php
}
}else
{
?>

<p style="text-center">NO USER HAS FMS ACCESS</p>

<?php
}
?>
                        </div>
                    </div>
                </div>
                <!-- end row -->
 


                <!-- Footer -->
               <?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


                <!-- jQuery  -->
        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>
        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>js/detect.js"></script>
        <script src="<?php echo assets_url;?>js/fastclick.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
        <script src="<?php echo assets_url;?>js/waves.js"></script>
        <script src="<?php echo assets_url;?>js/wow.min.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>

        <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		
 


<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
	 <script type="text/javascript" src="<?php echo assets_url;?>js/html2canvas.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script>
function printdiv()
{
	window.print();
	
}

function getscreenshot(userid){

html2canvas(document.getElementById("user"+userid)).then(function(canvas) {
var base64URL = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');

$(".button"+userid).css('display','none');
// AJAX request
$.ajax({
url: '<?php echo page_url;?>Reporting/sendmisonwhatsapp',
type: 'post',
data: {image: base64URL,userids:userid},
success: function(data){
	alert(data);
$(".button"+userid).css('display','');
}
});

});
}

</script>
</body>
<script>
function printdiv()
{
	window.print();
	
}
</script>
</html>
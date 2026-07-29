<?php
$CI =& get_instance();
$CI->load->model('Fms_mismodel');
$UI=& get_instance();
$UI->load->model('Form_mismodel');
$VI=& get_instance();
$VI->load->model('Delegation_model');
$sdate=base64_decode($this->uri->segment(3));
$edate=base64_decode($this->uri->segment(4));
$uid=$this->uri->segment(5);
if($sdate<>'' && $edate<>'')
{
	$svalue=date('d-m-Y',strtotime($sdate));
	$evalue=date('d-m-Y',strtotime($edate));
}else{
	$svalue='';
	$evalue='';
}

$restyu=$this->db->select('factor')->from('checklistmisfactor')->get();
if($restyu->num_rows()>0)
{
    foreach($restyu->result() as $restyu1);
    $checklistfactor=floatval($restyu1->factor);
   
}else
{
    //echo "CHECKLIST MIS FACTOR NOT AVAILABLE";EXIT;
    $checklistfactor='10';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> NON FMS MIS</title>

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

<link rel="stylesheet" href="<?php echo plugins_url;?>bootstrap-datepicker/css/bootstrap-datepicker3.css"/>
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>
		<style>
		
		.scissors
		{
			display:none;
		}
		.scissors {
    height: 43px; /* image height */
    width: 100%;
    margin: auto auto;
    background-image: url('http://i.stack.imgur.com/cXciH.png');
    background-repeat: no-repeat;
    background-position: right;
    position: relative;
}
.scissors div {
    position: relative;
    top: 50%;
    border-top: 3px dashed black;
    margin-top: -3px;
}

table.manglesh thead th {
				background: <?php echo $LOGO->colorcode;?>;
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
				background-color:white !important; 
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

.scissors
		{
			display:block;
		}
.whatsapp
	{
		display:none;
	}
	
	.printhide
	{
		display:none;
	}
 @page { 
        size: landscape;
		margin: 0;
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
					<div class="col-md-3">
                           
                            <h4 class="page-title">NON FMS MIS</h4>
							</div>
							 <?php
                        if($uid=='')
                        {
                        ?>
                        <form action="<?php echo page_url;?>FMS/setdatefornonfms" method="post">
                        <div class="col-md-6 card-box"><div class="col-md-4">
                        <input type="text" name="start" class="form-control datepicker-autoclose" placeholder="START DATE" value="<?php echo $svalue;?>" autocomplete="off"></div>
                        <div class="col-md-4">
                        <input type="text" name="end" class="form-control datepicker-autoclose" placeholder="END DATE" value="<?php echo $evalue;?>" autocomplete="off"></div>
                        <div class="col-md-4"><input type="submit" class="btn btn-warning">
                        </div>
                        </div>
                        </form>
                        <?php
                        }else
                        {
                        ?>
                        <div class="col-md-6"></div>
                        
                        <?php
                        }
                        ?>
							<div class="col-md-3"><span class="btn btn-success pull-right" onclick="printdiv();">Print Me </span>
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
if($sdate=='' && $edate=='')
{
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
}else{

$startdate=$sdate;
$enddate=$edate;

}

$allproductionflow= $CI->Fms_mismodel->getallproductionflowformis();
if($allproductionflow=='0')
{
	echo "NO PRODUCTION FLOEW INCLUDED IN MIS";EXIT;
}
$alluser= $CI->Fms_mismodel->getallnonfmsuser($uid);

if(count($alluser)>0)
{
foreach($alluser as $allusers)
{
	$useroverallmis=array();
	$username= $CI->Fms_mismodel->getsusername($allusers);
	
	foreach($username as $usernames);

$chekforform=$UI->Form_mismodel->checkifformisassigned($allusers);
$chekforchecklist=$UI->Form_mismodel->checkifchecklistassigned($allusers);
$delegationlist=$VI->Delegation_model->checkforalldelegationassigned($allusers,$startdate,$enddate);
if($chekforform!=0 || $chekforchecklist!=0 || count($delegationlist)>0)
	{
?>
<table border="1" width="100%" class="tracksheet" id="user<?php echo $allusers;?>">
 <thead>
      <tr>
	           <th colspan="3" rowspan="2"  style="background-color:white; font-weight:bold;"><div class="col-sm-3 pull-left whatsapp"><button class="btn btn-success button<?php echo $allusers;?> " onclick="getscreenshot(<?php echo $allusers;?>);">Whatsapp</button></div> <div class="col-sm-9">MIS</div></th>
			  <th style="background-color:white; font-weight:bold; text-align:center;">Planned</th>
			  <th style="background-color:white; font-weight:bold; text-align:center;">Actual</th>
			  <th  rowspan="2" style="background-color:white; font-weight:bold; text-align:center;">Diff</th>
			  <th  rowspan="2" style="background-color:white; font-weight:bold; text-align:center;">% Score</th>
			  <th rowspan="2" style="background-color:white; font-weight:bold; text-align:center;">Total</th>
               
       	  
		
		
      </tr>

	    <tr>
	          <th style="background-color:white; font-weight:bold; text-align:center;"><?php echo $startdate;?></th>
			  <th style="background-color:white; font-weight:bold; text-align:center;"><?php echo $enddate;?></th>
			  
               
       	  
		
		
      </tr>
    </thead>
    <tbody>
	  
	  <?php
	  
	  $formids=$UI->Form_mismodel->getuserwisedashboard($allusers);
	  if(count($formids)>0)
	  {
		  foreach($formids as $formid)
		  {
		  $getdashboardname=$UI->Form_mismodel->getdashboardname($formid);
		  
	  ?>
	  
	  <!--- FORM MIS --->
			<tr>
			<td rowspan="5" class="vertical"><div class="vertical"><?php echo $usernames;?></div></td>

			<td rowspan="5"><?php echo $getdashboardname;?></td>
			<td class="getbold">% of Work Not Done</td>
			<?php
			
			
			$getalltaskcount=$UI->Form_mismodel->getoverallformtask($formid,$startdate,$enddate);
			$getalltaskdonecount=$UI->Form_mismodel->getallformtaskdone($formid,$startdate,$enddate);
			$formdiffpart1=$getalltaskdonecount-$getalltaskcount;
			$formworknotdonemisscore=$UI->Form_mismodel->worknotdonemisscore($getalltaskdonecount,$getalltaskcount);
			$formworknotdonetaskdetails=$UI->Form_mismodel->formworknotdonetaskdetails($formid,$startdate,$enddate);
			/** END PART ONE **/
			$weeklytaskdone=$UI->Form_mismodel->weeklytaskdone($startdate,$enddate,$formid);
			$weeklytasknotdoneontime=$UI->Form_mismodel->weeklytasknotdoneontime($startdate,$enddate,$formid);
			$formdiffpart2=$weeklytasknotdoneontime-$weeklytaskdone;
			$formworknotdoneontimemisscore=$UI->Form_mismodel->worknotdonemisscore($weeklytasknotdoneontime,$weeklytaskdone);
			$formworknotdoneontimetaskdetails=$UI->Form_mismodel->formworknotdoneontimetaskdetails($startdate,$enddate,$formid);
			
			/** END PART TWO **/
			 $delaycalculationinworknotdonrontimemiscore=$CI->Form_mismodel->delaycalculationinworknotdonrontimemiscore($startdate,$enddate,$formid);
			
			$totalmisforuserforform=($formworknotdonemisscore)+($formworknotdoneontimemisscore)+($delaycalculationinworknotdonrontimemiscore);
	
	$useroverallmis[]=$totalmisforuserforform;
			?>
			<td><?php echo $getalltaskcount;?></td>
			<td><?php echo $getalltaskdonecount;?></td>

			<td><?php echo $formdiffpart1;?></td>

			<td><?php echo $formworknotdonemisscore;?>%</td>
			<td rowspan="5" style="background-color:red;color:white;font-weight:bold;"><?php echo $totalmisforuserforform;?> %</td>
			</tr>

			<tr>
			<td class="getbold">Detail Description</td>

			<td colspan="3" style="text-align:left;"><?php echo $formworknotdonetaskdetails;?></td>

			<td></td>






			</tr>

			<tr>
			<td class="getbold">% of Work not done on time</td>

			<td><?php echo $weeklytaskdone;?></td>
			<td><?php echo $weeklytasknotdoneontime;?></td>
			<td><?php echo $formdiffpart2;?></td>
			<td><?php echo $formworknotdoneontimemisscore;?>%</td>






			</tr>
			<tr>
			<td class="getbold">Detail Description</td>

			<td colspan="3" style="text-align:left;"><?php echo $formworknotdoneontimetaskdetails;?></td>

			<td></td>
			</tr>

			<tr style="border-bottom: 2px solid #767c10;">
			<td class="getbold">% of Delay in time</td>

			<td></td>
			<td></td>
			<td></td>
			<td><?php echo $delaycalculationinworknotdonrontimemiscore;?></td>
			</tr>
			
	<!-- END FORM MIS --->	
<?php
		  }
	  }
?>	  
	

 <!--- CHECKLIST MIS --->
 <?php
 
 $checklisttask=$UI->Form_mismodel->getallchecklistassigned($allusers,$startdate,$enddate);
 if(count($checklisttask)>0)
 {
	
		 $checklistalltaskassigned=$UI->Form_mismodel->checklistalltaskassigned($allusers,$startdate,$enddate);
		
		 $checklistalltaskassigneddone=$UI->Form_mismodel->checklistalltaskassigneddone($allusers,$startdate,$enddate);
		if($checklistalltaskassigneddone<>0)
		 {
		 $checlistdiff=$checklistalltaskassigneddone-$checklistalltaskassigned;
		 }else{
			 
			 $checlistdiff=0-$checklistalltaskassigned;
		 }
		 
		  $checklistalltaskassigneddonemisscore=$UI->Form_mismodel->worknotdonemisscoreforchecklist($checklistfactor,$checklistalltaskassigneddone,$checklistalltaskassigned);
	
$useroverallmis[]=	$checklistalltaskassigneddonemisscore;
 ?>
		
	<tr>
			<td rowspan="1" class="vertical"><div class="vertical"><?php echo $usernames;?> </div></td>

			<td rowspan="1" >CHECKLIST</td>
			<td class="getbold">% of Work Not Done</td>
		
			<td><?php echo $checklistalltaskassigned;?></td>
			<td><?php echo $checklistalltaskassigneddone;?></td>
			<td><?php echo $checlistdiff;?></td>
			<td><?php echo $checklistalltaskassigneddonemisscore;?> %</td>

			<td rowspan="1" style="background-color:red;color:white;font-weight:bold;"><?php echo $checklistalltaskassigneddonemisscore;?> %</td>
			</tr>

			

			
			

					

		
	<?php
	 
	 }

?> 
	<!-- END CHECKLIST MIS --->	

 <!--- DELEGATION MIS --->
 
 <?php
$delegationlist=$VI->Delegation_model->checkforalldelegationassigned($allusers,$startdate,$enddate);
if(count($delegationlist)>0)
{
$actualdele=$VI->Delegation_model->checkforalldelegationassignedmarkeddone($allusers,$startdate,$enddate);
$delegationdiff=$actualdele-array_sum($delegationlist);
$notdonetaskdetails=$VI->Delegation_model->checkforalldelegationassignedmarkednotdone($allusers,$startdate,$enddate);
$delegationmiscore=$VI->Delegation_model->delegationmiscore($allusers);
$useroverallmis[]=$delegationmiscore;
 ?>
		
	<tr>
			<td rowspan="1" class="vertical"><div class="vertical"><?php echo $usernames;?> </div></td>

			<td rowspan="1" >DELEGATION</td>
			<td class="getbold">% of Work Not Done</td>
		
			<td><?php echo array_sum($delegationlist);?></td>
			<td><?php echo $actualdele;?></td>
			<td><?php echo $delegationdiff;?></td>
			<td><?php echo $delegationmiscore;?> %</td>

			<td rowspan="1" style="background-color:red;color:white;font-weight:bold;"><?php echo $delegationmiscore;?> %</td>
			</tr>
			
			
			<tr>
			<td></td>
			<td></td>
			<td class="getbold">Detail Description</td>

			<td colspan="3" style="text-align:left;"><?php echo $notdonetaskdetails;?></td>

			<td></td>
			<td style="background-color:red;color:white;font-weight:bold;"></td>
			</tr>

		
		
	<?php

	 
}


?>
	<!-- END DELEGATION MIS --->	
	  <tr>
		 
		 <td></td>
		 <td></td>
		 <td></td>
		 <td></td>
		 <td></td>
		 <td></td>
		 <td style="font-weight:bold;">TOTAL MIS</td>
		 <?php
		 if(count($useroverallmis)>0)
		 {
			 $useroveral=array_sum($useroverallmis);
		 }else
		 {
			 $useroveral=0;
			 
		 }
		 ?>
		 <td style="color:white;background-color:red;font-weight:bold;"><?php echo $useroveral;?> %</td>
		 </tr>
		 
          </tbody>
</table>
<div style="clear:both;height:5px"></div>

<div class="scissors">
    <div></div>
</div>

<?php
}
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
  <script src="<?php echo plugins_url;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
            $(function () {
                $('.datepicker-autoclose').datepicker({
                    autoclose:"true",
                    orientation: "top",
                    changeMonth: true,
    changeYear: true,
    format: 'dd-mm-yyyy',
    endDate: '+0d'
                });
            });
        </script>
<script>
function printdiv()
{
	window.print();
	
}

function getscreenshot(userid){

$(".button"+userid).css('display','none');
html2canvas(document.getElementById("user"+userid)).then(function(canvas) {
var base64URL = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream');


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

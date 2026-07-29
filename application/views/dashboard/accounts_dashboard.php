<?php

$CI =& get_instance();

$MIS =& get_instance();

$CI->load->model('Fms_model');

$MIS->load->model('Fms_mismodel');

$user_id=$_SESSION['logged_in']['user_id'];

 $user_role =$this->session->userdata['logged_in']['role'];

$first_name =$this->session->userdata['logged_in']['user_name'];

$last_name =$this->session->userdata['logged_in']['last_name'];

$profile_image =$this->session->userdata['logged_in']['profile_image'];

$q = $this->db->select('profile_image')->from('system_users')->where('user_id',$user_id)->get();

if($q->num_rows()>0){

	foreach($q->result() as $row);

	$profilepic = $row->profile_image;

}else{

	$profilepic="";

}



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



$cm=base64_encode($current_monday);

$cs=base64_encode($current_saturday);

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="NJ Media">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle; ?> Dashboard</title>



        <!--Morris Chart CSS -->

		<link rel="stylesheet" href="<?php echo assets_url;?>plugins/morris/morris.css">



        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>

   

.numberCircle {

    border-radius: 50%;

    width: 45px;

    height: 45px;

    padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #64C3D1;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:20px;

}

.numberCirclegreen {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #80008080;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:20px;

}



.numberCircleleave {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid green;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:63px;

}

.numberCircleproduction {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #8B97BD;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:20px;

}



.numberCirclepurchase {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #F2652F99;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:20px;

}

.numberCirclestore {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #E6E31DCC;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:20px;

}





.numberCircleorders {

    

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #00800099;

    color: #666;

    text-align: center;

    font-size: 14px;

}

.numberCircleMIS {

    border-radius: 50%;

    width: 30px;

    height: 30px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #fff;

    color: #666;

    text-align: center;

    font-size: 13px;

}





.card-box {

    padding: 5px;

    box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);

    -webkit-border-radius: 5px;

    border-radius: 5px;

    -moz-border-radius: 5px;

    background-clip: padding-box;

    margin-bottom: 4px;

    background-color: #ffffff;

}

.blink {

    -webkit-animation: blink 1s step-end infinite;

            animation: blink 1s step-end infinite;

}

@-webkit-keyframes blink { 50% { visibility: hidden; }}

        @keyframes blink { 50% { visibility: hidden; }}

        

        .sonar-wrapper {

  position: relative;

  z-index: 0;

  overflow: hidden;

  padding: .5rem 0;

}



/* The circle */

.sonar-emitter {

position: relative;

margin: 0 auto;

width: 10px;

height: 10px;

border-radius: 9999px;

background-color: HSL(4.1, 100%, 51.6%);

}



/* the 'wave', same shape and size as its parent */

.sonar-wave {

  position: absolute;

  top: 0;

  left: 0;

  width: 10px;

  height: 10px;

  border-radius: 50%;

  background-color: HSL(4.1, 100%, 51.6%);

  opacity: 0;

  z-index: -1;

  pointer-events: none;

}



/*

  Animate!

  NOTE: add browser prefixes where needed.

*/

.sonar-wave {

  animation: sonarWave 2s linear infinite;

}



@keyframes sonarWave {

  from {

    opacity: 0.4;

  }

  to {

    transform: scale(3);

    opacity: 0;

  }

}

hr {

    margin-top: 8px;

    margin-bottom: 8px;

    border: 0;

        border-top-color: currentcolor;

        border-top-style: none;

        border-top-width: 0px;

    border-top: 1px solid #eee;

}

.numberCircleleave {

    

    border-radius: 50%;

    width: 45px;

    height: 45px;

    padding: 9px;

    background: #fff;

    border: 2px solid #C0C0C0;

    color: #666;

    text-align: center;

    font-size: 14px;

}



#mydiv {

  position: absolute;

  z-index: 9;

  background-color: #f1f1f1;

  border: 1px solid #d3d3d3;

  text-align: center;

}



#mydivheader {

  padding: 10px;

  cursor: move;

  z-index: 10;

  background-color: #2196F3;

  color: #fff;

}

#mydiv textarea{

    

    font-family: "Segoe UI";

}

 #mydiv textarea:focus{

        outline: none;

    }

    

    .pulsating-circle {

 position: absolute;

width: 45px;

height: 45px;

left: 50%;

transform: translateX(-50%);

  }

.pulsating-circle::before {

    content: '';

    position: relative;

    display: block;

    width: 150%;

    height: 150%;

    box-sizing: border-box;

    margin-left: -25%;

    margin-top: -25%;

    border-radius: 45px;

    background-color: #ef2a00;

    animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;

}

.pulsating-circle::after {

    content: '';

    position: absolute;

    left: 0;

    top: 0;

    display: block;

    width: 100%;

    height: 100%;

    background-color: white;

    border-radius: 45px;

    box-shadow: 0 0 8px rgba(0,0,0,.3);

    /*animation: pulse-dot 1.25s cubic-bezier(0.455, 0.03, 0.515, 0.955) -.4s infinite;*/

}



@keyframes pulse-ring {

  0% {

    transform: scale(.33);

  }

  80%, 100% {

    opacity: 0;

  }

}



@keyframes pulse-dot {

  0% {

    transform: scale(.8);

  }

  50% {

    transform: scale(1);

  }

  100% {

    transform: scale(.8);

  }

}



.misdesign {

    border-radius: 10%;

    width: 112px;

    height: 45px;

    padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #80008080;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin-left:0px;

}

</style>

<script type="text/javascript">

    window.onload = function(){

        location.href=document.getElementById("selectbox").value;

    }       

</script>



<script type="text/javascript">

    window.onload = function(){

        location.href=document.getElementById("selectbox").value;

    }  

	function onuserchange(id){

	var userid = $('#checklist_user'+id).val();

		if(userid!==''){

		url = "https://prestomitr.com/Checklist/view_user_checklist/"+userid;

		window.open(url, '_blank');

		}

	}

	

	function onref_search(id){

		var ref_id = $('#reference'+id).val();

		var sub_ref_id = $('#subref'+id).val();

		if(sub_ref_id!==''){

		url = "https://prestomitr.com/Form/fetch_ref_doc/"+ref_id+"/"+sub_ref_id;

		 window.open(url, '_blank');

		}

	}

	

	

		function onref_search1(id){

		var ref_id = $('#reference'+id).val();

		var sub_ref_id = $('#subref'+id).val();

		var subsubref=$("#subsubref"+id).val();

		if(sub_ref_id!='' && subsubref!=''){

		url = "https://prestomitr.com/Form/fetch_ref_doc/"+ref_id+"/"+sub_ref_id+"/"+subsubref;

		 window.open(url, '_blank');

		}

	}

	

	function onemail_search(id){

		var emailsubject = $('#emailsubject'+id).val();

		if(emailsubject!==''){

		url = "https://prestomitr.com/Master/User_management/view_email_template/"+emailsubject;

		 window.open(url, '_blank');

		}

	}

	

	function checkforsubsubreference(id)

	{

	    

	    var subref=$("#subref"+id).val();

	    if(subref!='')

	    {

	        

        $.ajax({

        type:"post",

        url:"<?php echo page_url;?>Form/get_sub_sub_ref",

        data:"subref="+subref,

        success:function(data){

            if($.trim(data)!='NA')

            {

                $(".subsubrefifavailable"+id).css('display','');

        $("#subsubref"+id).html(data);

                

            }else

            {

               $(".subsubrefifavailable"+id).css('display','none');

               onref_search(id); 

            }

          

        }

        });

	    

	        

	    }

	    

	    

	}

</script>

    </head>





    <body>

        

    



<div class="contentarea">

        <!-- Navigation Bar-->

       <?php $this->load->view('common/nav-menu');?>

        <!-- End Navigation Bar-->





        <div class="wrapper">

            <div class="container-fluid" style="background-color:#EBEFF2; min-height:463px;">



                <!-- Page-Title -->

               

          



<div class="row">

    <?php echo $this->session->flashdata('message'); ?>

                     <div class="col-lg-4 col-md-6">

                       

                         <div class="card-box widget-user" style="min-height:170px">

                            <div class="text-center">

                                <h4><a href="<?php echo page_url;?>Daily_reporting/dashboard"><span class="btn btn-warning btn-xs">Pending <?php 

								$q = $this->db->select('id')->from('staff_daily_work_list')->where('work_status','2')->where('added_by',$user_id)->where('old_task','0')->get();

								echo count($q->result());

								?></span></a> DAILY TASK <span class="btn btn-xs" style="background-color:#2986CE; color:#fff">Take Action</span> <span style="margin-left:20px"></span>

                                <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','235')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

                                

                                <a href="<?php echo page_url;?>Delegation/calls_delegation"><i class="fa fa-wpforms" aria-hidden="true" title="Calls delegation form"></i></a>

                                <?php }?>

                                <span style="margin-right:10px"></span>

                                <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','236')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

 <a href="<?php echo page_url;?>Delegation/calls_delegated_dashboard"><i class="fa fa-phone badge1" title="Calls delegation dashboard" data-badge="<?php 

                                $query = $this->db->select('id')->from('calls_delegation')->where('employee_id',$user_id)->where('status','0')->get();

                                $res =count($query->result());

                                echo $res;

                                ?>" ></i></a><?php }?></h4><hr>

                                <div class="row">

                                    <a href="<?php echo page_url;?>Delegation/delegated_task"></a><div class="col-md-3 col-sm-3 col-xs-3 text-center">

                                        <div class="numberCircle"><?php 

                                        $q = $this->db->select('id')->from('delegation_task')->where('task_status','0')->where('delegate_to',$user_id)->get();

                                        $report = count($q->result());

                                        echo $report; 

                                        ?></div>

                                        <p style="font-size:11px; text-align:center">DELEGATED TASK</p>

                                    </div></a>

                                    <a href="<?php echo page_url;?>Checklist/view_your_checklist"><div class="col-md-3 col-sm-3 col-xs-3">

                                        <div class="numberCircle"><?php 

                                        $taskarray = array();

                                        $query = $this->db->select('task_id')->from('compliance_task_report');

                                        $q = $this->db->where('user_id',$user_id)->where('status','1')->get();

                                        foreach($q->result() as $row){

                                            $taskarray[] = $row->task_id;

                                        }

                                        //$today = date('Y-m-d', strtotime(' +1 day'));

                                        $result = "'" . implode ( "', '", $taskarray) . "'";

                                        $query = $this->db->select('id')->from('compliance_set_date')->where_in('task_id',$result,false)->where('dateforemail',date('Y-m-d'))->get();

                                        $count = count($query->result());

                                        echo $count; 

                                        ?></div>

                                        <p title="total pending task" style="font-size:11px">CHECKLIST</p>

                                    </div></a>

                                    <a href="<?php echo page_url;?>Form/helpticket_dashboard"><div class="col-md-3 col-sm-3 col-xs-3">

                                        

                                        <div class="numberCircle"><?php 

                                         $formid = array('1','2','12','14','17','18','50','51','52','53','56','55');

                                        

                                        $query = $this->db->select('id')->from('dynamic_form_data')->Where_in('form_id',$formid)->where('added_by',$user_id)->get();

                                        echo count($query->result());

                                        

                                        ?> </div>

                                        <p title="help ticket" style="font-size:11px" >HELP TICKET STATUS</p>

                                    </div></a>

                                    

                                    <?php 

                                   

                                   

                                    ?>

                                    <?php 

									$formid="18";

                                    $query = $this->db->select('id')->from('dynamic_forms')->where('id',$formid)->where('user_id',$user_id)->get();

                                    if($query->num_rows()>0){

                                        

                                       

                                    ?>

                                     <a href="<?php echo page_url;?>Form/data_report/<?php echo $formid;?>"><div class="col-md-3 col-sm-3 col-xs-3">

                                         <?php 

                                              $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','0')->get();

                                         if($query->num_rows()>0){

                                         ?>

                                           <div class="pulsating-circle"><span style="position: absolute;

top: 0;

z-index: 10;

width: 100%;

display: block;

text-align: center;

height: 45px;

line-height: 45px;"><?php 

                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','0')->get();

                                        echo count($query->result());

                                        

                                        ?> </span></div>

                                        <?php }else{?>

                                          <div class="numberCircle"><?php 

                                          $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','0')->get();

                                        echo count($query->result());

                                        

                                        ?> </div>

                                        <?php }?>

                                        <p  style="font-size: 11px;

                                        position: relative;

                                        top: 50px;" >HELP DESK</p>

                                    </div></a>

                                    

                                    <?php 

                                    }?>

                                    

                                   

                                </div>

                                

                            </div>

                        </div>

                     

                        

                         

                 

                     </div><!-- end col -->



                    



                      <div class="col-lg-2 col-md-6">

                      

                     

                      <div class="card-box widget-user" style="min-height:170px">

                            <div class="text-center">

                                <h4>CONVEYANCE  </h4><hr>

                               

                                <div class="row">

                                    

                                   <a href="<?php echo page_url;?>Sales/local_conveyance_account_data"> <div class="col-md-6 col-sm-6 col-xs-6">

                                        <div class="numberCircleleave" style="margin-left: 15px;">

                                         <?php

                                        

                                       $query = $this->db->select('id')->from('conveyance_voucher')->where('status','0')->where('hod_status','1')->where('account_status','0')->order_by('travel_date','desc')->get();

		                               $res = $query->result(); 

		                               echo count($res);

                                        ?>

                                        

                                            

                                        </div>

                                       <p>LOCAL CONVEYANCE</p> 

                                    </div></a>

                                    

                                     <a href="<?php echo page_url;?>Sales/sale_service_conveyance_account_dashboard"><div class="col-md-6 col-sm-6 col-xs-6">

                                        <div class="numberCircleleave" style="margin-left: 15px;"><?php

                                        

                                       $query = $this->db->select('id')->from('member_conveyance_information')->where('status','0')->where('hod_status','1')->get();

                                       echo count($query->result());?></div>

                                        <p>TOUR CONVEYANCE</p>

                                        

                                    </div></a>

                                    

                                </div>

                               

                                

                            </div>

                        </div>

                     

                        

                      

                       

                     </div><!-- end col -->

                        <div class="col-lg-2">

                             <div class="card-box widget-user" style="min-height:170px">

                           <div class="text-center">

                                <h4>MIS SCORE</h4><hr>

                                 <div class="row">

                                     <?php 

                                     $rep=$this->db->select('b.production_flowid,a.production_flow')->from('flowtousers b')->join('production_flow a','a.id=b.production_flowid')->where('b.userid',$user_id)->get();

                                     if($rep->num_rows()>0){

                                         $misurl = "fmsmis";	

                                     }else{

                                         $misurl="nonfmsmis";

                                     }

                                       /**$sdate=$current_monday;

                                     $fdate=$current_saturday; **/



									$monday = date('Y-m-d', strtotime('monday this week'));

									$saturday = date('Y-m-d', strtotime('saturday this week'));

									$sdate=$monday;

									$fdate=$saturday;

									$cm=base64_encode($sdate);

									$cs=base64_encode($fdate);

                                     ?>

                                   <div class="col-md-2 hidden-xs hidden-sm"></div>

                                    <a href="<?php echo page_url;?>FMS/<?php echo $misurl;?>/<?php echo $cm;?>/<?php echo $cs;?>/<?php echo $user_id;?>"><div class="col-md-8 col-sm-12 col-xs-12">

                                        <div class="misdesign"><?php 

                                        $currentweeklyfmsscore=$MIS->Fms_mismodel->fmscurrentweekscore($user_id,$sdate,$fdate);

$currentweeklychecklistscore=$MIS->Fms_mismodel->checklistcurrentweekmis($user_id,$sdate,$fdate);

$currentweeklydelegationscore=$MIS->Fms_mismodel->delegationcurrentweekmis($user_id,$sdate,$fdate);

$formcurrentweekmis=$MIS->Fms_mismodel->formcurrentweekmis($user_id,$sdate,$fdate);



$currentweeksum=($currentweeklychecklistscore)+($currentweeklyfmsscore)+($formcurrentweekmis)+($currentweeklydelegationscore);

echo $currentweeksum;





                                        

                                        ?></div>

                                        <p title="total pending task">MIS SCORE</p>

                                    </div></a>

                                    <div class="col-md-2 hidden-xs hidden-sm"></div>

                                    

                                  

                                </div>

                                

                                

                            </div>

                        </div>

                        </div>

                    <div class="col-lg-4 col-md-6">

                        <div class="card-box" style="min-height:170px">

                           

                            <h4 class="text-center"><i class="zmdi zmdi-notifications-none m-r-5"></i> NOTIFICATION PANEL <a href="javacsript:void();" onclick="tognotes();"><i class="fa fa-sticky-note-o" aria-hidden="true" title="Sticky Notes"></i></a></h4><hr>



                             <ul class="list-group m-b-0 user-list" style="height: 116px; overflow-y:auto" id="notificationdata">

                                   

							<div id='loadingmessage2' style='display:none'>

							<img src='https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif'/>

							</div>

                              

                            </ul>

                        </div>

                        

                         

                    </div>

                    	<?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','3')->where('submoduleid','190')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

                          <div class="col-lg-2">

                             <div class="card-box widget-user" style="min-height:175px">

                           <div class="text-center">

                                <h4>DEBIT NOTE</h4><hr>

                                 <div class="row">

                                     

                                   <div class="col-md-3">

                                       

                                       

                                   </div>

                                    <a href="<?php echo page_url;?>Reporting/debitnote"><div class="col-md-6">

                                        <div class="numberCirclegreen"><?php 

                                        	$rest=$this->db->select('id')->from('debitnote')->order_by('addedOn','DESC')->get();

                                        	echo count($rest->result());

                                        ?></div>

                                        <p title="total pending task">DEBIT NOTE</p>

                                    </div></a>

                                     <div class="col-md-3">

                                       

                                    </div>

                                </div>

                                

                            </div>

                        </div>

                        </div>

                        

                        <?php }?>

                        

                        <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','171')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

                        

                        <div class="col-lg-2">

                             <div class="card-box widget-user" style="min-height:175px">

                           <div class="text-center">

                                <h4 style="font-size:14px">LEAVE APPLICATION</h4><hr>

                                 <div class="row">

                                    

                                        <a href="<?php echo page_url;?>Hr/hod_leave_dashboard"><div class="col-md-12">

                                        <div class="numberCircleleave"><?php 

                                        $user_id =$this->session->userdata['logged_in']['user_id'];	

		

		$q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader',$user_id)->get();

	

		if($q->num_rows()>0)

		{

		foreach($q->result() as $teamdetail);

		 $teammembers = array();

		$query = $this->db->select('team_id, employee_id')->from('presto_team_members')->where('team_id',$teamdetail->team_id)->get();

        $res = $query->result();

        

         foreach($res as $teaminfo){

            $teammembers[] = $teaminfo->employee_id;

            

        }

	

		//$team = implode(',',$teammembers);

		$team = "'" . implode ( "', '", $teammembers ) . "'";

		

		}else

		{

		    $team='NA';

		}

		if($team<>'NA'){

		$this->db->select('id')->from('leave_application')->where('approval_status','0');

		$query = $this->db->where_in('employee_id',$team,false)->get();

		echo count($query->result());

		}

                                        

                                        ?></div>

                                        <p title="total pending task">LEAVE APPLICATION FOR REVIEW</p>

                                    </div></a>

                                    

                                </div>

                                

                            </div>

                        </div>

                        </div>

                        <?php }?>

                        

                        

                        <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','297')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

 <div class="col-lg-2 col-md-6">

                      

                     

                      <div class="card-box widget-user" style="min-height:170px">

                           <div class="text-center">

                                <h4>PAYMENT RECONSILIATION</h4><hr>

                                 <div class="row">

                                    <div class="col-md-2"></div>

                                     

                                    <a href="<?php echo page_url;?>Payment/payment_reconciliation_dashboard"><div class="col-md-8">

                                        <div class="numberCircleproduction" style="margin-left: 43px;"><?php 

                                        $query = $this->db->select('id')->from('payment_reconciliation')->where('accepted_by','0')->get();

                                        echo count($query->result());

                                        ?> </div>

                                        <p >Payment Reconciliation</p>

                                    </div></a>

                                    

                                     <div class="col-md-2"></div>  

                                       

                                </div>

                                

                                

                            </div>

                        </div>

                        </div>

						

						<div class="col-lg-2 col-md-6">

						<div class="card-box widget-user" style="min-height:170px">

                           <div class="text-center">

                                <h4>DAR</h4><hr>

                                 <div class="row">

                                    

                                     <div class="col-md-2"></div>

									  <a href="<?php echo page_url;?>Daily_reporting"><div class="col-md-4">

                                       <span class="btn btn-success btn-xs">DAR Form</span>

                                        

                                    </div></a>

                                    <a href="<?php echo page_url;?>Daily_reporting/dashboard"><div class="col-md-4">

                                        <div class="numberCircleproduction" style="margin-left: 20px;"><?php 

                                        $query = $this->db->select('id')->from('staff_daily_reporting')->where('reporting_date',date('Y-m-d'))->where('employee_id',$user_id)->get();

                                        echo count($query->result());

                                        ?> </div>

                                        <p >View Report</p>

                                    </div></a>

									<div class="col-md-2"></div>  

                                       

                                </div>

                                

                                

                            </div>

                        </div>

                        </div>





                       <div class="col-lg-2 col-md-6">

                      

                     

                      <div class="card-box widget-user" style="min-height:170px">

                           <div class="text-center">

                                <h4>READY FOR BILLING</h4><hr>

                                 <div class="row">

                                    <div class="col-md-2"></div>

                                     

                                    <a href="<?php echo page_url;?>Reporting/dispatchforaccounts"><div class="col-md-8">

                                        <div class="numberCircleproduction" style="margin-left: 43px;"><?php 

                                        $query = $CI->Fms_model->checkreadyforbilling();

                                        echo $query; 

                                        ?> </div>

                                        <p></p>

                                    </div></a>

                                    

                                     <div class="col-md-2"></div>  

                                       

                                </div>

                                

                                

                            </div>

                        </div>

                        </div>



                        <?php }?>

                 </div>

                 

                 <div class="row">

                     

                     <div class="col-md-12 col-xs-12 col-sm-12">

				

     

<?php 

$hodquery = $this->db->select('team_leader')->from('prestogroup_teams')->where('team_leader',$user_id)->where('status','1')->get();

?>



<?php if($user_role=='1' || $user_role=='68' || $user_role=='84'){

?>

	<div class="row">

		

                    <div class="col-sm-12">

                        <div class="card-box table-responsive" id="target">

                           <table class="table table-bordered manglesh scroll display">

						   <?php

							$k=1; 

							$query = $this->db->select('department,department_id, reference,show_in_master_index')->from('departments')->where('status','1')->where('show_in_master_index','1')->order_by('department','asc')->get();

							foreach($query->result() as $department){

							    $dpt= strtoupper($department->department);

							    if($dpt=='HR & ADMIN'){

							        $departmentmodel="HRADMIN";

							    }else{

								$departmentmodel = preg_replace('/\s+/', '_', $department->department);

							    }

							?>

							<thead>

							

								<tr class="text-center">

								<th class="text-center" colspan="4"><?php echo strtoupper($department->department);?> <span style="margin-left:20px"></span><i class="fa fa-folder-open-o" aria-hidden="true" title="view_reference" data-toggle="modal" data-target="#<?php echo $departmentmodel;?><?php echo $department->department_id;?>"></i><span style="margin-left:20px"></span><i class="fa fa-check-square-o" aria-hidden="true" title="Checklist" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>"></i><span style="margin-left:20px"></span><i class="fa fa-envelope-square" aria-hidden="true" title="Email Template" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>emailtemplate"></i></th>

								</tr>

								

								

								<div id="<?php echo $departmentmodel;?>emailtemplate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

								<form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> EMAIL TEMPLATE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										

										<div class="col-md-12">

										<label>EMAIL SUBJECT</label>

										<select class="form-control" name="emailsubject<?php echo $department->department_id;?>" id="emailsubject<?php echo $department->department_id;?>"  onChange="onemail_search(<?php echo $department->department_id;?>);">

										<option value="">--Select Subject--</option>

										<?php 

										$q =$this->db->select('*')->from('departmentwise_email_template')->where('department_id',$department->department_id)->get();

										foreach($q->result() as $email){

										?>

										<option value="<?php echo $email->id;?>"><?php echo $email->email_subject;?></option>

										<?php }?>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

								<div id="<?php echo $departmentmodel;?><?php echo $department->department_id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> REFERENCE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<div class="col-md-6">

										<label>VIEW REFERENCE</label>

										<select class="form-control" id="reference<?php echo $department->department_id;?>" name="reference<?php echo $department->department_id;?>" onChange="get_sub_reference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										<?php $query = $this->db->select('ref_id, reference_title')->from('department_reference')->where('department_id',$department->department_id)->get();

										foreach($query->result() as $ref){?>

										<option value="<?php echo $ref->ref_id;?>"><?php echo $ref->reference_title;?></option>

										<?php }?>

										

										</select>

										

										<script type="text/javascript">

											

													function get_sub_reference(id){

													var ref=$("#reference"+id).val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Form/get_sub_ref",

													data:"ref="+ref,

													success:function(data){

													$("#subref"+id).html(data);

													}

													});

													

													}

													

											

												</script>

										</div>

										<div class="col-md-6">

										<label>VIEW SUB REFERENCE</label>

										<select class="form-control" name="subref<?php echo $department->department_id;?>" id="subref<?php echo $department->department_id;?>"  onChange="checkforsubsubreference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

										

										<div class="col-md-6 subsubrefifavailable<?php echo $department->department_id;?>" style="display:none;">

										<label>VIEW SUB SUB REFERENCE</label>

										<select class="form-control" name="subsubref<?php echo $department->department_id;?>" id="subsubref<?php echo $department->department_id;?>"  onChange="onref_search1(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							

							<div id="<?php echo $departmentmodel;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> USER CHECKLIST</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

										<div class="col-md-12">

										<label>VIEW USER CHECKLIST</label>

										<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<select class="form-control" name="checklist_user<?php echo $department->department_id;?>" id="checklist_user<?php echo $department->department_id;?>" onChange="onuserchange(<?php echo $department->department_id;?>);">

											<option value="">--SELECT USER--</option>

										<?php 

										$query = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',$department->department_id)->where('user_status','1')->where('hide_profile','0')->get();

										foreach($query->result() as $users){

										?>

										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>

										<?php }?>

									</select></div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							</thead>

							<tbody>

								<tr style="background-color:#fff; color:#1E98B1; font-size: 13px; font-weight:bold;">

									<td>SR NO</td>

									<td>FORM</td>

									<td class="text-center">SPEED FMS (PENDING)</td>

									<td>HISTORY</td>

								</tr>

								<?php 

								$i=1;

								$query = $this->db->select('id,department_id, form_title, description, status, dashboard_title, form_video_link, dashboard_video_link')->from('dynamic_forms')->where('department_id',$department->department_id)->where('status','1')->where('form_running_status','0')->get();

									$dyrow=$query->num_rows();

									

								foreach($query->result() as $row){

								?>

								<tr>

									<td><?php echo $i;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><span style="margin-left:20px"></span> 

									<?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','11')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?><a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px; color:#337ab7e6; !important" ></i></a>&nbsp; &nbsp; &nbsp;<a href="<?php echo page_url;?>Form/edit_form/<?php echo $row->id;?>"><i class="fa fa-pencil-square-o pull-right" style="margin-left: 1.3em !important; color:#337ab7e6; !important" title="EDIT FORM"></i></a> &nbsp; &nbsp; &nbsp;  <a href="<?php echo page_url;?>Form/set_permission_of_access/<?php echo $row->id;?>" target="_blank"><i class="fa fa-eye pull-right" style="    margin-left: 1.3em !important; color:#337ab7e6; !important;" title="SET LABEL PERMISSION"></i></a><?php }?></td>

								

									<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px"></i></a></td>

										<td style="color:#000;"><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								</tr>

								<?php $i++;}?>

								

								<?php

								if($dyrow>0)

								{

								$t=$dyrow+1;

								}else

								{

								    $t=1;

								    

								}

								$delegationavail=$this->db->select('a.id,a.video,a.dashboard_video_link,b.first_name,b.last_name,b.user_id')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id')->where('b.department_id',$department->department_id)->get();

								$delegationcount = $delegationavail->num_rows();

								if($delegationavail->num_rows()>0)

								{

									

									foreach($delegationavail->result() as $delegations)

									{

										

								?>

								<tr>

								<td><?php echo $t;?></td>

								<td><a href="<?php echo page_url;?>Delegation/delegation_task/<?php echo $delegations->user_id;?>/1" target="_blank">DELEGATION FORM FOR <?php echo strtoupper($delegations->first_name.' '.$delegations->last_name);?></a><a href="<?php echo $delegations->video;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

									<td class="text-center"><a href="<?php echo page_url;?>Delegation/delegation_dashboard/<?php echo $delegations->user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $delegations->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

								<td><a href="<?php echo page_url;?>Delegation/delegated_task_history/<?php echo $delegations->user_id;?>" target="_blank">DELEGATION HISTORY</a></td>

								</tr>

								<?php

								$t++;

								}

								}

								?>

								

								<?php

								if($delegationcount>0)

								{

								$s=$t+1;

								}else

								{

								    $s=$t;

								    

								}

								

								$q1=$this->db->select('form_title, form_url, dashboard_title, form_vide_link, dashboard_video_link, dashboard_url, history_url')->from('standalone_dashboard_access a')->where('department_id',$department->department_id)->get();

								$q1count = $q1->num_rows();

							

								if($q1->num_rows()>0)

								{

								    /**if($delegationcount!='0'){

									$s=$delegationcount+2;

									}else if($dyrow=='0'){

									    $s=1;

									}else{

									    $s=$dyrow+1;

									} **/

									foreach($q1->result() as $standalonedata)

									{

										

								?>

								<tr>

								<td><?php echo $s;?></td>

								<td>

								    <?php if($standalonedata->form_url){?>

								    <a href="<?php echo page_url;?><?php echo $standalonedata->form_url;?>" target="_blank"><?php echo $standalonedata->form_title;?></a><?php }else{?><?php echo $standalonedata->form_title;?><?php }?> <span class="pull-right"><a href="<?php echo $standalonedata->form_vide_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td class="text-center">

								    <a href="<?php echo page_url;?><?php echo $standalonedata->dashboard_url;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>

								    <span class="pull-right"><a href="<?php echo $standalonedata->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span>

								   </td>

								<td><?php if($standalonedata->history_url==''){?><?php echo $standalonedata->dashboard_title;?><?php }else{?><a href="<?php echo $standalonedata->history_url;?>" target="_blank"><?php echo $standalonedata->dashboard_title;?></a><?php }?></td>

							

								

								</tr>

								<?php

								$s++;

								}

								}

								?>

								

								<?php 

								if($q1count>0)

								{

							     $r=$s+1;

								}else

								{

								$r=$s;

								}

								$query = $this->db->select('a.form_id, a.user_id, b.user_id, b.department_id, c.id,c.form_title, c.description, c.status, c.dashboard_title, c.form_video_link, c.dashboard_video_link')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id')->join('dynamic_forms c','a.form_id=c.id')->where('b.department_id',$department->department_id)->where('c.status','1')->where('c.form_running_status','0')->group_by('a.form_id')->get();

									$dyrow=$query->num_rows();

								if($query->num_rows()>0){	

								  

								foreach($query->result() as $row){

								     

									

									

								?>

								<tr>

									<td><?php echo $s++;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><br>

									<?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','13')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px"></i></a>&nbsp; &nbsp; &nbsp;<a href="<?php echo page_url;?>Form/edit_form/<?php echo $row->id;?>"><i class="fa fa-pencil-square-o pull-right" style="margin-left: 1.3em !important;"></i></a><?php }?></td>

										<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>  <span class="pull-right"><a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								

								</tr>

								<?php $r++; }}?>

							</tbody>

							<?php $k++;}?>

						   </table>

                        </div>

                    </div>

                </div>





<?php



}else if($hodquery->num_rows()>0){



?>





	<div class="row" style="min-height:300px">

		

                    <div class="col-sm-12">

                        <div class="card-box table-responsive" id="target">

                           <table class="table table-bordered manglesh scroll display">

						   <?php

							$k=1; 

							$user_id=$_SESSION['logged_in']['user_id'];

                            $departmentid =$this->session->userdata['logged_in']['department_id'];

                            

                            /*Check User is Team Leader*/

                            $qq  = $this->db->select('team_leader')->from('prestogroup_teams')->where('team_leader',$user_id)->get();

                            	$query = $this->db->select('department,department_id, reference,show_in_master_index')->from('departments')->where('status','1')->where('department_id',$departmentid)->where('show_in_master_index','1')->order_by('department','asc')->get();

							foreach($query->result() as $department){

							    $dpt= strtoupper($department->department);

							    if($dpt=='HR & ADMIN'){

							        $departmentmodel="HRADMIN";

							    }else{

								$departmentmodel = preg_replace('/\s+/', '_', $department->department);

							    }

							?>

							<thead>

							

								<tr class="text-center">

								<th class="text-center" colspan="4"><?php echo strtoupper($department->department);?> <span style="margin-left:20px"></span><i class="fa fa-folder-open-o" aria-hidden="true" title="view_reference" data-toggle="modal" data-target="#<?php echo $departmentmodel;?><?php echo $department->department_id;?>"></i><span style="margin-left:20px"></span><i class="fa fa-check-square-o" aria-hidden="true" title="Checklist" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>"></i><span style="margin-left:20px"></span><i class="fa fa-envelope-square" aria-hidden="true" title="Email Template" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>emailtemplate"></i></th>

								</tr>

								

								

								<div id="<?php echo $departmentmodel;?>emailtemplate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

								<form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> EMAIL TEMPLATE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										

										<div class="col-md-12">

										<label>EMAIL SUBJECT</label>

										<select class="form-control" name="emailsubject<?php echo $department->department_id;?>" id="emailsubject<?php echo $department->department_id;?>"  onChange="onemail_search(<?php echo $department->department_id;?>);">

										<option value="">--Select Subject--</option>

										<?php 

										$q =$this->db->select('*')->from('departmentwise_email_template')->where('department_id',$department->department_id)->get();

										foreach($q->result() as $email){

										?>

										<option value="<?php echo $email->id;?>"><?php echo $email->email_subject;?></option>

										<?php }?>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

								<div id="<?php echo $departmentmodel;?><?php echo $department->department_id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> REFERENCE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<div class="col-md-6">

										<label>VIEW REFERENCE</label>

										<select class="form-control" id="reference<?php echo $department->department_id;?>" name="reference<?php echo $department->department_id;?>" onChange="get_sub_reference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										<?php $query = $this->db->select('ref_id, reference_title')->from('department_reference')->where('department_id',$department->department_id)->get();

										foreach($query->result() as $ref){?>

										<option value="<?php echo $ref->ref_id;?>"><?php echo $ref->reference_title;?></option>

										<?php }?>

										

										</select>

										

										<script type="text/javascript">

											

													function get_sub_reference(id){

													var ref=$("#reference"+id).val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Form/get_sub_ref",

													data:"ref="+ref,

													success:function(data){

													$("#subref"+id).html(data);

													}

													});

													

													}

													

											

												</script>

										</div>

										<div class="col-md-6">

										<label>VIEW SUB REFERENCE</label>

										<select class="form-control" name="subref<?php echo $department->department_id;?>" id="subref<?php echo $department->department_id;?>"  onChange="checkforsubsubreference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

										

										<div class="col-md-6 subsubrefifavailable<?php echo $department->department_id;?>" style="display:none;">

										<label>VIEW SUB SUB REFERENCE</label>

										<select class="form-control" name="subsubref<?php echo $department->department_id;?>" id="subsubref<?php echo $department->department_id;?>"  onChange="onref_search1(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							

							<div id="<?php echo $departmentmodel;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> USER CHECKLIST</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

										<div class="col-md-12">

										<label>VIEW USER CHECKLIST</label>

										<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<select class="form-control" name="checklist_user<?php echo $department->department_id;?>" id="checklist_user<?php echo $department->department_id;?>" onChange="onuserchange(<?php echo $department->department_id;?>);">

											<option value="">--SELECT USER--</option>

										<?php 

										$this->db->select('user_id, first_name, last_name')->from('system_users');

										if($qq->num_rows()>0){

										  $this->db->where('department_id',$department->department_id);  

										}else{

										    $this->db->where('user_id',$user_id);

										}

										$query = $this->db->where('user_status','1')->where('hide_profile','0')->get();

										foreach($query->result() as $users){

										?>

										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>

										<?php }?>

									</select></div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							</thead>

							<tbody>

								<tr style="background-color:#fff; color:#1E98B1; font-size: 13px; font-weight:bold;">

									<td>SR NO</td>

									<td>FORM</td>

									<td class="text-center">SPEED FMS (PENDING)</td>

									<td>HISTORY</td>

								</tr>

								<?php 

								$i=1;

								$this->db->select('id,department_id, form_title, description, status, dashboard_title, form_video_link, dashboard_video_link')->from('dynamic_forms');

								if($qq->num_rows()>0){

								   $this->db->where('department_id',$department->department_id); 

								}else{

								   $this->db->where('user_id',$user_id); 

								}

								

								$query = $this->db->where('status','1')->where('form_running_status','0')->get();

									$dyrow=$query->num_rows();

									

								foreach($query->result() as $row){

								?>

								<tr>

									<td><?php echo $i;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><span style="margin-left:20px"></span> 

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px" ></i></a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  </td>

								

									<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px"></i></a></td>

										<td style="color:#000;"><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								</tr>

								<?php $i++;}?>

								

								<?php

								if($dyrow>0)

								{

								$t=$dyrow+1;

								}else

								{

								    $t=1;

								    

								}

								$this->db->select('a.id,a.video,a.dashboard_video_link,b.first_name,b.last_name,b.user_id')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id');

								if($qq->num_rows()>0){

								    $this->db->where('b.department_id',$department->department_id); 

								    

								}else{

								   $this->db->where('a.assigned_to',$user_id);

								}

								$delegationavail=$this->db->get();

								$delegationcount = $delegationavail->num_rows();

								if($delegationavail->num_rows()>0)

								{

									

									foreach($delegationavail->result() as $delegations)

									{

										

								?>

								<tr>

								<td><?php echo $t;?></td>

								<td><a href="<?php echo page_url;?>Delegation/delegation_task/<?php echo $delegations->user_id;?>/1" target="_blank">DELEGATION FORM FOR <?php echo strtoupper($delegations->first_name.' '.$delegations->last_name);?></a><a href="<?php echo $delegations->video;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

									<td class="text-center"><a href="<?php echo page_url;?>Delegation/delegation_dashboard/<?php echo $delegations->user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $delegations->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

								<td><a href="<?php echo page_url;?>Delegation/delegated_task_history/<?php echo $delegations->user_id;?>" target="_blank">DELEGATION HISTORY</a></td>

								</tr>

								<?php

								$t++;

								}

								}

								?>

								

								<?php

								if($delegationcount>0)

								{

								$s=$t+1;

								}else

								{

								    $s=$t;

								    

								}

								

								$this->db->select('a.form_name, a.form_link, a.dashboard_link, a.history_title, a.history_link, a.form_video_link, a.dashboard_video_link')->from('dashboard_short_links_for_hod a');

								

								  	 $this->db->join('standalone_dashboard_hodwise_permission b','a.id=b.dashboard_id','left'); 

								  	 $this->db->where('b.user_id',$user_id);

								

								

							$q1=$this->db->get();

								$q1count = $q1->num_rows();

							

								if($q1->num_rows()>0)

								{

								    /**if($delegationcount!='0'){

									$s=$delegationcount+2;

									}else if($dyrow=='0'){

									    $s=1;

									}else{

									    $s=$dyrow+1;

									} **/

									foreach($q1->result() as $standalonedata)

									{

										

								?>

								<tr>

								<td><?php echo $s;?></td>

								<td>

								    <?php if($standalonedata->form_link){?>

								    <a href="<?php echo page_url;?><?php echo $standalonedata->form_link;?>" target="_blank"><?php echo $standalonedata->form_name;?></a><?php }else{?><?php echo $standalonedata->form_name;?><?php }?> <span class="pull-right"><a href="<?php echo $standalonedata->form_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td class="text-center">

								   

								   <a href="<?php echo page_url;?><?php echo $standalonedata->dashboard_link;?>/<?php echo $user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>

								   

								    <span class="pull-right"><a href="<?php echo $standalonedata->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span>

								   </td>

								<td><?php if($standalonedata->history_link==''){?><?php echo $standalonedata->history_title;?><?php }else{?>

							

								<a href="<?php echo $standalonedata->history_link;?>/<?php echo $user_id;?>" target="_blank"><?php echo $standalonedata->history_title;?></a><?php }?></td>

							

								

								</tr>

								<?php

								$s++;

									}

								}

								?>

								

								<?php 

								if($q1count>0)

								{

							     $r=$s+1;

								}else

								{

								$r=$s;

								}

								$this->db->select('a.form_id, a.user_id, b.user_id, b.department_id, c.id,c.form_title, c.description, c.status, c.dashboard_title, c.form_video_link, c.dashboard_video_link')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id')->join('dynamic_forms c','a.form_id=c.id');

								if($qq->num_rows()>0){

								 $this->db->where('b.department_id',$department->department_id);   

								}else{

								    $this->db->where('a.user_id',$user_id);

								}

								$query = $this->db->where('c.status','1')->where('c.form_running_status','0')->group_by('a.form_id')->get();

									$dyrow=$query->num_rows();

								if($query->num_rows()>0){	

								  

								foreach($query->result() as $row){

								     

									

									

								?>

								<tr>

									<td><?php echo $s++;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><br>

									

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px"></i></a>&nbsp; &nbsp; &nbsp;</td>

										<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>  <span class="pull-right"><a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								

								</tr>

								<?php $r++; }}?>

							</tbody>

							<?php $k++;}?>

						   </table>

                        </div>

                    </div>

                </div>







<?php }else{?>





	<div class="row" style="min-height:300px">

		

                    <div class="col-sm-12">

                        <div class="card-box table-responsive" id="target">

                           <table class="table table-bordered manglesh scroll display">

						   <?php

							$k=1; 

							$user_id=$_SESSION['logged_in']['user_id'];

                            $departmentid =$this->session->userdata['logged_in']['department_id'];

                            

                            /*Check User is Team Leader*/

                            $qq  = $this->db->select('team_leader')->from('prestogroup_teams')->where('team_leader',$user_id)->get();

                            

                            

                            

							$query = $this->db->select('department,department_id, reference,show_in_master_index')->from('departments')->where('status','1')->where('department_id',$departmentid)->where('show_in_master_index','1')->order_by('department','asc')->get();

							foreach($query->result() as $department){

							    $dpt= strtoupper($department->department);

							    if($dpt=='HR & ADMIN'){

							        $departmentmodel="HRADMIN";

							    }else{

								$departmentmodel = preg_replace('/\s+/', '_', $department->department);

							    }

							?>

							<thead>

							

								<tr class="text-center">

								<th class="text-center" colspan="4"><?php echo strtoupper($department->department);?> <span style="margin-left:20px"></span><i class="fa fa-folder-open-o" aria-hidden="true" title="view_reference" data-toggle="modal" data-target="#<?php echo $departmentmodel;?><?php echo $department->department_id;?>"></i><span style="margin-left:20px"></span><i class="fa fa-check-square-o" aria-hidden="true" title="Checklist" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>"></i><span style="margin-left:20px"></span><i class="fa fa-envelope-square" aria-hidden="true" title="Email Template" data-toggle="modal" data-target="#<?php echo $departmentmodel;?>emailtemplate"></i></th>

								</tr>

								

								

								<div id="<?php echo $departmentmodel;?>emailtemplate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

								<form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> EMAIL TEMPLATE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										

										<div class="col-md-12">

										<label>EMAIL SUBJECT</label>

										<select class="form-control" name="emailsubject<?php echo $department->department_id;?>" id="emailsubject<?php echo $department->department_id;?>"  onChange="onemail_search(<?php echo $department->department_id;?>);">

										<option value="">--Select Subject--</option>

										<?php 

										$q =$this->db->select('*')->from('departmentwise_email_template')->where('department_id',$department->department_id)->get();

										foreach($q->result() as $email){

										?>

										<option value="<?php echo $email->id;?>"><?php echo $email->email_subject;?></option>

										<?php }?>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

								<div id="<?php echo $departmentmodel;?><?php echo $department->department_id;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> REFERENCE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

									<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<div class="col-md-6">

										<label>VIEW REFERENCE</label>

										<select class="form-control" id="reference<?php echo $department->department_id;?>" name="reference<?php echo $department->department_id;?>" onChange="get_sub_reference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										<?php $query = $this->db->select('ref_id, reference_title')->from('department_reference')->where('department_id',$department->department_id)->get();

										foreach($query->result() as $ref){?>

										<option value="<?php echo $ref->ref_id;?>"><?php echo $ref->reference_title;?></option>

										<?php }?>

										

										</select>

										

										<script type="text/javascript">

											

													function get_sub_reference(id){

													var ref=$("#reference"+id).val();

													$.ajax({

													type:"post",

													url:"<?php echo page_url;?>Form/get_sub_ref",

													data:"ref="+ref,

													success:function(data){

													$("#subref"+id).html(data);

													}

													});

													

													}

													

											

												</script>

										</div>

										<div class="col-md-6">

										<label>VIEW SUB REFERENCE</label>

										<select class="form-control" name="subref<?php echo $department->department_id;?>" id="subref<?php echo $department->department_id;?>"  onChange="checkforsubsubreference(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

										

										<div class="col-md-6 subsubrefifavailable<?php echo $department->department_id;?>" style="display:none;">

										<label>VIEW SUB SUB REFERENCE</label>

										<select class="form-control" name="subsubref<?php echo $department->department_id;?>" id="subsubref<?php echo $department->department_id;?>"  onChange="onref_search1(<?php echo $department->department_id;?>);">

										<option value="">SELECT REFERENCE</option>

										</select>

										</div>

										

									</div>

											

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							

							<div id="<?php echo $departmentmodel;?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

                                <form id="loginForm" method="post" action="">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">VIEW <?php echo strtoupper($department->department);?> USER CHECKLIST</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

										<div class="col-md-12">

										<label>VIEW USER CHECKLIST</label>

										<input type="hidden" name="departmentid" value="<?php echo $department->department_id;?>">

										<select class="form-control" name="checklist_user<?php echo $department->department_id;?>" id="checklist_user<?php echo $department->department_id;?>" onChange="onuserchange(<?php echo $department->department_id;?>);">

											<option value="">--SELECT USER--</option>

										<?php 

										$this->db->select('user_id, first_name, last_name')->from('system_users');

										if($qq->num_rows()>0){

										  $this->db->where('department_id',$department->department_id);  

										}else{

										    $this->db->where('user_id',$user_id);

										}

										$query = $this->db->where('user_status','1')->where('hide_profile','0')->get();

										foreach($query->result() as $users){

										?>

										<option value="<?php echo $users->user_id;?>"><?php echo strtoupper($users->first_name." ".$users->last_name);?></option>

										<?php }?>

									</select></div>

										

									</div>	

											

                                        </div>

                                       

                                    </div>

                                </div>

								</form>

                            </div><!-- /.modal -->

							</thead>

							<tbody>

								<tr style="background-color:#fff; color:#1E98B1; font-size: 13px; font-weight:bold;">

									<td>SR NO</td>

									<td>FORM</td>

									<td class="text-center">SPEED FMS (PENDING)</td>

									<td>HISTORY</td>

								</tr>

								<?php 

								$i=1;

								$this->db->select('id,department_id, form_title, description, status, dashboard_title, form_video_link, dashboard_video_link')->from('dynamic_forms');

								if($qq->num_rows()>0){

								   $this->db->where('department_id',$department->department_id); 

								}else{

								   $this->db->where('user_id',$user_id); 

								}

								

								$query = $this->db->where('status','1')->where('form_running_status','0')->get();

									$dyrow=$query->num_rows();

									

								foreach($query->result() as $row){

								?>

								<tr>

									<td><?php echo $i;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><span style="margin-left:20px"></span> 

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px" ></i></a>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  </td>

								

									<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px"></i></a></td>

										<td style="color:#000;"><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								</tr>

								<?php $i++;}?>

								

								<?php

								if($dyrow>0)

								{

								$t=$dyrow+1;

								}else

								{

								    $t=1;

								    

								}

								$this->db->select('a.id,a.video,a.dashboard_video_link,b.first_name,b.last_name,b.user_id')->from('delegation_master a')->join('system_users b','a.assigned_to=b.user_id');

								if($qq->num_rows()>0){

								    $this->db->where('b.department_id',$department->department_id); 

								    

								}else{

								   $this->db->where('a.assigned_to',$user_id);

								}

								$delegationavail=$this->db->get();

								$delegationcount = $delegationavail->num_rows();

								if($delegationavail->num_rows()>0)

								{

									

									foreach($delegationavail->result() as $delegations)

									{

										

								?>

								<tr>

								<td><?php echo $t;?></td>

								<td><a href="<?php echo page_url;?>Delegation/delegation_task/<?php echo $delegations->user_id;?>/1" target="_blank">DELEGATION FORM FOR <?php echo strtoupper($delegations->first_name.' '.$delegations->last_name);?></a><a href="<?php echo $delegations->video;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

									<td class="text-center"><a href="<?php echo page_url;?>Delegation/delegation_dashboard/<?php echo $delegations->user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a> <a href="<?php echo $delegations->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="font-size:15px;"></i></a></td>

								<td><a href="<?php echo page_url;?>Delegation/delegated_task_history/<?php echo $delegations->user_id;?>" target="_blank">DELEGATION HISTORY</a></td>

								</tr>

								<?php

								$t++;

								}

								}

								?>

								

								<?php

								if($delegationcount>0)

								{

								$s=$t+1;

								}else

								{

								    $s=$t;

								    

								}

								

								$this->db->select('a.form_name, a.form_link, a.dashboard_link, a.history_title, a.history_link, a.form_video_link, a.dashboard_video_link')->from('dashboard_short_links a');

								

								  	 $this->db->join('standalone_dashboard_userwise_permission b','a.id=b.dashboard_id','left'); 

								  	 $this->db->where('b.user_id',$user_id);

								

								

							$q1=$this->db->get();

								$q1count = $q1->num_rows();

							

								if($q1->num_rows()>0)

								{

								    /**if($delegationcount!='0'){

									$s=$delegationcount+2;

									}else if($dyrow=='0'){

									    $s=1;

									}else{

									    $s=$dyrow+1;

									} **/

									foreach($q1->result() as $standalonedata)

									{

										

								?>

								<tr>

								<td><?php echo $s;?></td>

								<td>

								    <?php if($standalonedata->form_link){?>

								    <a href="<?php echo page_url;?><?php echo $standalonedata->form_link;?>" target="_blank"><?php echo $standalonedata->form_name;?></a><?php }else{?><?php echo $standalonedata->form_name;?><?php }?> <span class="pull-right"><a href="<?php echo $standalonedata->form_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td class="text-center">

								   

								   <a href="<?php echo page_url;?><?php echo $standalonedata->dashboard_link;?>/<?php echo $user_id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>

								   

								    <span class="pull-right"><a href="<?php echo $standalonedata->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span>

								   </td>

								<td><?php if($standalonedata->history_link==''){?><?php echo $standalonedata->history_title;?><?php }else{?>

							

								<a href="<?php echo $standalonedata->history_link;?>/<?php echo $user_id;?>" target="_blank"><?php echo $standalonedata->history_title;?></a><?php }?></td>

							

								

								</tr>

								<?php

								$s++;

									}

								}

								?>

								

								<?php 

								if($q1count>0)

								{

							     $r=$s+1;

								}else

								{

								$r=$s;

								}

								$this->db->select('a.form_id, a.user_id, b.user_id, b.department_id, c.id,c.form_title, c.description, c.status, c.dashboard_title, c.form_video_link, c.dashboard_video_link')->from('dynamic_form_dashboard_access a')->join('system_users b','a.user_id=b.user_id')->join('dynamic_forms c','a.form_id=c.id');

								if($qq->num_rows()>0){

								 $this->db->where('b.department_id',$department->department_id);   

								}else{

								    $this->db->where('a.user_id',$user_id);

								}

								$query = $this->db->where('c.status','1')->where('c.form_running_status','0')->group_by('a.form_id')->get();

									$dyrow=$query->num_rows();

								if($query->num_rows()>0){	

								  

								foreach($query->result() as $row){

								     

									

									

								?>

								<tr>

									<td><?php echo $s++;?></td>

									<td><a href="<?php echo page_url;?>Form/view/<?php echo $row->id;?>" target="_blank" ><?php echo strtoupper($row->form_title);?></a><br>

									

									<a href="<?php echo $row->form_video_link;?>" target="_blank"><i class="fa fa-video-camera pull-right" style="margin-left: 1.3em !important; font-size:15px"></i></a>&nbsp; &nbsp; &nbsp;</td>

										<td class="text-center"><a href="<?php echo page_url;?>Form/data_report/<?php echo $row->id;?>" target="_blank" class="btn btn-xs" style="background-color:#64C3D1; color:#fff;">SPEED FMS</a>  <span class="pull-right"><a href="<?php echo $row->dashboard_video_link;?>" target="_blank"><i class="fa fa-video-camera" style="font-size:15px"></i></a></span></td>

									<td><a href="<?php echo page_url;?>Form/view_history/<?php echo $row->id;?>" target="_blank"><?php echo strtoupper($row->description);?></a></td>

								

								</tr>

								<?php $r++; }}?>

							</tbody>

							<?php $k++;}?>

						   </table>

                        </div>

                    </div>

                </div>







<?php }?>

           





          

			  </div>

                 </div>

                 

			  <div id="con-close-modal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">



 <form id="loginForm" method="post" action="<?php echo page_url;?>Dashboard/add_news_events" enctype="multipart/form-data">



                                <div class="modal-dialog modal-lg">



                                    <div class="modal-content">



                                        <div class="modal-header">



                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>



                                            <h4 class="modal-title">ADD NEWS/EVENTS</h4>



                                        </div>



                                        <div class="modal-body">



                                            <div class="row">

										<div class="col-md-4">

											<div class="form-group">

												<label>Date</label>

												<input type="date" name="event_date" id="event_date" value="<?php echo date('Y-m-d');?>" class="form-control">

											</div>

												<div class="form-group">

												<label>Upload Image</label>

												<input type="file" name="event_file" id="event_file" value="" class="form-control">

											</div>

										</div>

										<div class="col-md-8">

											<div class="form-group">

											<label>News/Events</label>

												<textarea class="form-control" name="news_events" required></textarea>

											</div>

										</div>

												

                                                

                                                

										



                                            </div>

										</div>



                                        <div class="modal-footer">



                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>



                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 



                                        </div>



                                    </div>



                                </div>



								</form>



                            </div><!-- /.modal -->   

                            

                            

                            <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">



 <form id="loginForm" method="post" action="<?php echo page_url;?>Dashboard/add_Quote" enctype="multipart/form-data">



                                <div class="modal-dialog modal-lg">



                                    <div class="modal-content">



                                        <div class="modal-header">



                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>



                                            <h4 class="modal-title">ADD QUOTE OF THE DAY</h4>



                                        </div>



                                        <div class="modal-body">



                                            <div class="row">

			

										<div class="col-md-12">

											<div class="form-group">

												<textarea class="form-control" name="quote_of_the_day" required></textarea>

											</div>

										</div>

												

                                                

                                                

										



                                            </div>

</div>



                                        <div class="modal-footer">



                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>



                                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 



                                        </div>



                                    </div>



                                </div>



								</form>



                            </div><!-- /.modal -->



                <!-- Footer -->

               <?php $this->load->view('common/footer');?>

                <!-- End Footer -->



       

            <!-- end container -->








</div>
    </div>

        </div> <!-- contentarea end -->





<?php

  $rest=$this->db->select('notes')->from('storenotes')->where('userid',$_SESSION['logged_in']['user_id'])->get();

  if($rest->num_rows()>0)

  {

      foreach($rest->result() as $rest1);

      $note=$rest1->notes;

  }else

  {

      $note='';

  }



?>

<!-- Draggable DIV -->

<div id="mydiv" style="top: 125px; left: 1246px;" class="removestick">

  <!-- Include a header DIV with the same name as the draggable DIV, followed by "header" -->

  <div id="mydivheader" style="background-color:#FDFBB7;color:black;font-weight:bold;">NOTES</div>

 <div><textarea class="form-control" id="notess" style="background-color:#FDFBB7;border:none;" rows="10" onkeyup="storenotes();" value=""><?php echo $note;?></textarea></div>

</div>





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



        <!-- KNOB JS -->

        <!--[if IE]>

        <script type="text/javascript" src="<?php echo assets_url;?>plugins/jquery-knob/excanvas.js"></script>

        <![endif]-->

        <script src="<?php echo assets_url;?>plugins/jquery-knob/jquery.knob.js"></script>



        <!--Morris Chart-->

		<script src="<?php echo assets_url;?>plugins/morris/morris.min.js"></script>

		<script src="<?php echo assets_url;?>plugins/raphael/raphael-min.js"></script>



        <!-- Dashboard init -->

        <script src="<?php echo assets_url;?>pages/jquery.dashboard.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

    

    <script>

function myFunction() {

  var x = document.getElementById("mobilemenuwrap");

  if (x.style.display === "block") {

    x.style.display = "none";

  } else {

    x.style.display = "block";

  }

}

</script>



<script>



// Make the DIV element draggable:

dragElement(document.getElementById("mydiv"));



function dragElement(elmnt) {

  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;

  if (document.getElementById(elmnt.id + "header")) {

    // if present, the header is where you move the DIV from:

    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;

  } else {

    // otherwise, move the DIV from anywhere inside the DIV:

    elmnt.onmousedown = dragMouseDown;

  }



  function dragMouseDown(e) {

    e = e || window.event;

    e.preventDefault();

    // get the mouse cursor position at startup:

    pos3 = e.clientX;

    pos4 = e.clientY;

    document.onmouseup = closeDragElement;

    // call a function whenever the cursor moves:

    document.onmousemove = elementDrag;

  }



  function elementDrag(e) {

    e = e || window.event;

    e.preventDefault();

    // calculate the new cursor position:

    pos1 = pos3 - e.clientX;

    pos2 = pos4 - e.clientY;

    pos3 = e.clientX;

    pos4 = e.clientY;

    // set the element's new position:

    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";

    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";

  }



  function closeDragElement() {

    // stop moving when mouse button is released:

    document.onmouseup = null;

    document.onmousemove = null;

  }

}





function storenotes()

{



var content = document.getElementById('notess').value;



  $.ajax({

        type:"post",

        url:"<?php echo page_url;?>Dashboard/storenotes",

        data:"cont="+content,

        success:function(data){

           

         

        }

        });

	    







    

}



function tognotes()

{

    $("#mydiv").toggle();

}



$( document ).ready(function() {

  $("#mydiv").hide();

});

</script>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

        <script>

        

        $( document ).ready(function(){

			$('#loadingmessage2').show();

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/notification_data/',

                type: 'get',

                success: function(data){

                    $('#notificationdata').html(data);

					$('#loadingmessage2').hide(); 

                }

            });	

			

		});

		$( document ).ready(function() {

	$("#mydiv").hide();

	});

	</script>	

    

    </body>

</html>
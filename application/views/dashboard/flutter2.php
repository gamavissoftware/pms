<?php

$CI =& get_instance();
$CI->load->model('Fms_model');
$UI =& get_instance();
$UI->load->model('Store_model');


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

?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright; ?>">



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



body {
    background-color: white;
    padding-bottom: 80px;
}

.row-flex {
  display: flex;
  flex-wrap: wrap;
}

@media (max-width:576px){
    .row-flex{
        display:block;
    }
}




.flutter {
  height: 100%;
  padding: 10px;
  background-color:white;
box-shadow:1px 1px  10px lightgrey;
margin-top:20px;
border-radius:10px;
border-bottom: 2px solid lightseagreen;
}

.flutter p{
    font-size:11px;
    text-align:center;
}

.flutter h4{
    text-align:center;
    margin:0px;
    font-size:15px;
}
hr {
    margin-top: 10px;
    margin-bottom: 10px;
    border: 0;
    border-top: 1px solid #eee;
}

[class*="col-"] {
  margin-bottom: 10px;
}

.dailytaskcircle{
    border-radius: 50%;
    width: 45px;
    height: 45px;
    padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #703bda52;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin: auto;
    color:#703bda;
    font-weight:600;
}

.flutter i{
    color: #703bda;
}


.delegation-box i {
    font-size: 20px;
    color: #f9ab00;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px;
    background-color: #fff1ea;
    padding: 12px;
}

.refer{

    font-size: 28px;
    background: #DBF3FA;
    color: #283043;
 
    text-align: center;
    border-radius: 50%;

    margin: auto;
    width: 45px;
    height: 45px;

}
   

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

    margin:auto;

}

.dailytasksection { 

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #64C3D1;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin:auto;

}

.numberCirclegreen {

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #44D156;

    color: #666;

    text-align: center;

    font-size: 14px;

    margin:auto;

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

    margin:auto;

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

     margin:auto;

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

    margin:auto;

}



@media(max-width:769px)

{

    .removestick

    {

        display:none;

        

    }

}



.numberCircleleave {

    

    border-radius: 50%;

    width: 45px;

    height: 45px;

     padding: 9px 0px 0px 0px;

    background: #fff;

    border: 2px solid #C0C0C0;

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

    padding: 6px;

    box-shadow: 1px 1px 10px lightgray;

    -webkit-border-radius: 5px;

    border-radius: 5px;

    -moz-border-radius: 5px;

    background-clip: padding-box;

    margin-bottom: 7px;

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

</style>

	<style>

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

    background-color: lightgray;

    animation: pulse-ring 4.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;

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





.pulsating-circle1 {

position: absolute;

    width: 20px;

    height: 20px;

    left: 35%;

    top: -17px;

  }

  

 .pulsating-circle1  span

  {

        position: absolute;

        top: 0;

        z-index: 10;

        width: 100%;

        display: block;

        text-align: center;

        height: 20px;

        line-height: 20px;

        font-size: 10px;

  }

.pulsating-circle1::before {

    content: '';

    position: relative;

    display: block;

    width: 150%;

    height: 150%;

    box-sizing: border-box;

    margin-left: -25%;

    margin-top: -25%;

    border-radius: 45px;

    background-color: #1e4823;

    

    animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;

}

.pulsating-circle1::after {

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





table.manglesh thead th {

	position: relative;

				background: #e0e0e0;

				color:#413a3a;

				font-weight:bold;

				text-align:center;

				font-size:15px;

			}

			table.taskdetail thead th {

				background: yellow;

				color:#000;

				font-weight:bold;

			}

			

		table.sales thead th {

	position: relative;

				background: red;

				color:#fff;

				font-weight:bold;

				text-align:center;

			}

			

			

	

	#shortcut p

	{

	    text-align:center;
        font-size:11px;

	}

			
.mis-score{
    background-color: white; height:80px;
}


.mis-score:hover{
    background-color: #e9e9ff; height:80px;
}


.delegation-box{
    background-color: white; height: 80px;"
}

.delegation-box:hover{
    background-color: #fff1ea; height: 80px;"
}

.blink_me {
    animation: blinker 6s linear infinite;
    color: #2986CE;
    font-size: 15px;
    margin-left: 10px;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
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

            <div class="container-fluid" style="background-color:white; min-height:500px !important;">



                <!-- Page-Title -->

               

<div class="row row-flex" id="shortcut" >

    <?php echo $this->session->flashdata('message'); ?>
    
        

                     <div class=" col-md-3">

                       
                <?php $m1=$UI->Store_model->checkForDashboardModules(1);
                             
                             if($m1 > 0) { ?>
                         <div class="flutter" >

                            <div class="text-center">

                                <h4>DAILY TASK <span class="blink_me">Take Action</span> <span style="margin-left:20px"></span>

                                <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','235')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

                                

                                <a href="<?php echo page_url;?>Delegation/calls_delegation"><i class="fa fa-wpforms" aria-hidden="true" title="Calls delegation form"></i></a>

                                <?php }?>
                                
                                <span style="margin-right:10px"></span>
                                <?php 
									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','294')->where('submodule_access','1')->get();
									if($qry->num_rows()>0){
									?>
                                 <a href="<?php echo page_url;?>Delegation/calls_delegated_dashboard" style="position:relative;"><i class="fa fa-phone badge1" title="Calls delegation dashboard" style="color:green"></i>
                                 <?php 
                                 $query = $this->db->select('id')->from('calls_delegation')->where('employee_id',$user_id)->where('status','0')->get();
                                 if($query->num_rows()>0){
                                 ?>
                                    <div class="pulsating-circle1">  
                                    <span style="color:green"><?php 
                                $query = $this->db->select('id')->from('calls_delegation')->where('employee_id',$user_id)->where('status','0')->get();
                                $res =count($query->result());
                                echo $res;
                                ?></span></div>
                                <?php }?>
                                 </a><?php }?></h4>

                                <?php 

									$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','2')->where('submoduleid','294')->where('submodule_access','1')->get();

									if($qry->num_rows()>0){

									?>

                                 

                                 <?php 

                                 $query = $this->db->select('id')->from('calls_delegation')->where('employee_id',$user_id)->where('status','0')->get();
                                 if($query->num_rows()>0){

                                 ?>
                            <div class="pulsating-circle1">  

                                    <span style="color:green"><?php 

                                $query = $this->db->select('id')->from('calls_delegation')->where('employee_id',$user_id)->where('status','0')->get();

                                $res =count($query->result());

                                echo $res;

                                ?></span></div>

                                <?php }?>

                                 </a><?php }?></h4><hr>

                                <div class="row" style="margin-top: 15px;">
                <?php  $s1=$UI->Store_model->checkForDashboardSubmodules(1);
                    if($s1 > 0) {?>
                                    <a href="<?php echo page_url;?>Delegation/delegation_dashboard"><div class="col-md-3 col-sm-3 col-xs-3 text-center">

                                        <div class="dailytasksection" id="delegateddata">

										<?php 

										$q = $this->db->select('id')->from('delegation_task')->where('yourname',$user_id)->where('task_status','0')->get();

										$report = count($q->result());

										echo $report;

										?>

										</div>

                                        <p style="font-size:11px; text-align:center; margin-top:10px;">DELEGATED TASK</p>

                                    </div></a>
                                <?php } ?>
									
                        <?php  $s2=$UI->Store_model->checkForDashboardSubmodules(2);
                            if($s2 > 0) {?>
                                    <a href="<?php echo page_url;?>Reporting/pendingpoforapproval"><div class="col-md-3 col-sm-3 col-xs-3">

                                        <div class="dailytasksection" id="poapprovaldata"></div>

                                        <p title="total pending task" style="font-size:11px; margin-top:10px;">PO APPROVAL</p>

                                    </div></a>
                                    <?php } ?>

                                    
                        <?php  $s3=$UI->Store_model->checkForDashboardSubmodules(3);
                            if($s3 > 0) {?>
                                   <a href="<?php echo page_url;?>Form/data_report/12"> <div class="col-md-3 col-xs-3 col-sm-3">

                                       <div class="pulsating-circle"><span style="position: absolute;top: 0;z-index: 10;width: 100%;display: block;text-align: center;height: 35px;line-height: 43px;" id="helpticketdata"></span></div>

										<p  style="font-size: 11px;

										position: relative;

										top: 56px;" >HELP TICKET</p>

										</div></a>
                                <?php } ?>
                                    
                        <?php  $s4=$UI->Store_model->checkForDashboardSubmodules(4);
                            if($s4 > 0) {?>
                                     <a href="<?php echo page_url;?>Form/gm_escalation"><div class="col-md-3 col-sm-3 col-xs-3">

                                        <div class="dailytasksection" id="escalatedticketdata"></div>

                                        <p title="total pending task" style="font-size:11px; margin-top:10px;">ESCALATED TICKET</p>

                                    </div></a>
                                    <?php } ?>

                                <?php  $s5=$UI->Store_model->checkForDashboardSubmodules(5);
                            if($s5 > 0) {?>
                                    <a href="<?php echo page_url;?>Daily_reporting/reporting_dashboard"><div class="col-md-3 col-sm-3 col-xs-3">
                                         <div class="text-center">
                                
                                             <div class="dailytasksection"  id="dailyworkreport"></div>
                                             </div>
                                
                                             <p style="font-size:11px; margin-top:10px;">DAILY REPORT</p>
                                             </div></a>
                                             <?php } ?>

             <?php  $s6=$UI->Store_model->checkForDashboardSubmodules(6);
                            if($s6 > 0) {?>
             <a href="<?php echo page_url;?>Daily_reporting/"><div class="col-md-3 col-sm-3 col-xs-3">
         <div class="text-center">

             <div class="dailytasksection"  id="dailyworkreport"></div>
             </div>

             <p style="font-size:11px; margin-top:10px;">NA</p>
             </div></a>
             <?php } ?>

            <?php  $s7=$UI->Store_model->checkForDashboardSubmodules(7);
                            if($s7 > 0) {?>
             <a href="<?php echo page_url;?>Daily_reporting/"><div class="col-md-3 col-sm-3 col-xs-3">
         <div class="text-center">

             <div class="dailytasksection"  id="dailyworkreport"></div>
             </div>

             <p style="font-size:11px; margin-top:10px;">NA</p>
             </div></a>
             <?php } ?>
            
            <?php  $s8=$UI->Store_model->checkForDashboardSubmodules(8);
                            if($s8 > 0) {?>
             <a href="<?php echo page_url;?>Daily_reporting/"><div class="col-md-3 col-sm-3 col-xs-3">
         <div class="text-center">

             <div class="dailytasksection"  id="dailyworkreport"></div>
             </div>

             <p style="font-size:11px; margin-top:10px;">NA</p>
             </div></a>
             <?php } ?>

                                </div>

                                

                            </div>

                        </div>
                        <?php } ?>
						</div>
                        <div class=" col-md-3">
                       <?php $m7=$UI->Store_model->checkForDashboardModules(7);
                        if($m7 > 0) { ?>

                        <div class="flutter" >

                           <div class="text-center">

                                <h4>STORE</h4><hr>

                                 <div class="row">
                                     
                                     <?php  $s19 = $UI->Store_model->checkForDashboardSubmodules(19);
                                        if($s19 > 0) {?>

                                    <!--<a href="<?php echo page_url;?>Reporting/pendingpr/1"><div class="col-md-4 col-sm-4 col-xs-4 text-center">-->

                                    <!--    <div class="numberCirclestore" id="jobcardbom"></div>-->

                                    <!--    <p style="font-size:11px; text-align:center">BOM VS PR</p>-->

                                    <!--</div></a>-->
                                    <?php } ?>
                                    
                                    <?php  $s20 = $UI->Store_model->checkForDashboardSubmodules(20);
                                        if($s20 > 0) {?>

                                    <!--<a href="<?php echo page_url;?>Reporting/pendingpr/3"><div class="col-md-4 col-xs-4 col-sm-4">-->

                                    <!--    <div class="numberCirclestore" id="imsvsprdata"></div>-->

                                    <!--    <p style="font-size:11px; text-align:center">IMS VS PR</p>-->

                                    <!--</div></a>-->
                                    <?php } ?>
                                    
                                    <?php  $s21 = $UI->Store_model->checkForDashboardSubmodules(21);
                                        if($s21 > 0) {?>

                                    <a href="<?php echo page_url;?>Reporting/indent_vs_pr" target="_blank"><div class="col-md-4 col-xs-4 col-sm-4">

                                        <div class="numberCirclestore" id="indentvspr"></div>

                                        <p style="font-size:11px; text-align:center">INDENT VS PR</p>

                                    </div></a>

                                    <?php } ?>
                                    
                                    <?php  $s22 = $UI->Store_model->checkForDashboardSubmodules(22);
                                        if($s22 > 0) {?>

                                    <a href="<?php echo page_url;?>Challan/rgp_challan_dashboard/0" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCirclestore" id="rgpvsreturn"></div>

                                        <p style="font-size:11px; text-align:center">RGP VS RETURN</p>

                                    </div></a>
                                    <?php } ?>

                                    <?php  $s23 = $UI->Store_model->checkForDashboardSubmodules(23);
                                        if($s23 > 0) {?>

                                     <a href="<?php echo page_url;?>Form/data_report/58"><div class="col-md-4 col-xs-4 col-sm-4">

                                        <div class="numberCirclestore" id="rejectvsaction"></div>

                                        <p style="font-size:11px; text-align:center">REJECTION VS ACTION</p>

                                    </div></a>
                                    <?php } ?>
                                    <?php  $s24 = $UI->Store_model->checkForDashboardSubmodules(24);
                                        if($s24 > 0) {?>

                                    <!--<a href="<?php echo page_url;?>Form/"><div class="col-md-4 col-xs-4 col-sm-4">

                                        <div class="numberCirclestore" id="rejectvsaction">25</div>

                                        <p style="font-size:11px; text-align:center">DEAD STOCK</p>

                                        </div></a>-->
                                        <?php } ?>

                                </div>

                                 

                                

                            </div>

                        </div>
                        <?php } ?>
						</div>
                        <div class=" col-md-3">                     
                        
                        <?php $m3=$UI->Store_model->checkForDashboardModules(3);
             
                        if($m3 > 0) { ?>
                       <div class="flutter">

                           <div class="text-center">

                                <h4>ORDERS</h4><hr>

                                 <div class="row" >

                                   <?php  $s10 = $UI->Store_model->checkForDashboardSubmodules(10);
                                        if($s10 > 0) {?>

                                    <a href="<?php echo page_url;?>FMS/nondispatchedlist" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4 text-center">

                                        <div class="numberCirclegreen" id="totalordersdata"></div>

                                        <p>TOTAL ORDERS</p>

                                    </div></a>
                                    <?php } ?>
                                    
                                    <?php  $s11 = $UI->Store_model->checkForDashboardSubmodules(11);
                                        if($s11 > 0) {?>
                                    <a href="<?php echo page_url;?>Reporting/lotorderlist" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCirclegreen"><i class="fa fa-bar-chart" aria-hidden="true" style="font-size:20px"></i></div>

                                        <p title="total pending task">LOT ORDERS</p> 

                                    </div></a>
                                    <?php } ?>
                                   
                                     <?php  $s12 = $UI->Store_model->checkForDashboardSubmodules(12);
                                        if($s12 > 0) {?>
                                    <a href="<?php echo page_url;?>Reporting/pendingorders" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCirclegreen" id="pendingorderdata"></div>

                                        <p >PENDING</p>

                                    </div></a>
                                    <?php } ?>

                                    

                                     <?php  $s13 = $UI->Store_model->checkForDashboardSubmodules(13);
                                        if($s13 > 0) {?>

                                     <a href="<?php echo page_url;?>Reporting/pendingorders/1" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCirclegreen" id="pendingorderdatasales"></div>

                                        <p >SALES PENDING</p>

                                    </div></a>
                                    <?php } ?>
                                    
                                    <?php  $s14 = $UI->Store_model->checkForDashboardSubmodules(14);
                                        if($s14 > 0) {?>

                                    <a href="<?php echo page_url;?>FMS/nondispatchedlist" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">

                                    <div class="numberCirclegreen" id="pendingorderdatasales">3</div>
                                    
                                    <p >TODAYS ORDER</p>
                                    
                                    </div></a>
                                    <?php } ?>
                                    
                                    <?php  $s15 = $UI->Store_model->checkForDashboardSubmodules(15);
                                        if($s15 > 0) {?>

                                    <a href="<?php echo page_url;?>FMS/nondispatchedlist" target="_blank"><div class="col-md-4 col-sm-4 col-xs-4">
                                    
                                    <div class="numberCirclegreen" id="pendingorderdatasales">50</div>
                                    
                                    <p >MONTHLY ORDER</p>
                                    
                                    </div></a>
                                    <?php } ?>

                                    

                                </div>

                                

                                

                            </div>

                        </div>
                    <?php } ?>
                        </div>
                        <div class=" col-md-3">

<div class="flutter" >

   

    <h4 ><i class="zmdi zmdi-notifications-none m-r-5" style="color:red;"></i> NOTIFICATION PANEL <?php 

$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','1')->get();

if($module->num_rows()>0)

{

foreach($module->result() as $moddata);

if($moddata->access=='1')

{



?><span class="btn btn-warning btn-xs text-center" data-toggle="modal" data-target="#con-close-modal1"><i class="fa fa-plus-circle" aria-hidden="true"></i></span><?php }}?> <a href="<?php echo page_url;?>Dashboard/view_all_events"><i class="fa fa-eye"></i></a></h4> <hr>



    <ul class="list-group m-b-0 user-list" style="height: 170px; overflow-y:auto" id="notificationdata">

           

<div id='loadingmessage2' style='display:none'>

    <img src='https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif'/>

    </div>

      

    </ul>

</div>
</div>
<div class="col-md-3">
<div class="flutter">
                        <div class="row">

                            <a href="<?php echo page_url;?>Reporting/misscore"><div class="col-md-6 col-sm-6 col-xs-6">
                    <?php $m5=$UI->Store_model->checkForDashboardModules(5);
                        if($m5 > 0) { ?>
                                 <div class="  mis-score" style="margin-top:50px;">

                                      <div class="text-center">

                                          <i class="fa fa-wpforms" style="font-size:20px; color:#8484d7 ;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px; background-color:#e9e9ff; padding:12px; "></i>

                                <p style="color:#337ab7; font-size:11px"> MIS SCORE </p>

                                

                            </div>


                        </div>
                    <?php } ?>
                            </div></a>

                            <a href="<?php echo page_url;?>Delegation/delegation_task"><div class="col-md-6 col-sm-6 col-xs-6">
                    <?php $m6=$UI->Store_model->checkForDashboardModules(6);
                        if($m6 > 0) { ?>
                                 <div class="  delegation-box" style="margin-top:50px;">

                                      <div class="text-center">

                                          <div class="row">

                                          <i class="fa fa-users" style="font-size:20px; color:#f9ab00 ;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px; background-color:#fff1ea; padding:12px; "></i>

                                              <p style="color:#337ab7; font-size:11px"> DELEGATION </p>

                                             
                                             
                                            

                                            

                                          </div>                                

                            </div></a>


                        </div>
            <?php } ?>
                            </div>

                        </div>
                        </div>
						</div>
                        <div class=" col-md-3">
                    <?php $m11=$UI->Store_model->checkForDashboardModules(11);
                        if($m11 > 0) { ?>

                        <div class="flutter" >

<div class="text-center">

     <h4>PURCHASE</h4><hr>

      <div class="row">
          
          <?php  $s39 = $UI->Store_model->checkForDashboardSubmodules(39);
                                        if($s39 > 0) {?>

         <a href="<?php echo page_url;?>Reporting/pr_vs_po" target="_blank"> <div class="col-md-4 col-sm-4 col-xs-4">

             <div class="numberCirclepurchase" id="prvspodata"></div>

             <p >PR VS PO</p>

         </div></a>
<?php } ?>
        
        <?php  $s40 = $UI->Store_model->checkForDashboardSubmodules(40);
                    if($s40 > 0) {?>
         <a href="<?php echo page_url;?>Reporting/pendingconsolidatedpovsdelivery"> <div class="col-md-4 col-sm-4 col-xs-4">

             <div class="numberCirclepurchase" id="povsdeliverydata"></div>

             <p title="total pending task">PO VS DELIVERY</p>

         </div></a>
    <?php } ?>
         

        <?php  $s41 = $UI->Store_model->checkForDashboardSubmodules(41);
                    if($s41 > 0) {?>

            <a href="<?php echo page_url;?>Vendor/pending_for_review"> <div class="col-md-4 col-sm-4 col-xs-4">

             <div class="numberCirclepurchase" id="vendorforreviewcount"></div>

             <p title="total pending task">VENDOR APPROVAL</p>

         </div></a>
        <?php } ?>
        

     </div>

 </div>
</div> 
</div> 
<?php } ?>
<div class=" col-md-3">
                        <?php $m8=$UI->Store_model->checkForDashboardModules(8);
             
                        if($m8 > 0) { ?>
                        <div class="flutter" >

                           <div class="text-center">

                                <h4>PRODUCTION</h4><hr>

                                <?php  $s25 = $UI->Store_model->checkForDashboardSubmodules(25);
                                        if($s25 > 0) {?>

                                 <a href="<?php echo page_url;?>Reporting/completedorders" target="_blank"><div class="row" style="margin-top:30px;">

                                    <div class="col-md-3 col-sm-3 col-xs-3 text-center">

                                        <div class="numberCircleproduction"  id="readyordercount"></div>

                                        <p style="font-size:11px; text-align:center">READY ORDERS</p>

                                    </div></a>
                                    <?php } ?>
                                     <?php  $s26 = $UI->Store_model->checkForDashboardSubmodules(26);
                                        if($s26 > 0) {?>

                                     <a href="<?php echo page_url;?>FMS/unplannedorder" target="_blank"><div class="col-md-3 col-sm-3 col-xs-3">

                                        <div class="numberCircleproduction"  id="unplannedordersdata"></div>

                                        <p title="total pending task" style="font-size:11px; text-align:center">UNPLANNED ORDERS</p>

                                    </div></a>
                                    <?php } ?>
                                     <?php  $s27 = $UI->Store_model->checkForDashboardSubmodules(27);
                                        if($s27 > 0) {?>

                                     <a href="<?php echo page_url;?>Reporting/dispatchfortommorow/0" target="_blank"><div class="col-md-3 col-sm-3 col-xs-3">

                                         <div class="numberCircleproduction"  id="dispatchfortomorrow"></div>

                                        <p title="total pending task" style="font-size:11px; text-align:center">DISPATCH TOMORROW</p>

                                    </div></a>
                                    <?php } ?>

                                    
                                    <?php  $s28 = $UI->Store_model->checkForDashboardSubmodules(28);
                                        if($s28 > 0) {?>
                                     <a href="<?php echo page_url;?>Reporting/reorderlist" target="_blank"><div class="col-md-3 col-sm-3 col-xs-3">

                                         <div class="numberCircleproduction"  id="reorderdata"></div>

                                        <p title="total pending task" style="font-size:11px; text-align:center">REORDER</p>

                                    </div></a>
                                <?php } ?>
                                    

                                </div>

                                

                                

                            </div>

                        </div>
                        
                        <?php } ?>
                        

                        

                     </div><!-- end col -->
                     <div class=" col-md-3">
                    <?php $m13=$UI->Store_model->checkForDashboardModules(13);
             
                        if($m13 > 0) { ?>
                        <div class="flutter" >

<div class="text-center">

        <h4>LMS</h4><hr>

        <div class="row" style="margin-top:20px;">
<?php  $s47 = $UI->Store_model->checkForDashboardSubmodules(47);
                                        if($s47 > 0) {?>
<a href="<?php echo page_url;?>LMS/listlms/2"><div class="col-md-4 col-xs-4 col-sm-4 text-center">

    <div class="numberCircleproduction" id=""><i class="fa fa-bar-chart" aria-hidden="true"></i></div>

    <p style="font-size:11px; padding-right:0px">SALES</p>
 

</div></a>
<?php } ?>

<?php  $s48 = $UI->Store_model->checkForDashboardSubmodules(48);
        if($s48 > 0) {?>

<a href="<?php echo page_url;?>LMS/listlms/5"><div class="col-md-4 col-xs-4 col-sm-4">

    <div class="numberCircleproduction" id=""><i class="fa fa-cog" aria-hidden="true"></i></div>

    <p title="total pending task" style="font-size:11px; padding-right:0px;">SERVICE</p>

</div></a>
<?php } ?>
<?php  $s49 = $UI->Store_model->checkForDashboardSubmodules(49);
                                        if($s49 > 0) {?>
<a href="<?php echo page_url;?>LMS/listlms/4"><div class="col-md-4 col-sm-4 col-xs-4">

    <div class="numberCircleproduction" id=""><i class="fa fa-suitcase" aria-hidden="true"></i></div>

    <p  style="font-size:11px; padding-right:0px">HR</p>

</div></a>
<?php } ?>
</div>

</div>

</div>
<?php } ?>
     </div>    
<div class="col-md-3">
                    <?php $m10=$UI->Store_model->checkForDashboardModules(10);
                        if($m10 > 0) { ?>

                         <div class="flutter" >

                            <div class="text-center">

                                <h4>HELP DESK</h4><hr>

                                <div class="row" style="margin-top: 16px;">
                                     <?php  $s30=$UI->Store_model->checkForDashboardSubmodules(30);
                            if($s30 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/1"><div class="col-md-4 col-xs-4 col-sm-4 text-center">

                                        <div class="numberCircle" id="accounthelpticketdata"></div>

                                        <p style="font-size:11px; padding-right:0px">ACCOUNTS</p>
                                     

                                    </div></a>
                                    <?php } ?>
                    <?php  $s31=$UI->Store_model->checkForDashboardSubmodules(31);
                            if($s31 > 0) {?>

                                    <a href="<?php echo page_url;?>Form/data_report/8"><div class="col-md-4 col-xs-4 col-sm-4">

                                        <div class="numberCircle" id="servicehelpticketdata"></div>

                                        <p title="total pending task" style="font-size:11px; padding-right:0px;">SERVICE</p>

                                    </div></a>
                                    <?php } ?>
                                    
                    <?php  $s32=$UI->Store_model->checkForDashboardSubmodules(32);
                            if($s32 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/3"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCircle" id="ithelpticketdata"></div>

                                        <p  style="font-size:11px; padding-right:0px">IT</p>

                                    </div></a>
                                    <?php } ?>

                                    </div>

                                    <div class="row">
                    <?php  $s33=$UI->Store_model->checkForDashboardSubmodules(33);
                            if($s33 > 0) {?>
                                     <a href="<?php echo page_url;?>Form/data_report/5"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCircle"  id="saleshelpticketdata"></div>

                                        <p  style="font-size:11px; padding-right:0px">SALES</p>

                                    </div></a>
                            <?php } ?>
                             <?php  $s34=$UI->Store_model->checkForDashboardSubmodules(34);
                            if($s34 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/9"><div class="col-md-4 col-sm-4 col-xs-4 ">

                                        <div class="numberCircle" id="productionhelpticketdata"></div>

                                        <p style="font-size:11px; ">PRODUCTION</p>

                                    </div></a>
                                <?php } ?>
                     <?php  $s35=$UI->Store_model->checkForDashboardSubmodules(35);
                            if($s35 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/10"><div class="col-md-4 col-sm-4 col-xs-4 ">

                                        <div class="numberCircle" id="dispatchhelpticketdata"></div>

                                        <p title="total pending task" style="font-size:11px; ">DISPATCH</p>

                                    </div></a>
                                <?php } ?>
 
                     <?php  $s36=$UI->Store_model->checkForDashboardSubmodules(36);
                            if($s36 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/6"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCircle" id="eagmhelpticketdata"></div>

                                        <p  style="font-size:11px;  text-align:center;">E.A </p>

                                    </div></a>
                        <?php } ?>
                    <?php  $s37=$UI->Store_model->checkForDashboardSubmodules(37);
                            if($s37 > 0) {?>
                                    <a href="<?php echo page_url;?>Form/data_report/11"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCircle" id="purchasehelpticketdata"></div>

                                        <p  style="font-size:11px; ">PURCHASE</p>

                                    </div></a>
                        <?php } ?>
						
						
                                    
                    <?php  $s38=$UI->Store_model->checkForDashboardSubmodules(38);
                            if($s38 > 0) {?>
                                     <a href="<?php echo page_url;?>Form/data_report/4"><div class="col-md-4 col-sm-4 col-xs-4">

                                        <div class="numberCircle" id="hrhelpticketdata"></div>

                                        <p  style="font-size:11px; ">HR</p>

                                    </div></a>
                                   
                            <?php } ?>
                                   

                                </div>

                                

                            </div>

                        </div>
                <?php } ?>

                         

                </div><!-- end col -->

                    

                
                      <!-- <div class=" col-md-3">
                    <?php $m2=$UI->Store_model->checkForDashboardModules(2);
                        if($m2 > 0) { ?>
                      <div class="flutter" >

                            <a href="<?php echo page_url;?>FMS/planned_actual"><h4 class="text-center"> PLANNED VS ACTUAL</h4></a><hr>

                           <ul class="list-group m-b-0 user-list" style="height: 198px; overflow-y:auto; font-size:11px;" id="plannedvsactualdata">

							<div id='loadingmessage' style='display:none'>

							<img src='https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif'/>

							</div>

                            </ul>

                        </div>

                       <?php } ?>
					    </div> -->
                      
                       
<div class=" col-md-3">
 <?php $m12=$UI->Store_model->checkForDashboardModules(12);
             
                        if($m12 > 0) { ?>

                        <div class="flutter" >

                        <div class="text-center">

                                <h4>REFERENCE</h4><hr>

                                    <div class="row">

         <a href="<?php echo page_url;?>Dashboard/view_docs"><div class="col-sm-3 col-xs-3">
     <div class="text-center refer">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </div>
        <p style="font-size:11px; padding-right:0px">SALE REFERENCE</p>
         </div></a>
        
         <a href="<?php echo page_url;?>Master/User_management/email_template" target="_blank"><div class="col-sm-3 col-xs-3">
       <div class="text-center refer">
        <i class="fa fa-envelope" aria-hidden="true"></i>
        </div>

        <p style="font-size:11px; padding-right:0px">EMAIL TEMPLATE</p>
         </div></a>

         <a href="<?php echo page_url;?>Dashboard/view_videos" target="_blank"><div class="col-sm-3 col-xs-3">
       <div class="text-center refer">
        <i class="fa fa-youtube" aria-hidden="true"></i>
        </div>

        <p style="font-size:11px; padding-right:0px">SALES VIDEOS</p>
         </div></a>
        
         <a href="<?php echo page_url;?>IT Policy.pdf" target="_blank"><div class="col-sm-3 col-xs-3">
      <div class="text-center refer">
        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
        </div>

        <p style="font-size:11px; padding-right:0px">IT POLICY</p>
         </div></a>
         
        

     </div>

 </div>

</div>

<?php } ?>

                     </div><!-- end col -->


                    
                   
					

                   
						<div class=" col-md-3">

                <?php $m9=$UI->Store_model->checkForDashboardModules(9);
             
                        if($m9 > 0) { ?>

                        <div class="flutter" >

                            <div class="text-center">

                                <h4>STOCK VALUE - <span id="totalstockvalue"></span></h4><hr>

                                <ul class="list-group m-b-0 user-list" style=" overflow-y:auto; height: 152px;" id="totalstockvaluedata">

								

								<div id='loadingmessages' style='display:none'>

							<img src='https://media.giphy.com/media/3oEjI6SIIHBdRxXI40/giphy.gif'/>

							</div>

                            </ul>

                                

                            </div>

                        </div>
                        <?php } ?>
						</div>
					               

                    

                    

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



            </div>

            <!-- end container -->









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





</script>

<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

        <script>

        

        $( document ).ready(function(){

            $.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/1',

                type: 'get',

                success: function(data){

                    $('#delegateddata').html(data);

                }

            });

			

			 $.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/2',

                type: 'get',

                success: function(data){

                    $('#poapprovaldata').html(data);

                },

                error: function (xhr, ajaxOptions, thrownError) {

                    var errorMsg = 'Ajax request failed: ' + xhr.responseText;

                    $('#poapprovaldata').html(errorMsg);

                  }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/3',

                type: 'get',

                success: function(data){

                    $('#helpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/4',

                type: 'get',

                success: function(data){

                    $('#escalatedticketdata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/5',

                type: 'get',

                success: function(data){

                    $('#accounthelpticketdata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/6',

                type: 'get',

                success: function(data){

                    $('#servicehelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/7',

                type: 'get',

                success: function(data){

                    $('#ithelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/8',

                type: 'get',

                success: function(data){

                    $('#saleshelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/9',

                type: 'get',

                success: function(data){

                    $('#productionhelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/10',

                type: 'get',

                success: function(data){

                    $('#dispatchhelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/11',

                type: 'get',

                success: function(data){

                    $('#eagmhelpticketdata').html(data);

                }

            });

        $.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/12',

                type: 'get',

                success: function(data){

                    $('#eavmhelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/13',

                type: 'get',

                success: function(data){

                    $('#purchasehelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/14',

                type: 'get',

                success: function(data){

                    $('#hrhelpticketdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/15',

                type: 'get',

                success: function(data){

                    $('#paymentunclaimeddata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/16',

                type: 'get',

                success: function(data){

                    $('#paymenthistorydata').html(data);

                }

            });

			 		

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/23',

                type: 'get',

                success: function(data){

                    $('#totalleaveapplication').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/24',

                type: 'get',

                success: function(data){

                    $('#leaveapplicationforapproval').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/25',

                type: 'get',

                success: function(data){

                    $('#onleavetoday').html(data);

                }

            });	



			$('#loadingmessage2').show();

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/notification_data/',

                type: 'get',

                success: function(data){

                    $('#notificationdata').html(data);

					$('#loadingmessage2').hide(); 

                }

            });	



			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/26',

                type: 'get',

                success: function(data){

                    $('#totalordersdata').html(data);

                }

            });			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/28',

                type: 'get',

                success: function(data){

                    $('#pendingorderdata').html(data);

                }

            });

            

            

            $.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/38',

                type: 'get',

                success: function(data){

                    $('#pendingorderdatasales').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/17',

                type: 'get',

                success: function(data){

                    $('#jobcardbom').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/18',

                type: 'get',

                success: function(data){

                    $('#imsvsprdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/19',

                type: 'get',

                success: function(data){

                    $('#indentvspr').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/20',

                type: 'get',

                success: function(data){

                    $('#rgpvsreturn').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/21',

                type: 'get',

                success: function(data){

                    $('#rejectvsaction').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/22',

                type: 'get',

                success: function(data){

                    $('#dailyworkreport').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/29',

                type: 'get',

                success: function(data){

                    $('#readyordercount').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/30',

                type: 'get',

                success: function(data){

                    $('#unplannedordersdata').html(data);

                }

            });

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/31',

                type: 'get',

                success: function(data){

                    $('#dispatchfortomorrow').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/32',

                type: 'get',

                success: function(data){

                    $('#reorderdata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/33',

                type: 'get',

                success: function(data){

                    $('#prvspodata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/34',

                type: 'get',

                success: function(data){

                    $('#povsdeliverydata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/35',

                type: 'get',

                success: function(data){

                    $('#povsmrndata').html(data);

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/getdynamic_data/36',

                type: 'get',

                success: function(data){

                    $('#vendorforreviewcount').html(data);

                }

            });

			

			$('#loadingmessage').show();

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/plannedvsactual_data/',

                type: 'get',

                success: function(data){

                    $('#plannedvsactualdata').html(data);

					$('#loadingmessage').hide(); 

                }

            });

			

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/completestockvalue/',

                type: 'get',

                success: function(data){

                    $('#totalstockvalue').html(data);

                }

            });

			

			$('#loadingmessages').show();

			$.ajax({

                url: '<?php echo page_url;?>Fetch_dynamic_data/totalstockvaluedata/',

                type: 'get',

                success: function(data){

                    $('#totalstockvaluedata').html(data);

					$('#loadingmessages').hide(); 

                }

            });



		

		});

		

	$( document ).ready(function() { 

	$("#mydiv").hide();

	});

        </script>

    </body>

</html>


<?php
$CI =& get_instance();
$CI->load->model('Fms_model');
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
   
.numberCircle {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #64C3D1;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}
.numberCirclegreen {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #44D156;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}
.numberCircleproduction {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #8B97BD;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}

.numberCirclepurchase {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #F2652F99;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}
.numberCirclestore {
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #E6E31DCC;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin-left:20px;
}


.numberCircleleave {
    
    border-radius: 50%;
    width: 60px;
    height: 60px;
    padding: 17px;
    background: #fff;
    border: 3px solid #C0C0C0;
    color: #666;
    text-align: center;
    font-size: 14px;
}
.numberCircleMIS {
    border-radius: 50%;
    width: 30px;
    height: 30px;
    padding: 10px;
    background: #fff;
    border: 3px solid #fff;
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

</style>
	<style>
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
               
 <?php if($user_id=='1' || $user_id=='66' || $user_id=='67'){?>         

<div class="row">
                     <div class="col-lg-3 col-md-6">
                       
                         <div class="card-box widget-user" style="min-height:158px">
                            <div class="text-center">
                                <h4>DAILY TASK <span class="btn btn-xs" style="background-color:#2986CE; color:#fff">Take Action</span></h4><hr>
                                <div class="row">
                                    <a href="<?php echo page_url;?>Delegation/delegation_dashboard"><div class="col-md-4 text-center">
                                        <div class="numberCircle"><?php 
                                        $q = $this->db->select('id')->from('delegation_task')->where('task_status','0')->where('yourname',$user_id)->get();
                                        $report = count($q->result());
                                        echo $report; 
                                        ?></div>
                                        <p style="font-size:11px; text-align:center">DELEGATED TASK</p>
                                    </div></a>
                                    <a href="<?php echo page_url;?>Reporting/pendingpoforapproval" target="_blank"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $rest=$this->db->select('id')->from('purchase_order')->where('approved','0')->group_by('pono')->get();
                                        echo count($rest->result());
                                        ?></div>
                                        <p title="total pending task" style="font-size:11px">PO APPROVAL</p>
                                    </div></a>
                                    <?php 
                                    if($user_id=='66'){
                                            $formid = "14";
                                        }else if($user_id=='67'){
                                            $formid = "50";
                                        }else{
                                            $formid="14";
                                        }
                                        
                                    ?>
                                   <a href="<?php echo page_url;?>Form/data_report/<?php echo $formid;?>"> <div class="col-md-4">
                                        
                                        <div class="numberCircle"><?php 
                                        
                                        
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id',$formid)->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> <div class="sonar-wrapper">
	<div class="sonar-emitter">
    <div class="sonar-wave"></div>
  </div>
</div></div>
                                        <p  style="font-size:11px" >HELP TICKET</p>
                                    </div></a>
                                </div>
                                
                            </div>
                        </div>
                        <div class="row">
                            <a href="<?php echo page_url;?>Reporting/misscore"><div class="col-md-4">
                                 <div class="card-box widget-user" style="background-color: #9785C2; min-height: 60px;">
                             
                                      <div class="text-center">
                                          <i class="fa fa-wpforms" style="font-size:20px; color:#fff"></i>
                                <p style="color:#fff; font-size:12px"> MIS SCORE </p>
                                
                            </div>
                              
                            
                        </div>
                            </div></a>
                            <div class="col-md-8">
                                 <div class="card-box widget-user" style="background-color: orange; min-height: 67px;">
                             
                                      <div class="text-center">
                                          <div class="row">
                                              <p style="color:#fff; font-size:12px"> DELEGATE TASK </p><hr>
                                              <a href="<?php echo page_url;?>Delegation/delegation_task"><div class="col-md-4">
                                                  <div class="btn btn-success btn-xs">FORM</div>
                                              </div></a>
                                             <a href="<?php echo page_url;?>Delegation/delegation_dashboard"> <div class="col-md-8">
                                                  <div class="btn btn-danger btn-xs">DASHBOARD</div>
                                              </div></a>
                                          </div>
                               
                                
                            </div>
                              
                            
                        </div>
                            </div>
                        </div>
                         
                        
                        
                         <div class="card-box widget-user" style="min-height:268px">
                            <div class="text-center">
                                <h4>HELP DESK</h4><hr>
                                <div class="row">
                                    <a href="<?php echo page_url;?>Form/data_report/18"><div class="col-md-4 text-center">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','18')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p style="font-size:11px">ACCOUNTS</p>
                                    </div></a>
                                    <a href="<?php echo page_url;?>Form/data_report/17"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','17')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p title="total pending task" style="font-size:11px">SERVICE</p>
                                    </div></a>
                                    <a href="<?php echo page_url;?>Form/data_report/2"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','2')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p  style="font-size:11px">IT</p>
                                    </div></a>
                                </div>
                                
                                 <div class="row">
                                    <a href="<?php echo page_url;?>Form/data_report/51"><div class="col-md-4 text-center">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','51')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p style="font-size:11px">PRODUCTION</p>
                                    </div></a>
                                    <a href="<?php echo page_url;?>Form/data_report/52"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','52')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p title="total pending task" style="font-size:11px">DISPATCH</p>
                                    </div></a>
                                    <a href="<?php echo page_url;?>Form/data_report/1"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','1')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p  style="font-size:11px; text-">E.A</p>
                                    </div></a>
                                    
                                    <a href="<?php echo page_url;?>Form/data_report/53"><div class="col-md-4">
                                        <div class="numberCircle"><?php 
                                        $query = $this->db->select('id')->from('dynamic_form_data')->where('form_id','53')->where('work_status','0')->get();
                                        echo count($query->result());
                                        ?> </div>
                                        <p  style="font-size:11px; text-">PURCHASE</p>
                                    </div></a>
                                    
                                   
                                    
                                   
                                </div>
                                
                            </div>
                        </div>
                         
                 
                     </div><!-- end col -->

                      <div class="col-lg-3 col-md-6">
                       
                      <div class="card-box" style="min-height:270px">
                           

                            <h4 class="text-center"> PLANNED VS ACTUAL</h4><hr>

                            <ul class="list-group m-b-0 user-list" style="height: 198px; overflow-y:auto">
                                <?php 
                                $f=array('1,4,5');
                                $as=array();
                                    $query = $this->db->select('a.production_flow_id,a.fms_flow,a.flow_id as recordid')->from('fms_flow a')->where('a.status','1')->where_in('a.production_flow_id',$f,false)->get();
	                                	$res = $query->result();
	                                	foreach($res as $row){
	                                	 
	                                	$pendcounts= $CI->Fms_model->dashboardplannedtoactual($row->production_flow_id,$row->recordid);
	                                	
	                                	$as+=[$row->recordid=>$pendcounts];
	                                	}
	                                	
	                                		arsort($as);
	                                
	                       
                              foreach($as as $x=>$x_value)
                                 {
                                     $resytsyyud=$this->db->select('fms_flow')->from('fms_flow')->where('flow_id',$x)->get();
                                     foreach($resytsyyud->result() as $resytsyyud1);
                                    ?>
                                <li class="list-group-item">
                                    <a href="<?php echo page_url;?>FMS/pendingorder/<?php echo $x ;?>/0" class="user-list-item" target="_blank">
                                        <div class="avatar text-center">
                                       <?php echo $x_value;?>
                                        </div>
                                        <div class="user-desc">
                                           <span><?php echo $resytsyyud1->fms_flow;?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php
                                }
                                ?>
                                
                                
                                <?php
                            $query = $this->db->select('a.id as recordid, a.dashboard_title')->from('dynamic_forms a')->where('a.form_running_status','0')->order_by('a.dashboard_title','asc')->get();
                                $res = $query->result();
                                $i=1;
                                foreach($res as $row)
                                { 	
                                    
                                $todaysdate = date('Y-m-d');
                                $time = "23:59:59";
                                $finaldate = $todaysdate." ".$time;
                                $starttime= date('Y-m-d')." 00:00:00";
                                $q = $this->db->select('id, planned_date')->from('dynamic_form_data')->where('form_id',$row->recordid)->where('work_status','0')->where('planned_date<=',$finaldate)->get();
                                $totalpending = count($q->result());
                                    
                                
                                ?>
                                
                                <li class="list-group-item">
                                    <a href="<?php echo page_url;?>Form/data_report/<?php echo $row->recordid ;?>/0" class="user-list-item" target="_blank">
                                        <div class="avatar text-center">
                                       <?php echo $totalpending;?>
                                        </div>
                                        <div class="user-desc">
                                           <span><?php echo $row->dashboard_title;?></span>
                                        </div>
                                    </a>
                                </li>
                                
                                
                                
                                <?php
                                }
                                ?>

                            </ul>
                        </div>
                        <div class="card-box widget-user" style="min-height:259px">
                           <div class="text-center">
                                <h4>STORE</h4><hr>
                                 <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="numberCirclestore">30</div>
                                        <p style="font-size:11px; text-align:center">BOM VS PR</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="numberCirclestore">30</div>
                                        <p style="font-size:11px; text-align:center">IMS VS PR</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="numberCirclestore">30</div>
                                        <p style="font-size:11px; text-align:center">INDENT VS PR</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="numberCirclestore">30</div>
                                        <p style="font-size:11px; text-align:center">RGP VS RETURN</p>
                                    </div>
                                    
                                     <div class="col-md-4">
                                        <div class="numberCirclestore">30</div>
                                        <p style="font-size:11px; text-align:center">REJECTION VS ACTION</p>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                        
                       
                        
                        
                        
                      
                     </div><!-- end col -->

                      <div class="col-lg-3 col-md-6">
                       <div class="card-box widget-user" style="min-height:184px">
                            <div class="text-center">
                                <h4>LEAVES </h4><hr>
                               
                                <div class="row">
                                    
                                   <a href="<?php echo page_url;?>Hr/overallleave"> <div class="col-md-6">
                                        <div class="numberCircleleave" style="margin-left: 35px;">
                                            <?php $query = $this->db->select('id')->from('leave_application')->where('approval_status','0')->get();
                                            $result = count($query->result());
                                            echo $result;?>
                                        </div>
                                       <p>LEAVE APPLICATION</p> 
                                    </div></a>
                                    
                                     <a href="<?php echo page_url;?>Hr/onleavetoday"><div class="col-md-6">
                                        <div class="numberCircleleave" style="margin-left: 35px;"><?php $query = $this->db->select('id')->from('leave_application')->where('from_loc',date('Y-m-d'))->get();
                                            $result = count($query->result());
                                            echo $result;?></div>
                                        <p>ON LEAVE TODAY</p>
                                        
                                    </div></a>
                                    
                                </div>
                               
                                
                            </div>
                        </div>
                     
                       <div class="card-box widget-user" style="min-height:170px">
                           <div class="text-center">
                                <h4>ORDERS</h4><hr>
                                 <div class="row">
                                     <?php
                                      $lcountall=$CI->Fms_model->nondispatchedorders();
                                     ?>
                                    <a href="https://prestomitr.com/FMS/nondispatchedlist"  target="_blank"><div class="col-md-4 text-center">
                                        <div class="numberCirclegreen"><?php echo $lcountall;?></div>
                                        <p>TOTAL ORDERS</p>
                                    </div></a>
                                    <?php
                                    $lcount=$CI->Fms_model->lot_ordercount();
                                    ?>
                                    <a href="<?php echo page_url;?>Reporting/lotorderlist" target="_blank"><div class="col-md-4">
                                        <div class="numberCirclegreen"><?php echo $lcount; ?></div>
                                        <p title="total pending task">LOT ORDERS</p>
                                    </div></a>
                                    <?php
                                    $pendicount=$CI->Fms_model->pendingordercount();
                                    ?>
                                    <a href="<?php echo page_url;?>Reporting/pendingorders"><div class="col-md-4">
                                        <div class="numberCirclegreen"><?php echo $pendicount;?></div>
                                        <p >PENDING</p>
                                    </div></a>
                                </div>
                                
                                
                            </div>
                        </div>
                        
                        <div class="card-box widget-user">
                           <div class="text-center">
                                <h4>PRODUCTION</h4><hr>
                                 <?php
                                    $readyordercount=$CI->Fms_model->readyordercount();
                                 ?>
                                 <a href="<?php echo page_url;?>Reporting/completedorders" target="_blank"><div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="numberCircleproduction"><?php echo $readyordercount;?></div>
                                        <p>READY ORDERS</p>
                                    </div></a>
                                    <?php
                                    $unplannedordercount=$CI->Fms_model->unplannedordercount();
                                    ?>
                                     <a href="<?php echo page_url;?>FMS/unplannedorder" target="_blank"><div class="col-md-4">
                                        <div class="numberCircleproduction"><?php echo $unplannedordercount;?></div>
                                        <p title="total pending task">UNPLANNED ORDERS</p>
                                    </div></a>
                                    
                                    <?php
                                    $dispatchcount=$CI->Fms_model->dispatchfortommorowcount();
                                    ?>
                                     <a href="<?php echo page_url;?>Reporting/dispatchfortommorow/0" target="_blank"><div class="col-md-4">
                                         <div class="numberCircleproduction"><?php echo $dispatchcount;?></div>
                                        <p title="total pending task">DISPATCH TOMORROW</p>
                                    </div></a>
                                </div>
                                
                                
                            </div>
                        </div>
                     </div><!-- end col -->

                    <div class="col-lg-3 col-md-6">
                        <div class="card-box" style="min-height: 179px;">
                           
                            <h4 class="text-center"><i class="zmdi zmdi-notifications-none m-r-5" style="color:red;"></i> NOTIFICATION PANEL</h4> <hr>

                            <ul class="list-group m-b-0 user-list" style="height: 116px; overflow-y:auto">
                                <?php 
                                $i=1;
                                $q = $this->db->select('event_date, news_events, image')->from('presto_news_events')->order_by('event_date','desc')->get();
                                $count = count($q->result());
                                foreach($q->result() as $row){
                                ?>
                                     <li class="list-group-item">
                                    <a href="#" class="user-list-item">
                                        <div class="avatar text-center">
                                           	<?php 	if($row->image){?>
		<img src="<?php echo eventimgpath;?><?php echo $row->image;?>" width="100px">
		    <?php 
		    }else{?>
		    <i class="zmdi zmdi-circle text-primary"></i>
		    <?php }?>
                                        </div>
                                        <div class="user-desc">
                                           <span class="desc"><?php echo $row->news_events;?></span>
                                        </div>
                                    </a>
                                </li>
<?php }?>

<?php 
				  $query = $this->db->select('first_name, last_name,profile_image')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where("MONTH(date_of_birth) = MONTH(NOW()) AND DAY(date_of_birth) = DAY(NOW())")->get();
				  if($query->num_rows()>0){
				      foreach($query->result() as $birthday){
				  ?>
                                <li class="list-group-item">
                                    <a href="#" class="user-list-item">
                                        <div class="avatar text-center">
                                            <i class="zmdi zmdi-circle text-success"></i>
                                        </div>
                                        <div class="user-desc">
                                            
                                            <span class="desc"><img src="<?php echo user_profile;?><?php echo $birthday->profile_image;?>" alt="user-img" class="img-circle user-img" width="100px">Happy Birthday <?php echo $birthday->first_name." ".$birthday->last_name;?></span>
                                        </div>
                                    </a>
                                </li>
<?php }}?>
                             
                              

                              
                            </ul>
                        </div>
                        
                       <?php
                        $restyui=$CI->Fms_model->totalstockvalue();
                        $rt=count($restyui);
                        $p=1;
                        foreach($restyui as $totval)
                        {
                            if($p==$rt)
                            {
                        $hsou1=explode('-',$totval);
                            }
                        
                        $p++;
                            
                        }
                        
                        
                       
                                
                        ?>
                        <div class="card-box widget-user" style="min-height: 177px;">
                            <div class="text-center">
                                <h4>STOCK VALUE - <?php echo round($hsou1[1]);?></h4><hr>
                                <ul class="list-group m-b-0 user-list" style="height: 116px; overflow-y:auto">
                                <?php
                                $r=1;
                                 foreach($restyui as $restyui1)
                                {
                                    if($r<>$rt)
                                    {
                                    $hsou=explode('-',$restyui1);
                                    $val=round($hsou[0]);
                                    $label=$hsou[1];
                                    }else
                                    {
                                     $val='';
                                    $label='';
                                    }
                                ?>
                                <li class="list-group-item">
                                    <a href="#" class="user-list-item" style="text-decoration:none;color:none;">
                                        <div class="avatar text-center">
                                          <?php echo $label ;?>
                                        </div>
                                        <div class="user-desc">
                                           <span><?php echo $val ;?></span>
                                        </div>
                                    </a>
                                </li>
                                <?php
                              $r++;  }
                                ?>
                            

                            </ul>
                                
                            </div>
                        </div>
                        <?php
                        $indvspr=$CI->Fms_model->indentvspo();
                        $povspr=$CI->Fms_model->povsprcount();
                        $povsdeli=$CI->Fms_model->povsdelivery();
                        ?>
                         <div class="card-box widget-user">
                           <div class="text-center">
                                <h4>PURCHASE</h4><hr>
                                 <div class="row">
                                      <div class="col-md-4 text-center">
                                        <div class="numberCirclepurchase"><?php echo $indvspr;?></div>
                                        <p>INDENT VS PR</p>
                                    </div>
                                    
                                      <div class="col-md-4">
                                        <div class="numberCirclepurchase"><?php echo $povspr;?></div>
                                        <p >PR VS PO</p>
                                    </div>
                                   
                                    <div class="col-md-4">
                                        <div class="numberCirclepurchase"><?php echo $povsdeli;?></div>
                                        <p title="total pending task">PO VS DELIVERY</p>
                                    </div>
                                   
                                </div>
                                
                                
                            </div>
                        </div>
                        
                         
                    </div>
                 </div>
                 
                 
                 <?php }?>
                 
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

            </div>
            <!-- end container -->




        </div>
        </div> <!-- contentarea end -->



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
    
    </body>
</html>
<?php 
$total = 0;
$businesslocation =$this->session->userdata['logged_in']['business_location'];
$department_id =$this->session->userdata['logged_in']['department_id'];
if($businesslocation==2){
$user_id = $this->session->userdata['logged_in']['user_id'];
$st = date('Y-m-d',strtotime('-1 year +1 day'));
$et = date('Y-m-d');
$dfid = "ALL";
$assigned_module=array();
$assigned_module[]=0;

$CI =& get_instance();
$CI->load->model('MIS_model','mis_model');
$per6=0;
if($department_id==9)
{
$totalassigned = $CI->mis_model->getTotalFollowupDone($user_id);
$totalnotdonetask =  $CI->mis_model->getMissedFollowups($user_id,$dfid);
$done=$totalassigned-$totalnotdonetask;
$per6=$CI->mis_model->get_percentage($totalnotdonetask,$totalassigned);
if($totalassigned>0)
    {
        $assigned_module[]=1;
    }

}


$totalassigned = $CI->mis_model->allassignedtask($user_id,$st,$et,$dfid);
$totaldonetask =  $CI->mis_model->totalassignedworkdone($user_id,$st,$et,$dfid);
$diff = $totalassigned-$totaldonetask;
$per2=$CI->mis_model->get_percentage($diff,$totalassigned);
if($totalassigned>0)
    {
        $assigned_module[]=1;
    }

$totaldonecount = $CI->mis_model->totaldonetaskwithdate($user_id, $st, $et,$dfid);
$totaldoneontime = $CI->mis_model->totaldonetaskwithdateontime($user_id, $st, $et,$dfid);
$diff = $totaldonecount-$totaldoneontime;
$per3=$CI->mis_model->get_percentage($diff,$totaldonecount);
if($totaldonecount>0)
    {
        $assigned_module[]=1;
    }

/** help ticket **/
$totalassigned = $CI->mis_model->allassigneTickets($user_id,$dfid,$st,$et);
$totaldonetask =  $CI->mis_model->allassigneTicketsDone($user_id,$dfid,$st,$et);
$diff = $totalassigned-$totaldonetask;
$per4=$CI->mis_model->get_percentage($diff,$totalassigned);
if($totalassigned>0)
    {
        $assigned_module[]=1;
    }

/** CREATED HELP TICKET **/
$totalassigned = $CI->mis_model->allCreatedTickets($user_id,$dfid,$st,$et);
 $totaldonetask =  $CI->mis_model->allcreatedTicketsDone($user_id,$dfid,$st,$et);
 $diff = $totalassigned-$totaldonetask;
$per5=$CI->mis_model->get_percentage($diff,$totalassigned);
if($totalassigned>0)
    {
        $assigned_module[]=1;
    }



/** DELEGATION **/

$all_task=$CI->mis_model->all_task_delegated($st,$et,$user_id);
if(count($all_task)>0)
{
$all=count($all_task);
}else{
$all=0;
}
$all_task_done=$CI->mis_model->all_task_delegated_done($st,$et,$user_id);
if(count($all_task_done)>0)
{
$done=count($all_task_done);
}else{
$done=0;
}
$diff=$all-$done;
$per7=$CI->mis_model->get_percentage($diff,$all);

if($all>0)
    {
        $assigned_module[]=1;
    }


$all_task_done_this_week=$CI->mis_model->all_task_delegated_Done_this_week($st,$et,$user_id);
if(count($all_task_done_this_week)>0)
{
$all1=count($all_task_done_this_week);
}else{
$all1=0;
}

$all_task_delayed=$CI->mis_model->all_task_delegated_not_delayed($st,$et,$user_id);
if(count($all_task_delayed)>0)
{
$not_delayed=count($all_task_delayed);
}else{
$not_delayed=0;
}
$diff1=$all1-$not_delayed;
$per8=$CI->mis_model->get_percentage($diff1,$all1);
if($per8>0)
{
$sign1="-";
}else{
$sign1="";
}



    if($all1>0)
    {
        $assigned_module[]=1;
    }


/** mom **/
$getMomAssigned = $CI->mis_model->getTotalMomAssigned($st,$et,$user_id);
$getMomCompleted = $CI->mis_model->getTotalMomAssigned_completed($st,$et,$user_id);
$diff=$getMomAssigned-$getMomCompleted;
$per9=$CI->mis_model->get_percentage($diff,$getMomAssigned);

if($getMomAssigned>0)
    {
        $assigned_module[]=1;
    }


/** SECIND **/

$mom_done_this_week = $CI->mis_model->getTotalMomAssigned_Done_This_Week($st,$et,$user_id);
$mom_done_not_delayed_this_week = $CI->mis_model->getTotalMomAssigned_Done_n_Not_Delayed_This_Week($st,$et,$user_id);

$diff=$mom_done_this_week-$mom_done_not_delayed_this_week;
$per10=$CI->mis_model->get_percentage($diff,$mom_done_this_week);
if($mom_done_this_week>0)
    {
        $assigned_module[]=1;
    }
    
/** end **/


if(array_sum($assigned_module)>0)
    {
    $calculated_total=round(($per2+$per3+$per4+$per5+$per6+$per7+$per8+$per9+$per10)/array_sum($assigned_module));
    $total=$CI->mis_model->get_appraisal_issue_rate($user_id,$calculated_total);
    }else
    {
        $total=0;
    }

}else
{
	$total = 0;
}

$q = $this->db->select('profile_image')->from('system_users')->where('user_id',$user_id)->get();
if($q->num_rows()>0){
    foreach($q->result() as $row);
    $profilepic = $row->profile_image;
}else{
    $profilepic="";
}


?>
   
   <?php 
$CI = &get_instance();
$CI->load->model('Fms_model');
$VI = &get_instance();
$VI->load->model('Fms_mismodel');
$DI = &get_instance();
$DI->load->model('Lms_model');
$currentweekdates = $VI->Fms_mismodel->currentweekdates();
//$currentweekdates=$VI->Fms_mismodel->getcurrentweekalldates();
$ststst = base64_encode($currentweekdates[0]);
$endddd = base64_encode($currentweekdates[1]);
$user_id = $this->session->userdata['logged_in']['user_id'];
$profile_image = $this->session->userdata['logged_in']['profile_image'];
$user_role = $this->session->userdata['logged_in']['role'];
$master_write_access_enabled = true;
if ($this->db->field_exists('master_write_access', 'user_role')) {
    $master_access_row = $this->db->select('master_write_access')
        ->from('user_role')
        ->where('user_role_id', (int) $user_role)
        ->limit(1)
        ->get()
        ->row();
    if (!empty($master_access_row) && isset($master_access_row->master_write_access)) {
        $master_write_access_enabled = ((string) $master_access_row->master_write_access === '1');
    }
}
$first_name = $this->session->userdata['logged_in']['user_name'];
$last_name = $this->session->userdata['logged_in']['last_name'];
$department_id = $this->session->userdata['logged_in']['department_id'];
$q = $this->db->select('profile_image')->from('system_users')->where('user_id', $user_id)->get();
if ($q->num_rows() > 0) {
	foreach ($q->result() as $row);
	$profilepic = $row->profile_image;
} else {
	$profilepic = "";
}

$st = date('Y-m-d',strtotime('-30 days'));
					$et = date('Y-m-d');
                		// if($total<=20)
                		// {
                		// 	$icon='😀';
                		// }else
                		// {
                		// 	$icon='😟';
                		// }
?>
   <style type="text/css">
            .list-notificacao {
                min-width: 400px;
                background: #ffffff;
            }

            .list-notificacao li {
                border-bottom: 1px #d8d8d8 solid;
                text-align: justify;
                padding: 5px 10px 5px 10px;
                cursor: pointer;
                font-size: 12px;
            }

            .list-notificacao li:hover {
                background: #f1eeee;
            }

            .list-notificacao li:hover .exclusaoNotificacao {
                /*display: block;*/
            }

            .list-notificacao li p {
                color: black;
                /* width: 305px;*/
            }

            .list-notificacao li .exclusaoNotificacao {
                width: 25px;
                min-height: 40px;
                position: absolute;
                right: 0;
                /*    display: none;*/
            }

            .list-notificacao .media img {
                width: 40px;
                height: 40px;
                float: left;
                margin-right: 10px;
            }

            .badgeAlert {
                display: inline-block;
                min-width: 10px;
                padding: 3px 7px;
                font-size: 9px;
                font-weight: 700;
                color: #fff;
                line-height: 1;
                vertical-align: baseline;
                white-space: nowrap;
                text-align: center;
                background-color: #d9534f;
                border-radius: 10px;
                position: absolute;
                margin-top: -30px;
                margin-left: 4px;
            }

            .quotecss {



                color: #fff;



                text-align: center;



                padding-top: 26px;



                font-size: 13px;



                font-weight: bold;



            }

            .badge1 {
                position: relative;
            }

            .badge1[data-badge]:after {
                content: attr(data-badge);
                position: absolute;
                top: -10px;
                right: -10px;
                font-size: 12px;
                font-weight: bold;
                background: black;
                color: white;
                width: 18px;
                height: 18px;
                text-align: center;
                border-radius: 50%;
                box-shadow: 0 0 1px #333;
            }

            .quotecss {
                color: #fff;
                text-align: center;
                padding-top: 26px;
                font-size: 13px;
                font-weight: bold;
            }

            #topnav .topbar-main {
                position: fixed;
                width: 85.5%;
                z-index: 50;
                height: 55px;
                /* border: 1px solid; */
                /* right: 0; */
                background-color: #fff;
                left: 220px;
                border-top: 1px solid #e5e5e5;
                border-bottom: 1px solid #e5e5e5;
            }

            @media (max-width:576px) {
                #topnav .topbar-main {
                    /* background-color: #2986CE; */
                    /* background-image: url('https://prestomitr.com/assets/images/pt.jpg'); */
                    background-color: whitesmoke;
                    position: absolute;
                    width: 100%;
                    z-index: 50;
                    height: 54px;
                    box-shadow: 1px 1px 5px lightgray;
                }

            }

            .usernamecss {
                margin-top: 17px;
                font-size: 14px;
                color: #000;
                font-weight: 500;
            }

            @media only screen and (max-width: 600px) {
                .usernamecss {
                    margin-top: 0px;
                }
            }

            .secondul {
                max-height: 450px;
                overflow-y: auto;
            }

            .secondulss {
                max-height: 200px;
                overflow-y: auto;
            }

            @media (max-width:576px) {
                #mobilemenuwrap ul li ul {
                    border: 1px solid #d3d3d366;
                    box-shadow: none;
                    height: 100%;
                    overflow-y: unset;
                }

                .dash {
                    border-bottom: 1px solid whitesmoke;
                }

                .logo a img {
                    width: 100px;
                    margin-left: 10px;
                }

                .dash i {
                    font-size: 20px !important;
                    text-align: center;
                }
            }
        </style>
 <div class="leftmenu" id="mobilemenu">
            <div class="logo"><a href="#"><img src="https://shubhampack.in/beta/assets/images/shubhampack.png"
                        alt="Logo"></a>
            </div>

            <div id="mobilemenuwrap">
                <ul class="">

                    <!-- <li>
                                <a data-toggle="collapse" href="#username" aria-controls="username" role="button"
                                    aria-expanded="false" class="right_arrow">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M12,12.5h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051V4h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-6.051V11.5H6.949c-.245-1.692-1.691-3-3.449-3-1.93,0-3.5,1.57-3.5,3.5s1.57,3.5,3.5,3.5c1.758,0,3.204-1.308,3.449-3h4.051v8.5h6.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051v-7.5Zm8.5-3c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Zm0-8.5c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.122-2.5-2.5,1.121-2.5,2.5-2.5ZM3.5,14.5c-1.379,0-2.5-1.121-2.5-2.5s1.121-2.5,2.5-2.5,2.5,1.122,2.5,2.5-1.121,2.5-2.5,2.5Zm17,3.5c1.379,0,2.5,1.121,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Z" />
                                        </svg>
                                    </div>
                                    <div style="display: flex; align-items: center;">
                                        Manglesh Upadhyay<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                            data-name="Layer 1" viewBox="0 0 24 24" class="collapse_arrow">
                                            <path
                                                d="m10.279,18.342l-.707-.707,5.281-5.281c.094-.095.146-.22.146-.354s-.052-.259-.146-.354l-5.281-5.281.707-.707,5.281,5.281c.283.283.439.66.439,1.061s-.156.777-.439,1.061l-5.281,5.281Z" />
                                        </svg></div>
        
                                </a>
                                <div class="collapse" id="username">
                                    <ul class="nav">
        
                                        <li><a href="https://shubhampack.in/beta1/index.php/Dashboard/user_report">User
                                                Management</a></li>
                                        <li><a href="https://shubhampack.in/beta1/index.php/Dashboard/task_master_dashboard">Task
                                                Management</a></li>
        
                                        <li><a href="https://shubhampack.in/beta1/index.php/Dashboard/Sales_master">Sales
                                                Management</a></li>
                                    </ul>
                                </div>
                            </li> -->

                    <li>



                        <a <?php if ($this->uri->segment(1) == 'Dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Dashboard">
                            <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="m12,1C5.383,1,0,6.383,0,13c0,3.912,1.948,7.616,5.213,9.909l.129.091h13.316l.129-.091c3.265-2.293,5.213-5.997,5.213-9.909,0-6.617-5.383-12-12-12Zm6.34,21H5.66c-2.878-2.084-4.66-5.519-4.66-9C1,6.935,5.935,2,12,2s11,4.935,11,11c0,3.481-1.782,6.916-4.66,9Zm-.371-14.262l-.707-.707-4.247,4.247c-.298-.176-.645-.278-1.015-.278-1.103,0-2,.897-2,2s.897,2,2,2,2-.897,2-2c0-.37-.102-.717-.278-1.015l4.247-4.247Zm-5.969,6.262c-.552,0-1-.448-1-1s.448-1,1-1,1,.448,1,1-.448,1-1,1Zm-4.95-5.95c-2.729,2.729-2.729,7.171,0,9.9l-.707.707c-3.118-3.119-3.118-8.195,0-11.314,2.502-2.502,6.26-2.992,9.26-1.481l-.752.752c-2.571-1.143-5.694-.67-7.801,1.435Zm10.607,10.607l-.707-.707c2.105-2.106,2.579-5.229,1.435-7.801l.752-.752c1.51,3,1.02,6.758-1.48,9.26Z" />
                                </svg></div>
                            <div>Dashboard</div>
                        </a>

                    </li>
	<?php
			if ($master_write_access_enabled) {
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '1')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {
					
			?>
                    <li>
                        <a data-toggle="collapse" href="#master" aria-controls="master" role="button"
                            aria-expanded="false" class="right_arrow">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12,12.5h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051V4h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-6.051V11.5H6.949c-.245-1.692-1.691-3-3.449-3-1.93,0-3.5,1.57-3.5,3.5s1.57,3.5,3.5,3.5c1.758,0,3.204-1.308,3.449-3h4.051v8.5h6.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051v-7.5Zm8.5-3c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Zm0-8.5c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.122-2.5-2.5,1.121-2.5,2.5-2.5ZM3.5,14.5c-1.379,0-2.5-1.121-2.5-2.5s1.121-2.5,2.5-2.5,2.5,1.122,2.5,2.5-1.121,2.5-2.5,2.5Zm17,3.5c1.379,0,2.5,1.121,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Z" />
                                </svg>
                            </div>
                            <div style="display: flex; align-items: center;">
                                Master<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24" class="collapse_arrow">
                                    <path
                                        d="m10.279,18.342l-.707-.707,5.281-5.281c.094-.095.146-.22.146-.354s-.052-.259-.146-.354l-5.281-5.281.707-.707,5.281,5.281c.283.283.439.66.439,1.061s-.156.777-.439,1.061l-5.281,5.281Z" />
                                </svg></div>

                        </a>
                        <div class="collapse" id="master">
                            <ul class="nav">
	<?php 
					$submoduleid = array('1', '2','3','4','5','6','24','26','47','48','59','60','61');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
                                <li><a href="<?php echo page_url; ?>Dashboard/user_report">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>
                                        <div>User
                                            Management</div>
                                    </a></li>
                                    	<?php }?>
							<?php 
					$submoduleid = array('7', '8','9','10','11','12');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
                                <li><a href="<?php echo page_url; ?>Dashboard/task_master_dashboard">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>
                                        <div>Task
                                            Management</div>
                                    </a></li>
<?php }?>

							<?php 
					$submoduleid = array('31', '32','33','34','35','67');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '1')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
								?>
                                <li><a href="<?php echo page_url; ?>Dashboard/Sales_master">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>

                                        <div>Sales Management</div>
                                    </a></li>
                                    <?php }?>
                                <!-- Add more items here -->
                            </ul>
                        </div>
                    </li>
<?php }}}?>
	<?php
				$module=$this->db->select('access')->from('module_access')->where('role_id',$user_id)->where('moduleid','15')->get();
				if($module->num_rows()>0)
				{
				foreach($module->result() as $moddata);
				if($moddata->access=='1')
				{
				?>
                    <li>

                        <a <?php if ($this->uri->segment(2) == 'opportunity_dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Dashboard/opportunity_dashboard">

                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M22,24H2V2.5C2,1.122,3.122,0,4.5,0h15c1.378,0,2.5,1.122,2.5,2.5V24Zm-19-1H21V2.5c0-.827-.673-1.5-1.5-1.5H4.5c-.827,0-1.5,.673-1.5,1.5V23ZM18,5h-7v1h7v-1Zm0,6h-7v1h7v-1Zm0,6h-7v1h7v-1ZM9,7h-3v-3h3v3Zm-2-1h1v-1h-1v1Zm2,7h-3v-3h3v3Zm-2-1h1v-1h-1v1Zm2,7h-3v-3h3v3Zm-2-1h1v-1h-1v1Z" />
                                </svg>
                            </div>
                            <div>
                                Opportunity & Quotation

                            </div>
                        </a>
                    </li>
	<?php } } ?>
	<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '3')->get();
			$module_three_access_enabled = false;
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				$module_three_access_enabled = isset($moddata->access) && $moddata->access == '1';
			}

			$mom_qry = false;
			if ($module_three_access_enabled) {
				$mom_submoduleid = array('18', '19','20','21','22','23');
				$mom_qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where_in('submoduleid', $mom_submoduleid)->where('submodule_access', '1')->get();
			}

			$has_df_create_permission = false;
			$has_df_dashboard_permission = false;
			if ($module_three_access_enabled) {
				$df_submoduleid = array('41','42');
				$df_qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where_in('submoduleid', $df_submoduleid)->where('submodule_access', '1')->get();
				if ($df_qry->num_rows() > 0) {
					foreach ($df_qry->result() as $df_permission_row) {
						if ((int)$df_permission_row->submoduleid === 41) {
							$has_df_create_permission = true;
						}
						if ((int)$df_permission_row->submoduleid === 42) {
							$has_df_dashboard_permission = true;
						}
					}
				}
			}

			$change_alert_count = 0;
			if ($this->db->table_exists('df_change_control_departments')) {
				$this->db->select('id')->from('df_change_control_departments');
				$this->db->group_start();
				$this->db->group_start()->where('department_head_id', $user_id)->where('status', 'PENDING_HEAD_ACTION')->group_end();
				$this->db->or_group_start()->where('assigned_user_id', $user_id)->where_in('status', array('ASSIGNED', 'IN_PROGRESS'))->group_end();
				$this->db->group_end();
				$change_alert_count = $this->db->get()->num_rows();
			}

			if ($mom_qry && $mom_qry->num_rows() > 0) {
			?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Mom') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Mom/momdashboard">
                                <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19.476,14.656l-.746-.749c.172-.607,.271-1.245,.271-1.907,0-.648-.096-1.274-.261-1.87l.756-.755c1.329-1.275,1.331-3.57,.002-4.846-1.276-1.329-3.573-1.329-4.847,.002l-.74,.741c-.608-.173-1.248-.272-1.911-.272-.649,0-1.276,.096-1.873,.262l-.746-.751c-1.267-1.33-3.596-1.354-4.846-.014-1.334,1.272-1.338,3.568-.011,4.847l.747,.749c-.354,1.193-.358,2.585-.009,3.78l-.751,.746c-1.333,1.271-1.339,3.567-.014,4.846,1.273,1.333,3.569,1.338,4.846,.01l.749-.746c1.193,.354,2.586,.358,3.781,.009l.746,.751c1.272,1.334,3.566,1.341,4.846,.014,1.334-1.272,1.338-3.568,.011-4.847ZM15.357,5.237c.902-.943,2.53-.943,3.433-.001,.941,.903,.941,2.529-.002,3.431l-.428,.427c-.695-1.514-1.911-2.736-3.421-3.438l.418-.419Zm-10.124,3.4c-.941-.905-.938-2.531,.007-3.432,.906-.94,2.531-.934,3.432,.01l.421,.424c-1.514,.695-2.735,1.912-3.438,3.421l-.422-.424Zm3.403,10.129c-.904,.94-2.531,.936-3.431-.007-.939-.906-.935-2.532,.01-3.431l.424-.421c.695,1.513,1.912,2.735,3.421,3.437l-.425,.423Zm-2.637-6.767c0-3.309,2.691-6,6-6s6,2.691,6,6-2.691,6-6,6-6-2.691-6-6Zm12.76,6.794c-.906,.94-2.531,.934-3.432-.01l-.421-.424c1.514-.695,2.735-1.912,3.438-3.421l.422,.424c.941,.905,.938,2.531-.007,3.432Zm2.99-14.294c1.24,0,2.25-1.009,2.25-2.25s-1.01-2.25-2.25-2.25-2.25,1.009-2.25,2.25,1.01,2.25,2.25,2.25Zm0-3.5c.689,0,1.25,.561,1.25,1.25s-.561,1.25-1.25,1.25-1.25-.561-1.25-1.25,.561-1.25,1.25-1.25ZM2.25,4.5c1.24,0,2.25-1.009,2.25-2.25S3.49,0,2.25,0,0,1.009,0,2.25s1.01,2.25,2.25,2.25Zm0-3.5c.689,0,1.25,.561,1.25,1.25s-.561,1.25-1.25,1.25-1.25-.561-1.25-1.25,.561-1.25,1.25-1.25ZM21.75,19.5c-1.24,0-2.25,1.009-2.25,2.25s1.01,2.25,2.25,2.25,2.25-1.009,2.25-2.25-1.01-2.25-2.25-2.25Zm0,3.5c-.689,0-1.25-.561-1.25-1.25s.561-1.25,1.25-1.25,1.25,.561,1.25,1.25-.561,1.25-1.25,1.25ZM2.25,19.5c-1.24,0-2.25,1.009-2.25,2.25s1.01,2.25,2.25,2.25,2.25-1.009,2.25-2.25-1.01-2.25-2.25-2.25Zm0,3.5c-.689,0-1.25-.561-1.25-1.25s.561-1.25,1.25-1.25,1.25,.561,1.25,1.25-.561,1.25-1.25,1.25Z" />
                                    </svg></div>

                                <div>MOM Module</div>
<?php 
    $iom_alert_count = 0;

    $q = $this->db->select('id')->from('dfwise_iom_points')->where('responsible_person',$user_id)->where('workstatus',0)->get();
    $iom_alert_count = $q->num_rows();

    if($iom_alert_count>0){
?>

<div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" style="fill: #e82646; position: absolute; right: 10px; margin-top: 4px; width: 16px;"><path d="M20.93,7.3c-.34-1.91-2-3.3-3.94-3.3h-.17c.11-.31,.18-.65,.18-1,0-1.65-1.35-3-3-3h-4c-1.65,0-3,1.35-3,3,0,.35,.07,.69,.18,1h-.17c-1.94,0-3.6,1.39-3.94,3.3L.81,19H23.19l-2.27-11.7ZM9,3c0-.55,.45-1,1-1h4c.55,0,1,.45,1,1s-.45,1-1,1h-4c-.55,0-1-.45-1-1Zm-.86,18h7.72c-.45,1.72-2,3-3.86,3s-3.41-1.28-3.86-3Z"/></svg></div>


<div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
    position: absolute; right: 9px; top: 8px;"><?php echo $iom_alert_count;?></div>

<?php }?>


                            </a>
                        </div>
                    </li>
	<?php }
			if ($has_df_create_permission || $has_df_dashboard_permission || $change_alert_count > 0) {
				$df_navigation_url = ($has_df_dashboard_permission || $change_alert_count > 0) ? page_url . 'Df_change_control' : page_url . 'Df_change_control/create';
			?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Df_change_control') { ?>class="active" <?php } ?> href="<?php echo $df_navigation_url; ?>">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="m15,2.338V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.536c-.163-.023-.33-.036-.5-.036h-2.5V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.5h-3V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.5h-2.5c-.17,0-.337.012-.5.036V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.838c-1.181.563-2,1.769-2,3.162v14c0,2.481,2.019,4.5,4.5,4.5h8c2.481,0,4.5-2.019,4.5-4.5V5.5c0-1.393-.819-2.599-2-3.162Zm1,17.162c0,1.93-1.57,3.5-3.5,3.5H4.5c-1.93,0-3.5-1.57-3.5-3.5V5.5c0-1.379,1.122-2.5,2.5-2.5h10c1.378,0,2.5,1.121,2.5,2.5v14Zm-3-13c0,.276-.224.5-.5.5H4.5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h8c.276,0,.5.224.5.5Zm0,4c0,.276-.224.5-.5.5H4.5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h8c.276,0,.5.224.5.5Zm-3,4c0,.276-.224.5-.5.5h-5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h5c.276,0,.5.224.5.5ZM21.5,0c-1.378,0-2.5,1.121-2.5,2.5v17.758c0,.922.374,1.823,1.025,2.475l1.121,1.121c.098.098.226.146.354.146s.256-.049.354-.146l1.121-1.121c.652-.651,1.025-1.553,1.025-2.475V2.5c0-1.379-1.122-2.5-2.5-2.5Zm1.5,20.258c0,.658-.267,1.303-.732,1.768l-.768.768-.768-.768c-.465-.465-.732-1.109-.732-1.768V2.5c0-.827.673-1.5,1.5-1.5s1.5.673,1.5,1.5v17.758Z" />
                                    </svg>
                                </div>
                                <div>DF ECN/ IOM Dashboard</div>
                                <?php if ($change_alert_count > 0) { ?>
                                    <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" style="fill: #e82646; position: absolute; right: 10px; margin-top: 4px; width: 16px;"><path d="M20.93,7.3c-.34-1.91-2-3.3-3.94-3.3h-.17c.11-.31,.18-.65,.18-1,0-1.65-1.35-3-3-3h-4c-1.65,0-3,1.35-3,3,0,.35,.07,.69,.18,1h-.17c-1.94,0-3.6,1.39-3.94,3.3L.81,19H23.19l-2.27-11.7ZM9,3c0-.55,.45-1,1-1h4c.55,0,1,.45,1,1s-.45,1-1,1h-4c-.55,0-1-.45-1-1Zm-.86,18h7.72c-.45,1.72-2,3-3.86,3s-3.41-1.28-3.86-3Z"/></svg></div>
                                    <div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
                                        position: absolute; right: 9px; top: 8px;"><?php echo $change_alert_count;?></div>
                                <?php } ?>
                            </a>
                        </div>
                    </li>


	<?php } ?>


     <?php
            $module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '3')->get();
            if ($module->num_rows() > 0) {
                foreach ($module->result() as $moddata);
                if ($moddata->access == '1') {

                    $submoduleid = array('71');
                    $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '3')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
                    if ($qry->num_rows() > 0) {
            ?>

                     <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'DF_revision') { ?>class="active" <?php } ?> href="<?php echo page_url;?>DF_revision/revision_dashboard">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="m15,2.338V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.536c-.163-.023-.33-.036-.5-.036h-2.5V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.5h-3V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.5h-2.5c-.17,0-.337.012-.5.036V.5c0-.276-.224-.5-.5-.5s-.5.224-.5.5v1.838c-1.181.563-2,1.769-2,3.162v14c0,2.481,2.019,4.5,4.5,4.5h8c2.481,0,4.5-2.019,4.5-4.5V5.5c0-1.393-.819-2.599-2-3.162Zm1,17.162c0,1.93-1.57,3.5-3.5,3.5H4.5c-1.93,0-3.5-1.57-3.5-3.5V5.5c0-1.379,1.122-2.5,2.5-2.5h10c1.378,0,2.5,1.121,2.5,2.5v14Zm-3-13c0,.276-.224.5-.5.5H4.5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h8c.276,0,.5.224.5.5Zm0,4c0,.276-.224.5-.5.5H4.5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h8c.276,0,.5.224.5.5Zm-3,4c0,.276-.224.5-.5.5h-5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h5c.276,0,.5.224.5.5ZM21.5,0c-1.378,0-2.5,1.121-2.5,2.5v17.758c0,.922.374,1.823,1.025,2.475l1.121,1.121c.098.098.226.146.354.146s.256-.049.354-.146l1.121-1.121c.652-.651,1.025-1.553,1.025-2.475V2.5c0-1.379-1.122-2.5-2.5-2.5Zm1.5,20.258c0,.658-.267,1.303-.732,1.768l-.768.768-.768-.768c-.465-.465-.732-1.109-.732-1.768V2.5c0-.827.673-1.5,1.5-1.5s1.5.673,1.5,1.5v17.758Z" />
                                    </svg>
                                </div>
                                <div>DF Revsion Dashboard</div>
                              
                            </a>
                        </div>
                    </li>
                <?php } } } ?>



    <?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '2')->get();
			if ($module->num_rows() > 0) {
				foreach ($module->result() as $moddata);
				if ($moddata->access == '1') {

					$submoduleid = array('13', '14','15','16','17','25');
					$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '2')->where_in('submoduleid', $submoduleid)->where('submodule_access', '1')->get();
					if ($qry->num_rows() > 0) {
			?>
                    <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(1) == 'Delegation') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Delegation/delegationdashboard">

                                <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="m8,5h-4v-1h4V0h1v4h4v1h-4v4h-1v-4Zm15.363,8.182l-9.639,10.818H2.5c-1.378,0-2.5-1.122-2.5-2.5v-7c0-1.378,1.122-2.5,2.5-2.5h10.858c1.208,0,2.229.814,2.542,1.922l3.732-4.102c.451-.496,1.069-.787,1.739-.818.67-.022,1.312.2,1.809.652,1.012.923,1.094,2.505.182,3.527Zm-.856-2.788c-.298-.272-.688-.407-1.088-.393-.403.019-.775.194-1.047.492l-4.495,4.94c-.301.945-1.124,1.678-2.146,1.824l-5.667.738-.129-.991,5.661-.737c.797-.114,1.404-.813,1.404-1.625,0-.905-.737-1.642-1.642-1.642H2.5c-.827,0-1.5.673-1.5,1.5v7c0,.827.673,1.5,1.5,1.5h10.776l9.34-10.483c.549-.615.5-1.567-.109-2.123Z" />
                                    </svg></div>

                                <div>Delegation</div>

                                <?php 
    $q = $this->db->select('id')->from('delegation_task')->where('delegate_to',$user_id)->where('task_status',0)->get();
    if($q->num_rows()>0){
?>
<div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" style="fill: #e82646; position: absolute; right: 10px; margin-top: 4px; width: 16px;"><path d="M20.93,7.3c-.34-1.91-2-3.3-3.94-3.3h-.17c.11-.31,.18-.65,.18-1,0-1.65-1.35-3-3-3h-4c-1.65,0-3,1.35-3,3,0,.35,.07,.69,.18,1h-.17c-1.94,0-3.6,1.39-3.94,3.3L.81,19H23.19l-2.27-11.7ZM9,3c0-.55,.45-1,1-1h4c.55,0,1,.45,1,1s-.45,1-1,1h-4c-.55,0-1-.45-1-1Zm-.86,18h7.72c-.45,1.72-2,3-3.86,3s-3.41-1.28-3.86-3Z"/></svg></div>
<div style="background: black; color: white;width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%;
    position: absolute; right: 9px; top: 8px;"><?php echo count($q->result());?></div>

<?php }?>

                            </a>
                        </div>
                    </li>
<?php }}}?>
<?php
$task_management_open_count = 0;
if ($this->db->table_exists('task_management_items')) {
    $task_management_open_count = (int) $this->db->select('id')
        ->from('task_management_items')
        ->where('assigned_to_user_id', $user_id)
        ->where('status !=', 'COMPLETED')
        ->count_all_results();
}
?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Task_management') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Task_management">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="M19.5,1H4.5C2.019,1,0,3.019,0,5.5v13c0,2.481,2.019,4.5,4.5,4.5h15c2.481,0,4.5-2.019,4.5-4.5V5.5c0-2.481-2.019-4.5-4.5-4.5ZM4.5,2h15c1.93,0,3.5,1.57,3.5,3.5v2.5H1v-2.5c0-1.93,1.57-3.5,3.5-3.5Zm15,20H4.5c-1.93,0-3.5-1.57-3.5-3.5V9H23v9.5c0,1.93-1.57,3.5-3.5,3.5ZM3,5c0-.552,.448-1,1-1s1,.448,1,1-.448,1-1,1-1-.448-1-1Zm3,0c0-.552,.448-1,1-1s1,.448,1,1-.448,1-1,1-1-.448-1-1Zm14,8.5c0,.276-.224,.5-.5,.5h-7c-.276,0-.5-.224-.5-.5s.224-.5,.5-.5h7c.276,0,.5,.224,.5,.5Zm0,5c0,.276-.224,.5-.5,.5h-7c-.276,0-.5-.224-.5-.5s.224-.5,.5-.5h7c.276,0,.5,.224,.5,.5Zm-9.145-2.625c.194,.196,.192,.513-.004,.707l-2.939,2.904c-.345,.34-.797,.511-1.25,.511s-.902-.169-1.246-.506l-1.35-1.324c-.196-.193-.2-.51-.006-.707,.192-.196,.509-.2,.707-.006l1.349,1.323c.301,.295,.792,.295,1.093-.002l2.939-2.904c.197-.194,.514-.191,.707,.004Zm0-5c.194,.197,.192,.513-.004,.707l-2.939,2.904c-.345,.34-.797,.511-1.25,.511s-.902-.169-1.246-.506l-1.35-1.324c-.196-.193-.199-.51-.006-.707,.192-.197,.509-.199,.707-.007l1.349,1.324c.301,.295,.792,.295,1.093-.002l2.939-2.904c.197-.195,.514-.192,.707,.004Z"/>
                                    </svg>
                                </div>
                                <div>Task Management</div>
                                <?php if ($task_management_open_count > 0) { ?>
                                    <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" style="fill: #e82646; position: absolute; right: 10px; margin-top: 4px; width: 16px;"><path d="M20.93,7.3c-.34-1.91-2-3.3-3.94-3.3h-.17c.11-.31,.18-.65,.18-1,0-1.65-1.35-3-3-3h-4c-1.65,0-3,1.35-3,3,0,.35,.07,.69,.18,1h-.17c-1.94,0-3.6,1.39-3.94,3.3L.81,19H23.19l-2.27-11.7ZM9,3c0-.55,.45-1,1-1h4c.55,0,1,.45,1,1s-.45,1-1,1h-4c-.55,0-1-.45-1-1Zm-.86,18h7.72c-.45,1.72-2,3-3.86,3s-3.41-1.28-3.86-3Z"/></svg></div>
                                    <div style="background: black; color: white; width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%; position: absolute; right: 9px; top: 8px;"><?php echo $task_management_open_count; ?></div>
                                <?php } ?>
                            </a>
                        </div>
                    </li>
	       <?php
			if($_SESSION['logged_in']['adminuser']==1 || $user_id==67)
			{
			?>
                    <li>

                        <div class="dash">

                            <a href="<?php echo page_url;?>MIS/index/ALL/<?php echo date('Y-m-d', strtotime('-1 year +1 day'));?>/<?php echo date('Y-m-d');?>/ALL/ALL">

                                <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="m21.5,0H7.5c-1.378,0-2.5,1.122-2.5,2.5v4.5H0v14c0,1.654,1.346,3,3,3h18c1.654,0,3-1.346,3-3V2.5c0-1.378-1.122-2.5-2.5-2.5ZM5,21c0,1.103-.897,2-2,2s-2-.897-2-2v-13h4v13Zm18,0c0,1.103-.897,2-2,2H5.234c.476-.531.766-1.232.766-2V2.5c0-.827.673-1.5,1.5-1.5h14c.827,0,1.5.673,1.5,1.5v18.5ZM15,7h6v1h-6v-1Zm-7,4h13v1h-13v-1Zm0,4h13v1h-13v-1Zm0,4h13v1h-13v-1ZM13,3h-5v5h5V3Zm-1,4h-3v-3h3v3Z" />
                                    </svg></div>

                                <div>MIS Report</div>

                            </a>
                        </div>
                    </li>
                    	<?php } ?>


                        
                       

		<?php
			$module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '5')->get();
			if ($module->num_rows() > 0) {?>

                    <li>
                        <a data-toggle="collapse" href="#finance" aria-controls="finance" role="button"
                            aria-expanded="false" class="right_arrow <?php if ($this->uri->segment(1) == 'Accounts') { ?> active <?php } ?>">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M24,6.5v2c0,.276-.224,.5-.5,.5s-.5-.224-.5-.5v-2c0-1.93-1.57-3.5-3.5-3.5H4.5c-1.93,0-3.5,1.57-3.5,3.5v11c0,1.93,1.57,3.5,3.5,3.5h4c.276,0,.5,.224,.5,.5s-.224,.5-.5,.5H4.5c-2.481,0-4.5-2.019-4.5-4.5V6.5C0,4.019,2.019,2,4.5,2h15c2.481,0,4.5,2.019,4.5,4.5Zm-10.5,1.5h7c.276,0,.5-.224,.5-.5s-.224-.5-.5-.5h-7c-.276,0-.5,.224-.5,.5s.224,.5,.5,.5Zm0,4h3c.276,0,.5-.224,.5-.5s-.224-.5-.5-.5h-3c-.276,0-.5,.224-.5,.5s.224,.5,.5,.5Zm9.768-.268c.473,.472,.732,1.1,.732,1.768s-.26,1.296-.732,1.768l-7.707,7.707c-.66,.661-1.539,1.025-2.475,1.025h-1.586c-.276,0-.5-.224-.5-.5v-1.586c0-.921,.374-1.823,1.025-2.475l7.707-7.707c.975-.975,2.561-.975,3.535,0Zm-.268,1.768c0-.401-.156-.777-.439-1.061-.584-.585-1.537-.585-2.121,0l-7.707,7.707c-.466,.465-.732,1.11-.732,1.768v1.086h1.086c.668,0,1.296-.26,1.768-.732l7.707-7.707c.283-.283,.439-.66,.439-1.061Zm-15.163-1.758l-2.49-.467c-.78-.146-1.347-.83-1.347-1.624,0-.911,.741-1.652,1.651-1.652h1.774c.623,0,1.204,.335,1.517,.875,.139,.239,.446,.32,.683,.183,.239-.138,.321-.444,.183-.683-.49-.848-1.402-1.375-2.382-1.375h-.426v-1.5c0-.276-.224-.5-.5-.5s-.5,.224-.5,.5v1.5h-.349c-1.462,0-2.651,1.189-2.651,2.652,0,1.275,.909,2.371,2.163,2.606l2.49,.467c.78,.146,1.347,.83,1.347,1.624,0,.911-.741,1.652-1.651,1.652h-1.774c-.623,0-1.204-.335-1.517-.875-.14-.241-.448-.321-.683-.183-.239,.138-.321,.444-.183,.683,.49,.848,1.402,1.375,2.382,1.375h.426v1.5c0,.276,.224,.5,.5,.5s.5-.224,.5-.5v-1.5h.349c1.462,0,2.651-1.189,2.651-2.652,0-1.275-.909-2.371-2.163-2.606Z" />
                                </svg>
                            </div>
                            <div style="display: flex; align-items: center;">
                                Finance<svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24" class="collapse_arrow">
                                    <path
                                        d="m10.279,18.342l-.707-.707,5.281-5.281c.094-.095.146-.22.146-.354s-.052-.259-.146-.354l-5.281-5.281.707-.707,5.281,5.281c.283.283.439.66.439,1.061s-.156.777-.439,1.061l-5.281,5.281Z" />
                                </svg></div>

                        </a>
                        <div class="collapse" id="finance">
                            	<?php 
					$threeMonthsAgo = date('Y-m-d', strtotime("-11 months"));
					$todaysdate = date('Y-m-d');
					?>
                            <ul class="nav">
                            <?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','28')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	
                                <li><a
                                        href="<?php echo page_url;?>Accounts/allrunningdf/<?php echo $threeMonthsAgo;?>/<?php echo $todaysdate;?>/ALL">

                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>

                                        <div>Finance Master</div>
                                    </a></li>
	<?php }?>

							<?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','29')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	

                                <li><a
                                        href="<?php echo page_url;?>Accounts/paymentdashboard/<?php echo $threeMonthsAgo;?>/<?php echo $todaysdate;?>/ALL">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>
                                        <div>Payment
                                            Collection</div>
                                    </a></li>
<?php }?>

							<?php 

							$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','30')->where('submodule_access','1')->get();

							if($qry->num_rows()>0){

							?>	

                                <li><a
                                        href="<?php echo page_url;?>Accounts/marketingpaymentdashboard/<?php echo base64_encode($user_id);?>">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>
                                        <div>Marketing
                                            Payment</div>
                                    </a></li>
                                    	<?php }?>
                                <!-- Add more items here -->

                               
                                <?php 

                            $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','44')->where('submodule_access','1')->get();

                            if($qry->num_rows()>0){

                            ?>  

                                <li><a
                                        href="<?php echo page_url;?>Form/payment_list">
                                        <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M23.55,13.38l-6.18-6.72-.74,.68,6.12,6.66H2.5c-.83,0-1.5-.67-1.5-1.5V2H0V12.5c0,1.38,1.12,2.5,2.5,2.5H22.76l-6.12,6.66,.74,.68,6.16-6.71c.62-.62,.62-1.64,.01-2.25Z" />
                                            </svg></div>
                                        <div>Payment Dashboard</div>
                                    </a></li>
                                        <?php }?>
                            </ul>
                        </div>
                    </li>

                <?php }?>




                <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(2) == 'paymentform' || $this->uri->segment(2)=='your_payment_list_dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Form/paymentform">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
    <path d="M22,4H2C0.897,4,0,4.897,0,6v12c0,1.103,0.897,2,2,2h20c1.103,0,2-0.897,2-2V6C24,4.897,23.103,4,22,4ZM2,6h20v4H2V6Zm0,12V11h20v7H2ZM5,14h6v1H5v-1Zm9,0h5v1h-5v-1Z"/>
</svg>

                                </div>

                                <div>Payment Request Form</div>


                            </a>
                        </div>
                    </li>
 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','49')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
                     <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(2) == 'dfmeetingnotification') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Form/dfmeetingnotification">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
    <path d="M22,4H2C0.897,4,0,4.897,0,6v12c0,1.103,0.897,2,2,2h20c1.103,0,2-0.897,2-2V6C24,4.897,23.103,4,22,4ZM2,6h20v4H2V6Zm0,12V11h20v7H2ZM5,14h6v1H5v-1Zm9,0h5v1h-5v-1Z"/>
</svg>

                                </div>

                                <div>DF Meeting Notification</div>


                            </a>
                        </div>
                    </li>

                <?php }?>

<?php
/* ===== CHAT MODULE — sidebar link =====================================
   Chat is company-wide: chat_can() lets every logged-in user open the
   messenger, while the privileged actions (create group, manage members,
   pin, link records) stay gated by the CHAT module in
   Master > User management.

   Hidden entirely until CHAT_MODULE_VISIBLE is TRUE in
   application/config/constants.php — see chat_nav_visible(). */
$this->load->helper('chat_access');
if (chat_nav_visible($this)):
?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) === 'Chat') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Chat">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM7 9h10v2H7V9zm7 5H7v-2h7v2zm3-6H7V6h10v2z"/></svg>
                                </div>
                                <div style="display:flex;align-items:center;">
                                    Chat
                                    <span id="ccChatMenuBadge" style="display:none;margin-left:8px;background:#dc2626;color:#fff;border-radius:9px;font-size:10px;font-weight:700;padding:1px 6px;">0</span>
                                </div>
                            </a>
                        </div>
                    </li>
                    <?php if (chat_can_manage_df_groups($this)): ?>
                    <!-- DF groups are marketing's and administrators' to
                         manage — see chat_can_manage_df_groups(). Everyone
                         else still takes part in a DF group once added; they
                         simply do not create them, so this entry is hidden
                         rather than shown-and-refused.

                         The page itself backfills DFs released before groups
                         became automatic. -->
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(2) === 'df_groups' || $this->uri->segment(2) === 'df-groups') { ?>class="active" <?php } ?> href="<?php echo page_url;?>chat/df-groups">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 3-1.34 3-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                                </div>
                                <div>DF Chat Groups</div>
                            </a>
                        </div>
                    </li>
                    <?php endif; ?>
<?php endif; ?>
<!-- ===== END CHAT MODULE — sidebar link ===== -->

<!-- ===== APP LOG — sidebar link =====
     Who has the mobile app, which build they are on, and who is actually
     opening it. Gated to the same roles App_log's own constructor allows
     (12 and 41); that controller re-checks on every request, so hiding the
     link here is presentation only.

     Top-level rather than inside Master on purpose: App_log does not go
     through module_access / module_capablity at all, so nesting it under
     Master would hide it from a role that is allowed the page but has no
     Master access. -->
<?php if (in_array((int) $user_role, array(12, 41), true)): ?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) === 'App_log') { ?>class="active" <?php } ?> href="<?php echo page_url;?>App_log">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>
                                </div>
                                <div>App Log</div>
                            </a>
                        </div>
                    </li>
<?php endif; ?>
<!-- ===== END APP LOG — sidebar link ===== -->

<?php
$df_weekly_meeting_points_count = 0;
$df_weekly_meeting_points_show_all = false;
if (!empty($_SESSION['logged_in']['adminuser']) && (int) $_SESSION['logged_in']['adminuser'] === 1) {
    $df_weekly_meeting_points_show_all = true;
}
if (in_array((int) $user_id, array(61, 161, 189, 209), true)) {
    $df_weekly_meeting_points_show_all = true;
}
if ($this->db->table_exists('dfmom_points') && $this->db->field_exists('responsible_person', 'dfmom_points') && $this->db->field_exists('workstatus', 'dfmom_points')) {
    $this->db->from('dfmom_points');
    $this->db->where('responsible_person >', 0);
    $this->db->where('workstatus !=', 1);
    if (!$df_weekly_meeting_points_show_all) {
        $this->db->where('responsible_person', $user_id);
    }
    $df_weekly_meeting_points_count = (int) $this->db->count_all_results();
}
?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Task' && $this->uri->segment(2) == 'dfweeklymeetingpoints') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>Task/dfweeklymeetingpoints">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="M22,4H2C0.897,4,0,4.897,0,6v12c0,1.103,0.897,2,2,2h20c1.103,0,2-0.897,2-2V6C24,4.897,23.103,4,22,4ZM2,6h20v4H2V6Zm0,12V11h20v7H2ZM5,14h6v1H5v-1Zm9,0h5v1h-5v-1Z"/>
                                    </svg>
                                </div>
                                <div>DF Weekly Meeting Points</div>
                                <?php if ($df_weekly_meeting_points_count > 0) { ?>
                                    <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" style="fill: #e82646; position: absolute; right: 10px; margin-top: 4px; width: 16px;"><path d="M20.93,7.3c-.34-1.91-2-3.3-3.94-3.3h-.17c.11-.31,.18-.65,.18-1,0-1.65-1.35-3-3-3h-4c-1.65,0-3,1.35-3,3,0,.35,.07,.69,.18,1h-.17c-1.94,0-3.6,1.39-3.94,3.3L.81,19H23.19l-2.27-11.7ZM9,3c0-.55,.45-1,1-1h4c.55,0,1,.45,1,1s-.45,1-1,1h-4c-.55,0-1-.45-1-1Zm-.86,18h7.72c-.45,1.72-2,3-3.86,3s-3.41-1.28-3.86-3Z"/></svg></div>
                                    <div style="background: black; color: white; width: 16px; height: 16px; font-size: 9px; text-align: center; line-height: 16px; border-radius: 50%; position: absolute; right: 9px; top: 8px;"><?php echo $df_weekly_meeting_points_count; ?></div>
                                <?php } ?>
                            </a>
                        </div>
                    </li>

                    <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','16')->where('submoduleid','45')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                    <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(2) == 'basicmachine') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Task/basicmachine">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="m21.5,0H7.5c-1.378,0-2.5,1.122-2.5,2.5v4.5H0v14c0,1.654,1.346,3,3,3h18c1.654,0,3-1.346,3-3V2.5c0-1.378-1.122-2.5-2.5-2.5ZM5,21c0,1.103-.897,2-2,2s-2-.897-2-2v-13h4v13Zm18,0c0,1.103-.897,2-2,2H5.234c.476-.531.766-1.232.766-2V2.5c0-.827.673-1.5,1.5-1.5h14c.827,0,1.5.673,1.5,1.5v18.5ZM15,7h6v1h-6v-1Zm-7,4h13v1h-13v-1Zm0,4h13v1h-13v-1Zm0,4h13v1h-13v-1ZM13,3h-5v5h5V3Zm-1,4h-3v-3h3v3Z" />
                                    </svg>
                                </div>

                                <div>Basic Machine</div>


                            </a>
                        </div>
                    </li> 


                <?php }?>


                <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','1')->where('submoduleid','50')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                    <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(3) == 'delay_table') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Master/User_management/delay_table">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="m21.5,0H7.5c-1.378,0-2.5,1.122-2.5,2.5v4.5H0v14c0,1.654,1.346,3,3,3h18c1.654,0,3-1.346,3-3V2.5c0-1.378-1.122-2.5-2.5-2.5ZM5,21c0,1.103-.897,2-2,2s-2-.897-2-2v-13h4v13Zm18,0c0,1.103-.897,2-2,2H5.234c.476-.531.766-1.232.766-2V2.5c0-.827.673-1.5,1.5-1.5h14c.827,0,1.5.673,1.5,1.5v18.5ZM15,7h6v1h-6v-1Zm-7,4h13v1h-13v-1Zm0,4h13v1h-13v-1Zm0,4h13v1h-13v-1ZM13,3h-5v5h5V3Zm-1,4h-3v-3h3v3Z" />
                                    </svg>
                                </div>

                                <div>Running DF Report for CMD Sir</div>


                            </a>
                        </div>
                    </li>

                <?php } ?>

                    <?php
                    $design_dashboard_access = $this->db->select('acessid')->from('module_capablity')
                        ->where('role_id', $user_id)->where('moduleid', '20')->where('submoduleid', '81')
                        ->where('submodule_access', '1')->limit(1)->get();
                    if ($design_dashboard_access->num_rows() > 0) { ?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(3) == 'design_department_dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Master/User_management/design_department_dashboard">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M22.85,4.56,19.44,1.15a3.94,3.94,0,0,0-5.56,0L1.31,13.72A4.46,4.46,0,0,0,0,16.89V24H7.11a4.46,4.46,0,0,0,3.17-1.31L22.85,10.12a3.94,3.94,0,0,0,0-5.56ZM8.87,21.28A2.48,2.48,0,0,1,7.11,22H2V16.89a2.48,2.48,0,0,1,.72-1.76L13,4.86,19.14,11ZM21.44,8.71l-.89.88L14.41,3.45l.88-.89a1.94,1.94,0,0,1,2.74,0L21.44,6a1.94,1.94,0,0,1,0,2.74ZM5,17H7v2H5Z" />
                                    </svg>
                                </div>
                                <div>Design Department Dashboard</div>
                            </a>
                        </div>
                    </li>
                    <?php } ?>

                    <?php
                    $rnd_design_access = $this->db->select('acessid')->from('module_capablity')
                        ->where('role_id', $user_id)->where('moduleid', '20')->where('submoduleid', '82')
                        ->where('submodule_access', '1')->limit(1)->get();
                    if ($rnd_design_access->num_rows() > 0) { ?>
                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Task_management' && $this->uri->segment(2) == 'rnd_design_dashboard') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Task_management/rnd_design_dashboard">
                                <div><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 21h6v-1H9v1Zm3-21C7.59 0 4 3.59 4 8c0 2.94 1.59 5.51 3.95 6.9L8 18h8l.05-3.1A7.98 7.98 0 0 0 20 8c0-4.41-3.59-8-8-8Zm2.63 13.44-.58.31L14.02 16H9.98l-.03-2.25-.58-.31A5.99 5.99 0 0 1 6 8a6 6 0 0 1 12 0 5.99 5.99 0 0 1-3.37 5.44Z"/></svg></div>
                                <div>R&amp;D Design Dashboard</div>
                            </a>
                        </div>
                    </li>
                    <?php } ?>



                <?php
            $module = $this->db->select('access')->from('module_access')->where('role_id', $user_id)->where('moduleid', '17')->get();
            if ($module->num_rows() > 0) {
                foreach ($module->result() as $moddata);
                if ($moddata->access == '1') {
                    ?>
                 <li>



                        <a href="<?php echo page_url; ?>Dashboard/service_spare_dashboard">
                            <div>
                                 <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M12,12.5h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051V4h5.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-6.051V11.5H6.949c-.245-1.692-1.691-3-3.449-3-1.93,0-3.5,1.57-3.5,3.5s1.57,3.5,3.5,3.5c1.758,0,3.204-1.308,3.449-3h4.051v8.5h6.051c.245,1.692,1.691,3,3.449,3,1.93,0,3.5-1.57,3.5-3.5s-1.57-3.5-3.5-3.5c-1.758,0-3.204,1.308-3.449,3h-5.051v-7.5Zm8.5-3c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Zm0-8.5c1.379,0,2.5,1.122,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.122-2.5-2.5,1.121-2.5,2.5-2.5ZM3.5,14.5c-1.379,0-2.5-1.121-2.5-2.5s1.121-2.5,2.5-2.5,2.5,1.122,2.5,2.5-1.121,2.5-2.5,2.5Zm17,3.5c1.379,0,2.5,1.121,2.5,2.5s-1.121,2.5-2.5,2.5-2.5-1.121-2.5-2.5,1.121-2.5,2.5-2.5Z" />
                                </svg>
                            </div>
                            <div>Service & Spares</div>
                        </a>

                    </li>
                <?php } } ?>

<?php
$service_visit_overview_submodule = $this->db->select('id')
    ->from('submodule')
    ->where('moduleid', '17')
    ->where('submodule', 'SERVICE ENGINEER VISIT ASSIGNMENT OVERVIEW')
    ->limit(1)
    ->get()
    ->row();

$service_visit_overview_visible = false;
if (!empty($service_visit_overview_submodule)) {
    $qry = $this->db->select('id')
        ->from('module_capablity')
        ->where('role_id', $user_id)
        ->where('moduleid', '17')
        ->where('submoduleid', (int) $service_visit_overview_submodule->id)
        ->where('submodule_access', '1')
        ->limit(1)
        ->get();

    if ($qry->num_rows() > 0) {
        $service_visit_overview_visible = true;
    }
}

if (!$service_visit_overview_visible && !empty($_SESSION['logged_in']['adminuser']) && (int) $_SESSION['logged_in']['adminuser'] === 1) {
    $service_visit_overview_visible = true;
}

if ($service_visit_overview_visible) {
?>
<li>
    <div class="dash">
        <a <?php if ($this->uri->segment(1) == 'ServiceLeads' && $this->uri->segment(2) == 'engineer_assignment_overview') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>ServiceLeads/engineer_assignment_overview">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                    <path d="m18.5,15.5c-1.103,0-2,.897-2,2s.897,2,2,2,2-.897,2-2-.897-2-2-2Zm0,3c-.551,0-1-.449-1-1s.449-1,1-1,1,.449,1,1-.449,1-1,1Zm3.888-4.896c-1.039-1.034-2.419-1.604-3.888-1.604s-2.85.569-3.889,1.604c-1.039,1.035-1.611,2.411-1.611,3.875s.572,2.839,1.626,3.888l1.971,1.813c.512.498,1.188.772,1.903.772s1.391-.274,1.892-.762l1.996-1.837c1.039-1.035,1.611-2.411,1.611-3.875s-.572-2.84-1.611-3.875Zm-.691,7.027l-1.992,1.833c-.647.631-1.752.641-2.421-.01l-1.967-1.81c-.85-.846-1.317-1.97-1.317-3.166s.468-2.32,1.317-3.166c.85-.846,1.98-1.312,3.183-1.312s2.333.466,3.182,1.312c.85.846,1.317,1.97,1.317,3.166s-.468,2.32-1.303,3.152Zm-8.697,2.87c0,.276-.224.5-.5.5H4.5c-2.481,0-4.5-2.019-4.5-4.5V4.5C0,2.019,2.019,0,4.5,0h7c2.481,0,4.5,2.019,4.5,4.5v6c0,.276-.224.5-.5.5s-.5-.224-.5-.5v-6c0-1.93-1.57-3.5-3.5-3.5h-7c-1.93,0-3.5,1.57-3.5,3.5v15c0,1.93,1.57,3.5,3.5,3.5h8c.276,0,.5.224.5.5Zm-6-10c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Zm5,0c0,.276-.224,.5-.5,.5h-2c-.276,0-.5-.224-.5-.5s.224-.5,.5-.5h2c.276,0,.5,.224,.5,.5Zm-5,4c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Zm5,0c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5ZM7,5.5c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Zm5,0c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Zm-5,4c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Zm5,0c0,.276-.224.5-.5.5h-2c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h2c.276,0,.5.224.5.5Z"/>
                </svg>
            </div>
            <div>Engineer Visit Assignments</div>
        </a>
    </div>
</li>
<?php
    }
?>

<?php
$service_payment_request_visible = false;
$service_payment_hod_visible = false;
$service_payment_request_submodule = $this->db->select('id')
    ->from('submodule')
    ->where('moduleid', '17')
    ->where('submodule', 'SERVICE PAYMENT REQUESTS')
    ->limit(1)
    ->get()
    ->row();
$service_payment_approval_submodule = $this->db->select('id')
    ->from('submodule')
    ->where('moduleid', '17')
    ->where('submodule', 'SERVICE PAYMENT APPROVALS')
    ->limit(1)
    ->get()
    ->row();

if (!empty($service_payment_request_submodule)) {
    $service_payment_request_visible = $this->db->select('id')
        ->from('module_capablity')
        ->where('role_id', $user_id)
        ->where('moduleid', '17')
        ->where('submoduleid', (int) $service_payment_request_submodule->id)
        ->where('submodule_access', '1')
        ->limit(1)
        ->get()
        ->num_rows() > 0;
}

if (!empty($service_payment_approval_submodule)) {
    $service_payment_hod_visible = $this->db->select('id')
        ->from('module_capablity')
        ->where('role_id', $user_id)
        ->where('moduleid', '17')
        ->where('submoduleid', (int) $service_payment_approval_submodule->id)
        ->where('submodule_access', '1')
        ->limit(1)
        ->get()
        ->num_rows() > 0;
}

if (!empty($_SESSION['logged_in']['adminuser']) && (int) $_SESSION['logged_in']['adminuser'] === 1) {
    $service_payment_request_visible = true;
    $service_payment_hod_visible = true;
}

if ($service_payment_request_visible) {
?>
<li>
    <div class="dash">
        <a <?php if ($this->uri->segment(1) == 'ServiceLeads' && in_array($this->uri->segment(2), ['service_payment_request_form', 'service_payment_requests'], true)) { ?>class="active" <?php } ?> href="<?php echo page_url; ?>ServiceLeads/service_payment_requests">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                    <path d="m19.416,16.101l-2.55,2.74c-.188.202-.504.214-.707.025-.202-.188-.213-.505-.025-.707l2.533-2.721c.459-.547.445-1.39-.049-1.94-.267-.298-.634-.475-1.032-.496-.395-.01-.782.113-1.08.38l-3.204,3.239c-.84.849-1.302,1.973-1.302,3.165v3.714c0,.276-.224.5-.5.5s-.5-.224-.5-.5v-3.714c0-1.458.565-2.832,1.591-3.868l3.226-3.26c.52-.467,1.158-.7,1.824-.654.666.036,1.277.33,1.722.826.824.919.848,2.325.054,3.271ZM21.5,0h-5c-1.379,0-2.5,1.121-2.5,2.5v8c0,.276.224.5.5.5s.5-.224.5-.5V2.5c0-.827.673-1.5,1.5-1.5h5c.827,0,1.5.673,1.5,1.5v9c0,.542-.294,1.043-.768,1.31-.24.135-.326.439-.19.681.092.163.261.255.436.255.083,0,.167-.021.245-.064.788-.442,1.277-1.278,1.277-2.181V2.5c0-1.379-1.121-2.5-2.5-2.5Zm-13,23h-4c-1.93,0-3.5-1.57-3.5-3.5V4.5c0-1.93,1.57-3.5,3.5-3.5h8c.276,0,.5-.224.5-.5s-.224-.5-.5-.5H4.5C2.019,0,0,2.019,0,4.5v15c0,2.481,2.019,4.5,4.5,4.5h4c.276,0,.5-.224.5-.5s-.224-.5-.5-.5ZM5.3,5h6.2c.276,0,.5-.224.5-.5s-.224-.5-.5-.5h-6.2c-.717,0-1.3.583-1.3,1.3v1.4c0,.717.583,1.3,1.3,1.3h6.2c.276,0,.5-.224.5-.5s-.224-.5-.5-.5h-6.2c-.165,0-.3-.135-.3-.3v-1.4c0-.165.135-.3.3-.3Zm.2,5h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm0,3h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm4-3h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm0,3h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm-4,3h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm4,0h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm-4,3h-1c-.276,0-.5.224-.5.5s.224.5.5.5h1c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm18-4c-.276,0-.5.224-.5.5v1.099c0,1.438-.486,2.855-1.369,3.99l-2.025,2.604c-.17.218-.131.532.088.701.091.071.199.105.307.105.148,0,.296-.066.395-.193l2.025-2.604c1.019-1.309,1.58-2.944,1.58-4.604v-1.099c0-.276-.224-.5-.5-.5Z"/>
                </svg>
            </div>
            <div>Service Payment Requests</div>
        </a>
    </div>
</li>
<?php
}

if ($service_payment_hod_visible) {
?>
<li>
    <div class="dash">
        <a <?php if ($this->uri->segment(1) == 'ServiceLeads' && $this->uri->segment(2) == 'service_payment_approvals') { ?>class="active" <?php } ?> href="<?php echo page_url; ?>ServiceLeads/service_payment_approvals">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                    <path d="M24,5.5c0-1.378-1.121-2.5-2.5-2.5h-5.5V0H0V21c0,1.654,1.346,3,3,3H13c1.654,0,3-1.346,3-3v-5h8V5.5Zm-2.5-.5c0,.275,.224,.5,.5,.5v1.5h-6v-2h5.5Zm-7.5,16c0,.551-.448,1-1,1H3c-.552,0-1-.449-1-1v-3H14v3Zm0-5H2V2H14v14Zm2-2v-5h6v5h-6Zm-10.075-2.575l-2.182-2.268,1.387-1.441,2.216,2.301,3.614-3.703,1.398,1.43-3.607,3.696h.001l-.004,.004c-.744,.744-2.058,.746-2.823-.019Zm4.075,9.575H6v-2h4v2Z"/>
                </svg>
            </div>
            <div>Service Payment Approvals</div>
        </a>
    </div>
</li>
<?php
}
?>

<?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','18')->where('submoduleid','58')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
 <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(2) == 'team_dashboard' || $this->uri->segment(2)=='team_task_detail') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Dashboard/team_dashboard">

                                <div>
                                    <svg id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                                        <path d="m20.5 15c1.103 0 2-.897 2-2s-.897-2-2-2-2 .897-2 2 .897 2 2 2zm0-3c.551 0 1 .448 1 1s-.449 1-1 1-1-.448-1-1 .449-1 1-1zm-17 3c1.103 0 2-.897 2-2s-.897-2-2-2-2 .897-2 2 .897 2 2 2zm0-3c.551 0 1 .448 1 1s-.449 1-1 1-1-.448-1-1 .449-1 1-1zm10.5 5c0-1.103-.897-2-2-2s-2 .897-2 2 .897 2 2 2 2-.897 2-2zm-3 0c0-.552.449-1 1-1s1 .448 1 1-.449 1-1 1-1-.448-1-1zm8.025-14.009c-.087-.263.055-.545.316-.633l2-.666c.265-.089.546.055.633.316.087.263-.055.545-.316.633l-2 .666c-.255.086-.546-.052-.633-.316zm0 4.018c.087-.262.368-.405.633-.316l2 .666c.262.088.403.37.316.633-.087.264-.378.403-.633.316l-2-.666c-.262-.088-.403-.37-.316-.633zm-17-5c.087-.262.369-.405.633-.316l2 .666c.262.088.403.37.316.633-.087.264-.378.403-.633.316l-2-.666c-.262-.088-.403-.37-.316-.633zm0 5.982c-.087-.263.055-.545.316-.633l2-.666c.263-.089.545.055.633.316.087.263-.055.545-.316.633l-2 .666c-.255.086-.546-.052-.633-.316zm21.975 11.509c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.379-1.122-2.5-2.5-2.5s-2.5 1.121-2.5 2.5c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.93 1.57-3.5 3.5-3.5s3.5 1.57 3.5 3.5zm-8.5 4c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.379-1.122-2.5-2.5-2.5s-2.5 1.121-2.5 2.5c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.93 1.57-3.5 3.5-3.5s3.5 1.57 3.5 3.5zm-8.5-4c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.379-1.122-2.5-2.5-2.5s-2.5 1.121-2.5 2.5c0 .276-.224.5-.5.5s-.5-.224-.5-.5c0-1.93 1.57-3.5 3.5-3.5s3.5 1.57 3.5 3.5zm8.147-18.385c-1.176-.954-2.718-1.316-4.229-1.001-1.998.421-3.548 2.07-3.858 4.104-.311 2.036.631 4.026 2.398 5.069.339.2.542.531.542.886v.327c0 .827.673 1.5 1.5 1.5h1c.827 0 1.5-.673 1.5-1.5v-.326c0-.354.204-.687.546-.889 1.537-.91 2.454-2.512 2.454-4.285 0-1.514-.675-2.931-1.853-3.885zm-2.147 9.385c0 .275-.224.5-.5.5h-1c-.276 0-.5-.225-.5-.5 0-.122.006-.384-.023-.5h2.047c-.03.115-.024.379-.024.5zm1.037-2.076c-.256.151-.457.354-.624.576h-.912v-3.075c.667-.159 1.23-.613 1.468-1.249.097-.259-.034-.547-.293-.644-.257-.1-.547.034-.644.292-.151.404-.566.676-1.032.676s-.88-.271-1.032-.676c-.097-.258-.384-.393-.644-.292-.259.097-.39.385-.293.644.239.637.801 1.09 1.468 1.249v3.075h-.911c-.166-.222-.367-.423-.622-.574-1.414-.834-2.167-2.427-1.918-4.058.244-1.598 1.509-2.945 3.076-3.275.289-.061.58-.091.867-.091.92 0 1.809.308 2.527.89.942.764 1.482 1.897 1.482 3.108 0 1.416-.734 2.696-1.963 3.424z"/>
                                    </svg>
                                </div>

                                <div>Team Monitoring Dashboard</div>


                            </a>
                        </div>
                    </li>
                <?php }?>


                <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','18')->where('submoduleid','66')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
 <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(1) == 'Masters' && in_array($this->uri->segment(2), array('manage_jobs', 'allocate_job', 'view_job_timeline', 'assembly_machine_report'))) { ?>class="active" <?php } ?> href="<?php echo page_url;?>Masters/manage_jobs">

                                <div>
                                    <svg id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                                        <path d="m17.5 0h-8c-2.481 0-4.5 2.019-4.5 4.5v12c0 2.481 2.019 4.5 4.5 4.5h8c2.481 0 4.5-2.019 4.5-4.5v-12c0-2.481-2.019-4.5-4.5-4.5zm3.5 16.5c0 1.93-1.57 3.5-3.5 3.5h-8c-1.93 0-3.5-1.57-3.5-3.5v-12c0-1.93 1.57-3.5 3.5-3.5h8c1.93 0 3.5 1.57 3.5 3.5zm-8.5-4v4c0 .276-.224.5-.5.5s-.5-.224-.5-.5v-4c0-.276.224-.5.5-.5s.5.224.5.5zm-3 2v2c0 .276-.224.5-.5.5s-.5-.224-.5-.5v-2c0-.276.224-.5.5-.5s.5.224.5.5zm6-4v6c0 .276-.224.5-.5.5s-.5-.224-.5-.5v-6c0-.276.224-.5.5-.5s.5.224.5.5zm3 2v4c0 .276-.224.5-.5.5s-.5-.224-.5-.5v-4c0-.276.224-.5.5-.5s.5.224.5.5zm-1-4.5c-.276 0-.5-.224-.5-.5v-1.793l-2.249 2.249c-.401.402-1.101.402-1.502 0l-.705-.705-2.69 2.603c-.195.195-.512.195-.707 0s-.195-.512 0-.707l2.603-2.603c.401-.402 1.101-.402 1.502 0l.705.705 2.325-2.249h-1.781c-.276 0-.5-.224-.5-.5s.224-.5.5-.5h2c.827 0 1.5.673 1.5 1.5v2c0 .276-.224.5-.5.5zm-.5 15.5c0 .276-.224.5-.5.5h-10c-2.481 0-4.5-2.019-4.5-4.5v-14c0-.276.224-.5.5-.5s.5.224.5.5v14c0 1.93 1.57 3.5 3.5 3.5h10c.276 0 .5.224.5.5z"/>
                                    </svg>
                                </div>

                                <div>Assembly and Trial Floor Reporting</div>


                            </a>
                        </div>
                    </li>
                <?php }?>


                 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','64')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
 <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(1) == 'Machine') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Machine/machineonfloor">

                                <div>
                                    <svg id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1">
                                        <path d="m6 6h4v4h-4zm8.5 11.5c0 1.381-1.119 2.5-2.5 2.5s-2.5-1.119-2.5-2.5c0-.171.018-.338.05-.5h-3.55v7h12v-7h-3.55c.033.162.05.329.05.5zm6.5-17.5h-18c-1.654 0-3 1.346-3 3v17h4v-5h16v5h4v-17c0-1.654-1.346-3-3-3zm-9 12h-8v-8h8zm4 0h-2v-2h2zm0-3h-2v-2h2zm0-3h-2v-2h2zm4 6h-2v-2h2zm0-3h-2v-2h2zm0-3h-2v-2h2z"/>
                                    </svg>
                                </div>

                                <div>Machine on Floor (Accounts)</div>


                            </a>
                        </div>
                    </li>

                    <?php }?>


                                     <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','80')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>

                    <li>
                        <div class="dash">
                            <a <?php if ($this->uri->segment(1) == 'Df_dispatch_plan') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Df_dispatch_plan">
                                <div>
                                    <svg id="Layer_1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 3h-1V1h-2v2H8V1H6v2H5C3.35 3 2 4.35 2 6v13c0 1.65 1.35 3 3 3h14c1.65 0 3-1.35 3-3V6c0-1.65-1.35-3-3-3zm1 16c0 .55-.45 1-1 1H5c-.55 0-1-.45-1-1V9h16v10zM4 7V6c0-.55.45-1 1-1h1v2h2V5h8v2h2V5h1c.55 0 1 .45 1 1v1H4zm3 5h5v5H7v-5zm7 0h3v2h-3v-2zm0 3h3v2h-3v-2z"/>
                                    </svg>
                                </div>
                                <div>DF Dispatch Morning Meeting</div>
                            </a>
                        </div>
                    </li>
                <?php } ?>

                <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','5')->where('submoduleid','65')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
 <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(1) == 'Machine') { ?>class="active" <?php } ?> href="<?php echo page_url;?>Machine/mcsdispatchreport">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="M21.5,24c-1.379,0-2.5-1.122-2.5-2.5V13.5c0-.827-.673-1.5-1.5-1.5H6.5c-.827,0-1.5,.673-1.5,1.5v8c0,1.378-1.121,2.5-2.5,2.5s-2.5-1.122-2.5-2.5V9.561c0-1.499,.741-2.893,1.983-3.73L9.483,.77c1.527-1.031,3.504-1.032,5.033,0l7.5,5.061c1.242,.837,1.983,2.232,1.983,3.73v11.939c0,1.378-1.121,2.5-2.5,2.5ZM6.5,11h11c1.379,0,2.5,1.122,2.5,2.5v8c0,.827,.673,1.5,1.5,1.5s1.5-.673,1.5-1.5V9.561c0-1.166-.576-2.25-1.542-2.901L13.958,1.599c-1.189-.803-2.727-.803-3.916,0L2.542,6.66c-.966,.651-1.542,1.736-1.542,2.901v11.939c0,.827,.673,1.5,1.5,1.5s1.5-.673,1.5-1.5V13.5c0-1.378,1.121-2.5,2.5-2.5Zm3.3,13h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Zm1.6-3h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Zm7.6,9h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Z"/>
                                    </svg>
                                </div>

                                <div>M/cs Dispatch Report (Accounts)</div>


                            </a>
                        </div>
                    </li>
                <?php }?>



 <?php 

$qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id',$user_id)->where('moduleid','19')->where('submoduleid','77')->where('submodule_access','1')->get();

if($qry->num_rows()>0){

?>
 <li>

                        <div class="dash">

                            <a <?php if ($this->uri->segment(1) == 'abom') { ?>class="active" <?php } ?> href="<?php echo page_url;?>abom/generate">

                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24">
                                        <path d="M21.5,24c-1.379,0-2.5-1.122-2.5-2.5V13.5c0-.827-.673-1.5-1.5-1.5H6.5c-.827,0-1.5,.673-1.5,1.5v8c0,1.378-1.121,2.5-2.5,2.5s-2.5-1.122-2.5-2.5V9.561c0-1.499,.741-2.893,1.983-3.73L9.483,.77c1.527-1.031,3.504-1.032,5.033,0l7.5,5.061c1.242,.837,1.983,2.232,1.983,3.73v11.939c0,1.378-1.121,2.5-2.5,2.5ZM6.5,11h11c1.379,0,2.5,1.122,2.5,2.5v8c0,.827,.673,1.5,1.5,1.5s1.5-.673,1.5-1.5V9.561c0-1.166-.576-2.25-1.542-2.901L13.958,1.599c-1.189-.803-2.727-.803-3.916,0L2.542,6.66c-.966,.651-1.542,1.736-1.542,2.901v11.939c0,.827,.673,1.5,1.5,1.5s1.5-.673,1.5-1.5V13.5c0-1.378,1.121-2.5,2.5-2.5Zm3.3,13h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Zm1.6-3h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Zm7.6,9h-1.6c-.662,0-1.2-.539-1.2-1.2v-1.6c0-.662,.538-1.2,1.2-1.2h1.6c.662,0,1.2,.539,1.2,1.2v1.6c0,.662-.538,1.2-1.2,1.2Zm-1.6-3c-.11,0-.2,.09-.2,.2v1.6c0,.11,.09,.2,.2,.2h1.6c.11,0,.2-.09,.2-.2v-1.6c0-.11-.09-.2-.2-.2h-1.6Z"/>
                                    </svg>
                                </div>

                                <div>Automation BOM Generator</div>


                            </a>
                        </div>
                    </li>
                <?php }?>
                

                </ul>

            <!--   <li>
        
        	<div class="dash">
        
        		<a  href="<?php echo page_url;?>Dashboard"><i class="fa fa-dashboard"></i> TEAM REPORTING</a>
        	</div>
        </li>
 -->
          


        <!--</ul>
        </li>
        </ul> -->
            </div>


            <a href="javascript:void(0);" class="icon colexpicon" onclick="myFunction()"><i class="fa fa-bars"></i></a>



        </div>
        <div id="profileimg" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true" style="display: none;">
           <form method="post" action="<?php echo page_url;?>User/update_profile/<?php echo $user_id;?>" enctype="multipart/form-data">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">CHANGE YOUR PROFILE IMAGE</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">
            
                                        
                            <div class="col-md-12">
                            <input type="file" class="form-control" name="profile" value="" placeholder="upload" required></div>

                                            </div>
</div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="save" class="btn btn-info" value="Save"> 

                                        </div>

                                    </div>

                                </div>

                                </form>
        </div><!-- /.modal -->
        <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">
                    <div class="menu-extras row">
                        <div class="quotecss hidden-xs"
                            style="display: inline-block;padding-top: 16px;padding-left: 100px;">



                            </span>
                        </div>

                        <?php 
					// $st = date('Y-m-d',strtotime('-30 days'));
					// $et = date('Y-m-d');
                		if($total<=20)
                		{
                			$icon='😀';
                		}else
                		{
                			$icon='😟';
                		}
                		//if($_SESSION['logged_in']['business_location']!=1 && $user_role!=12){ 
                			if($_SESSION['logged_in']['business_location']!=1){ 
                			?>
                        <div class="col-md-2"></div>
                        <div class="col-md-2" style="padding-left: 0px; padding-top:10px; color:red;text-align: left;"><a href="<?php echo page_url;?>Maintenance_support/meetinglog"><span class="btn btn-success">Log Your Meeting Info <i class="fa fa-handshake-o"></i></span></a></div>


                        <?php 
                        if($_SESSION['logged_in']['role']!=12)
                        {
                        ?>
                        <div class="col-md-4 col-sm-12 col-xs-12 hidden-xs" id="blink_text">
                            <div style="margin-top: 13px;">
                                <a style="background-color: #fdf5dd; padding: 10px 10px 10px 10px; font-size:14px; color:#635221;"
                                    href='<?php echo page_url;?>MIS/index/ALL/<?php echo date('Y-m-d', strtotime('-1 year +1 day'));?>/<?php echo date('Y-m-d');?>/<?php echo $_SESSION['logged_in']['user_id'];?>/ALL'
                                    target="_blank">Your Work
                                    Pending/Delayed MIS: <span style="color:#635221;font-weight: 600;font-size:17px"
                                        id="blink_text1"><?php echo $total;?>% &nbsp;<span style="font-size:17px;"><?php echo $icon;?></span></span></a>
                            </div>

                        </div>
                    <?php }else
                    {
                    ?>
                       <div class="col-md-4 col-sm-12 col-xs-12 hidden-xs" id="blink_text">
                            <div style="margin-top: 13px; visibility:hidden;">
                               HIDDEN 
                            </div>

                        </div>


                <?php } ?>
  <?php } ?>
                        <div class="col-md-4">
                            <ul class="nav navbar-nav navbar-right pull-right">

<?php
/* ===== CHAT MODULE — topbar icon, unread badge, notifications, dock =====
   Nothing here renders while CHAT_MODULE_VISIBLE is FALSE, so there is
   also no background poll before go-live. */
$this->load->helper('chat_access');
if (chat_nav_visible($this)):
?>
                                <!-- margin-top:8px centres the 39px icon on the same
                                     axis as the username and avatar (their centre is
                                     y=25 in this row). The neighbouring li carries
                                     margin-top:37px, but that one renders at zero
                                     height — copying its offset pushed the icon onto
                                     its own line below the topbar. -->
                                <li class="dropdown user-box" style="margin-top:8px;">
                                    <div style="position:relative;">
                                        <a href="javascript:void(0);" id="ccChatBtn" class="cc-chatbtn" title="Chat" aria-label="Chat" style="display:inline-block;padding:6px 10px;color:#188ae2;font-size:19px;line-height:1;">
                                            <i class="fa fa-comments-o" aria-hidden="true"></i>
                                            <span class="cc-chat-badge" id="ccChatBadge">0</span>
                                        </a>
                                        <div class="cc-chatpop" id="ccChatPop">
                                            <h6>Chat notifications
                                                <a href="<?php echo page_url; ?>Chat"
                                                   onclick="if(window.ChatDock){event.preventDefault();window.ChatDock.open();this.closest('.cc-chatpop').style.display='none';}">Open chat</a>
                                            </h6>
                                            <div class="items" id="ccChatItems"></div>
                                        </div>
                                    </div>
                                </li>
<?php endif; ?>
<!-- ===== END CHAT MODULE — topbar icon ===== -->

                                <li class="dropdown user-box saurabh" style="margin-top:37px;">
                                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav navbar-right">
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    <!-- <img src="https://shubhampack.in/beta1/bell.png" class='img-responsive'
                                                                style='width:20px;'> -->
                                                    <!-- <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                                        data-name="Layer 1" viewBox="0 0 24 24" class='img-responsive'
                                                        style='width:20px; margin-top: 6px;'>
                                                        <path
                                                            d="M23.391,16.207l-2.413-9.39C19.864,2.803,16.172,0,12,0,7.598,0,3.855,3.002,2.903,7.29L.692,16.359c-.197,.887,.019,1.805,.59,2.518,.572,.714,1.425,1.123,2.34,1.123h3.479c.465,2.279,2.484,4,4.899,4s4.434-1.721,4.899-4h3.599c.946,0,1.817-.432,2.389-1.185,.573-.753,.755-1.707,.504-2.608Zm-11.391,6.793c-1.858,0-3.411-1.279-3.858-3h7.716c-.447,1.721-2,3-3.858,3Zm10.091-4.79c-.381,.502-.962,.79-1.593,.79H3.623c-.61,0-1.179-.272-1.56-.748-.381-.476-.525-1.087-.396-1.666L3.877,7.517C4.729,3.68,8.07,1,12,1c3.724,0,7.02,2.502,8.012,6.075l2.413,9.39c.169,.607,.047,1.243-.334,1.745Z" />
                                                    </svg> -->
                                                    <span class='badgeAlert' id="notifyBadge"></span>
                                                </a>
                                                <ul class="list-notificacao dropdown-menu" id="livenotifications"
                                                    style="height:383px;">

                                                    <!-- <li id='item_notification_2'>
                                    <div class="media">
                                       
                                       <div class="media-body">
                                          <div class='exclusaoNotificacao'><button class='btn btn-danger btn-xs' id='2' onclick='excluirItemNotificacao(this)'>x</button>
                                          </div>
                                          <h4 class="media-heading">ITEM 2</h4>
                                          <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                                       </div>
                                    </div>
                                 </li>  -->
                                                </ul>
                                            </li>

                                        </ul>
                                    </div>
                                </li>
                                <li class="dropdown user-box">
                                    <div>
                                        <ul class="list-inline m-b-0">
                                            <li style="margin-left: -8px;display:none;">

                                                <div style="    display: flex;">

                                                    <i class="fa fa-clock-o" aria-hidden="true"
                                                        style="margin-right: 6px; margin-top: 6px; font-size: 15px; color: #188ae2;"></i>

                                                    <p id="demo" style="font-weight:600; margin-top: 4px;">

                                                    </p>

                                                </div>

                                                <!-- <script>
            										var myVar = setInterval(myTimer, 1000);
            
            										function myTimer() {
            
            											var d = new Date();
            
            											document.getElementById("demo").innerHTML = d.toLocaleTimeString();
            
            										}
            									</script> -->

                                            </li>

                                            <li><a href="" class="dropdown-toggle waves-effect waves-light profile "
                                                    data-toggle="dropdown" aria-expanded="true"
                                                    style="font-size: 20px;color: #0000009e;line-height: 56px; margin-top: 3px;">

                                                    <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                                        data-name="Layer 1" viewBox="0 0 24 24" style="width: 18px;">
                                                        <path
                                                            d="m12,15c-.916,0-1.776-.356-2.424-1.004l-4.418-4.131c-.201-.188-.212-.505-.023-.707.188-.201.505-.212.707-.023l4.43,4.143c.941.939,2.527.928,3.445.012l4.441-4.154c.202-.188.519-.178.707.023.188.202.178.519-.023.707l-4.43,4.143c-.636.636-1.496.992-2.412.992Z" />
                                                    </svg>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    	<?php
											$user_id = $this->session->userdata['logged_in']['user_id'];
											?>
                                                    <li><a
                                                            href="<?php echo page_url; ?>Master/User_management/change_password">
                                                            <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                                                    data-name="Layer 1" viewBox="0 0 24 24">
                                                                    <path
                                                                        d="m10.5,23h-6c-1.93,0-3.5-1.57-3.5-3.5v-7c0-1.93,1.57-3.5,3.5-3.5h10c1.93,0,3.5,1.57,3.5,3.5,0,.276.224.5.5.5s.5-.224.5-.5c0-1.953-1.258-3.602-3-4.224v-1.776c0-3.584-2.916-6.5-6.5-6.5S3,2.916,3,6.5v1.776c-1.742.621-3,2.271-3,4.224v7c0,2.481,2.019,4.5,4.5,4.5h6c.276,0,.5-.224.5-.5s-.224-.5-.5-.5ZM4,6.5c0-3.033,2.467-5.5,5.5-5.5s5.5,2.467,5.5,5.5v1.551c-.166-.019-.329-.051-.5-.051H4.5c-.171,0-.334.032-.5.051v-1.551Zm5.5,8.5c.276,0,.5.224.5.5v2c0,.276-.224.5-.5.5s-.5-.224-.5-.5v-2c0-.276.224-.5.5-.5Zm14,1h-1.879l.182-1.45c.035-.274-.16-.524-.434-.559-.281-.032-.524.16-.559.434l-.198,1.575h-4.171l.183-1.45c.035-.274-.16-.524-.434-.559-.284-.032-.524.16-.559.434l-.198,1.575h-1.934c-.276,0-.5.224-.5.5s.224.5.5.5h1.808l-.504,4h-1.804c-.276,0-.5.224-.5.5s.224.5.5.5h1.679l-.181,1.438c-.035.274.16.524.434.559.021.002.042.004.063.004.249,0,.464-.185.496-.438l.197-1.562h4.172l-.181,1.438c-.035.274.16.524.434.559.021.002.042.004.063.004.249,0,.464-.185.496-.438l.197-1.562h2.134c.276,0,.5-.224.5-.5s-.224-.5-.5-.5h-2.008l.503-4h2.005c.276,0,.5-.224.5-.5s-.224-.5-.5-.5Zm-3.516,5h-4.172l.504-4h4.171l-.503,4Z" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                Change Password
                                                            </div>

                                                        </a></li>
                                                    <li><a href="<?php echo page_url; ?>User/signout">
                                                            <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                                                    data-name="Layer 1" viewBox="0 0 24 24">
                                                                    <path
                                                                        d="m24,12c0,.534-.208,1.037-.586,1.414l-4.565,4.444-.697-.717,4.561-4.439c.058-.058.093-.132.135-.202H6v-1h16.846c-.042-.071-.078-.147-.139-.207l-4.556-4.435.697-.717,4.561,4.439c.383.382.591.885.591,1.419Zm-13,9.5c0,.827-.673,1.5-1.5,1.5H2.5c-.827,0-1.5-.673-1.5-1.5V2.5c0-.827.673-1.5,1.5-1.5h7c.827,0,1.5.673,1.5,1.5v6.5h1V2.5c0-1.378-1.122-2.5-2.5-2.5H2.5C1.122,0,0,1.122,0,2.5v19c0,1.379,1.122,2.5,2.5,2.5h7c1.378,0,2.5-1.121,2.5-2.5v-6.5h-1v6.5Z" />
                                                                </svg></div>
                                                            <div>Logout</div>
                                                        </a></li>
                                                        	<?php
											if ($this->uri->segment(1) == 'Dashboard') {
											?>
                                                    <li><a href="javascript:void(0);" data-toggle="modal"
                                                            data-target="#profileimg">
                                                            <div><svg xmlns="http://www.w3.org/2000/svg" id="Layer_1"
                                                                    data-name="Layer 1" viewBox="0 0 24 24">
                                                                    <path
                                                                        d="M9,12c3.309,0,6-2.691,6-6S12.309,0,9,0,3,2.691,3,6s2.691,6,6,6Zm0-11c2.757,0,5,2.243,5,5s-2.243,5-5,5-5-2.243-5-5S6.243,1,9,1Zm3.767,14.94c-1.151-.615-2.454-.94-3.767-.94-4.411,0-8,3.589-8,8v.5c0,.276-.224,.5-.5,.5s-.5-.224-.5-.5v-.5c0-4.962,4.038-9,9-9,1.478,0,2.943,.366,4.239,1.059,.244,.13,.335,.433,.205,.677-.13,.244-.433,.334-.677,.205Zm10.5-4.208c-.943-.944-2.592-.944-3.535,0l-7.707,7.707c-.661,.661-1.025,1.54-1.025,2.475v1.586c0,.276,.224,.5,.5,.5h1.586c.935,0,1.814-.364,2.475-1.025l7.707-7.707c.472-.472,.732-1.1,.732-1.768s-.26-1.296-.732-1.768Zm-.707,2.828l-7.707,7.707c-.472,.472-1.1,.732-1.768,.732h-1.086v-1.086c0-.668,.26-1.295,.732-1.768l7.707-7.707c.566-.566,1.555-.566,2.121,0,.283,.283,.439,.66,.439,1.061s-.156,.777-.439,1.061Z" />
                                                                </svg></div>
                                                            <div>Change Your Profile
                                                                Picture</div>
                                                        </a></li>
                                                        	<?php } ?>
                                                    <!-- <li><a href="javascript:void(0);" data-toggle="modal" data-target="#con-close-modal"><i class="ti-upload m-r-5"></i>Add Profile Status</a></li> -->
                                                </ul>

                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="usernamecss"><span><?php echo $first_name; ?>&nbsp;<?php echo $last_name; ?></span></li>
                                <li class="dropdown user-box">
                                    <a href="" class="dropdown-toggle waves-effect waves-light profile "
                                        data-toggle="dropdown" aria-expanded="true">
                                        <?php if ($profilepic) { ?>
                                       <img src="<?php echo user_profile; ?><?php echo $profilepic; ?>" alt="user-img" class="img-circle user-img"><?php } else { ?><img src="https://prestomitr.com/image_bank/users/1534487737.jpg" alt="user-img" class="img-circle user-img"> <?php } ?>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </header>



        <!--Shubham Pack Menu closed here-->
        <script>
            $(document).ready(function () {
                getnotificationdata();
                notificationuserwiseCount();
            });
            // setInterval(getnotificationdata, 1000);
            function getnotificationdata() {
                $("#livenotifications").html('');
                var departmentid = '10';
                var userid = '161';
                $.ajax({
                    type: "post",
                    url: "https://shubhampack.in/beta1/index.php/Task/notificationuserwise",
                    data: "departmentid=" + departmentid + "&userid=" + userid,
                    success: function (data) {

                        $("#livenotifications").html(data);

                    }
                });
            }
            function markasread(id, flag) {
                $.ajax({
                    type: "post",
                    url: "https://shubhampack.in/beta1/index.php/Task/markasreadNotifications",
                    data: "id=" + id,
                    success: function (data) {
                        notificationuserwiseCount();
                        $("#item_notification_" + flag).hide();

                    }
                });
            }

            function notificationuserwiseCount() {
                var departmentid = '10';
                var userid = '161';
                $.ajax({
                    type: "post",
                    url: "https://shubhampack.in/beta1/index.php/Task/notificationuserwiseCount",
                    data: "departmentid=" + departmentid + "&userid=" + userid,
                    success: function (data) {
                        $("#notifyBadge").text(data);

                    }
                });
            }
        </script>




    </header>

<?php
/* ===== CHAT MODULE — notification widget + floating dock ==============
   Deliberately OUTSIDE the topbar's <ul>. Both views emit fixed-position
   containers and a <style> block; inside a <ul> the HTML parser hoists
   any non-<li> content out of the list, which moves it somewhere neither
   view controls. Here they sit at the end of the header, where they are
   valid and their positioning is their own.

   Nothing renders while CHAT_MODULE_VISIBLE is FALSE. */
$this->load->helper('chat_access');
if (chat_nav_visible($this)) {
    $this->load->view('chatmodule/_navwidget');
    // Skipped on the Chat page itself, where the full messenger is
    // already on screen.
    if ($this->uri->segment(1) !== 'Chat') $this->load->view('chatmodule/_dock');
}
?>
<!-- ===== END CHAT MODULE ===== -->

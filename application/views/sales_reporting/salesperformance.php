<?php 
$CI = &get_instance();
$CI->load->model('Salescrm_model');
$DI = &get_instance();
$DI->load->model('Lead_model');    

$from_month=$this->uri->segment(3);
$to_month=$this->uri->segment(4);
$rest=$this->db->select('lead_name,lead_id')->from('lead_stage')->where('show_in_funnel',1)->get();
if($rest->num_rows()>0)
{
$i=1;
foreach($rest->result() as $row)
{
${"current_count_" . $row->lead_id}=$CI->Salescrm_model->check_status(0,$row->lead_id,$to_month);
${"previous_count_" . $row->lead_id}=$CI->Salescrm_model->check_status(1,$row->lead_id,$from_month);
}

}  

if($this->uri->segment(5)<>'')
{
    if($this->uri->segment(5)=='ALL')
    {
        $users="ALL AGENTS COMBINED";
    }else
    {
        $users=$CI->Salescrm_model->getusername($this->uri->segment(5));
        
    }
}else
{
    $users='';
}  
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">

    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">

    <title><?php echo sitetitle; ?></title>

    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
  <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
      

     <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
       <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css">
    <?php } ?>

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>
    <?PHP
    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
    foreach ($q->result() as $LOGO);
    ?>
    <style>
        .funnel_outer {
            width: 50%;
            float: left;
            position: relative;
            padding: 0 10%;
        }

        .funnel_outer * {
            box-sizing: border-box
        }

        .funnel_outer ul {
            margin: 0;
            padding: 0;
        }

        .funnel_outer ul li {
            float: left;
            position: relative;
            margin: 2px 0;
            height: 50px;
            clear: both;
            text-align: center;
            width: 100%;
            list-style: none
        }

        .funnel_outer li span {
            border-top-width: 50px;
            border-top-style: solid;
            border-left: 25px solid transparent;
            border-right: 25px solid transparent;
            height: 0;
            display: inline-block;
            vertical-align: middle;
        }

        .funnel_step_1 span {
            width: 100%;
            border-top-color: #8080b6;
        }

        .funnel_step_2 span {
            width: calc(100% - 50px);
            border-top-color: #669966
        }

        .funnel_step_3 span {
            width: calc(100% - 100px);
            border-top-color: #a27417
        }

        .funnel_step_4 span {
            width: calc(100% - 150px);
            border-top-color: #ff66cc
        }

        .funnel_step_5 span {
            width: calc(100% - 200px);
            border-top-color: #0099ff
        }

        .funnel_step_6 span {
            width: calc(100% - 250px);
            border-top-color: #027002
        }

        .funnel_step_7 span {
            width: calc(100% - 300px);
            border-top-color: #ff0000;
        }

        .funnel_outer ul li:last-child span {
            border-left: 0;
            border-right: 0;
            border-top-width: 40px;
        }

        .funnel_outer ul li.not_last span {
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top-width: 50px;
        }

        .funnel_outer ul li span p {
            margin-top: -30px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .page-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 0px;
    margin-top: 0px;
    line-height: 70px;
}
    </style>
</head>


<body>


    <!-- Navigation Bar-->
    <header id="topnav">
        <?php $this->load->view('common/nav-menu'); ?>
    </header>
    <!-- End Navigation Bar-->


    <div class="wrapper">
        <div class="container-fluid">

          
        
            <div class="row">
                <div class="col-md-12 page-title">Overall Company's Leads Progression</div>
               
            </div>
             <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    
                        <form method="post" action="<?php echo page_url;?>Sales_stats_reporting/filter_salesperformance">
                            <div class="card-box col-md-12">
                                <div class="">
                                    <div class="col-md-3"></div>
                                   <!--  <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="from_date" class="form-control" value="<?php echo date('Y-m',strtotime($this->uri->segment(3)));?>" required="">
                                        </div>
                                    </div> -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <input type="month" name="to_date" class="form-control" value="<?php echo date('Y-m',strtotime($this->uri->segment(4)));?>" required="">
                                        </div>
                                    </div>

                                     <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Agent</label>
                                            <span id="error_create_date" style="color:red;">*</span>
                                            <select name="user" id="user" class="form-control" required="">
                                                <option value="ALL"  <?php if($this->uri->segment(5)=='ALL'){?> selected <?php } ?>>ALL</option>
                                            <?php 
                                            $q1 = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('department_id',6)->where('user_status',1)->or_where('user_id',25)->where('user_status',1)->get();

                                            foreach($q1->result() as $rowss){?>
                                            <option value='<?php echo $rowss->user_id;?>' <?php if($this->uri->segment(5)==$rowss->user_id){?> selected <?php } ?>><?php echo $rowss->first_name." ".$rowss->last_name;?></option>
                                           <?php }
                                            ?>

                                            </select>
                                        </div>
                                    </div>
                               
                                    <div class="col-md-1" style="margin-top: 23px;">
                                        <input type="submit" class="btn btn-success" value="Filter">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
           
             <?php 
                    $date = date('Y-m',strtotime($from_month));
                    $start_datess=$date;
                    ?>

                <div class="row">
                <!-- <div class="col-sm-6"> -->
                <div class="card-box table-responsive">
                    <div class="col-md-12"><h5 class="text-center"><?php echo strtoupper($users);?> SALES PERFORMANCE</h5></div>


                    <?php 
                        $start_date=date('Y-m-01',strtotime($from_month));
                        $end_date=date('Y-m-t',strtotime($start_date));
                        $start_date1=date('Y-m-01',strtotime($to_month));
                        $end_date1=date('Y-m-t',strtotime($to_month));
                        $previous_month_sale=$CI->Salescrm_model->get_sales_numbers($start_date,$end_date,$this->uri->segment(5));
                        $this_month_sale=$CI->Salescrm_model->get_sales_numbers($start_date1,$end_date1,$this->uri->segment(5));

                       // $previous_month_profit=0;
                        $previous_month_profit=$CI->Salescrm_model->get_profit_numbers($start_date,$end_date,$this->uri->segment(5));
                        $this_month_profit=$CI->Salescrm_model->get_profit_numbers($start_date1,$end_date1,$this->uri->segment(5));


                        $prev_month_target=$CI->Salescrm_model->get_target($start_date,$end_date,$this->uri->segment(5));
                        //$prev_month_target=0;

                        $this_month_target=$CI->Salescrm_model->get_target($start_date1,$end_date1,$this->uri->segment(5));

                        if($this_month_profit>=$this_month_target)
                        {
                            $balance_left=0;
                        }else{

                        $balance_left=$this_month_profit-$this_month_target;
                        }

                          $next_month_target=$CI->Salescrm_model->get_target(date('Y-m-d',strtotime($start_date1.' +1 Month')),date('Y-m-d',strtotime($start_date1.' +1 Month')),$this->uri->segment(5));

                    ?>
                     <div class="col-md-12">
                        <div class="col-md-4" style="display: none;">
                            <div class="col-md-12 card-box" style="padding:0px;min-height:25px !important;"><p class="text-center" style="color:red;"><?php echo date('M-Y',strtotime($start_datess));?></p></div>
                             <div class="col-md-4 card-box">
                            <p class="page-title text-center" style="color:red;line-height: 0px;">Total Sales(in ₹)</p>
                            <p class="page-title text-center" style="color:red"><?php echo $previous_month_sale;?></p>
                        </div>
                         
                        <div class="col-md-4 card-box">
                            <p class="page-title text-center" style="color:red;line-height: 0px;">Net Profit (in ₹)</p>
                            <p class="page-title text-center" style="color:red;"><a href="<?php echo page_url;?>Sales_stats_reporting/margin_sheet/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $this->uri->segment(5);?>"><?php echo $previous_month_profit;?></a></p>
                        </div>
                        </div>


                        <div class="col-md-4"></div>

                         <div class="col-md-4" style="display:none">
                            <div class="col-md-12 card-box" style="padding:0px;min-height:25px !important;"><p class="text-center" style="color:red;"><?php echo date('M-Y',strtotime($to_month));?></p></div>
                        <div class="col-md-4 card-box">
                            <p class="page-title text-center" style="color:red;line-height: 0px;">Total Sales(in ₹)</p>
                            <p class="page-title text-center" style="color:red"><?php echo $this_month_sale;?></p>
                        </div>
                        <div class="col-md-4 card-box">
                            <p class="page-title text-center" style="color:red;line-height: 0px;">Target</p>
                            <p class="page-title text-center" style="color:red"><?php echo $this_month_target;?></p>
                        </div>
                        <div class="col-md-4 card-box">
                            <p class="page-title text-center" style="color:red;line-height: 0px;">Net Profit (in ₹)</p>
                            <p class="page-title text-center" style="color:red;"><a href="<?php echo page_url;?>Sales_stats_reporting/margin_sheet/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $this->uri->segment(5);?>"><?php echo $this_month_profit;?></a></p>
                        </div>
                

                        <div class="col-md-12 card-box">
                             <p class="page-title text-center" style="color:red;line-height: 0px;">Difference (in ₹)</p>
                            <p class="page-title text-center" style="color:red;"><?php echo $balance_left;?></p>
                        </div>

                        <?php 
                        if($this->uri->segment(5)<>'ALL' && $this->uri->segment(5)<>'')
                        {
                        ?>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                  <div class="form-group">
                                    <label>Prev. Balance</label>
                                    <input type="text" name="previous_bal" id="previous_bal" class="form-control" value="<?php echo abs($balance_left);?>" readonly>
                            </div>
                        </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Profit Target For <?php echo date('M',strtotime($to_month .'+1 Month'));?></label>
                                    <input type="number" min=0 name="target" id="target" class="form-control" value="<?php echo $next_month_target;?>" onkeyup="save_month_target();">
                                    <input type="hidden" id="period" value="<?php echo date('Y-m-01',strtotime($to_month .'+1 Month'));?>">
                                </div>
                            </div>
                             <div class="col-md-1">
                                <div style="color:black;font-weight: bold;font-size: 16px;margin-top: 25px;">=</div>
                            </div>
                            <?php 
                            $total=$next_month_target+abs($balance_left);
                            ?>
                              <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total Profit</label>
                                    <input type="text" name="total_target" id="total_target" class="form-control" value="<?php echo $total;?>" readonly>
                                </div>
                            </div>

                        </div>
                    <?php } ?>
                        </div>
                    </div>

                   
                    <div class="col-md-12">
                        <table class="table table-bordered">
                        <thead>
                        <tr>
                        <th style="width: 25%;">Parameter</th>
                        <!-- <th style="width: 25%;"><?php echo date('M-Y',strtotime($start_datess));?></th> -->
                        <th style="width: 25%;"><?php echo date('M-Y',strtotime($to_month));?></th>
                        <!-- <th style="width: 25%;">Total Activities</th> -->
                        </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $newvisit_previous=$CI->Salescrm_model->visit_for_new_customers($from_month,$this->uri->segment(5));
                                $newvisit_current=$CI->Salescrm_model->visit_for_new_customers($to_month,$this->uri->segment(5));
                                $newvisit_currenttotal=$CI->Salescrm_model->totalvisit_for_new_customers($to_month,$this->uri->segment(5));

                        
                                $start_date=date('Y-m-01',strtotime($from_month));
                                $end_date=date('Y-m-t',strtotime($start_date));
                                $start_date1=date('Y-m-01',strtotime($to_month));
                                $end_date1=date('Y-m-t',strtotime($to_month));
                                if($this->uri->segment(5)<>'ALL')
                                {
                                $u=$this->uri->segment(5);
                                }else
                                {
                                $u="ALL";
                                }


                                 $incomplete_visit=$CI->Salescrm_model->incomplete_visit($to_month,$this->uri->segment(5));
                            ?>
                            <tr>
                            <td>NEW VISITS</td>
                            <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>/1' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $newvisit_previous;?></a></td> -->
                            <td><a href='<?php echo page_url;?>Leads/new_visit/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $newvisit_current;?></a></td>

                             <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit/2022-01-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $newvisit_currenttotal;?></a></td> -->
                            </tr>

                            <tr>
                            <td>OVERALL INCOMPLETE VISITS</td>
                            <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>/1' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $newvisit_previous;?></a></td> -->
                            <td><a href='<?php echo page_url;?>Leads/incomplete_visits/NA/NA/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $incomplete_visit;?></a></td>

                             <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit/2022-01-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $newvisit_currenttotal;?></a></td> -->
                            </tr>

                            <?php 

                             $oldvisit_previous=$CI->Salescrm_model->visit_for_old_customers($from_month,$this->uri->segment(5));
                                $oldvisit_current=$CI->Salescrm_model->visit_for_old_customers($to_month,$this->uri->segment(5));
                                // $oldvisittotalvisit=$CI->Salescrm_model->totalvisit_for_old_customers($to_month,$this->uri->segment(5));

                        
                                $start_date=date('Y-m-01',strtotime($from_month));
                                $end_date=date('Y-m-t',strtotime($start_date));
                                $start_date1=date('Y-m-01',strtotime($to_month));
                                $end_date1=date('Y-m-t',strtotime($to_month));
                                if($this->uri->segment(5)<>'ALL')
                                {
                                $u=$this->uri->segment(5);
                                }else
                                {
                                $u="ALL";
                                }


                            ?>

                        <tr>
                            <td>RUNNING CUSTOMER VISITS</td>
                            <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit_old_customer/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $oldvisit_previous;?></a></td> -->
                            <td><a href='<?php echo page_url;?>Leads/todays_visit_old_customer/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $oldvisit_current;?></a></td>
                             <!-- <td><a href='<?php echo page_url;?>Leads/todays_visit_old_customer/2022-01-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $oldvisittotalvisit;?></a></td> -->
                        </tr>
                        <?php 
                        $rest=$this->db->select('lead_name,lead_id,quotation_step,conversion_step')->from('lead_stage')->get();
                        if($rest->num_rows()>0)
                        {
                        $i=1;
                        foreach($rest->result() as $row)
                        {
                            ${"current_count_" . $row->lead_id}=$CI->Salescrm_model->check_status_via_user(1,$row->lead_id,$to_month,$this->uri->segment(5));
                            ${"previous_count_" . $row->lead_id}=$CI->Salescrm_model->check_status_via_user(1,$row->lead_id,$from_month,$this->uri->segment(5));
                            // ${"overall_count_" . $row->lead_id}=$CI->Salescrm_model->overall_check_status_via_user(1,$row->lead_id,'2022-02-01',date('Y-m-d'),$this->uri->segment(5));

                            $curr=count(${"current_count_" . $row->lead_id});
                            $prev=count(${"previous_count_" . $row->lead_id});
                           // $overall=count(${"overall_count_" . $row->lead_id});

                            $start_datess=date('Y-m',strtotime($from_month));
                            $start_date=date('Y-m-01',strtotime($start_datess));
                            $end_date=date('Y-m-t',strtotime($start_date));


                        $start_date1=date('Y-m-01',strtotime($to_month));
                        $end_date1=date('Y-m-t',strtotime($to_month));
                        $stage="'$row->lead_id'";
                        $st=base64_encode($stage);

                        if($this->uri->segment(5)<>'ALL')
                        {
                            $u=base64_encode($this->uri->segment(5));
                        }else
                        {
                            $u="NA";
                        }
                        ?>
                        <tr>
                        <td><?php echo strtoupper($row->lead_name);?></td>
                        <!-- <td><a href='<?php echo page_url;?>Search/globalfilter/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>/<?php echo $st;?>/NA/NA/NA/NA/NA/1' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $prev;?></a></td> -->
                        <td><a href='<?php echo page_url;?>Search/globalfilter/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>/<?php echo $st;?>/NA/NA/NA/NA/NA/1' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $curr;?></a></td>

                          <!-- <td><a href='<?php echo page_url;?>Search/globalfilter/2022-02-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>/<?php echo $st;?>/NA/NA/NA/NA/NA' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $overall;?></a></td> -->
                        </tr>
                            <?php 
                            if($row->quotation_step==1)
                            {

                                $start_date=date('Y-m-01',strtotime($from_month));
                                $end_date=date('Y-m-t',strtotime($start_date));

                                $start_date1=date('Y-m-01',strtotime($to_month));
                                $end_date1=date('Y-m-t',strtotime($start_date1));

                                if($this->uri->segment(5)<>'ALL')
                                {
                                $u=$this->uri->segment(5);
                                }else
                                {
                                $u="ALL";
                                }

                                $previous_quote_shared=$CI->Salescrm_model->running_quote_send($start_date,$end_date,$u);
                                $current_quote_shared=$CI->Salescrm_model->running_quote_send($start_date1,$end_date1,$u);
                                // $overall_quote_shared=$CI->Salescrm_model->running_quote_send('2022-01-01',date('Y-m-d'),$u);

                            ?>

                            <tr>
                            <td>QUOTATION SEND (RUNNING CUSTOMER)</td>
                            <!-- <td><a href='<?php echo page_url;?>Customer/running_customer_quotation_dashboard/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $previous_quote_shared;?></a></td> -->
                            <td><a href='<?php echo page_url;?>Customer/running_customer_quotation_dashboard/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $current_quote_shared;?></a></td>
                            <!-- <td><a href='<?php echo page_url;?>Customer/running_customer_quotation_dashboard/2022-01-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $overall_quote_shared;?></a></td> -->
                            </tr>   

                            <?php } ?>


                             <?php 
                            if($row->conversion_step==1)
                            {

                                $start_date=date('Y-m-01',strtotime($from_month));
                                $end_date=date('Y-m-t',strtotime($start_date));

                                $start_date1=date('Y-m-01',strtotime($to_month));
                                $end_date1=date('Y-m-t',strtotime($start_date1));

                                if($this->uri->segment(5)<>'ALL')
                                {
                                $u=$this->uri->segment(5);
                                }else
                                {
                                $u="ALL";
                                }

                                $previous_quote_shared=$CI->Salescrm_model->running_customer_orders($start_date,$end_date,$u);
                                $current_quote_shared=$CI->Salescrm_model->running_customer_orders($start_date1,$end_date1,$u);
                                // $overall_quote_shared=$CI->Salescrm_model->running_customer_orders('2022-01-01',date('Y-m-d'),$u);

                            ?>
                            <tr>
                            <td>RUNNING CUSTOMER ORDERS CREATED</td>
                            <!-- <td><a href='<?php echo page_url;?>Daily_report/total_billed/<?php echo $start_date;?>/<?php echo $end_date;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $previous_quote_shared;?></a></td> -->
                            <td><a href='<?php echo page_url;?>Daily_report/total_billed/<?php echo $start_date1;?>/<?php echo $end_date1;?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $current_quote_shared;?></a></td>
                            <!-- <td><a href='<?php echo page_url;?>Daily_report/total_billed/2022-01-01/<?php echo date('Y-m-d');?>/<?php echo $u;?>' style="color:black;font-weight:bold;text;text-decoration: underline;" target="_blank"><?php echo $overall_quote_shared;?></a></td> -->
                            </tr>   

                            <?php } ?>


                        <?php } } ?>

                        </tbody>
                        </table>
                    </div>

                    <div class="col-md-12">
                        
                        <table class="table table-bordered">
   

        <?php 


        $start_date=date('Y-m-01',strtotime($from_month));
        $end_date=date('Y-m-t',strtotime($start_date));

        $start_date1=date('Y-m-01',strtotime($to_month));
        $end_date1=date('Y-m-t',strtotime($start_date1));

        if($this->uri->segment(5)<>'ALL')
        {
        $u=$this->uri->segment(5);
        }else
        {
        $u="ALL";
        }

        $previous_moth_delegation_report=$CI->Salescrm_model->delegatedtaskreport($start_date,$end_date,$u);
        $current_moth_delegation_report=$CI->Salescrm_model->delegatedtaskreport($start_date1,$end_date1,$u);
        $overall_delegation_report=$CI->Salescrm_model->delegatedtaskreport('2022-01-01',date('Y-m-d'),$u);



        ?>
    <tbody>
     
     
       
    </tbody>
  </table>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-6">
                            <h4 class="text-center">OVERDUE AMOUNT</h4><hr>
                            <div class="row">
                               <table class="table table-bordered">
                                    <thead>
                                        <th style="text-align: center;">Count</th>
                                        <th style="text-align: center;">Total Amount</th>
                                    </thead>
                                    <?php 


        $start_date=date('Y-m-01',strtotime($from_month));
        $end_date=date('Y-m-t',strtotime($start_date));

        $start_date1=date('Y-m-01',strtotime($to_month));
        $end_date1=date('Y-m-t',strtotime($start_date1));

        if($this->uri->segment(5)<>'ALL')
        {
        $u=$this->uri->segment(5);
        }else
        {
        $u="ALL";
        }

        $totalnumberofoverdues=$CI->Salescrm_model->all_payment_overdue_list($u);
        
        //echo "<pre>"; print_r($totalnumberofoverdues); exit;


        ?>
                                    <tbody>
                                        <tr>
                                            <?php if($u<>'ALL'){
                                                $user = $u;
                                            }else{
                                                $user = "";
                                            }?>
                                            <td style="text-align: center;"><a href="<?php echo page_url;?>Customer/payment_overdue/<?php echo $user;?>"><?php 
                                            echo count($totalnumberofoverdues);

                                        ?></a></td>
                                        <?php if(count($totalnumberofoverdues)>0){
                                                $finalval = array_sum($totalnumberofoverdues);
                                        }else{
                                            $finalval = 0;
                                        }
                                        ?>
                                            <td style="text-align: center;"><a href="<?php echo page_url;?>Customer/payment_overdue/<?php echo $user;?>"><?php echo round($finalval,2);?></a></td>
                                        
                                        </tr>
                                    </tbody>
                               </table>
                            </div>
                        </div>

                    </div>
                </div>
                </div>

<?php if($this->uri->segment(5)<>'ALL'){?>
    
            <!-- end row -->
          
            <div class="row" style="display: none;">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <p style="font-size:14px;font-weight:bold;color:red;">USER CONVEYANCE APPROVAL</p>
                            <table id="example3" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO.</th>
                                    <th>PERIOD</th>
                                    <th>USER</th>
                                    <th>TYPE</th>
                                    <th>START READING</th>
                                    <th>END READING</th>
                                    <th>PER KM RATE</th>
                                    <th>NET KM(s)</th>
                                    <th>PETROL USED</th>
                                    <th>MISC CHARGES</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>VEHICLE AVERAGE</th>
                                    <th>VIEW BIFURCATION</th>
                                    <th>HOD STATUS</th>
                                
                                
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
<?php }?>
            <!-- Footer -->
            <?php $this->load->view('common/footer'); ?>
            <!-- End Footer -->


        </div> <!-- end container -->
    </div>
    <!-- end wrapper -->


    <!-- jQuery  -->
    <script src="<?php echo assets_url; ?>js/jquery.min.js"></script>
   
    <script src="<?php echo assets_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo assets_url; ?>js/detect.js"></script>
    <script src="<?php echo assets_url; ?>js/fastclick.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.slimscroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.blockUI.js"></script>
    <script src="<?php echo assets_url; ?>js/waves.js"></script>
    <script src="<?php echo assets_url; ?>js/wow.min.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.nicescroll.js"></script>
    <script src="<?php echo assets_url; ?>js/jquery.scrollTo.min.js"></script>

     <!-- Datatables-->
        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>
        <?php   if($_SESSION['logged_in']['role']==7 || $_SESSION['logged_in']['role']==1){ ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>
    <?php } ?>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>

  
     <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    
    <script>
        $(document).ready(function() {
            
        
            $('#example').dataTable({
                "bProcessing": true,
                "pagination": true,
                "deferRender": true,
                 dom: 'lBfrtip',
        buttons: [
         {
                extend: 'excelHtml5',
                title: 'Lead Data Dump'
            }

        
        ],

            "sAjaxSource": "<?php echo page_url; ?>Billing/tds_input_report_list/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>",
                "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'billing_company'},
                        { mData: 'lead_manager' },
                        { mData: 'company_name' },
                        { mData: 'customer_name' },
                        { mData: 'invoice_no' },
                        { mData: 'product' },
                        { mData: 'basic_order_amount' },
                        { mData: 'gst_order_amount' },
                        { mData: 'order_amount' },
                        { mData: 'tds_per' },
                        { mData: 'tds_input' }
                    

                ]
            });


           
        });



function validate()
{

    var isValid=0;
    $(".mand").each(function() {
    var element = $(this).val();
    if (element=="") {

    isValid=1;
    }
    });


    if(isValid==0)
    {
    return true;
    }else
    {
    alert('Fields marked with (*) are mandatory');
    return false;
    }
    
}

$( document ).ready(function() {

$('.select8').select2({});
});
    </script>
<?php if($this->uri->segment(5)<>'ALL'){?>
    <script type="text/javascript">
    $( document ).ready(function() {
$('#example3').dataTable({
 "bProcessing": false,
 "pagination":true,
 "sAjaxSource": "<?php echo page_url;?>Sales/conveyancecommonreporting/1/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>",
 "aoColumns": [
                        { mData: 'sr_no' } ,
                        { mData: 'period' },
                        { mData: 'user' },
                        { mData: 'type' },
                        { mData: 'start_reading' },
                        { mData: 'end_reading' },
                        { mData: 'per_km_rate' },
                        { mData: 'net_km' },
                        { mData: 'petrol_used' },
                        { mData: 'misc_charges' },
                        { mData: 'total_amount' },
                        { mData: 'average' },
                        { mData: 'bifurcation' },
                        { mData: 'status' }

            
                        
                ]
        }); 
        }); 


</script>
<?php }?>
<script>
    function save_month_target()
    {
        var target=$("#target").val();
        var period=$("#period").val();
        var previous_bal=$("#previous_bal").val();

        $.ajax({
                type: "post",
                url: "<?php echo page_url;?>Sales_stats_reporting/save_target",
                data: "target="+target+"&agent=<?php echo $this->uri->segment(5);?>&period="+period+"&previous_bal="+previous_bal,
                success: function(data) {
                var total=parseFloat(previous_bal)+parseFloat(target);
                $("#total_target").val(total);
                }
                });


    }
</script>

</body>

</html>
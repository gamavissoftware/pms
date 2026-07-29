<?php
$basic_terms="Price Basis : Ex - factory Faridabad<br/>Packing and Forwarding : @3% Extra.<br/>Freight : Extra at Actuals<br/>Delivery : 2 Daysfrom the date of written confirmation with Advance Payment.<br/>Payment Terms : 100% advance with PO";
// echo base64_decode($this->uri->segment(4));exit;
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$MI = &get_instance();
$MI->load->model('Master_model', 'master');
$DI = &get_instance();
$DI->load->model('Dashboard_model');
// $settingval = $DI->Dashboard_model->getsettings();
// if (count($settingval) > 0) {
//   $mode = $settingval['mode'];
// } else {
//   $mode = 1;
// }

$pistep = $DI->Dashboard_model->getsinglePIstep();
$quotestep = $DI->Dashboard_model->getsingleQuotestep();

// $quotation_name = $CI->salescrm->getsettings();
// if (count($quotation_name) > 0) {
//   $quotefile = $quotation_name['quotefile'] . '.php';
//   $pifile = $quotation_name['pifile'] . '.php';
// } else {
//   $quotefile = '';
//   $pifile = '';
// }

$productdetail=$DI->Dashboard_model->getproductdetails($this->uri->segment(3));
$getLeadProducts=$CI->salescrm->getLeadProducts($this->uri->segment(3));
$getLeadStatus = $CI->salescrm->getLeadStatus($this->uri->segment(3));
$getAllStates = $CI->salescrm->getAllStates();

if ($getLeadStatus != '') {

  foreach ($getLeadStatus as $status);

  $lead_id = $status->lead_id;

  $lead_name = $status->lead_name;
} else {

  $lead_id = '';

  $lead_name = '';
}

$getPreviousSchedule = $CI->salescrm->getPreviousSchedule($this->uri->segment(3));

$getMeetingParticipants = $CI->salescrm->getMeetingParticipants($this->uri->segment(3));

$quotesend = $CI->salescrm->checkifavailable($this->uri->segment(3), $quotestep);
$pisend = $CI->salescrm->checkifavailable($this->uri->segment(3), $pistep);


$getLeadType = $CI->salescrm->getLeadType($lead_id);
$getLeadAttachment = $CI->salescrm->getLeadAttachment($this->uri->segment(3));



if ($getLeadType != '') {

  // echo "hi";exit;

  foreach ($getLeadType as $lead_type);

  if ($lead_id == 2) {

    $color = "<span class='btn btn-xs pull-right' style='background-color:#FAC898; font-weight:bold; color:white;font-size:14px;'>" . $lead_type->lead_type . "</span>";
  } else if ($lead_id == 4) {

    $color = "<span class='btn btn-xs pull-right' style='background-color:#77dd77; font-weight:bold; color:white;font-size:14px;'>" . $lead_type->lead_type . "</span>";
  } else if ($lead_id == 6) {

    $color = "<span class='btn btn-success btn-xs pull-right' style='background-color:#03c03c; font-weight:bold; color:white;font-size:14px;'>" . $lead_type->lead_type . "</span>";
  } else {

    $color = '';
  }
} else {

  if ($lead_id == 7 || $lead_id == 9) {



    $color = "<span class='btn btn-xs pull-right' style='background-color:#77dd77; font-weight:bold; color:white'>Warm Lead</span>";
  } else {

    $color = '';
  }
}



// echo $color;exit;



$checkLeadAssignment = $CI->salescrm->checkLeadAssignment($this->uri->segment(3));
$color = '';



$getOnlyQuotationSentID = $CI->salescrm->getOnlyQuotationSentID();
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

  <!-- DataTables -->

  <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css">

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

  <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>

  <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />

  <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">

  <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">



  <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

  <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->



  <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

  <style>

     .marginbottom {

      margin-bottom: 20px;

    }



/*    .iii[disabled] {

      pointer-events: none;

      opacity: 0.49;

    }



    .iii i {

      position: absolute;

      top: 50%;

      left: 50%;

      transform: translate(-50%, -50%);

      z-index: 1;

      font-size: 100px;

    }*/

    .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 1;
            top: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid lightgray;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 50px;
            padding-bottom: 50px;
            z-index: 100;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: #818181;
            display: block;
            transition: 0.3s;
        }

        .sidenav a:hover {
            color: #818181;
        }

        .sidenav .closebtn {
            position: absolute;
            top: -15px;
            right: 5px;
            font-size: 36px;
            margin-left: 50px;
        }

        @media screen and (max-height: 450px) {
            .sidenav {
                padding-top: 15px;
            }

            .sidenav a {
                font-size: 18px;
            }
        }

        .todo-box {
            border: 1px solid lightgray;
            border-radius: 5px;
            height: none;
        }

        .all-notes {
            height: 50px;
            padding: 15px;
            border-bottom: 1px solid #cdcdcd;
        }

        .all-notes p {
            font-weight: 600;
        }

        .all-notes i {
            color: #3d48c4;
        }

        .to-list {
            padding: 20px;

        }

        .todo-list {
            margin: 10px 0;
            overflow-y: auto;
            height: 535px;
        }

        .todo-list .todo-item {
            padding: 15px;
            margin: 5px 0;
            border-radius: 0;
            background: #f7f7f7;
        }

          .search-page h3{
font-weight: 600;
        }

        .search-page h3 span{
            background: #fff1ea;
            color: #f9ab00;
            border-radius: 5px;
            padding: 5px;
            font-size: 20px;
        }

        .search-page p{
            color: black;
            /* font-size: 14px; */
            margin: 0;
        }

        .search-page p i{
            margin-right: 10px;
        }

        .search-page h5 {
            font-size: 17px;
            margin: 0px;
            padding: 5px 0px;
            font-weight: 700;
            border-bottom: 1px solid #25272e52;
            margin-bottom: 10px;
        }
        

        .search-page table{
            width: 100%;
          
           
        }

        .search-page table th{
            padding: 5px;
            text-align: center;
            border: 1px solid lightgray;
            color: black;
            background-color: whitesmoke;
        }

        .search-page table td{
            border: 1px solid lightgray;
            padding: 5px;
            text-align: center;
            color: black;
        }

      .select2-container {
        width: 100% !important;
      }
  </style>


</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>

  <!-- End Navigation Bar-->
<!--------------------------------NOTES-------------------------------------->
<!-- 
<div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="todo-box">
                        <div class="all-notes">
                            <div class="row">
                                <div class="col-sm-12">
                                    <p><i class="fa fa-list" aria-hidden="true"></i> Add Notes</p>
                                </div>
                            </div>
                        </div>
                        <div class="to-list">
                            <form>
                                <input type="text" class="form-control">
                            </form>

                            <div class="todo-list">
                                <div class="todo-item">

                                    <span>Create theme</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">
                                    <span>Work
                                        on wordpress</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                                <div class="todo-item">

                                    <span>Organize office main department</span> <a href="javascript:void(0);"
                                        class="float-right remove-todo-item"><i class="icon-close"></i></a>
                                </div>
                              
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div> -->
<!--     <span style="font-size: 24px;
    cursor: pointer;
    position: fixed;
    right: 2px;
    background: #4241bf;
    width: 65px;
    font-weight: 900;
    color: white;
    text-align: center;
    padding: 5px 10px;
    border-radius: 30px 0px 0px 30px;" onclick="openNav()"><i class="fa fa-sticky-note" aria-hidden="true"></i></span> -->
<!--------------------------------NOTES-------------------------------------->

  <div class="wrapper">
    <div class="container">
      <!-- Page-Title -->
      <div class="row">

        <div class="col-sm-12" >
        <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btn-xs" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>

          <div class=" " style="margin-top: 30px;">
            <div class="profile-info-name">
              <?php
              $user_role = $this->session->userdata['logged_in']['role'];

              $query = $this->db->select('a.*,d.zone,b.country_id, b.country_name, c.state_name, e.companyname as company_location, g.first_name, g.last_name')
                                ->from('leads a')
                                ->join('countries b', 'a.country=b.country_id', 'left')
                                ->join('saleszone d','a.client_location=d.id','left')
                                ->join('store_rack_location e','e.id=a.hpcl_company','left')
                                ->join('states c', 'a.state=c.state_id', 'left')
                                ->join('lead_assigned_to_team_member f', 'f.lead_id=a.id')
                                ->join('system_users g', 'g.user_id=f.member_id')
                                ->where('a.id', $this->uri->segment(3))
                                ->get();

                 // echo "<pre>";print_r($query->result());

              foreach ($query->result() as $customer_detail)
                $hpcl_company = $customer_detail->hpcl_company;
                //echo "<pre>"; print_r($customer_detail);exit;

              ?>

              <div class="profile-info-detail" style="margin-top:1px">
                <?php
              if ($customer_detail->country_code == '91') {
                $customer_contact_number = $customer_detail->contact_no;
              } else {
                $customer_contact_number = "00" . $customer_detail->country_code . $customer_detail->contact_no;
              }

              if($customer_detail->validity_date == '0000-00-00' || $customer_detail->validity_date == '1970-01-01') {
                  $validity_date = '';
              } else {
                  $validity_date = date('d-m-Y',strtotime($customer_detail->validity_date));
              }

                // echo 'hi'.$quotesend;exit;
                if($quotesend == 0) {
                  $check_terms = 0;
                  $drums_tnc = $MI->master->getAllTnC(1);
                  $bulk_tnc = $MI->master->getAllTnC(2);
                } else {
                  $check_terms=$customer_detail->check_terms;
                  $drums_tnc=$customer_detail->general_terms;
                  $bulk_tnc=$customer_detail->bulk_terms;
                }

              $call = base64_encode($customer_contact_number);
                ?>

                <div class="text-center card-box search-page">
                  <h3 class="m-t-0 m-b-0">
                    <?php echo ucwords($customer_detail->company_name); ?>&nbsp;<span>(<?php echo $customer_detail->unique_id; ?>)</span>&nbsp;&nbsp;&nbsp;<?php echo $color; ?></h3>
                  <br />
                  <div class="text-center">

                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-5">
                    <div class="card-box search-page">
                      <h5>Overview</h5>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Create Date:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo date('d-m-Y', strtotime($customer_detail->create_date)); ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Company Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo ucwords($customer_detail->company_name); ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong><i class="fa fa-calendar" aria-hidden="true" style="color:#f9ab00;"></i>Customer Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo ucwords($customer_detail->customer_name); ?></p>
                        </div>
                      </div>


                      <!---------------------------------------------------------->

                      <?php
                      $user_id = $this->session->userdata['logged_in']['user_id'];
                      $query = $this->db->select('user_id')->from('system_users')->where('user_id', $user_id)->get();
                      $res = $query->result();

                      if ($user_role == '1') { ?>
                          <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-envelope" aria-hidden="true" style="color:#f9ab00;"></i>Contact Person Details:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->contact_person; ?>&nbsp;<?php echo $customer_detail->email; ?>&nbsp;<?php echo $customer_detail->contact_no; ?></p>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-envelope" aria-hidden="true" style="color:#f9ab00;"></i>Alternate Contact Person Details:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->alt_contact; ?>&nbsp;<?php echo $customer_detail->alt_contact_no; ?></p>
                          </div>
                        </div>


                          <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-envelope" aria-hidden="true" style="color:#f9ab00;"></i>Address</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->postal_address; ?>&nbsp;<?php echo $customer_detail->city; ?></p>
                          </div>
                        </div>
                      
                        <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-user" aria-hidden="true" style="color:#f9ab00;"></i>Lead Added By: </strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->first_name." ".$customer_detail->last_name;?></p>
                          </div>
                        </div>
                      <?php } else if ($res) { ?>
                        <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-envelope" aria-hidden="true" style="color:#f9ab00;"></i>Email:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->email; ?></p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong><i class="fa fa-phone" aria-hidden="true" style="color:#f9ab00;"></i>Contact Number: </strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->country_code; ?><?php echo $customer_detail->contact_no; ?></p>
                          </div>
                        </div>
                      <?php } ?>

                      <hr>
                      <!------------------------------------------>

                      <?php
                      $sql = $this->db->select('a.patient_type')
                        ->from('patient_type a')
                        ->join('leads b', 'b.patient_type_id=a.patient_id')
                        ->where('a.status', 1)
                        ->where('b.id', $this->uri->segment(3))
                        ->get();

                      if ($sql->num_rows() > 0) {
                        foreach ($sql->result() as $patient_type);
                        $patient_type = $patient_type->patient_type;
                      } else {
                        $patient_type = '';
                      }

                      ?>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Customer Type:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $patient_type; ?> </p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Distributor Name:</strong></p>
                        </div>
                        <?php 
                            if($customer_detail->distributor>0)
                            {
                            $distributorName=$this->salescrm->getdistributorName($customer_detail->distributor);
                            }else{
                            $distributorName='';
                            }
                        ?>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $distributorName; ?> </p>
                        </div>
                      </div>


                      <?php $query = $this->db->select('a.lead_source, b.city')
                        ->from('lead_source a')
                        ->join('leads b', 'b.lead_source_id=a.source_id')
                        ->where('a.status', '1')
                        ->where('b.id', $this->uri->segment(3))
                        ->get();

                      foreach ($query->result() as $lead_source); ?>

                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Lead Source:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $lead_source->lead_source; ?></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>City:</strong>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $customer_detail->city; ?></p>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Email:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $customer_detail->email_id; ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Media Attachment:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $getLeadAttachment; ?></p>
                        </div>
                      </div>

                      
                     <!--  <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Contact No:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $customer_detail->country_code; ?><?php echo $customer_detail->contact_no; ?></p>
                        </div>
                      </div> -->




                      <div class="clearfix"></div>
                    </div>

                  </div>
                  <div class="col-sm-7">
                    <div class="card-box search-page" style="height: 305px; overflow-y: auto;">
                      <h5>Products</h5>
                       <table>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Our Product</th>
                                            <th>Qty</th>
                                            <!-- <th>Actual Product Value</th> -->
                                        </tr>

                                        <?php
                                        $prip=array();
                                        $prip[]=0;
                                        if(count($productdetail)>0)
                                        { 
                                            for($i=0;$i<count($productdetail);$i++)
                                            {
                                                if($productdetail[$i]['qty']==0)
                                                {
                                                    $qty=1;
                                                }else
                                                {
                                                    $qty=$productdetail[$i]['qty'];
                                                }

                                                if($productdetail[$i]['packsize']==1)
                                                {
                                                    $pack="Drum";
                                                }else if($productdetail[$i]['packsize']==2)
                                                {
                                                    $pack="Bucket";
                                                }else
                                                {
                                                    $pack="Bulk";
                                                }

                                                // $prip[]=$productdetail[$i]['value']*$qty;
                                        ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php echo $productdetail[$i]['name'];?></td>
                                            <td><?php echo $qty;?> <?php echo $productdetail[$i]['packsize'];?></td>
                                           <!--  <td><?php //echo floatval($productdetail[$i]['value']);?></td> -->
                                        </tr>
                                        <?php  
                                        }                                       
                                        ?>
                                       
                                    <?php }else{ ?>

                                            <tr>
                                            <td colspan="3">No Product Available</td>
                                        
                                    
                                            </tr>

                                    <?php } ?>
                                    </table>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



            <?php

              if ($checkLeadAssignment == 0) {

                $disabled = "disabled"; ?>

                <div class="text-center">

                  <span style="    position: relative;

                                top: 200px;

                                font-size: 13px;

                                font-weight: 700;">Followup will be Activated Once Lead is Assigned to a Team Member</span>

                </div>

            <?php } else {

                $disabled = "";
              }

            ?>



            <!------------------To disable Folloup Use disabled attribute--------------------->

            <div class="iii" <?php
                                echo $disabled;
                               ?>>

              <?php
                if ($checkLeadAssignment == 0) { ?>

                  <i class="fa fa-lock" aria-hidden="true"></i>

              <?php } ?>

            <form method="post" class="card-box" method="post" action="<?php echo page_url; ?>Leads/update_remarks/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>" enctype="multipart/form-data" onsubmit="return validate()">
                <div class="row">
                  <div class="col-md-3">
                    <label>Previous Status</label>
                    <input type="text" class="form-control" name="previous_status" id="previous_status" value="<?php echo $lead_name; ?>" readonly>
                     <input type="hidden" name="previous_lead_stage" value="<?php echo $lead_id;?>">
                     <input type="hidden" name="hpcl_company_hidden" value="<?php echo $customer_detail->hpcl_company;?>">

                    <label>Lead Stage<span style="color: red">*</span></label>
                    <select class="form-control mand" name="leadquality" id="leadquality" onchange="getFields(); removeFollowup(this.value); checkvisitstage(); checkdemostage(); checkdistributorwon();" required>
                      <option value="">Change Lead Stage</option>
                      <?php $getLeadStageRelation = $CI->salescrm->getLeadStageRelation($lead_id);

                      if ($getLeadStageRelation != '') {
                        foreach ($getLeadStageRelation as $row1) { 
                         // $checkavai=$DI->Dashboard_model->checkifroleassigned($row1->lead_id,$_SESSION['logged_in']['role']);
                         // if($checkavai>0)
                          //{
                          ?>
                          <option value="<?php echo $row1->lead_id; ?>"><?php echo $row1->lead_name; ?></option>
                      <?php }
                      //}
                      }

                      ?>
                    </select>

                    <script type="text/javascript">
                        function checkdistributorwon(){
                             $("#distrinutororderbox :input").removeClass('mand');
                           var lead_stage_id = $("#leadquality").val(); 
                           if(lead_stage_id==27){
                            $("#distrinutororderbox").show();
                            $("#distrinutororderbox :input").addClass('mand');
                           

                           }else{
                            $("#distrinutororderbox").hide();
                            $("#distrinutororderbox :input").removeClass('mand');
                           }
                        }

                        function checkvisitstage(){
                            var lead_stage_id = $("#leadquality").val();
                            if(lead_stage_id==18){
                                $("#showvisitschedulediv").show();
                                $("#schedulevisitdate").attr('required',true);
                                $("#showvisitscheduletimediv").show();
                                $("#visittime").attr('required',true);
                            }else{
                                $("#showvisitschedulediv").hide();
                                $("#showvisitscheduletimediv").hide(); 
                                $("#schedulevisitdate").attr('required',true);
                                $("#visittime").attr('required',true);

                            }
                        }

                        function checkdemostage(){
                            var lead_stage_id = $("#leadquality").val();
                            if(lead_stage_id==20){
                                $("#democheduledate").show();
                                $("#democheduledate").attr('required',true);
                            }else{
                                 $("#democheduledate").hide();
                                  $("#democheduledate").attr('required',false);
                            }
                            
                        }
                    </script>

                     <div id="democheduledate" style="display:none;">
                        
                                <label>Demo Schedule Date</label>
                                <input class="form-control" name="democheduledate" id="democheduledate" type="date" value="<?php echo date('Y-m-d'); ?>">
                           
                    </div>
                    
                     <div id="showvisitschedulediv" style="display:none;">
                        
                                <label>Visit Date</label>
                                <input class="form-control" name="schedulevisitdate" id="schedulevisitdate" type="text" value="<?php echo date('d-m-Y'); ?>">
                           
                    </div>

                     <div id="showvisitscheduletimediv" style="display:none;">
                        
                                <label>Visit Time</label>
                                <input class="form-control" name="visittime" id="visittime" type="text" value="<?php echo date('h:i A'); ?>">
                           
                    </div>

                    <div id="followup" style="display: none;">
                      <label>Followup Date<span style="color: red">*</span></label>
                      <input class="form-control" name="followup_date" id="followup_date" type="text" value="<?php echo date('d-m-Y'); ?>">
                    </div>

                    <div id="nonqualifiedreason" style="display: none;">
                      <label>State Reason<span style="color: red">*</span></label>
                      <select class="form-control" name="nonqualifiedreason" id="reasons">
                        <option value="">Select Reason</option>

                        <?php
                        $q = $this->db->select('reason_id,reason')->from('leads_unqualified_reason')->where('status', 1)->get();
                        if ($q->num_rows() > 0) {
                          foreach ($q->result() as $reason) {
                        ?>
                            <option value="<?php echo $reason->reason_id; ?>"><?php echo $reason->reason; ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>

                    <?php

                    if ($getPreviousSchedule > 0) {
                      foreach ($getPreviousSchedule as $rowp);
                      $previous_meeting_date = $rowp->meeting_date;
                      $previous_meeting_time = $rowp->meeting_time;
                      $previous_reminder_date = $rowp->reminder_date;
                      $previous_reminder_time = $rowp->reminder_time;
                    } else {
                      $previous_meeting_date = '';
                      $previous_meeting_time = '';
                      $previous_reminder_date = '';
                      $previous_reminder_time = '';
                    } ?>

                  </div>

                  <div class="col-md-9" style="margin-bottom:10px">
                    <span class="input-icon icon-right">
                      <input type="text" class="form-control" name="remark_title" id="remark_title" readonly>
                    </span>
                  </div>

                  <div class="col-md-9" style="margin-bottom:10px">
                    <span class="input-icon icon-right">
                      <label id="rmkdynamic">Remarks<span style="color: red"></span></label>
                      <textarea rows="2" name="remarks" id="remarks" class="form-control" placeholder="Remarks"></textarea>
                    </span>
                  </div>

                  <div class="col-md-4">
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                      <input type="file" name="upload_file[]" multiple>
                    </span>
                  </div>
              </div>

                    <div id="distrinutororderbox" style="display:none">

                        <?php $q= $this->db->select('a.product_id, b.instruments_name, b.mvalue, b.unit')->from('lead_demo_feedback a')->join('presto_instruments b','a.product_id=b.id','left')->where('a.lead_id',$this->uri->segment(3))->get();
                        if($q->num_rows()>0){
                            foreach($q->result() as $demoproduct){?>
                        <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <span style="color:red">*</span>
                                        <input type="hidden" name="demoproductid[]" id="productids" value="<?php echo $demoproduct->product_id;?>">
                                        <input type="text" class="form-control" name="productname[]" id="productname" value="<?php echo $demoproduct->instruments_name;?>" readonly>
                                    </div>
                                </div>

                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Unit</label>
                                        <span style="color:red">*</span>
                                        <input type="text" class="form-control" name="unit[]" id="unit" value="<?php echo $demoproduct->unit;?>" readonly>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Order Qty</label>
                                        <span style="color:red">*</span>
                                        <input type="text" class="form-control" name="orderqty[]" onchange="calculatewonprice(<?php echo $demoproduct->product_id;?>);" id="orderqty<?php echo $demoproduct->product_id;?>" value="">
                                    </div>
                                </div>

                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Item Price</label>
                                        <span style="color:red">*</span>
                                        <input type="text" class="form-control" name="itemprice[]" id="itemprice<?php echo $demoproduct->product_id;?>" onkeyup="calculatewonprice(<?php echo $demoproduct->product_id;?>);" value="<?php echo $demoproduct->mvalue;?>">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Total Amount</label>
                                        <span style="color:red">*</span>
                                        <input type="text" class="form-control" name="totalamount[]" id="totalamount<?php echo $demoproduct->product_id;?>" value="" readonly>
                                    </div>
                                </div>

                        </div>
                    <?php }
                }?>
                    </div>

                    <script type="text/javascript">
                        function calculatewonprice(i) {
                           var orderqty = $("#orderqty"+i).val();
                           var itemprice = $("#itemprice"+i).val();
                           if(orderqty>0){
                            var finalvalue = parseInt(orderqty)*parseInt(itemprice);
                            $("#totalamount"+i).val(finalvalue);
                           }
                        }
                    </script>


                <div class="product_details" style="display: none;">
                  <!-- <br><hr><br> -->
                  <div class="row">
                      <div class="col-md-12">
                          <div class="col-md-3">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">Company Name<span style="color:red">*</span></label>
                                  <span id="error_state" style="color:red;"></span>
                                  <input type="text" class="form-control" value="<?php echo $customer_detail->company_name; ?>" readonly>

                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">Customer Name</label>
                                  <span id="error_rack_location" style="color:red;">*</span>
                                  <input type="text" class="form-control" value="<?php echo $customer_detail->customer_name; ?>" readonly>
                              </div>
                          </div>

                          <div class="col-md-3">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">Company Location</label>
                                  <span id="error_rack_location" style="color:red;">*</span>
                                  <input type="text" class="form-control" name="hpcl_company" id="hpcl_company" value="<?php echo $customer_detail->company_location; ?>" readonly>

                              </div>
                          </div>
                          <div class="col-md-3">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">Validity Date</label>
                                  <span id="error_rack_location" style="color:red;">*</span>
                                  <input type="text" class="form-control datepicker" name="validity_date" id="validity_date" value="<?php echo $validity_date;?>" autocomplete="off">
                              </div>
                          </div>
                    </div>
                  </div>
                  <?php if($getLeadProducts != '') { 
                          foreach($getLeadProducts as $row5) {?>
                  <div class="col-md-12">
                    <div class="col-md-3" style="display:none;">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Competitor Product</label>
                              <select class="form-control" name="comp_product_edit<?php echo $row5->id;?>" id="comp_product_edit<?php echo $row5->id;?>">
                                <option value="NA">NA</option>
                                   <!-- <option value="<?php echo $row5->competitor_product;?>"><?php echo $row5->competitor_product;?></option> -->
                              </select>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Product</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="hidden" name="editid[]" value="<?php echo $row5->id;?>">
                              <select class="form-control" name="productedit<?php echo $row5->id;?>" id="productedit<?php echo $row5->id;?>" onchange="geteditprice(<?php echo $row5->id;?>,this.value);">
                                <option value="">Select</option>
                                <?php 
                        
                            $sql1 = $this->db->select('b.id, b.instruments_name, b.pack_size')

                            ->from('presto_instruments b', 'b.id=a.product_id')
                            ->where('b.status',1)
                            ->get();

                            
                            if($sql1->num_rows()>0) {
                                foreach($sql1->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>" <?php if($row4->id == $row5->product_id) { echo 'selected' ;}?>><?php echo $row4->instruments_name;?></option>
                                <?php } } ?>
                              </select>
                              <span id="recommendation_edit<?php echo $row5->id;?>"></span>
                          </div>
                      </div>
                      <?php 
                      if($row5->packsize==1)
                      {
                        $p="Drum";

                      }else if($row5->packsize==2)
                      {
                        $p="Bucket";

                      }else
                      {
                        $p="Bulk";
                      }

                      if($lead_id == $getOnlyQuotationSentID) {
                        $pp = '';
                      } else {
                        $pp = $p;
                      }
                      ?>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Qty</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control"  name="qtyedit<?php echo $row5->id;?>" id="qtyedit<?php echo $row5->id;?>" value="<?php echo $row5->qty;?>" oninput="allow_decimal('qtyedit<?php echo $row5->id;?>');">
                              

                          </div>
                      </div>


                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Unit</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text"  readonly class="form-control"  name="unitedit<?php echo $row5->id;?>" id="unitedit<?php echo $row5->id;?>" value="<?php echo $row5->shortname;?>">
                              <input type="hidden" name="unit_id_edit<?php echo $row5->id;?>" value="<?php echo $row5->unit_id;?>" id="unit_id_edit<?php echo $row5->id;?>">
                          </div>
                      </div>

                      <?php //$discountpricehideedit = $CI->master->getdiscountpriceeditdata($row5->id);?>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit<?php echo $row5->id;?>"><?php echo $row5->shortname;?></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control" name="listpriceedit<?php echo $row5->id;?>" id="listpriceedit<?php echo $row5->id;?>" value="<?php echo $row5->price;?>" onblur="getnetamtedit(<?php echo $row5->id;?>)" oninput="allow_decimal('listpriceedit<?php echo $row5->id;?>');">
                              <p id="discountpriceshowedit<?php echo $row5->id;?>" style="color:red;font-weight:bold">Allowed Price: <?php echo $row5->discount_price;?></p>
                              <input type="hidden" name="discountpricehideedit<?php echo $row5->id;?>" value="<?php echo $row5->discount_price;?>" id="discountpricehideedit<?php echo $row5->id;?>">
                          </div>
                      </div>



                      <div class="col-md-2" style="display: none;">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Discount Price Per <?php echo $row5->unit;?><span></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control" name="discountpriceedit<?php echo $row5->id;?>" id="discountpriceedit<?php echo $row5->id;?>" value="0" onblur="getnetamtedit(<?php echo $row5->id;?>);" oninput="allow_decimal('discountpriceedit<?php echo $row5->id;?>');" value="<?php echo $row5->percent_amt;?>">
                              

                          </div>
                      </div>

                   <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Net Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="netpriceedit<?php echo $row5->id;?>" id="netpriceedit<?php echo $row5->id;?>" value="0" readonly value="<?php echo $row5->net_price;?>">

                      </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group" style="margin-top:25px">
                            <a href="<?php echo page_url; ?>Leads/delete_lead_product/<?php echo $row5->id;?>/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" style="font-size: 27px; color: red;"></i></a>
                        </div>
                    </div>
                     </div>
                   <?php } }?>

                     <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Add More Products</label>
                                <input type="checkbox" name="add_product" id="add_product" value="1" onchange="add_product_data();">
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12" style="display:none;" id="shownewproduct">
                      <div class="col-md-2" style="display:none">
                          <div class="form-group">
                              <label for="field-1" class="control-label">Competitor Product</label>
                              <select name="competitor_product[]" id="competitor_product0" onchange="getourproductname(0);">
                              </select>
                             <!--  <input type="text" name="competitor_product[]" id="competitor_product0" autocomplete="nope" class="mand search-box"> -->
                           
                          </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="field-3" class="control-label">Product</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <select name="product[]" id="product0" onchange="getprice(0,this.value);getdiscount(0,this.value);getstock(0);match_stock_availability2(0);">
                                    <option value="">Select</option>
                                     <?php 
                                $query = $this->db->select('b.id,b.instruments_name,b.pack_size')
                                                  ->from('presto_instruments b')
                                                  ->where('b.status',1)
                                                  ->get();
                            
                            if($query->num_rows()>0) {
                                foreach($query->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?></option>
                                  <?php } } ?>
                                </select>
                                <span id="recommendation0"></span>
                            </div>
                        </div>

                         <div class="col-md-1">
                                <div class="form-group">
                                <label for="field-2" class="control-label">Stock Avail.</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" readonly class="form-control"  id="stock0" value="">

                                </div>
                                </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Qty</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="qty[]" id="qty0" oninput="allow_decimal('qty0');" onkeyup="match_stock_availability2(0);">

                            </div>
                        </div>

                        <div class="col-md-1">
                          <div class="form-group"> 
                            <label for="field-2" class="control-label">Unit</label> 
                            <span id="error_rack_location" style="color:red;">*</span> 
                            <input type="text" readonly class="form-control" name="unit[]" id="unit0"> 
                            <input type="hidden" name="unit_id[]" id="unit_id0">
                          </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit0"></span></label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="listprice[]" id="listprice0" value=""  onblur="getnetamt(0)" oninput="allow_decimal('listprice0');">
                                <p id="discountpriceshow0" style="display: none;"></p>
                                <input type="hidden" name="discountpricehide[]" value="" id="discountpricehide0">
                            </div>
                        </div>
                        <div class="col-md-2" style="display: none;">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Discount Price Per <span></span></label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" value="0" name="discountprice[]" id="discountprice0"  onblur="getnetamt(0)" oninput="allow_decimal('discountprice0');">
                                

                            </div>
                        </div>
                        <div class="col-md-2" style="display: none;">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Net Price</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="netprice[]" value="0" id="netprice0"  readonly>

                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group" style="margin-top:25px">
                                <a class="btn btn-warning" name="add" id="addmore_btn"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                                <div id="dynamictasks"></div>
                               
                            </div>

                 
                    <div class="row" style="display:none;">
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <label for="field-2" class="control-label">Terms & Conditions Type</label>
                                <span id="error_rack_location" style="color:red;">*</span><br>
                                <input type="radio" name="chk_tnc" value="2" checked onchange="check_tnc()">&nbsp;&nbsp;Terms & Conditions

                                
                            </div>
                        </div>
                    </div>

                    <?php 
                                $ab = "display: none;";
                                $ba = "";

                                if($check_terms == 1) {
                                    $ab = "";
                                } else if($check_terms == 2) {
                                    $ba = "";
                                }

                            ?>

                    <div class="row drums_tnc" style="<?php echo $ba;?>">
                  <div class="col-md-12 remarkbox" style="margin-top:20px;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Terms & Conditions</label>
                          <span id="error_item_name" style="color:red;"></span>
                          <textarea class="form-control drums" name="drums_tnc" id="drums"></textarea>
                      </div>
                  </div>
                  <script>
                  CKEDITOR.replace('drums');
                  </script>
                </div>
                
                <div class="row bulk_tnc" style="<?php echo $ab;?>">
                  <div class="col-md-12 remarkbox" style="margin-top:20px;">
                    <div class="form-group">
                      <label for="field-2" class="control-label">Terms & Conditions(Bulk)</label>
                      <span id="error_item_name" style="color:red;"></span>
                      <textarea class="form-control bulk" name="bulk_tnc" id="bulk" value=""></textarea>
                    </div>
                  </div>
                  <script>
                  CKEDITOR.replace('bulk');
                  </script>
                </div>
                </div>

                <div style="clear:both;height:20px;"></div>

                <div class="col-md-12" id="closewon" style="display:none;">
                  <div class="col-md-4" style="display:none">
                    <div class="form-group">
                        <label for="field-2" class="control-label">Monthly Consumption <span id="error_item_name" style="color:red;">*</span></label>
                        
                        <input type="text" class="form-control" name="monthly_consumption" id="monthly_consumption" autocomplete="nope" value="0">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                        <label for="field-2" class="control-label">Payment Type <span id="error_item_name" style="color:red;">*</span></label>
                       <select name="payment_type" class="form-control" id="payment_type" onchange="checkpdf();cheque_detail_for_adv();">
                            <option value="">Select</option>
                            <option value="2">Cash</option>
                            <option value="3">Online</option>
                            <option value="4">PDC</option>
                            <option value="5">Credit</option>
                            <option value="6">Advance</option>
                       </select>
                    </div>
                  </div>
                  <script type="text/javascript">
                      function checkpdf()
                      {
                        $("#pcd_details").css('display','none');
                        $(".cheque_details").css('display','none');
                        $("#pdcrecv").removeClass('mand');
                        var payment_type=$("#payment_type").val();
                        if(payment_type==4 || payment_type==5)
                        {
                            $("#paymentterms").addClass('mand');
                            $("#credit_days").css('display','');
                        }else
                        {
                             $("#paymentterms").removeClass('mand');
                             $("#credit_days").css('display','none');
                        }

                        if(payment_type==4)
                        {
                          $("#pcd_details").css('display','');
                          $(".cheque_details").css('display','');
                          $("#pdcrecv").addClass('mand');
                        }

                      }
                  </script>

                   <div class="col-md-4" id="credit_days" style="display:none;">
                    <div class="form-group">
                        <label for="field-2" class="control-label">Payment Terms/Credit Days (In Days) <span id="error_item_name" style="color:red;">*</span></label>
                        <input type="number" class="form-control" name="paymentterms" id="paymentterms" min="0" autocomplete="nope">
                    </div>
                  </div>
                    <div class="row">
                      <div class="col-md-4" id="pcd_details" style="display:none;">
                          <div class="form-group">
                              <label>PDC Recieved?</label>
                              <select name="pdcrecv" id="pdcrecv" class="form-control" onchange="cheque_detail();">
                                  <option value="">Select</option>
                                  <option value="1">Yes</option>
                                  <option value="0">No</option>
                              </select>
                          </div>
                      </div>
                      <script>
                          function cheque_detail()
                          {
                               $(".cheque_details").css('display','none');
                                  $("#cheque_no").removeClass('mand');
                                  $("#cheque_date").removeClass('mand');
                                  $("#cheque_no").attr('required',false);
                                  $("#cheque_date").attr('required',false);

                              var pdcrecv=$("#pdcrecv").val();
                              if(pdcrecv==1)
                              {
                                  // alert('hi');
                                  $(".cheque_details").css('display','');
                                  $("#cheque_no").addClass('mand');
                                  $("#cheque_date").addClass('mand');
                                  $("#cheque_no").attr('required',true);
                                  $("#cheque_date").attr('required',true);

                              }

                          }

                          function cheque_detail_for_adv() {
                              var payment_type=$("#payment_type").val();
                                  $(".cheque_details").css('display','none');
                                  $("#cheque_no").removeClass('mand');
                                  $("#cheque_date").removeClass('mand');
                                  $("#cheque_no").attr('required',false);
                                  $("#cheque_date").attr('required',false);

                              if(payment_type==6)
                              {
                                  $(".cheque_details").css('display','');
                                  $("#cheque_no").addClass('mand');
                                  $("#cheque_date").addClass('mand');
                                  $("#cheque_no").attr('required',true);
                                  $("#cheque_date").attr('required',true);

                              }
                          }
                      </script>

                      <div class="col-md-4 cheque_details" style="display:none;">
                          <div class="form-group">
                              <label>Cheque No. <span style="color: red">*</span></label>
                              <input type="text" name="cheque_no" id="cheque_no" class="form-control">
                          </div>
                      </div>

                        <div class="col-md-4 cheque_details" style="display:none;">
                          <div class="form-group">
                              <label>Cheque Date <span style="color: red">*</span></label>
                              <input type="date"  min="<?php echo date('Y-m-d');?>" name="cheque_date" id="cheque_date" class="form-control">
                          </div>
                      </div>
                  </div>

                  <div class="row" style="margin-top:20px;">
                      <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                          <div class="page-title-box">                           
                              <h4 class="page-title text-center">&nbsp; Billing Company</h4>
                          </div>
                      </div>
                    </div>
                     <div class="row">
                      <div class="col-md-4">
                        <label>COMPANY FROM WHICH CLIENT WILL BE BILLED<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <select class="form-control" name="hpcl_company" id="hpcl_company">
                            <?php          $sql3 = $this->db->select('id, companyname')
                            ->from('store_rack_location')
                            ->where('id', $hpcl_company)
                            ->get();

                            if($sql3->num_rows() >  0) {
                            foreach($sql3->result() as $row3);?>   

                            <option value="<?php echo $row3->id;?>" <?php if($row3->id == $hpcl_company) { echo 'selected';}?>> <?php echo $row3->companyname;?> </option>    

                            <?php }?>
                        </select>
                        </span>
                      </div>
                      <div class="col-md-4" style="display:none;">
                        <label>Customer Code<span style="color: red;">*</span></label>
                        <span class="input-icon icon-right" style="margin-bottom:10px">
                        <input type="number" min="0" class="form-control" name="customer_codes" id="customer_codes" value="0">
                        </span>
                      </div>
                  </div>


                  <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp; Order Details</h4>
                        </div>
                    </div>
                  </div>

                  <?php if($getLeadProducts != '') { 
                          foreach($getLeadProducts as $row5) {
                            $stock=$CI->salescrm->get_stock_availability($row5->product_id,$hpcl_company);
                    ?>
                  <div class="col-md-12">
                    <div class="col-md-2" style="display:none">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Competitor Product</label>
                              <select class="form-control" name="comp_product_edit1<?php echo $row5->id;?>" id="comp_product_edit1<?php echo $row5->id;?>">
                                <option value="NA">NA</option>
                                   <!-- <option value="<?php echo $row5->competitor_product;?>"><?php echo $row5->competitor_product;?></option> -->
                              </select>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-3" class="control-label">Product</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="hidden" class="editid1" name="editid1[]" value="<?php echo $row5->id;?>">
                              <select class="form-control" name="productedit1<?php echo $row5->id;?>" id="productedit1<?php echo $row5->id;?>" onchange="geteditprice(<?php echo $row5->id;?>,this.value);">
                                <?php 
                        
                            $sql1 = $this->db->select('id, instruments_name, pack_size')
                                             ->from('presto_instruments')
                                             ->where('id', $row5->product_id)
                                             ->where('status',1)
                                             ->get();

                            
                            if($sql1->num_rows()>0) {
                                foreach($sql1->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>" <?php if($row4->id == $row5->product_id) { echo 'selected' ;}?>><?php echo $row4->instruments_name;?></option>
                                <?php } } ?>
                              </select>
                              <span id="recommendation_edit<?php echo $row5->id;?>"></span>
                          </div>
                      </div>
                      <?php 
                      if($row5->packsize==1)
                      {
                        $p="Drum";

                      }else if($row5->packsize==2)
                      {
                        $p="Bucket";

                      }else
                      {
                        $p="Bulk";
                      }

                      if($lead_id == $getOnlyQuotationSentID) {
                        $pp = '';
                      } else {
                        $pp = $p;
                      }
                      ?>

                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Stock Avail.</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" readonly class="form-control"  id="stockedit1<?php echo $row5->id;?>" value="<?php echo $stock;?>">
                              
                          </div>
                      </div>


                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Qty</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control"  name="qtyedit1<?php echo $row5->id;?>" id="qtyedit1<?php echo $row5->id;?>" value="<?php echo $row5->qty;?>" oninput="allow_decimal('qtyedit1<?php echo $row5->id;?>');" onkeyup="match_stock_availability(<?php echo $row5->id;?>)">
                              <span style="color:red;font-weight:bold;font-size:14px;">Quoted Qty- <?php echo $row5->qty;?></span>
                              

                          </div>
                      </div>


                       <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Unit</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" readonly class="form-control"  name="unitedit1<?php echo $row5->id;?>" id="unitedit1<?php echo $row5->id;?>" value="<?php echo $row5->shortname;?>">
                              <input type="hidden" name="unit_id_edit1<?php echo $row5->id;?>" value="<?php echo $row5->unit_id;?>" id="unit_id_edit1<?php echo $row5->id;?>">
                          </div>
                      </div>

                      <?php //$discountpricehideedit = $CI->master->getdiscountpriceeditdata($row5->id);?>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit<?php echo $row5->id;?>"><?php echo $row5->shortname;?></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control" name="listpriceedit1<?php echo $row5->id;?>" id="listpriceedit1<?php echo $row5->id;?>" value="<?php echo $row5->price;?>" onblur="getnetamtedit(<?php echo $row5->id;?>)" oninput="allow_decimal('listpriceedit1<?php echo $row5->id;?>');" onkeyup="match_stock_availability(<?php echo $row5->id;?>)">
                              <p id="discountpriceshowedit<?php echo $row5->id;?>" style="color:red;font-weight:bold">Allowed Price: <?php echo $row5->discount_price;?></p>
                              <input type="hidden" name="discountpricehideedit<?php echo $row5->id;?>" value="<?php echo $row5->discount_price;?>" id="discountpricehideedit<?php echo $row5->id;?>">
                          </div>
                      </div>



                      <div class="col-md-2" style="display: none;">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Discount Price Per <?php echo $row5->unit;?><span></span></label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" class="form-control" name="discountpriceedit<?php echo $row5->id;?>" id="discountpriceedit<?php echo $row5->id;?>" value="0" onblur="getnetamtedit(<?php echo $row5->id;?>);" oninput="allow_decimal('discountpriceedit<?php echo $row5->id;?>');" value="<?php echo $row5->percent_amt;?>">
                              

                          </div>
                      </div>

                   <div class="col-md-2" style="display: none;">
                      <div class="form-group">
                          <label for="field-2" class="control-label">Net Price</label>
                          <span id="error_rack_location" style="color:red;">*</span>
                          <input type="text" class="form-control" name="netpriceedit<?php echo $row5->id;?>" id="netpriceedit<?php echo $row5->id;?>" value="0" readonly value="<?php echo $row5->net_price;?>">

                      </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                          <label for="field-2" class="control-label">Order Price</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="agreed_price_edit1<?php echo $row5->id;?>" id="agreed_price_edit1<?php echo $row5->id;?>" class="form-control" onkeyup="match_stock_availability(<?php echo $row5->id;?>);">
                        </div>
                      </div>
                    <div class="col-md-1">
                        <div class="form-group" style="margin-top:25px">
                            <a href="<?php echo page_url; ?>Leads/delete_lead_product/<?php echo $row5->id;?>/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash" style="font-size: 27px; color: red;"></i></a>
                        </div>
                    </div>
                     </div>
                   <?php } }?>

                   <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Add More Products</label>
                                <input type="checkbox" name="add_product_won" id="add_product_won" value="1" onchange="add_product_data_won();">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" style="display:none;" id="shownewproduct_won">
                      <div class="col-md-2" style="display:none;">
                          <div class="form-group">
                              <label for="field-1" class="control-label">Competitor Product</label>
                              <select name="competitor_product1[]" id="competitor_product10" onchange="getourproductname(0);">
                                <option value="NA">NA</option>
                              </select>
                             <!--  <input type="text" name="competitor_product[]" id="competitor_product0" autocomplete="nope" class="mand search-box"> -->
                           
                          </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-3" class="control-label">Product</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <select name="product1[]" id="product10" onchange="getprice1(0,this.value);getdiscount1(0,this.value);match_stock_availability1(0);getstock1(0);">
                                    <option value="">Select</option>
                                     <?php 
                                $query1 = $this->db->select('id, instruments_name, pack_size')
                                                  ->from('presto_instruments')
                                                  ->where('status',1)
                                                  ->get();
                            
                            if($query1->num_rows()>0) {
                                foreach($query1->result() as $row4) { ?>
                                  <option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?></option>
                                  <?php } } ?>
                                </select>
                                <span id="recommendation10"></span>
                            </div>
                        </div>

                         <div class="col-md-1">
                          <div class="form-group">
                              <label for="field-2" class="control-label">Stock Avail.</label>
                              <span id="error_rack_location" style="color:red;">*</span>
                              <input type="text" readonly class="form-control"  id="stock10" value="">
                              
                          </div>
                      </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Qty</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="qty1[]" id="qty10" oninput="allow_decimal('qty10');" onkeyup="match_stock_availability1(0);">
                                <span style="color:red;font-weight:bold;font-size:14px;" id="qty_stock10"></span>

                            </div>
                        </div>

                        <div class="col-md-1">
                          <div class="form-group"> 
                            <label for="field-2" class="control-label">Unit</label> 
                            <span id="error_rack_location" style="color:red;">*</span> 
                            <input type="text" readonly class="form-control" name="unit1[]" id="unit10"> 
                            <input type="hidden" name="unit_id1[]" id="unit_id10">
                          </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit10"></span></label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="listprice1[]" id="listprice10" value=""  onblur="getnetamt(0)" oninput="allow_decimal('listprice10');">
                                <p id="discountpriceshow10" style="display: none;"></p>
                                <input type="hidden" name="discountpricehide1[]" value="" id="discountpricehide10">
                            </div>
                        </div>
                        <div class="col-md-2" style="display: none;">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Discount Price Per <span></span></label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" value="0" name="discountprice1[]" id="discountprice10"  onblur="getnetamt(0)" oninput="allow_decimal('discountprice10');">
                                

                            </div>
                        </div>
                        <div class="col-md-2" style="display: none;">
                            <div class="form-group">
                                <label for="field-2" class="control-label">Net Price</label>
                                <span id="error_rack_location" style="color:red;">*</span>
                                <input type="text" class="form-control" name="netprice[]" value="0" id="netprice0"  readonly>
                            </div>
                        </div>

                         <div class="col-md-2">
                              <div class="form-group">
                                  <label for="field-2" class="control-label">Order Price/<span class="list_price_unit10"></span></label>
                                  <span id="error_rack_location" style="color:red;">*</span>
                                  <input type="text" class="form-control" name="agreed_price1[]" id="agreed_price10" value="" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal('agreed_price10');">
                              </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group" style="margin-top:25px">
                                <a class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                                <div id="dynamictasks1"></div>
                               
                            </div>

                      <div class="row">
                        <div class="col-md-12" style="margin-top:20px;">
                           <div class="col-md-4" style="display: none;">
                              <label>Invoice No.</label>
                              <input type="text" name="invoice_no" id="invoice_no" class="form-control" autocomplete="nope" readonly>
                            </div>
                             <div class="col-md-4">
                              <label>Sales Order No.</label>
                              <input type="text" name="sales_no" id="sales_no" class="form-control" autocomplete="nope" readonly>
                            </div>
                            <div class="col-md-4">
                              <label>PO No.</label>
                              <input type="text" name="po_no" class="form-control" autocomplete="nope">
                            </div>
                            <div class="col-md-4">
                              <label>PO Date</label>
                              <input type="text" name="po_date" id="datepicker" class="form-control" autocomplete="nope">
                            </div>
                             <div class="col-md-4">
                                <label>Upload PO/Evidence</label>
                                <span class="input-icon icon-right">
                                  <input type="file" name="upload_file" id="upload_file">
                                </span>
                              </div>
                          </div>
                      </div>

                      

                  <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp Customer Details</h4>
                        </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-3">
                        <label>Customer Name<span style="color: red;">*</span></label>
                        <input type="text" name="customer_name_cust" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->customer_name;?>">
                      </div>
                      <div class="col-md-3">
                        <label>Contact No.<span style="color: red;">*</span></label>
                        <input type="text" name="contact_no_cust" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->contact_no;?>">
                      </div>
                    </div>
                  </div>

                  <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Billing Address</h4>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Bill To<span style="color: red;">*</span></label>
                      <input type="text" name="bill_to" id="bill_to" autocomplete="nope" value="<?php echo ucwords($customer_detail->company_name); ?>" class="form-control">
                      <input type="hidden" name="company_name_bill" value="<?php echo $customer_detail->company_name;?>">
                      <input type="hidden" name="title_bill" value="<?php echo $customer_detail->title;?>">
                    </div>
                    <div class="col-md-3" style="display:none;">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="billing_name" id="billing_name" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->customer_name;?>">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="billing_address" autocomplete="nope" id="billing_address"><?php echo $customer_detail->postal_address;?></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="billing_state" id="billing_state">
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>"><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="billing_city" id="billing_city" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->city;?>">
                    </div>
                    <div style="clear:both;height:5px"></div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="billing_pincode" id="billing_pincode" autocomplete="nope" class="form-control" value="" onblur="check_billing_pincode()">
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="billing_phone_no" id="billing_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="billing_mobile_no" id="billing_mobile_no" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->contact_no;?>" onblur="check_billing_mobile()">
                    </div>
                    <div class="col-md-3">
                      <label>Email</label>
                      <input type="text" name="billing_email" id="billing_email" autocomplete="nope" class="form-control" value="<?php echo $customer_detail->email_id;?>" onblur="check_billing_email()">
                    </div>
                  </div>
                </div>

                <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">
                          <label>Billing Address same as Delivery Address?</label>
                          <input type="checkbox" id="check_billing" name="check_billing" value="1" onchange="check_address()">
                      </div>
                  </div>
                </div>

                <div class="row" style="margin-top:20px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Shipping Address</h4>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Ship To<span style="color: red;">*</span></label>
                      <input type="text" name="ship_to" id="ship_to" autocomplete="nope" class="form-control" value="<?php echo ucwords($customer_detail->company_name); ?>">
                    </div>
                    <div class="col-md-3" style="display:none;">
                      <label>Customer Name<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_name" id="shipping_name" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-3">
                      <label>Address<span style="color: red;">*</span></label>
                      <textarea class="form-control" name="shipping_address" autocomplete="nope" id="shipping_address"></textarea>
                    </div>
                    <div class="col-md-3">
                      <label>State<span style="color: red;">*</span></label>
                      <select class="form-control" name="shipping_state" id="shipping_state">
                        <option value="">SELECT STATE</option>
                        <?php if($getAllStates != '') {
                                foreach($getAllStates as $rows) {?>
                          <option value="<?php echo $rows->state_id;?>"><?php echo $rows->state_name;?></option>
                        <?php } } ?>
                      </select>
                      <!-- <input type="text" name="state" class="form-control" value="<?php echo $state_name;?>"> -->
                    </div>
                    <div class="col-md-3">
                      <label>City<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_city" id="shipping_city" autocomplete="nope" class="form-control">
                    </div>
                    <div style="clear:both;height:5px"></div>
                    <div class="col-md-3">
                      <label>Pincode<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_pincode" id="shipping_pincode" autocomplete="nope" class="form-control" onblur="check_shipping_pincode()">
                    </div>
                    <div class="col-md-3">
                      <label>Phone No</label>
                      <input type="text" name="shipping_phone_no" id="shipping_phone_no" autocomplete="nope" class="form-control">
                    </div>
                    <div class="col-md-3">
                      <label>Mobile No<span style="color: red;">*</span></label>
                      <input type="text" name="shipping_mobile_no" id="shipping_mobile_no" autocomplete="nope" class="form-control" onblur="check_shipping_mobile()">
                    </div>
                    <div class="col-md-3">
                      <label>Email</label>
                      <input type="text" name="shipping_email" id="shipping_email" autocomplete="nope" class="form-control" onblur="check_shipping_email()">
                    </div>
                    
                  </div>
                </div>

                <div class="row text-center" style="margin-top:50px;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="col-md-4">
                          <label>Freight Applicable?</label>
                          <input type="checkbox" id="check_freight" name="check_freight" value="1" onchange="checkfreight()">
                      </div>
                      <div class="col-md-4" id="freight" style="display:none;">
                        <label>Freight Amount<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="freight_amt" id="freight_amt" oninput="allow_decimal('freight_amt')">
                      </div>
                  </div>
                </div>

                <div class="row" style="margin-top:20px; display:none;">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                      <div class="page-title-box">                           
                          <h4 class="page-title text-center">&nbsp Transport Detail</h4>
                      </div>
                  </div>
              </div>
              <div class="row" style="display:none;">
                <div class="col-md-12">
                   <div class="col-md-4">
                        <label>Vehicle No.<span style="color: red;">*</span></label>
                    
                                      <select name="vehicle_no" id="vehicle_no" class="form-control select34">
                                        <option value=""></option>
                                        <?php 
                                        $restey=$this->db->select('name')->from('our_vehicles')->get();
                                        if($restey->num_rows()>0)
                                            { 
                                                foreach($restey->result() as $row)
                                                {
                                                ?>
                                        <option value="<?php echo $row->name;?>"><?php echo $row->name;?></option>
                                        <?php } } ?>
                                      </select>


                    </div>
                     <div class="col-md-4">
                        <label>Vehicle Type<span style="color: red;">*</span></label>
                        <input type="text" name="vehicle_type" id="vehicle_type" class="form-control" autocomplete="nope">
                     </div>
                      <div class="col-md-4">
                        <label>Destination<span style="color: red;">*</span></label>
                        <input type="text" name="destination" id="destination" class="form-control" autocomplete="nope">
                     </div>
                </div>
              </div>

                 <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp; Tax Registration Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                     <div class="col-md-3">
                      <label>MSME No.</label>
                      <input type="text" name="msme_no" id="msme_no" class="form-control" value="" autocomplete="nope">
                    </div>

                    <div class="col-md-3">
                      <label>PAN/IT No.<span style="color: red;">*</span></label>
                      <input type="text" name="pan_no" id="pan_no" class="form-control" autocomplete="nope" onblur="check_pan()">
                    </div>
                    <div class="col-md-3">
                      <label>Registration Type<span style="color: red;">*</span></label>
                      <input type="text" name="registration_type" id="registration_type" class="form-control" autocomplete="nope" value="REGULAR" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>GSTIN/UIN<span style="color: red;">*</span></label>
                      <input type="text" name="gst_no" id="gst_no" class="form-control" value="<?php echo $customer_detail->customer_gstn;?>" autocomplete="nope" onblur="validate_gst()" <?php  if($customer_detail->customer_gstn!=''){?> readonly <?php } ?>>
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">                           
                            <h4 class="page-title text-center">&nbsp; Other Details</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-3">
                      <label>Reference<span style="color: red;"></span></label>
                      <input type="text" name="reference" class="form-control" autocomplete="nope">
                    </div>
                    <div class="col-md-9">
                      <label>Instructions for Billing & Dispatch Department<span style="color: red;"></span></label>
                      <textarea class="form-control" name="note"></textarea>
                    </div>
                  </div>
                </div>

                </div>

                <div class="row" id="sample_required" style="display:none;">

                    <?php if($getLeadProducts != '') { 
                    foreach($getLeadProducts as $row5) {?>
                    <div class="col-md-12">
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                         <label for="field-2" class="control-label">Product</label>
                            <select class="form-control" name="prdforsample[]">
                            <?php 
                            $sql1=$this->db->select('id, instruments_name')
                            ->from('presto_instruments')
                            ->where('id',  $row5->product_id)
                            ->get();

                            if($sql1->num_rows()>0) {
                            foreach($sql1->result() as $row4) { ?>
                            <option value="<?php echo $row4->id;?>" selected><?php echo $row4->instruments_name;?></option>
                            <?php } } ?>
                            </select>

                        </div>
                    </div>
                     <?php 
                      if($row5->packsize==1)
                      {
                        $p="Drum";

                      }else if($row5->packsize==2)
                      {
                        $p="Bucket";

                      }else
                      {
                        $p="Bulk";
                      }

                      if($lead_id == $getOnlyQuotationSentID) {
                        $pp = '';
                      } else {
                        $pp = $p;
                      }
                      ?>
                    <!-- <div class="col-md-3">
                         <div class="form-group">
                              <label for="field-2" class="control-label">QTY</label><br/>
                
                              <input type="text" class="form-control"  name="qtyedit<?php //echo $row5->id;?>" id="qtyedit<?php //echo $row5->id;?>" value="<?php //echo $row5->qty.' '.$pp;?>">

                          </div>
                    </div> -->
                    <input type="hidden" name="lead_product_id[]" value="<?php echo $row5->id;?>">

                    <div class="col-md-3">
                     <div class="form-group" style="margin-top:8px;">
                          <label for="field-2" class="control-label">Send Sample</label><br/>
                          <input type="checkbox" class="sendsample"  name="sendsample<?php echo $row5->id;?>" id="sendsample" style="width:20px;" value="1">

                      </div>
                    </div>
                    </div>
                    <div style="clear:both; height:10px;"></div>

                    <?php } }?>

                </div>
                


                 <div class="row" id="trail_required" style="display:none;">

                    <?php if($getLeadProducts != '') { 
                    foreach($getLeadProducts as $row5) {?>
                    <div class="col-md-12">
                    <div class="col-md-3"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                         <label for="field-2" class="control-label">Product</label>
                            <select class="form-control" name="prdfortrail<?php echo $row5->id;?>">
                            <?php 
                            $sql1=$this->db->select('id, instruments_name')
                            ->from('presto_instruments')
                            ->where('id',  $row5->product_id)
                            ->get();

                            if($sql1->num_rows()>0) {
                            foreach($sql1->result() as $row4) { ?>
                            <option value="<?php echo $row4->id;?>" selected><?php echo $row4->instruments_name;?></option>
                            <?php } } ?>
                            </select>

                        </div>
                    </div>
                     <?php 
                      if($row5->packsize==1)
                      {
                        $p="Drum";

                      }else if($row5->packsize==2)
                      {
                        $p="Bucket";

                      }else
                      {
                        $p="Bulk";
                      }

                      if($lead_id == $getOnlyQuotationSentID) {
                        $pp = '';
                      } else {
                        $pp = $p;
                      }
                      ?>
                    <!-- <div class="col-md-3">
                         <div class="form-group">
                              <label for="field-2" class="control-label">QTY</label><br/>
                
                              <input type="text" class="form-control"  name="qtyedit<?php //echo $row5->id;?>" id="qtyedit<?php //echo $row5->id;?>" value="<?php //echo $row5->qty.' '.$pp;?>">

                          </div>
                    </div> -->
                    <input type="hidden" name="lead_product_id_trail[]" value="<?php echo $row5->id;?>">
                    
                    <div class="col-md-3">
                     <div class="form-group" style="margin-top:8px;">
                          <label for="field-2" class="control-label">Send Trail</label><br/>
                          <input type="checkbox" class="sendtrail"  name="sendtrail<?php echo $row5->id;?>" id="sendtrail" style="width:20px;" value="1">

                      </div>
                    </div>
                    </div>
                    <div style="clear:both; height:10px;"></div>

                    <?php } }?>

                </div>

                <div class="p-t-10 pull-right">

                  <input type="submit" class="btn btn-sm btn-primary" style="background-color: #383b43 !important;border: 1px solid #383b43 !important;" id="saves" value="Update Followup Remarks">

                </div>

                <div class="marginbottom"></div>

                <div class="clearfix"></div>



              </form>

            </div>

            <div class="card-box" style="overflow: auto;">
              <table id="example" class="table table-bordered pretty">
                <thead>
                  <tr>
                    <th>Sr No.</th>
                    <th>Next Followup</th>
                    <th>Visit Date/Time</th>
                    <th>Demo Schedule Date</th>
                    <th>Lead Stage</th>
                    <th>Followup Remarks</th>
                    <th>Added On</th>
                    <th>Added By</th>
                    <th>Media Files</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.lead_name,c.quotation_step,c.trail, c.sort_order')
                    ->from('progress_remarks a')
                    ->join('system_users b', 'a.added_by=b.user_id', 'left')
                    ->join('lead_stage c', 'a.lead_status=c.lead_id')
                    ->where('a.lead_id', $this->uri->segment(3))
                    ->order_by('a.id', 'desc')
                    ->get();

                  $meeting_date = '';
                  $meeting_time = '';
                  $reminder_date = '';
                  $reminder_time = '';
                  $meeting_instructions = '';

                  if ($query->num_rows() > 0) {

                    $i = 1;

                    foreach ($query->result() as $row) {

                      $getTrialSentID = $CI->salescrm->getTrialSentID($row->lead_id);

                      if ($row->next_follow_date == '0000-00-00') {

                        $next_followup_date = '';
                      } else {

                        $next_followup_date = date('d-m-Y', strtotime($row->next_follow_date));
                      }

                      if ($row->nonqualifiedreason <> 0) {
                       
                              $reason=$DI->Dashboard_model->unqualifiedreason($row->nonqualifiedreason);

                              $reason="<strong>Reason: ".$reason."</strong>";
                      
                    }else{
                      $reason="";
                    }


                      if ($row->visitdate == '0000-00-00') {

                        $visitdate = '';
                        $visittime = "";
                        $vtime = "";
                      } else {
                        $visittime = date('H:i a',strtotime($row->visittime));
                        $visitdate = date('d-m-Y', strtotime($row->visitdate));
                        $vtime = $visitdate."<br>".$visittime;
                      }

                       if ($row->demoscheduledate == '0000-00-00') {

                        $demoscheduledate = '';
                      } else {

                        $demoscheduledate = date('d-m-Y', strtotime($row->demoscheduledate));
                      }



                      $edit = "<a href='" . page_url . "Leads/edit_progress_report/" . $row->lead_id . "/" . $row->id . "'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

                      // if ($row->lead_status != 1) { ?>

                        <tr>

                          <td><?php echo $i; ?></td>

                          <td><?php echo $next_followup_date; ?></td>
                          <td><?php echo $vtime; ?></td>
                          <td><?php echo $demoscheduledate; ?></td>

                          <td><?php echo $row->lead_name; ?><br /><?php echo $reason; ?></td>



                          <td><?php echo $row->remarks; ?></td>

                          <td><?php $added = date('d-m-Y H:i:s', strtotime($row->added_on));

                              if ($added <> '30-11--0001 00:00:00') {
                                echo $added;
                              } ?>

                          </td>

                          <td><?php echo $row->first_name . " " . $row->last_name; ?></td>

                           <td>
                               <?php if($row->quotation_step==1)
                               { ?>
                                <a href="<?php echo site_http_root;?>poformat/tcpdf/examples/hpcl_emailer.php?lead_id=<?php echo $row->lead_id;?>" class="btn btn-success btn-xs" target="_blank">Quotation</a>
                               <?php } else if($row->sort_order == 1) {
                                  echo $getLeadAttachment;
                               } else if($row->trail == 1) {

                                    $rty=$this->db->select('a.id,c.instruments_name')->from('trial_to_be_sent a')->join('lead_products b','a.lead_product_id=b.id')->join('presto_instruments c','c.id=b.product_id')->where('a.lead_id',$row->lead_id)->get();
                                    if($rty->num_rows()>0)
                                    {
                                        foreach($rty->result() as  $rowww)
                                        {
                                    ?>
                                  <a href="<?php echo page_url;?>Sampling/trial_readings/<?php echo $rowww->id;?>/<?php echo $row->lead_id;?>/t4r4i4a4l" class="btn btn-success btn-xs" target="_blank"><?php echo $rowww->instruments_name;?> Readings</a>
                              <?php } } } ?>
                           </td>
                        </tr>

                  <?php $i++;
                      // }
                    }
                  } ?>

                </tbody>
              </table>
            </div>
          </div>

          <!-- end row -->





          <!-- Footer -->

          <?php $this->load->view('common/footer'); ?>

          <!-- End Footer -->



        </div>

        <!-- end container -->



      </div>



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

      <!-- App js -->

      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

      <!-- Datatables-->

      <script src="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.bootstrap.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.buttons.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/jszip.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/pdfmake.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/vfs_fonts.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/buttons.html5.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/buttons.print.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.fixedHeader.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.keyTable.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.responsive.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/datatables/dataTables.scroller.min.js"></script>



      <!-- Datatable init js -->

      <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>



      <!-- App js -->

      <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>

      <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

      <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

      <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

      <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>



      <script>
        $(document).ready(function() {

            get_tnc();
            $('.select34').select2();
            
          /** REMOVE ANY VALIDATION **/


          /** END **/
          var hpcl_company = "<?php echo $hpcl_company;?>";
          

                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/getinvoice_no",
                data: "compid=" + hpcl_company,
                success: function(data) {
                $("#invoice_no").val(data);
                }
                });

                 $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Billing/getsalesorder_no",
                data: "compid=" + hpcl_company,
                success: function(data) {
                $("#sales_no").val(data);
                }
                });


          $('#example').dataTable({

            "bProcessing": true,

            "pagination": true

          });



          $('#example1').dataTable({

            "bProcessing": true,

            "pagination": true,
              pageLength:50,

            "sAjaxSource": "<?php echo page_url; ?>Leads/customer_remarks_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'remarks'
              },

              {
                mData: 'added_on'
              },

              {
                mData: 'name'
              }



            ]

          });

          $('#example2').dataTable({

            "bProcessing": true,

            "pagination": true,

              pageLength:50,
            "sAjaxSource": "<?php echo page_url; ?>Leads/call_history_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'called_by'
              },

              {
                mData: 'call_Date'
              },

              {
                mData: 'start_time'
              },

              {
                mData: 'end_time'
              }



            ]

          });

          $('#example3').dataTable({

            "bProcessing": true,

            "pagination": true,
              pageLength:50,

            "sAjaxSource": "<?php echo page_url; ?>Leads/customer_mail_history_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'Send_to'
              },

              {
                mData: 'subject'
              },

              {
                mData: 'content'
              },

              {
                mData: 'file'
              },

              {
                mData: 'remark'
              },

              {
                mData: 'date'
              }



            ]

          });

          $('#example4').dataTable({

            "bProcessing": true,

            "pagination": true,

              pageLength:50,
            "sAjaxSource": "<?php echo page_url; ?>Leads/shared_lead_list/<?php echo $this->uri->segment(3); ?>",

            "aoColumns": [

              {
                mData: 'sr_no'
              },

              {
                mData: 'name'
              },

              {
                mData: 'added_on'
              }



            ]

          });



            var i = 1;
            $('#addmore_btn').click(function() {
                $('#dynamictasks').append('<div id="row' + i + '" class="row"><div class="col-md-12"> <div class="col-md-2" style="display:none"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label><select name="competitor_product[]" id="competitor_product'+i+'" class="form-control" onchange="getourproductname('+i+'); "><option value="NA">NA</option></select></div></div><div class="col-md-3"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control" name="product[]" id="product'+i+'" onchange="getprice(' + i + ',this.value);getdiscount(' + i + ',this.value); getstock('+i+');match_stock_availability2('+i+');"> <option value="">Select</option><?php $sql1= $this->db->select('b.id,b.instruments_name')->from('company_products a')->join('presto_instruments b','b.id=a.product_id')->where('a.company_id',$customer_detail->hpcl_company)->get(); if($sql1->num_rows()>0){ foreach($sql1->result() as $row4){ ?><option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?></option><?php }} ?></select><span id="recommendation'+i+'"></span></div></div> <div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Stock Avail.</label><span id="error_rack_location" style="color:red;">*</span><input type="text" readonly class="form-control"  id="stock'+i+'" value=""></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty[]" id="qty' + i + '" required onkeyup="match_stock_availability2('+i+');"></div></div><div class="col-md-1"> <div class="form-group"> <label for="field-2" class="control-label">Unit</label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" readonly class="form-control" name="unit[]" id="unit'+i+'"><input type="hidden" name="unit_id[]" id="unit_id'+i+'"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice[]" id="listprice' + i + '" oninput="allow_decimal("listprice' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide[]" id="discountpricehide' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="discountprice[]" id="discountprice' + i + '" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice[]" id="netprice' + i + '" readonly> </div></div><div class="col-md-1" style="margin-top:25px"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove  btn btn-danger" id="' + i + '"><i class="fa fa-times"></i></button></div></div></div><br/>');

                initializeSelect2("competitor_product"+i);
                initializeSelect2_product("product"+i);
                i++;
            });


            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });

            var i = 1;
            $('#addmore_btn1').click(function() {
                $('#dynamictasks1').append('<div id="row' + i + '" class="row"><div class="col-md-12"> <div class="col-md-2" style="display:none"> <div class="form-group"> <label for="field-1" class="control-label">Competitor Product</label><select name="competitor_product1[]" id="competitor_product1'+i+'" class="form-control" onchange="getourproductname('+i+');"><option value="NA">NA</option></select></div></div><div class="col-md-2"><div class="form-group"><label for="field-3" class="control-label">Product</label><span id="error_rack_location" style="color:red;">*</span><select class="form-control" name="product1[]" id="product1'+i+'" onchange="getprice1(' + i + ',this.value);getdiscount1(' + i + ',this.value);match_stock_availability1('+i+'); getstock1('+i+');"> <option value="">Select</option><?php $sql1= $this->db->select('b.id,b.instruments_name')->from('company_products a')->join('presto_instruments b','b.id=a.product_id')->where('a.company_id',$customer_detail->hpcl_company)->get(); if($sql1->num_rows()>0){ foreach($sql1->result() as $row4){ ?><option value="<?php echo $row4->id;?>"><?php echo $row4->instruments_name;?></option><?php }} ?></select><span id="recommendation1'+i+'"></span></div></div><div class="col-md-1"><div class="form-group"><label for="field-2" class="control-label">Stock Avail.</label><span id="error_rack_location" style="color:red;">*</span><input type="text" readonly class="form-control"  id="stock1'+i+'" value=""></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Qty</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="qty1[]" id="qty1' + i + '" required onkeyup="match_stock_availability1('+i+');"><span style="color:red;font-weight:bold;font-size:14px;" id="qty_stock1'+i+'"></span></div></div><div class="col-md-1"> <div class="form-group"> <label for="field-2" class="control-label">Unit</label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" readonly class="form-control" name="unit1[]" id="unit1'+i+'"><input type="hidden" name="unit_id1[]" id="unit_id1'+i+'"></div></div><div class="col-md-2"><div class="form-group"><label for="field-2" class="control-label">Offered Price Per <span class="list_price_unit1'+i+'"></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control mand" name="listprice1[]" id="listprice1' + i + '" oninput="allow_decimal("listprice1' + i + '");"  onblur="getnetamt(' + i + ')" required><p id="discountpriceshow1'+i+'" style="display: none;"></p><input type="hidden" name="discountpricehide1[]" id="discountpricehide1' + i + '" ></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Discount Price Per <span></span></label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="discountprice1[]" id="discountprice1' + i + '" onblur="getnetamt(' + i + ')" oninput="allow_decimal("discountprice1' + i + '");"></div></div><div class="col-md-2" style="display:none;"><div class="form-group"><label for="field-2" class="control-label">Net Price</label><span id="error_rack_location" style="color:red;">*</span><input type="text" class="form-control" name="netprice1[]" id="netprice1' + i + '" readonly> </div></div><div class="col-md-2"> <div class="form-group"> <label for="field-2" class="control-label">Order Price/<span class="list_price_unit1'+i+'"></span></label> <span id="error_rack_location" style="color:red;">*</span> <input type="text" class="form-control" name="agreed_price1[]" id="agreed_price10" value="" onblur="getnetamt(0)" autocomplete="off" oninput="allow_decimal("agreed_price1'+i+'");"> </div></div><div class="col-md-1" style="margin-top:25px"><div class="form-group pull-left"><label for="field-1" class="control-label">&nbsp;</label><button type="button" name="add" class="btn_remove1  btn btn-danger" id="' + i + '"><i class="fa fa-times"></i></button></div></div></div><br/>');

                initializeSelect2("competitor_product1"+i);
                initializeSelect2_product("product1"+i);
                i++;
            });


            $(document).on('click', '.btn_remove1', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });


        });
      </script>

      <script type="text/javascript">

         function get_tnc() {
        
        var d="<?php echo $basic_terms;?>";
        CKEDITOR.instances['drums'].setData(d);
                                    
                 
        }
      $(document).ready(function(){

        var purl="<?php echo page_url;?>Open_leads/getrecommendations";
        $('#competitor_product0').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                tags:true,
                ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
                data: function (params) {

                return {
                searchTerm: params.term
                };

                },
                processResults: function (data) {
                return {
                results: data
                };
        },
        cache: true

                }
        });

        $('#product0').select2();

        var purl="<?php echo page_url;?>Open_leads/getrecommendations";
        $('#competitor_product10').select2({ 
                placeholder: 'TYPE TO SELECT',
                minmumInputLength:4,
                allowClear: true,
                tags:true,
                ajax: {
                url: purl,
                dataType: 'json',
                delay: 250,
                data: function (params) {

                return {
                searchTerm: params.term
                };

                },
                processResults: function (data) {
                return {
                results: data
                };
        },
        cache: true

                }
        });

        $('#product10').select2();


});
        
        function getourproductname(id)
      {
        var comproduct=$("#competitor_product"+id).val();

        if(comproduct!='')
        {

            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Open_leads/getrecommendations_our_product",
            data:"comproduct="+comproduct,
            success:function(data){
                 $("#recommendation"+id).css('font-size','12px');
            $("#recommendation"+id).css('color','red');
            $("#recommendation"+id).css('font-weight','bold');
            $("#recommendation"+id).html(data);
            }
            });

        }

      }

       


 function initializeSelect2(selectElementObj) {
   
    var purl="<?php echo page_url;?>Open_leads/getrecommendations";

            $('#'+selectElementObj).select2({ 
            placeholder: 'TYPE TO SELECT',
            minmumInputLength:4,
            allowClear: true,
            tags:true,
            ajax: {
            url: purl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
            return {
            searchTerm: params.term
            };
            },
            processResults: function (data) {
            return {
            results: data
            };
            },
            cache: true

            }
            });
         
      }


function initializeSelect2_product(selectElementObj) {

    $('#'+selectElementObj).select2({ });

}

      </script>


      <script type="text/javascript">
        $(document).ready(function() {



          var url = "<?php echo page_url; ?>Leads/getUsers";



          $('#participants').select2({

            placeholder: 'TYPE TO SELECT',

            minmumInputLength: 4,

            allowClear: true,

            multiple: true,



            ajax: {

              url: url,

              dataType: 'json',

              delay: 250,



              processResults: function(data) {

                return {

                  results: data

                };

              },

              cache: true

            }



          });



          jQuery('#followup_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()

          });

           jQuery('#schedulevisitdate').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()

          });

           


          jQuery('#meeting_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#meeting_time').timepicker({

            defaultTime: false,

            showMeridian: false

          });


$('#visittime').timepicker({
    timeFormat: 'h:mm p',
    interval: 60,
    minTime: '10',
    maxTime: '6:00pm',
    defaultTime: '11',
    startTime: '10:00',
    dynamic: false,
    dropdown: true,
    scrollbar: true
});



          jQuery('#reminder_date').datepicker({

            autoclose: true,

            todayHighlight: true,

            format: 'dd-mm-yyyy'

            // startDate: new Date()



          });



          $('#reminder_time').timepicker({



            defaultTime: false,

            showMeridian: false



          });

           var date = new Date();
           jQuery('.datepicker').datepicker({
                startDate: date,
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker').datepicker({

                        autoclose: true,

                        todayHighlight: true,

                        format: 'dd-mm-yyyy'

                        // startDate: new Date()

                      });

        });
      </script> 

 

      <script type="text/javascript">
        function getFields() {
          $("#rmkdynamic").html('Remarks<span style="color: red">*</span>');
          $("#sample_required").css('display','none');
          $("#trail_required").css('display','none');
          $("#remarks").attr('placeholder','Remarks');
         // $("#remarks").addClass('mand');
          var lead_stage_id = $("#leadquality").val();
          var leadquality_name = $("#leadquality option:selected").text();
          $("#remark_title").val(leadquality_name);
          $("#followup").css('display', 'none');
          $("#nonqualifiedreason").css('display', 'none');
          $(".product_details").css('display', 'none');
          $(".product_detailsedit").css('display', 'none');
          $("#validity_date").removeClass('mand');
          $("#terms").css('display', 'none');
          $("#termscondition").attr('required', false);
          $("#customergst").attr('required', false);
          $("#gst").css('display', 'none');
          $("#fcharges").css('display', 'none');
          $("#closewon").css('display', 'none');
          $("#paymentterms").removeClass('mand');
          $("#payment_type").removeClass('mand');
          $("#upload_file").removeClass('mand');
          $("#monthly_consumption").removeClass('mand');
          $("#credit_days").css('display','none');
          $("#payment_type").val('');
          $("#bill_to").removeClass('mand');
          $("#billing_address").removeClass('mand');
          $("#billing_state").removeClass('mand');
          $("#billing_city").removeClass('mand');
          $("#billing_pincode").removeClass('mand');
          $("#billing_mobile_no").removeClass('mand');
          $("#ship_to").removeClass('mand');
          $("#shipping_address").removeClass('mand');
          $("#shipping_state").removeClass('mand');
          $("#shipping_city").removeClass('mand');
          $("#shipping_pincode").removeClass('mand');
          $("#shipping_mobile_no").removeClass('mand');  
          $("#vehicle_no").removeClass('mand');
          $("#vehicle_type").removeClass('mand');
          $("#destination").removeClass('mand');
          $("#pan_no").removeClass('mand');
          $("#gst_no").removeClass('mand');
          $("#customer_codes").removeClass('mand');

          $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Leads/getLeadStageDetails",
            beforeSend: function(){
            $("#saves").attr('disabled',true);
            },
            complete: function(){
            $("#saves").attr('disabled',false);
            },
            data: {
              lead_stage_id: lead_stage_id
            },
            success: function(data) {


              var arr = data.split('|');
              var quotation_step = arr[0];
              var quotation_revised_step = arr[1];
              var pi_step = arr[2];
              var pi_revised_step = arr[3];
              var followup_date = arr[4];
              var reason = arr[5];
              var conversion = arr[6];
              var sample = arr[7];
              var trial = arr[8];

                
              if (quotation_step == 1) {

                $(".product_details").css('display', '');
                $("#validity_date").addClass('mand');
              }

               if (quotation_revised_step == 1) {
                $(".product_details").css('display', '');
                $("#validity_date").addClass('mand');

              } 

              if (pi_step == 1 || pi_revised_step == 1) {
                $(".product_detailsedit").css('display', '');
                $("#terms").css('display', '');
                $("#termscondition").attr('required', true);
                $("#customergst").attr('required', true);
                $("#gst").css('display', '');
              }
               if (followup_date == 1) {
                $("#followup").css('display', '');
              } 
               if (reason == 1) {
                $("#nonqualifiedreason").css('display', '');
              }

              if(conversion==1)
              {
                $("#closewon").css('display', '');
                $("#paymentterms").addClass('mand');
                $("#payment_type").addClass('mand');
               // $("#upload_file").attr('required',true);
                $("#monthly_consumption").addClass('mand');
                $("#rmkdynamic").html('Any Special Remarks<span style="color: red"></span>');
                $("#remarks").removeClass('mand');
                $("#remarks").attr('placeholder','Special Remarks');
                $("#bill_to").addClass('mand');
                $("#billing_address").addClass('mand');
                $("#billing_state").addClass('mand');
                $("#billing_city").addClass('mand');
                $("#billing_pincode").addClass('mand');
                $("#billing_mobile_no").addClass('mand');
                $("#ship_to").addClass('mand');
                $("#shipping_address").addClass('mand');
                $("#shipping_state").addClass('mand');
                $("#shipping_city").addClass('mand');
                $("#shipping_pincode").addClass('mand');
                $("#shipping_mobile_no").addClass('mand');
                $("#vehicle_no").removeClass('mand');
                $("#vehicle_type").removeClass('mand');
                $("#destination").removeClass('mand');
                $("#pan_no").addClass('mand');
                $("#gst_no").addClass('mand');
                $("#customer_codes").removeClass('mand');

                $(".editid1").each(function() {
                    var editid = $(this).val();
                    $('#qtyedit1'+editid).addClass('mand');
                    $('#listpriceedit1'+editid).addClass('mand');
                    $('#agreed_price_edit1'+editid).addClass('mand');
                    // $('#product10').addClass('mand');
                    // $('#qty10').addClass('mand');
                    // $('#listprice10').addClass('mand');
                    // $('#agreed_price10').addClass('mand');
                });
              }

              if(sample==1)
              {
                $("#sample_required").css('display','');
              }

              if(trial==1)
              {
                $("#trail_required").css('display','');
              }
            }
          });
        }

        function check_tnc() {
            $(".bulk_tnc").css('display', 'none');
            $(".drums_tnc").css('display', 'none'); 
            // alert($('input[name=chk_tnc]:checked').val());
            if ($('input[name=chk_tnc]:checked').val() == 1) {
                $(".bulk_tnc").css('display', '');
            } else if ($('input[name=chk_tnc]:checked').val() == 2) {
                $(".drums_tnc").css('display', ''); 
            }
        }

          function check_address() {

              var billing_name = $('#billing_name').val();
              var billing_address = $('#billing_address').val();
              var billing_state = $('#billing_state').val();
              var billing_city = $('#billing_city').val();
              var billing_pincode = $('#billing_pincode').val();
              var billing_contact_person = $('#billing_contact_person').val();
              var billing_phone_no = $('#billing_phone_no').val();
              var billing_mobile_no = $('#billing_mobile_no').val();
              var billing_email = $('#billing_email').val();
              var bill_to = $('#bill_to').val();

              if($('input[name=check_billing]').is(':checked')) {
                  $('#shipping_name').val(billing_name);
                  $('#shipping_address').val(billing_address);
                  $('#shipping_state').val(billing_state);
                  $('#shipping_city').val(billing_city);
                  $('#shipping_pincode').val(billing_pincode);
                  $('#shipping_contact_person').val(billing_contact_person);
                  $('#shipping_phone_no').val(billing_phone_no);
                  $('#shipping_mobile_no').val(billing_mobile_no);
                  $('#shipping_email').val(billing_email);
                  $("#ship_to").val(bill_to);
              } else {
                  $('#shipping_name').val('');
                  $('#shipping_address').val('');
                  $('#shipping_state').val('');
                  $('#shipping_city').val('');
                  $('#shipping_pincode').val('');
                  $('#shipping_contact_person').val('');
                  $('#shipping_phone_no').val('');
                  $('#shipping_mobile_no').val('');
                  $('#shipping_email').val('');
                   $("#ship_to").val('');
              }
        }


          function getnetamt(i) {
            var qty = $('#qty' + i).val();
            var listprice = $('#listprice' + i).val();
            var discountprice = $('#discountprice' + i).val();
            var discountpricehide = $('#discountpricehide' + i).val();
            

            if(parseFloat(listprice) > 0) {
                $("#discountprice" + i).attr('readonly', false);
                $('#netprice' + i).val(listprice);
            } else {
                $("#discountprice" + i).attr('readonly', true);
                $('#netprice' + i).val(0);
            }

            if (parseFloat(listprice) < parseFloat(discountpricehide)) {
                if (confirm('Offered Price is less than ' + discountpricehide + '. Are you sure you want to continue with this price?')) {

                    if (parseFloat(listprice) > 0 && parseFloat(discountprice) != '') {
                        var netamt = parseFloat(listprice) - parseFloat(discountprice);
                        $('#netprice' + i).val(netamt);
                    } else {
                        $('#netprice' + i).val(0);
                    }
                } else {
                    
                    $("#discountprice" + i).val('');
                }
            } else {
                var netamt = parseFloat(listprice) - parseFloat(discountprice);
                $('#netprice' + i).val(netamt);
            }

        }

          function getnetamtedit(i) {
            var qty = $('#qtyedit' + i).val();

            var listprice = $('#listpriceedit' + i).val();
            var discountprice = $('#discountpriceedit' + i).val();
            var discountpricehide = $('#discountpricehideedit' + i).val();
        
            if(parseFloat(listprice) > 0) {
                $("#discountpriceedit" + i).attr('readonly', false);
                $('#netpriceedit' + i).val(listprice);
            } else {
                $("#discountpriceedit" + i).attr('readonly', true);
                $('#netpriceedit' + i).val(0);
            }

            if (parseFloat(listprice) < parseFloat(discountpricehide)) {
                if (confirm('Offered Price is less than ' + discountpricehide + '. Are you sure you want to continue with this price?')) {

                    if (parseFloat(listprice) > 0 && parseFloat(discountprice) != '') {
                        var netamt = parseFloat(listprice) - parseFloat(discountprice);
                        $('#netpriceedit' + i).val(netamt);
                    } else {
                        $('#netpriceedit' + i).val(0);
                    }
                } else {
                    
                    $("#discountpriceedit" + i).val('');
                }
            } else {
                var netamt = parseFloat(listprice) - parseFloat(discountprice);
                $('#netpriceedit' + i).val(netamt);
            }

        }

        function add_product_data()
        {
            if($('#add_product').is(":checked"))
            {
                $("#shownewproduct").css('display','');

                $("#product0").addClass('mand');
                $("#qty0").addClass('mand');
                $("#listprice0").addClass('mand');
                $("#discountprice0").addClass('mand');
                $("#netprice0").addClass('mand');
                $('.remarkbox').css('display','');


            }else
            {
                 $("#shownewproduct").css('display','none');
                $("#product0").removeClass('mand');
                $("#qty0").removeClass('mand');
                $("#listprice0").removeClass('mand');
                $("#discountprice0").removeClass('mand');
                $("#netprice0").removeClass('mand');
                $("input[name='qty[]']").attr('required',false);
                $("input[name='listprice[]']").attr('required',false);
            }
        }

        function add_product_data_won()
        {
            if($('#add_product_won').is(":checked"))
            {
                $("#shownewproduct_won").css('display','');

                $("#product10").addClass('mand');
                $("#qty10").addClass('mand');
                $("#listprice10").addClass('mand');
                $("#discountprice10").addClass('mand');
                $("#netprice10").addClass('mand');
                // $('.remarkbox').css('display','');


            }else
            {
                 $("#shownewproduct_won").css('display','none');
                $("#product10").removeClass('mand');
                $("#qty10").removeClass('mand');
                $("#listprice10").removeClass('mand');
                $("#discountprice10").removeClass('mand');
                $("#netprice10").removeClass('mand');
                // $("input[name='qty[]']").attr('required',false);
                // $("input[name='listprice[]']").attr('required',false);
            }


        }

          function geteditprice(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        var arr = data.split('|');
                        if(arr[0]>0)
                        {
                        // $("#listpriceedit" + i).val(arr[0]);
                        $("#netpriceedit" + i).val(arr[0]);
                        }else
                        {
                        // $("#listpriceedit" + i).val('');
                        $("#netpriceedit" + i).val('');
                        }
                        $(".list_price_unit" + i).text(arr[1]);
                        $("#unitedit" + i).val(arr[1]);
                        $("#discountpriceshow" + i).css('display', '');
                        $("#discountpriceshowedit" +pid+i).text('display', '');


                        $("#discountpriceshowedit" +i).text('Allowed Price: '+arr[3]);
                        $("#discountpriceshowedit" +i).css('color', 'red');
                        $("#discountpriceshowedit" +i).css('font-weight', 'bold');

                        $("#unit_id_edit"+i).val(arr[2]);

                        if (arr[0] > 0) {
                            $("#discountprice" + i).attr('readonly', false);
                        } else {
                            $("#discountprice" + i).val(0);
                            $("#discountprice" + i).attr('readonly', true);
                        }
                    }
                });
            }
        }
        
        function getprice(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        var arr = data.split('|');
                        if(arr[0]>0)
                        {
                        // $("#listprice" + i).val(arr[0]);
                        $("#netprice" + i).val(arr[0]);
                        }else
                        {
                        // $("#listprice" + i).val('');
                        $("#netprice" + i).val('');
                        }
                        $(".list_price_unit" + i).text(arr[1]);
                        $("#unit" + i).val(arr[1]);
                        $("#unit_id" + i).val(arr[2]);
                        $("#discountpriceshow" + i).css('display', '');

                        if (arr[0] > 0) {
                            $("#discountprice" + i).attr('readonly', false);
                        } else {
                            $("#discountprice" + i).val(0);
                            $("#discountprice" + i).attr('readonly', true);
                        }
                    }
                });
            }
        }

          function getprice1(i, pid) {
            var proid = pid;
            
            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getproductpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        var arr = data.split('|');
                        if(arr[0]>0)
                        {
                        // $("#listprice" + i).val(arr[0]);
                        $("#netprice1" + i).val(arr[0]);
                        }else
                        {
                        // $("#listprice" + i).val('');
                        $("#netprice1" + i).val('');
                        }
                        $(".list_price_unit1" + i).text(arr[1]);
                        $("#unit1" + i).val(arr[1]);
                        $("#unit_id1" + i).val(arr[2]);
                        $("#discountpriceshow1" + i).css('display', '');

                        if (arr[0] > 0) {
                            $("#discountprice1" + i).attr('readonly', false);
                        } else {
                            $("#discountprice1" + i).val(0);
                            $("#discountprice1" + i).attr('readonly', true);
                        }
                    }
                });
            }
        }

        function getdiscount(i, pid) {
            var proid = pid;
            $("#discountpriceshow" + i).css('display', 'none');

            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getdiscountpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        $("#discountpricehide"+i).val(data);
                        $("#discountpriceshow" + i).text('Allowed Price: '+data);
                        $("#discountpriceshow" + i).css('color', 'red');
                        $("#discountpriceshow" + i).css('font-weight', 'bold');
                    }
                });
            }
        }


        function getdiscount1(i, pid) {
            var proid = pid;
            $("#discountpriceshow1" + i).css('display', 'none');

            if (proid != '') {
                $.ajax({
                    type: "post",
                    url: "<?php echo page_url; ?>Customer/getdiscountpricedata",
                    data: "proid=" + proid,
                    success: function(data) {
                        $("#discountpricehide1"+i).val(data);
                        $("#discountpriceshow1" + i).text('Allowed Price: '+data);
                        $("#discountpriceshow1" + i).css('color', 'red');
                        $("#discountpriceshow1" + i).css('font-weight', 'bold');
                    }
                });
            }
        }

        function allow_decimal(data) {
            var self = $("#" + data);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }


        }

        function checkfreight() {
            $('#freight').css('display', 'none');
            $('#freight_amt').removeClass('mand');
            
            if($('input[name=check_freight]').is(':checked')) {
              $('#freight').css('display', '');
              $('#freight_amt').addClass('mand');
            }
        }

                function check_shipping_pincode() {
              var shipping_pincode = $('#shipping_pincode').val();
              var zipRegex = /^\d{6}$/;

              if(shipping_pincode!='')
              {
              if (!zipRegex.test(shipping_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#shipping_pincode').val('');
              }
            }
        }

        function check_shipping_email() {
          var shipping_email = $('#shipping_email').val();
          var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(shipping_email!='')
            {
            if (!regex.test(shipping_email)) {
              alert('Invalid Email ID!');
              $('#shipping_email').val('');
            }
            }
        }

        function check_shipping_mobile() {
          var shipping_mobile_no = $('#shipping_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            if(shipping_mobile_no!='')
            {
            if (!regex.test(shipping_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#shipping_mobile_no').val('');
            }
            }
        }

        function check_billing_pincode() {
              var billing_pincode = $('#billing_pincode').val();
              var zipRegex = /^\d{6}$/;
              if(billing_pincode!='')
              {
              if (!zipRegex.test(billing_pincode))
              {
                  alert('Invalid Pincode!');
                  $('#billing_pincode').val('');
              }
          }
        }

        function check_billing_email() {
          var billing_email = $('#billing_email').val();
          // alert(billing_email);
          if(billing_email != '') {
              var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                
                if (!regex.test(billing_email)) {
                  alert('Invalid Email ID!');
                  $('#billing_email').val('');
                }
          }
        }

        function check_billing_mobile() {
          var billing_mobile_no = $('#billing_mobile_no').val();
          var regex = /^[7-9][0-9]{9}$/;
            
            if(billing_mobile_no!='')
            {
            if (!regex.test(billing_mobile_no)) {
              alert('Invalid Mobile No!');
              $('#billing_mobile_no').val('');
            }
            }
        }

         function check_pan() {
          var pan_no = $('#pan_no').val();
          var regex = /[a-zA-z]{5}\d{4}[a-zA-Z]{1}/;
            if(pan_no!='')
            {
            if (!regex.test(pan_no)) {
              alert('Invalid PAN No!');
              $('#pan_no').val('');
            }
            }
        }

        function CKEditorChange(name) {
          CKEDITOR.replace(name, {
            toolbar: [{
                name: 'clipboard',
                items: ['Undo', 'Redo']
              },
              {
                name: 'styles',
                items: ['Format', 'Font', 'FontSize']
              },
              {
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting']
              },
              {
                name: 'colors',
                items: ['TextColor', 'BGColor']
              },
              {
                name: 'align',
                items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
              },
              {
                name: 'links',
                items: ['Link', 'Unlink']
              },
              {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
              },
              {
                name: 'insert',
                items: ['Image', 'Table']
              },
              {
                name: 'tools',
                items: ['Maximize']
              },
              {
                name: 'editing',
                items: ['Scayt']
              }
            ]
          });
        }


        function validate() {
          var leadquality=$("#leadquality").val();
          $("#saves").attr('disabled', false);
          $("#saves").val('Update Followup Remarks');

          var isValid = 0;
          var isValid1 = 0;
          var isValid2 = 0;

          $(".mand").each(function() {
            var element = $(this).val();
            console.log($(this));
            if (element == "") {
              isValid = 1;
            }
          });

        //   function validateRadio() {
        //     if ($('.work_type').is(':checked')) {
        //         var valid = checkIfAllWorkFilled();
        //         return valid;
        //     } else {
        //         alert('Select the type of work before proceeding');
        //         return false;
        //     }
        // }

        if(leadquality==9)
        {
            var chlend=$('[class="sendsample"]:checked').length;

            if(chlend>0)

            {
                isValid1=0;
            }else
            {
                alert('Atleast one sample needs to be send');
                isValid1=1;
            }

        }


          if(leadquality==10)
        {
            var chlend=$('[class="sendtrail"]:checked').length;

            if(chlend>0)

            {
                isValid2=0;
            }else
            {
                alert('Atleast one Trial needs to be checked');
                isValid2=1;
            }

        }

        // alert(isValid);
        // alert(isValid1);
        // alert(isValid2);
          if (isValid == 0 && isValid1==0 && isValid2==0)
          {
            $("#saves").attr('disabled', true);
            $("#saves").val('Please Wait..');
            return true;
          } else
          {

            $("#saves").attr('disabled', false);
            $("#saves").val('Update Followup Remarks');
            alert('All fields marked with (*) are mandatory');
            return false;
          }
        }



        function removeFollowup(id) {

          if (id == 11 || id == 2 || id == 6 || id == 5) {

            $("#followup").css('display', 'none');

            $("#followup_date").removeClass('mand');

          }

        }



        function show_discount(id) {

          $(".discount_type" + id).css('display', 'none');
          var discount_type = $("#discount_type" + id).val();

          if (discount_type != 3) {
            $(".discount_type" + id).css('display', '');

          }

        }



        function show_discountedit(id) {

          $(".discount_typeedits" + id).css('display', 'none');
          var discount_type = $("#discount_typeedit" + id).val();
          if (discount_type != 3) {

            $(".discount_typeedits" + id).css('display', '');


          }

        }
      </script>

<script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "450px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
        }


        function calculate_stock(flag)
        {
            var hpcl_company = "<?php echo $hpcl_company;?>";
           // $("#saves").attr('disabled',true);
            $("#qty_stock"+flag).html('');
            var qty=$("#qtyedit1"+flag).val();
            var product=$("#productedit1"+flag).val();
            if(qty!='')
            {
                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_availability",
                data:"compid="+hpcl_company+"&qty="+qty+"&product_id="+product,
                success: function(data) {
                    if(data>0)
                    {
                        //$("#saves").attr('disabled',false);
                    }else
                    {
                       //$("#saves").attr('disabled',true);
                       $("#qty_stock"+flag).html('Stock Not Available');

                    }

                }
                });
            }


        }

         function calculate_stock_new(flag)
        {
            var hpcl_company = "<?php echo $hpcl_company;?>";
            $("#saves").attr('disabled',true);
            $("#qty_stock1"+flag).html('');
            var qty=$("#qty1"+flag).val();
            var product=$("#product1"+flag).val();
            if(qty!='' && product!='')
            {
                $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_availability",
                data:"compid="+hpcl_company+"&qty="+qty+"&product_id="+product,
                success: function(data) {
                    if(data>0)
                    {
                        $("#saves").attr('disabled',false);
                    }else
                    {
                       $("#saves").attr('disabled',true);
                       $("#qty_stock1"+flag).html('Stock Not Available');

                    }

                }
                });
            }


        }


        function getstock1(flag)
        {
             $("#saves").attr('disabled',true);
             var hpcl_company = "<?php echo $hpcl_company;?>";
             var product=$("#product1"+flag).val();

               $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_available",
                data:"compid="+hpcl_company+"&product_id="+product,
                success: function(data) {

                    $("#stock1"+flag).val(data);
                    $("#saves").attr('disabled',false);
                    
                }
                });

        }

        function match_stock_availability(flag)
        {
            // var stock_avail=$("#stockedit1"+flag).val();
            // if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
            // var qty_edit=$("#qtyedit1"+flag).val();
            // if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

            // if(parseFloat(qty_edit)>parseFloat(stock_avail))
            // {
            //     alert('Available Stock is less than inputted Qty. Please change the Qty');
            //     $("#qtyedit1"+flag).val('');
            // }

        }


         function match_stock_availability1(flag)
        {
           var product=$("#product1"+flag).val();
           var stock_avail=$("#stock1"+flag).val();
           if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
            var qty_edit=$("#qty1"+flag).val();
            if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

             if(parseFloat(qty_edit)>parseFloat(stock_avail))
            {
                alert('Available Stock is less than inputted Qty. Please change the Qty');
                $("#qty1"+flag).val('');
            }

        }

        function getstock(flag)
        {

             $("#saves").attr('disabled',true);
             var hpcl_company = "<?php echo $hpcl_company;?>";
             var product=$("#product"+flag).val();

               $.ajax({
                type: "post",
                url: "<?php echo page_url; ?>Leads/check_stock_available",
                data:"compid="+hpcl_company+"&product_id="+product,
                success: function(data) {

                    $("#stock"+flag).val(data);
                    $("#saves").attr('disabled',false);
                    
                }
                });

        }

        function match_stock_availability2(flag)
        {

                // var product=$("#product"+flag).val();
                // var stock_avail=$("#stock"+flag).val();
                // if(stock_avail!=''){ var stock_avail=stock_avail}else{ var stock_avail=0;}
                // var qty_edit=$("#qty"+flag).val();
                // if(qty_edit!=''){ var qty_edit=qty_edit}else{ var qty_edit=0;}

                // if(parseFloat(qty_edit)>parseFloat(stock_avail))
                // {
                // alert('Available Stock is less than inputted Qty. Please change the Qty');
                // $("#qty"+flag).val('');
                // }
 
        }
    </script>

</body>

</html>
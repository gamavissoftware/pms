<?php
$DI = &get_instance();
$DI->load->model('Dashboard_model');

  $mode = 1;

$pistep = $DI->Dashboard_model->getsinglePIstep();
$quotestep = $DI->Dashboard_model->getsingleQuotestep();
$CI = &get_instance();
$CI->load->model('Salescrm_model', 'salescrm');
$productdetail=$DI->Dashboard_model->getproductdetails($this->uri->segment(3));
$getLeadStatus = $CI->salescrm->getLeadStatus($this->uri->segment(3));
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



// echo $color;exi
$color = '';

?>



<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <meta name="description" content="">

  <meta name="author" content="<?php echo copyright; ?>">



  <link rel="shortcut icon" href="">



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



    .iii[disabled] {

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

    }

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
  </style>

</head>





<body>





  <!-- Navigation Bar-->

  <header id="topnav">

    <?php $this->load->view('common/nav-menu'); ?>

  </header>



  <div class="wrapper">
    <div class="container">
      <span style="color:red;"><?php echo $this->session->flashdata('updatemessage'); ?></span>
      <!-- Page-Title -->
      <div class="row">

        <div class="col-sm-12" >
        <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btn-xs" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>

          <div class=" " style="margin-top: 30px;">
            <div class="profile-info-name">
              <?php
              $user_role = $this->session->userdata['logged_in']['role'];

              $query = $this->db->select('a.*,d.zone,b.country_id, b.country_name, c.state_name,e.company_name as client_company,e.email as clientEmail, e.customer_name as csname, e.contact_no as contactnos')

                ->from('leads a')
                ->join('customer_detail e','a.company_name=e.id')
                ->join('countries b', 'a.country=b.country_id', 'left')
                ->join('saleszone d','a.client_location=d.id','left')

                ->join('states c', 'a.state=c.state_id', 'left')

                ->where('a.id', $this->uri->segment(3))

                ->get();

              foreach ($query->result() as $customer_detail)

               
              ?>

              <div class="profile-info-detail" style="margin-top:1px">
                <?php
              if ($customer_detail->country_code == '91') {
                $customer_contact_number = $customer_detail->contact_no;
              } else {
                $customer_contact_number = "00" . $customer_detail->country_code . $customer_detail->contact_no;
              }

              $call = base64_encode($customer_contact_number);

                ?>

                <div class="text-center card-box search-page">
                  <h3 class="m-t-0 m-b-0">
                    <?php echo ucwords($customer_detail->client_company); ?> - <?php echo $customer_detail->unique_id; ?></h3>
                  <br />
                  <div class="text-center">
                    <?php

                    if ($quotesend > 0) {

                     $restqwqw=$this->db->select('id')->from('quotation_customer_data')->where('lead_id',$this->uri->segment(3))->get();
                     if($restqwqw->num_rows()>0)
                     {
                      foreach($restqwqw->result() as $row);
                      $d=$row->id;
                     }else
                     {
                      $d=0;
                     }
                    ?>
                      <a href="<?php echo page_url;?>Opportunity/GeneratedQuote/<?php echo $d;?>" target="_blank"><span class="btn btn-success btn-xs">Generated Quotation </span></a>&nbsp;&nbsp;
                    <?php
                    }
                    ?>

                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-5">
                    <div class="card-box search-page">
                      <h5>Overview</h5>

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
                          <p><strong>Opportunity Type:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo $patient_type; ?> </p>
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
                          <p><strong>Opportunity Date:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo date('d-m-Y', strtotime($customer_detail->create_date)); ?></p>
                        </div>
                      </div>

                       <div class="row">
                        <div class="col-sm-6 col-xs-6">
                          <p><strong>Company Name:</strong></p>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                          <p><?php echo ucwords($customer_detail->client_company); ?></p>
                        </div>
                      </div>


                      <!---------------------------------------------------------->

                      
                       <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong>Country:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->country_name; ?></p>
                          </div>
                        </div>
                    
                        <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong>Email:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->clientEmail; ?></p>
                          </div>
                        </div>

                         <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong>Contact Person Name:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->csname; ?></p>
                          </div>
                        </div>

                          <div class="row">
                          <div class="col-sm-6 col-xs-6">
                            <p><strong>Contact No:</strong></p>
                          </div>
                          <div class="col-sm-6 col-xs-6">
                            <p><?php echo $customer_detail->contactnos; ?></p>
                          </div>
                        </div><hr>


                      


                       
                      

                      
                      <!------------------------------------------>

                     
                     
                      <div class="clearfix"></div>
                    </div>

                  </div>
                  <div class="col-sm-7">
                    <div class="card-box search-page" style="height: 166px; overflow-y: auto;">
                      <h5>Commercial</h5>
                       <table>
                                        <tr>
                                            <th>S.no</th>
                                            <th>Machine Type</th>
                                            <th>Machine</th>
                                             <th>Qty</th>
                                      
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

                                              
                                        ?>
                                        <tr>
                                            <td><?php echo $i+1;?></td>
                                            <td><?php if($customer_detail->machine_type==1){?> Liquid <?php }else if($customer_detail->machine_type==2){echo "Powder";}else{ ?> Customise <?php } ?></td>
                                            <td><?php echo $productdetail[$i]['name'];?></td>
                                            <td><?php echo $qty;?> Nos.</td>
                                           
                                        </tr>
                                        <?php  
                                        }                                       
                                        ?>
                                       
                                    <?php }else{ ?>

                                            <tr>
                                            <td colspan="6">No Product Available</td>
                                        
                                    
                                            </tr>

                                    <?php } ?>
                                    </table>
                    </div>

                    <div class="card-box search-page">
                      <h5>Quotation Version</h5><hr>
                      <table>
                        <thead>
                      <tr>
                      <th>S.no</th>
                      <th>View Quotation</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php

                    
                      $ret=$this->db->select('id')->from('quotation_customer_data')->where('lead_id',$this->uri->segment(3))->get();
                      if($ret->num_rows()>0)
                      {
                        foreach($ret->result() as $rr);
                        $recordid=$rr->id;
                      }else
                      {
                        $recordid=0;

                      }

                        $getopprtunityuniquecode = $this->salescrm->getopportunitygeneraterefno($recordid);


                      $version = $this->salescrm->getopportunityversionfno($recordid);
                      //echo $version; exit;
                      $refrencenumber = str_replace('/','_',$getopprtunityuniquecode);
                      //echo $getopprtunityuniquecode; exit;


                      $filelocation = softwarepath.'shubhamquotation/';
                      

                       $q = $this->db->select('version')->from('quotation_customer_data')->where('lead_id',$this->uri->segment(3))->get();
                      if($q->num_rows()>0){
                        foreach($q->result() as $versioninfo);
                        $v = $versioninfo->version;
                        for($i=1; $i<=$v; $i++){
                          $fileNL='Quotation_'.$refrencenumber.'_V'.$i.'.pdf';
                          $fileurl = $filelocation.'Quotation_'.$refrencenumber.'_V'.$i.'.pdf';
                        ?>
                      <tr>
                        <td><?php echo $i;?></td>
                        <td><a href="<?php echo $fileurl;?>" target="_blank"><?php echo $fileNL;?></a></td>
                      </tr>
                    <?php 
                    } }?>

                    </tbody>

                    </table>

                    </div>
                  </div>
                </div>

              </div>
            </div>

            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



            <?php

            //echo $checkLeadAssignment;exit;
            if ($mode == 1) {
                $checkLeadAssignment=1;
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
            }

            ?>



            <!------------------To disable Folloup Use disabled attribute--------------------->

            <div class="iii" <?php if ($mode == 1) {
                                echo $disabled;
                              } ?>>

              <?php
              if ($mode == 1) {
                if ($checkLeadAssignment == 0) { ?>

                  <i class="fa fa-lock" aria-hidden="true"></i>

              <?php }
              } ?>




              <form method="post" class="card-box" method="post" action="<?php echo page_url; ?>Leads/update_remarksNew/<?php echo $this->uri->segment(3); ?>" enctype="multipart/form-data" onsubmit="return validate()">
                <div class="row">
                  <div class="col-md-3">
                    <label>Previous Status</label>
                    <input type="text" class="form-control" name="previous_status" id="previous_status" value="<?php echo $lead_name; ?>" readonly>
                     <input type="hidden" name="previous_lead_stage" value="<?php echo $lead_id;?>">

                    <label>Lead Stage<span style="color: red">*</span></label>
                    <?php
                      if($lead_id==36){?>
                        <?php if($_SESSION['logged_in']['user_id']<>139){?>
                        <br><br><span style="color:red">This Opportunity in Pending for approval. Please request Sir to appove.</span>
                      <?php }?>
                        <?php 
                     if($_SESSION['logged_in']['user_id']==139) {?>

                    <select class="form-control mand" name="leadquality" id="leadquality" onchange="getFields(); removeFollowup(this.value)" required>
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

                  <?php }}else{?>
                      <select class="form-control mand" name="leadquality" id="leadquality" onchange="getFields(); removeFollowup(this.value)" required>
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
                  <?php }?>


                    <div id="followup" style="display: none;">
                      <label>Next Followup Date<span style="color: red">*</span></label>
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
                      <label>Remarks<span style="color: red">*</span></label>
                      <textarea rows="2" name="remarks" id="remarks" class="form-control mand" placeholder="Remarks"></textarea>
                    </span>
                  </div>

                  <div class="col-md-4">
                    <span class="input-icon icon-right" style="margin-bottom:10px">
                      <input type="file" name="upload_file[]" multiple>
                    </span>
                  </div>

               
                </div>

                <div class="product_details" style="display: none;">
                  <?php $get_products = $CI->salescrm->getProducts($this->uri->segment(3)); ?>
                  <div class="row">
                    <?php foreach ($get_products as $products) { ?>
                      <div class="col-md-12">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Product Name</label>
                            <input type="hidden" name="product_id[]" value="<?php echo $products->id; ?>">
                            <input type="hidden" name="lead_product_id[]" value="<?php echo $products->leadproduct; ?>">
                            <input type="text" class="form-control" value="<?php echo $products->instruments_name; ?>" readonly>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Qty</label>
                            <input type="text" class="form-control" name="qty<?php echo $products->leadproduct; ?>" id="qty" value="1" min="1">
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Price</label>
                            <input type="text" class="form-control" name="price<?php echo $products->leadproduct; ?>" id="price" value="<?php echo $products->mvalue; ?>">
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Discount In</label>
                            <select class="form-control" name="discount_type<?php echo $products->leadproduct; ?>" id="discount_type<?php echo $products->leadproduct; ?>" onchange="show_discount(<?php echo $products->leadproduct; ?>)">
                              <option value="3" selected>Not Applicable</option>
                              <option value="0">In Percent (%)</option>
                              <option value="1">Fix Amount</option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-2 discount_type<?php echo $products->leadproduct; ?>" style="display: none;">
                          <div class="form-group">
                            <label>Percent/Amt</label>
                            <input type="number" class="form-control" name="percent_amt<?php echo $products->leadproduct; ?>" id="percent_amt">
                          </div>
                        </div>
                      </div>

                    <?php } ?>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label>Packing In</label>
                          <select class="form-control" name="packing_type" id="packingtype" onchange="getpackinginfo();">
                            <option value="3">Not Applicable</option>
                            <option value="1">In Percent</option>
                            <option value="2">Fixed Amount</option>
                          </select>
                        </div>
                      </div>

                      <script>
                        function getpackinginfo() {
                          $("#packingbox").css('display', 'none');
                          $("#fixedamountshow").css('display', 'none');
                          $("#inpercentshow").css('display', 'none');
                          $("#packing_charges").css('required', false);
                          var packing_type = $("#packingtype").val();
                          if (packing_type != 3) {

                            $("#packingbox").css('display', '');
                            $("#packing_charges").attr('required', true);

                            if (packing_type == 1) {
                              $("#inpercentshow").css('display', '');
                            } else {
                              $("#fixedamountshow").css('display', '');
                            }
                          }
                        }
                      </script>


                      <div class="col-md-3" id="packingbox" style="display:none;">
                        <div class="form-group">
                          <label>Packing Charges<span id="inpercentshow" style="display:none;">In (%)</span><span id="fixedamountshow" style="display:none;">In Amount</span></label>
                          <input type="text" class="form-control" name="packing_charges" id="packing_charges" value="">
                        </div>
                      </div>

                      <div class="col-md-2">
                        <div class="form-group">
                          <label>Freight at actual</label><br />
                          <select name="freight_actual" id="freight_actual" onchange="fcharges();" class="form-control">
                            <option value="1">At Actuals</option>
                            <option value="0">Freight Extra</option>
                          </select>
                        </div>
                      </div>

                      <script>
                        function fcharges() {
                          $("#freight_charges").attr('required', false);
                          $("#fcharges").css('display', 'none');
                          var freight = $("#freight_actual").val();
                          if (freight == 0) {
                            $("#freight_charges").attr('required', true);
                            $("#fcharges").css('display', '');
                          }

                        }
                      </script>

                      <div class="col-md-3" id="fcharges">
                        <div class="form-group">
                          <label>Freight Charges</label>
                          <input type="text" class="form-control" name="freight_charges" id="freight_charges">
                        </div>
                      </div>

                      <script>
                        $(document).ready(function() {
                          $('#leadquality').on('change', function() {
                            if (this.value == '3') {
                              $("#termconditionsdiv").show();
                            } else {
                              $("#termconditionsdiv").hide();
                            }
                          });
                        });
                      </script>
                    </div>
                  </div>
                </div>




                <div class="product_detailsedit" style="display: none;">

                  <?php $get_products = $CI->salescrm->getProducts($this->uri->segment(3)); ?>

                  <div class="row">

                    <?php foreach ($get_products as $productss) {

                      $quoteddata = $CI->salescrm->getquoteddata($productss->leadproduct);

                      $quotesdata = $CI->salescrm->getquotedpackdata($this->uri->segment(3));


                      if (count($quoteddata) > 0) {
                        $qty = $quoteddata['qty'];
                        $price = $quoteddata['price'];
                        $discounttype = $quoteddata['discount_type'];
                        $percentamt = $quoteddata['percent_amt'];
                      } else {
                        $qty = '';
                        $price = '';
                        $discounttype = '';
                        $percentamt = '';
                      }

                      if (count($quotesdata) > 0) {
                        $packing_type = $quotesdata['packing_type'];
                        $packing_price = $quotesdata['packing_price'];
                        $freight_actual = $quotesdata['freight_actual'];
                        $freight_charges = $quotesdata['freight_charges'];
                      } else {
                        $packing_type = '';
                        $packing_price = '';
                        $freight_actual = '';
                        $freight_charges = '';
                      }
                    ?>

                      <div class="col-md-12">
                        <div class="col-md-4">
                          <div class="form-group">
                            <label>Product Name</label>
                            <input type="hidden" name="product_idedit[]" value="<?php echo $productss->id; ?>">
                            <input type="hidden" name="lead_product_idedit[]" value="<?php echo $productss->leadproduct; ?>" required>
                            <input type="text" class="form-control" value="<?php echo $productss->instruments_name; ?>" readonly required>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Qty</label>
                            <input type="text" class="form-control" name="qtyedit<?php echo $productss->leadproduct; ?>" id="qtyedit" value="<?php echo $qty; ?>" required>
                          </div>
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Price</label>
                            <input type="text" class="form-control" name="priceedit<?php echo $productss->leadproduct; ?>" id="priceedit" value="<?php echo $price; ?>" required>
                          </div>
                        </div>


                        <?php
                        if ($discounttype != 3) {
                          $di = "";
                        } else {
                          $di = "display:none";
                        }

                        ?>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label>Discount In</label>
                            <select class="form-control" name="discount_typeedit<?php echo $productss->leadproduct; ?>" id="discount_typeedit<?php echo $productss->leadproduct; ?>" onchange="show_discountedit(<?php echo $productss->leadproduct; ?>)" required>
                              <option value="">Select Option</option>
                              <option value="3" <?php if ($discounttype == 3) { ?> selected <?php } ?>>Not Applicable</option>
                              <option value="0" <?php if ($discounttype == 0) { ?> selected <?php } ?>>In Percent (%)</option>
                              <option value="1" <?php if ($discounttype == 1) { ?> selected <?php } ?>>Fix Amount</option>
                            </select>
                          </div>
                        </div>

                        <div class="col-md-2 discount_typeedits<?php echo $productss->leadproduct; ?>" style="<?php echo $di; ?>">
                          <div class="form-group">
                            <label>Percent/Amt</label>
                            <input type="number" class="form-control" name="percent_amtedit<?php echo $productss->leadproduct; ?>" id="percent_amtedit<?php echo $productss->leadproduct; ?>" value="<?php echo $percentamt; ?>">
                          </div>
                        </div>
                      </div>

                    <?php } ?>

                  </div>





                  <div class="row">

                    <div class="col-md-12">

                      <div class="col-md-3">

                        <div class="form-group">

                          <label>Packing In</label>

                          <select class="form-control" name="packing_typeedit" id="packingtypeedit" onchange="getpackinginfoedit();">

                            <option value="3" <?php if ($packing_type == 3) { ?> selected <?php } ?>>Not Applicable</option>
                            <option value="1" <?php if ($packing_type == 1) { ?> selected <?php } ?>>In Percent</option>
                            <option value="2" <?php if ($packing_type == 2) { ?> selected <?php } ?>>Fixed Amount</option>

                          </select>

                        </div>

                      </div>
                      <script>
                        function getpackinginfoedit() {


                          $("#packingboxedit").css('display', 'none');
                          $("#fixedamountshowedit").css('display', 'none');
                          $("#inpercentshowedit").css('display', 'none');
                          $("#packing_chargesedit").css('required', false);
                          var packing_type = $("#packingtypeedit").val();
                          if (packing_type != 3) {

                            $("#packingboxedit").css('display', '');
                            $("#packing_chargesedit").attr('required', true);

                            if (packing_type == 1) {
                              $("#inpercentshowedit").css('display', '');
                            } else {
                              $("#fixedamountshowedit").css('display', '');
                            }
                          }
                        }
                      </script>


                      <?php if ($packing_type <> 3) {
                        $packdi = "";
                        $fix = "display:none";
                        $per = "display:none";
                      } else {
                        $packdi = "display:none";
                        if ($packing_type == 1) {

                          $per = "display:block;";
                          $fix = "display:none";
                        } else if ($packing_type == 2) {
                          $per = "display:none;";
                          $fix = "display:block;";
                        } else {

                          $per = "display:none;";
                          $fix = "display:none;";
                        }
                      }
                      ?>


                      <div class="col-md-3" style="<?php echo $packdi; ?>" id="packingboxedit">
                        <div class="form-group">
                          <label>Packing Charges <span id="inpercentshowedit" style="<?php echo $per; ?>">In (%)</span><span id="fixedamountshowedit" style="<?php echo $fix; ?>">In Amount</span></label>


                          <input type="text" class="form-control" name="packing_chargesedit" id="packing_chargesedit" value="<?php echo $packing_price; ?>">
                        </div>
                      </div>

                      <div class="col-md-2">

                        <div class="form-group">

                          <label>Freight at actual</label><br />

                          <Select name="freight_actualedit" id="freight_actualedit" onchange="fchargesedit();" required class="form-control">
                            <option value="1" <?php if ($freight_actual == 1) { ?> selected <?php } ?>>At Actuals</option>
                            <option value="0" <?php if ($freight_actual == 0) { ?> selected <?php } ?>>Freight Extra</option>
                          </select>
                          <!--<input type="checkbox" name="freight_actual" id="freight_actual" value="1" onchange="fcharges();">-->

                        </div>

                      </div>
                      <script>
                        function fchargesedit() {
                          $("#freight_chargesedit").attr('required', false);
                          $("#fchargesedit").css('display', 'none');
                          var freight = $("#freight_actualedit").val();

                          if (freight == 0) {
                            $("#freight_chargesedit").attr('required', true);
                            $("#fchargesedit").css('display', '');

                          }


                        }
                      </script>

                      <?php
                      if ($freight_actual <> 1) {
                        $fa = "";
                      } else {
                        $fa = "display:none";
                      }
                      ?>
                      <div class="col-md-3" id="fchargesedit" style="<?php echo $fa; ?>">

                        <div class="form-group">

                          <label>Freight Charges</label>

                          <input type="text" class="form-control" name="freight_chargesedit" id="freight_chargesedit" value="<?php echo $freight_charges; ?>">

                        </div>

                      </div>




                    </div>

                    <div class="col-md-12" id="terms">
                      <div class="col-md-4">

                        <div class="form-group">

                          <label>CUSTOMER GST <span style="color:red">*</span></label>

                          <input type="text" class="form-control" name="customer_gst" id="customer_gst" value="<?php echo $customer_detail->gst; ?>">

                        </div>

                      </div>

                      <?php
                      $terms='';

                      ?>


                      <div class="col-md-8">

                        <div class="form-group">

                          <label>Terms & Conditions <span style="color:red">*</span></label>
                          <textarea name="termscondition" id="termscondition"><?php echo $terms; ?></textarea>
                          <script>
                            CKEDITOR.replace('termscondition');
                          </script>
                        </div>
                      </div>

                    </div>

                  </div>

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
                    <th>Lead Stage</th>
                    <th>Followup Remarks</th>
                     <th>Uploaded Files</th>
                    <th>Added On</th>
                    <th>Added By</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  $query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name, c.lead_name')
                    ->from('progress_remarks a')
                    ->join('system_users b', 'a.added_by=b.user_id')
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

                      $edit = "<a href='" . page_url . "Leads/edit_progress_report/" . $row->lead_id . "/" . $row->id . "'><i class='fa fa-pencil' title='Edit Lead'></i></a>";

                     ?>

                        <tr>

                          <td><?php echo $i; ?></td>

                          <td><?php echo $next_followup_date; ?></td>

                          <td><?php echo $row->lead_name; ?><br /><?php echo $reason; ?></td>



                          <td><?php echo $row->remarks; ?></td>
                          <td>
                            <?php
                              $upload_file = ''; 
                              $sql = $this->db->select('upload_file')
                                              ->from('lead_remarks_files')
                                              ->where('remarks_id', $row->id)
                                      
                                              ->get();
                              if($sql->num_rows() > 0) {
                              foreach($sql->result() as $rowss) {
                                $upload_file = $rowss->upload_file;?>
                                  <span><a href="<?php echo lead_pictures.$upload_file;?>" download>Download</a></span><br>
                               <?php  } } ?>
                          </td>

                          <td><?php $added = date('d-m-Y H:i:s', strtotime($row->added_on));

                              if ($added <> '30-11--0001 00:00:00') {
                                echo $added;
                              } ?>

                          </td>

                          <td><?php echo $row->first_name . " " . $row->last_name; ?></td>

                          <!-- <td><?php //echo $edit;
                                    ?></td> -->

                        </tr>

                  <?php $i++;
                    
                    }
                  } ?>

                </tbody>
              </table>
            </div>
          </div>

          <!-- end row -->


<div class="row card-box">
    <div class="col-md-12">
      <h4 class="text-center" style="color:red;">Extend Next Followup Date if Quotation Under Approval and getting delayed by Shubham Sir</h4><hr>

        <?php
        // Get the lead_id from the URL (segment 3)
        $lead_id = $this->uri->segment(3);
        ?>

        <div class="form-group">
            <label for="nextfollowupdate">Next Follow-up Date</label>
            <input type="date" name="nextfollowupdate" id="nextfollowupdate" class="form-control" data-lead-id="<?= htmlspecialchars($lead_id ?? ''); ?>" min="<?= date('Y-m-d'); ?>">
        </div>

        <div id="update-status"></div>

        <script>
        $(document).ready(function() {
            $('#nextfollowupdate').on('change', function() {
                
                // 1. Get the data
                var newDate = $(this).val();
                var leadId = $(this).data('lead-id');
                var statusDiv = $('#update-status');

                // Clear previous messages
                statusDiv.html('');

                if (!newDate || !leadId) {
                    console.error('Date or Lead ID is missing.');
                    statusDiv.html('<p style="color:red;">Could not update: Date or Lead ID is missing.</p>').show().fadeOut(5000);
                    return; // Stop if data is not available
                }

                // 2. Perform the AJAX request
                $.ajax({
                    // Using site_url() is the standard and recommended way in CodeIgniter
                    url: '<?= page_url."Leads/update_followup_date"; ?>', 
                    type: 'POST',
                    data: {
                        lead_id: leadId,
                        new_followup_date: newDate,
                        // CSRF Protection
                        '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                    },
                    dataType: 'json',
                    
                    // Disable the input during the request to prevent multiple clicks
                    beforeSend: function() {
                        $('#nextfollowupdate').prop('disabled', true);
                    },
                    
                    // Always re-enable the input when the request is complete
                    complete: function() {
                        $('#nextfollowupdate').prop('disabled', false);
                    },
                    
                    // 3. Handle the success response
                    success: function(response) {
                        if (response && response.status === 'success') {
                            console.log('Success:', response.message);
                            // Use .show() to make sure the div is visible before fading out
                            statusDiv.html('<p style="color:green;">' + response.message + '</p>').show().fadeOut(3000);
                        } else {
                            console.error('Error from server:', response.message);
                            statusDiv.html('<p style="color:red;">' + (response.message || 'An unknown error occurred.') + '</p>').show().fadeOut(5000);
                        }
                    },
                    
                    // 4. Handle AJAX errors (e.g., 404 Not Found, 500 Internal Server Error)
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ' + error);
                        statusDiv.html('<p style="color:red;">An unexpected error occurred. Please check the console.</p>').show().fadeOut(5000);
                    }
                });
            });
        });
        </script>

    </div>
</div>



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


          /** REMOVE ANY VALIDATION **/


          /** END **/




          $('#example').dataTable({

            "bProcessing": true,

            "pagination": true

          });



          $('#example1').dataTable({

            "bProcessing": true,

            "pagination": true,

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

        });
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



        // A helper function to add business days to a date
function addBusinessDays(startDate, days) {
    // Create a copy to avoid changing the original date
    var currentDate = new Date(startDate);
    var businessDaysAdded = 0;

    while (businessDaysAdded < days) {
        // Move to the next day
        currentDate.setDate(currentDate.getDate() + 1);
        var dayOfWeek = currentDate.getDay();

        // Check if the new day is a weekday (Monday=1 to Friday=5)
        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
            businessDaysAdded++;
        }
    }
    return currentDate;
}

// --- Your Datepicker Initialization ---
var startDate = new Date();
var endDate = addBusinessDays(startDate, 15); // Calculate endDate 15 business days out

jQuery('#followup_date').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd-mm-yyyy',
    startDate: startDate,
    endDate: endDate,
    
    // This part stays the same - it disables weekends from being clickable
    beforeShowDay: function(date) {
        var day = date.getDay();
        // Return true only if day is not Saturday (6) or Sunday (0)
        return day !== 0 && day !== 6;
    }
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





        });
      </script>



      <script type="text/javascript">
        function getFields() {
           $("#saves").attr('disabled',false);
          var lead_stage_id = $("#leadquality").val();
          if(lead_stage_id==35){
             document.location="<?php echo page_url;?>Opportunity/orderwon/<?php echo $this->uri->segment(3);?>";
          }
          var leadquality_name = $("#leadquality option:selected").text();
          $("#remark_title").val(leadquality_name);
          $("#followup").css('display', 'none');
          $("#nonqualifiedreason").css('display', 'none');
          $(".product_details").css('display', 'none');
          $(".product_detailsedit").css('display', 'none');
          $("#terms").css('display', 'none');
          $("#termscondition").attr('required', false);
          $("#customergst").attr('required', false);
          $("#gst").css('display', 'none');
          $("#fcharges").css('display', 'none');

          $.ajax({
            type: "post",
            url: "<?php echo page_url; ?>Leads/getLeadStageDetails",
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

              if (quotation_step == 1) {

                //$(".product_details").css('display', '');
                $("#saves").attr('disabled',true);
                document.location="<?php echo page_url;?>Opportunity/newopportunity/<?php echo $this->uri->segment(3);?>";

              } else if (quotation_revised_step == 1) {
                // $(".product_detailsedit").css('display', '');
                // $(".product_detailsedit").css('display', '');
                $("#saves").attr('disabled',true);
                document.location="<?php echo page_url;?>Opportunity/edit_opportunity/<?php echo $this->uri->segment(3);?>";


              } else if (pi_step == 1 || pi_revised_step == 1) {
                $(".product_detailsedit").css('display', '');
                $("#terms").css('display', '');
                $("#termscondition").attr('required', true);
                $("#customergst").attr('required', true);
                $("#gst").css('display', '');
              } else if (followup_date == 1) {
                $("#followup").css('display', '');
              } else if (reason == 1) {
                $("#nonqualifiedreason").css('display', '');
              }
            }
          });
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


        function validate()



        {



          $("#saves").attr('disabled', false);



          $("#saves").val('Update Followup Remarks');







          // $("#form :input").attr('required',false);







          var isValid = 0;



          $(".mand").each(function() {



            var element = $(this).val();



            if (element == "") {







              isValid = 1;



            }











          });











          if (isValid == 0)



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
    </script>

</body>

</html>
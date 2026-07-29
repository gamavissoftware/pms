<?php 
//echo "<pre>"; print_r($_SESSION['logged_in']); exit;
$user_id = base64_decode($this->uri->segment(3));
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle;?>Demo Feedback</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <style>
            table.manglesh thead th {


                background: #332b24;

                text-align: center;
                color:#fff;


                font-weight:bold;


            }

             table.manglesh tbody td {


                text-align: center;
                font-weight:bold;


            }


				#mybutton {
				  position: fixed;
				  bottom: -4px;
				  right: 10px;
				}
				.select2-container {
				    width: 100% !important;
				}

			   .add_more {
		        margin-top: 33px;
		        }

		        .remove {
		        margin-top: 33px;
		        }

		        .delete_product {
		        margin-top: 33px;
		        }
			</style>
			  <style>
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
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
        <div class="container">

                <!-- Page-Title -->
            <div class="row" style="margin-top:20px;">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
	                <div class="page-title-box">
	                    <!-- <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a> -->
					 <div class="btn-group pull-right"></div>
	                   
	                    <h4 class="page-title text-center">Customer Feedback On Demo</h4>
	                </div>
                </div>
            </div>
                <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

        <div class="row">
            <div class="col-xs-12">
                <div class="card-box">
                    <div class="row">
                        <div class="col-sm-12 col-xs-12 col-md-12">


                <?php 
                
                   $qq = $this->db->select('city,customer_name, id, company_name, contact_person,email, contact_no, postal_address, area_name, state_name, type_of_organization, demo_date, demofile')->from('leads')->where('id',$this->uri->segment(3))->get();
                        foreach($qq->result() as $rowss);

                       
                    
                ?>       
<table class="table table-bordered manglesh">
    <thead>
      <tr>
        <th>Name of Organization/Firm</th>
        <th>Type of Organization/ Firm</th>
        <th>Address </th>
        <th>City </th>
        <th>State </th>
        <th>Company Demo Person Name </th>
        <th>Demo Date  </th>
        <th>Any Customization Requirement </th>
        <th>Download Demo Signed Copy</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $rowss->company_name;?></td>
        <td><?php echo $rowss->type_of_organization;?></td>
        <td><?php echo $rowss->postal_address;?></td>
        <td><?php echo $rowss->area_name;?></td>
        <td><?php echo $rowss->state_name;?></td>
        <td><?php echo $rowss->contact_person;?></td>
        <td><?php echo date('d/m/Y',strtotime($rowss->demo_date));?></td>
        <td></td>
        <td><a href="<?php echo sfdocument;?>demofiles/demoreport/<?php echo $rowss->demofile;?>" download="">Click to Download</a></td>
      </tr>
     
    
    </tbody>
  </table> 
<h4 class="text-center">PRODUCT INFORMATION <a href="<?php echo page_url;?>Leads/demofeedbackphotosview/<?php echo $this->uri->segment(3);?>"><span class="btn btn-xs btn-primary">Click Here to Preview Pre and Post Images</span></a></h4>

  <table class="table table-bordered manglesh">
    <thead>
      <tr>
        <th>SR NO</th>
        <th>PRODUCT</th>
        <th>DEMO AREA </th>
        <th>PLACE </th>
        <th>PRODUCT DILUTION </th>
        <th>REMARK</th>
        <th>DOWNLOAD PHOTOS </th>
        <th>DOWNLOAD VIDEOS</th>
      </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;
            $q = $this->db->select('a.id, a.product_id, a.demo_area, a.place, a.product_dilution, a.remarks, b.instruments_name')->from('lead_demo_feedback a')->join('presto_instruments b','a.product_id=b.id')->where('a.lead_id',$this->uri->segment(3))->get();
            if($q->num_rows()>0){
                foreach($q->result() as $row){
        ?>
      <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $row->instruments_name;?></td>
        <td><?php echo $row->demo_area;?></td>
        <td><?php echo $row->place;?></td>
        <td><?php echo $row->product_dilution;?></td>
        <td><?php echo $row->remarks;?></td>
        <td>
            <?php 
        $a =1;
            $q3 = $this->db->select('a.picture,a.post_picture')->from('demo_feedback_photos a')->where('a.demo_id',$row->id)->where('a.product_id',$row->product_id)->get();
            if($q3->num_rows()>0){?>
                <table>
                        <thead>
                            <th>#</th>
                            <th>Pre-Photo</th>
                            <th>Post-Photo</th>
                        </thead>
                        <tbody>

                <?php

                 foreach($q3->result() as $row1){?>

                    
                            <tr>
                                <td><?php echo $a;?></td>
                                <td><a href="<?php echo sfdocument;?>demofiles/<?php echo $row1->picture;?>" download>Click to Download</a></td>
                                <td><a href="<?php echo sfdocument;?>demofiles/<?php echo $row1->post_picture;?>" download>Click to Download</a></td>
                            </tr>
                   <?php  $a++;}?>
                        </tbody>
                    </table>

             <?php 
            }
    ?></td>
        <td> <?php 
        $a =1;
            $q = $this->db->select('video')->from('demo_feedback_videos')->where('demo_id',$row->id)->where('product_id',$row->product_id)->get();
            if($q->num_rows()>0){?>
                <table>
                        <thead>
                            <th>#</th>
                            <th>Videos</th>
                        </thead>
                        <tbody>

                <?php foreach($q->result() as $row1){?>

                    
                            <tr>
                                <td><?php echo $a;?></td>
                                <td><a href="<?php echo sfdocument;?>demofiles/<?php echo $row1->video;?>" download>Click to Download</a></td>
                            </tr>
                   <?php  $a++;}?>
                        </tbody>
                    </table>

             <?php 
            }
    ?></td>
      </tr>
     <?php $i++;
 }
}?>
    
    </tbody>
  </table> 

  <h4 class="text-center">DYNACHEM PERSON ATTENDED MEETING</h4>

  <table class="table table-bordered manglesh">
    <thead>
      <tr>
        <th>SR NO</th>
        <th>PERSON NAME</th>
        <th>DESIGNATION</th>
        
      </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;
            $q = $this->db->select('person_name, contact_no, designation')->from('person_attendent_demo')->where('lead_id',$this->uri->segment(3))->get();
            if($q->num_rows()>0){
                foreach($q->result() as $row){
        ?>
      <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $row->person_name;?></td>
        <td><?php echo $row->designation;?></td>
        
      </tr>
     <?php $i++;
 }
}?>
    
    </tbody>
  </table> 

   <h4 class="text-center"><?php echo $rowss->company_name;?> PERSON ATTENDED MEETING</h4>

  <table class="table table-bordered manglesh">
    <thead>
      <tr>
        <th>SR NO</th>
        <th>PERSON NAME</th>
        <th>DESIGNATION</th>
        <th>CONTACT NO</th>
        
      </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;
            $q = $this->db->select('person_name, contact_no, designation')->from('customer_person_attendent_demo')->where('lead_id',$this->uri->segment(3))->get();
            if($q->num_rows()>0){
                foreach($q->result() as $row){
        ?>
      <tr>
        <td><?php echo $i;?></td>
        <td><?php echo $row->person_name;?></td>
        <td><?php echo $row->designation;?></td>
        <td><?php echo $row->contact_no;?></td>
       
        
      </tr>
     <?php $i++;
 }
}?>
    
    </tbody>
  </table> 

                            </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

                </div>
                <!-- end row -->
 

                <!-- Footer -->
<?php $this->load->view('common/footer');?>
                <!-- End Footer -->

            </div> <!-- end container -->
        </div>
        <!-- end wrapper -->


         <!-- jQuery  -->
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
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

      

        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

 

		<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
    </body>
</html>
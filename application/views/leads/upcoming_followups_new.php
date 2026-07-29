<?php 

$CI =& get_instance();

$CI->load->model('Salescrm_model');

$getTeamMembers = $CI->Salescrm_model->getTeamMembers();



if($this->uri->segment(3) != '') {

    $member_id = $this->uri->segment(3);

    $startdate=$this->uri->segment(4);

    $enddate=$this->uri->segment(5);

} else {

    $member_id = '';

    $startdate='';

    $enddate='';

}



?>



<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?></title>



        <!-- Table Responsive css -->

		<script src="<?php echo assets_url;?>js/angular.min.js"></script>

		 <!-- DataTables -->

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



        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->

<?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>

<style>

            table.pretty thead th {

                text-align: center;

                background:<?php echo $LOGO->colorcode;?>;

                color:#fff;

				font-size:12px;

            }

			table.pretty td {

                text-align: center;

                font-size:12px;

            }



            .btns {

                margin-top: 20px;

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

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box col-md-1">

                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success btns" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>

                        </div>

                        <?php if($_SESSION['logged_in']['role']==32)

                        {?>

                        <div class="col-md-4"></div>

                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label for="field-1" class="control-label">Start Date</label>

                                <span style="color:red;">*</span>

                                <span id="error_create_date" style="color:red;"></span>                          

                                <input class="form-control" type="text" name="start" id="start" autocomplete="off" value="<?php echo $startdate;?>">

                            </div>

                        </div>



                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label for="field-1" class="control-label">End Date</label>

                                <span style="color:red;">*</span>

                                <span id="error_create_date" style="color:red;"></span>                          

                                <input class="form-control" type="text" name="end" id="end" autocomplete="off" value="<?php echo $enddate;?>">

                            </div>

                        </div>



                        <div class="col-md-2"> 

                            <div class="form-group">

                                <label for="field-1" class="control-label">User</label>                          

                                    <select class="form-control" id="team_members">

                                        <option value="0" <?php if ($member_id == 0) {echo 'selected';}?>>ALL</option>

                                        <?php if(!empty($getTeamMembers)) {

                                            foreach ($getTeamMembers as $row) {?>

                                              <option value="<?php echo $row->user_id;?>" <?php if ($member_id== $row->user_id) {echo 'selected';}?>><?php echo $row->first_name." ".$row->last_name;?></option> 

                                         <?php }

                                         } ?>

                                    </select>

                                    

                            </div>

                        </div>

                        <div class="col-md-1">

                            <div class="form-group">

                                <button class="btn btn-success text-center btns" onclick="getMemberList()" type="subm">Submit</button>

                            </div>

                        </div>

                            

                        

                    <?php } ?>

                    </div>

                </div>



                <div class="row">

                    <div class="page-title-box col-md-12">

                        <h4 class="page-title text-center">&nbsp Upcoming Followup List</h4>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->

<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>



                 <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">

                            <table id="example" class="table table-striped table-bordered pretty">

                                <thead>

                                <tr>

                                     <th>Sr No.</th>

                                   

                                     <th>Query No/Date</th>

                                      <th>Customer Type</th>

                                       <th>Lead Source</th>

                                     <th>Company</th>

                                     <th>Customer Name</th>

                                    

                                  

                                     <th>Alternate Contact</th>

                                     

                                     <th>Client Remarks</th>

                                     <th>Lead Manager</th>

                                     <th>Upcoming date</th>
                                     <th>Progress Updated On</th>

                                     <th>Update Progress</th>

                                    

                                    

                                </tr>

                                </thead>

                                

                            </table>

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



        <!-- Datatable init js -->

        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>



        <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>



       



        <script>

$( document ).ready(function() {

var member_id = '<?php echo $member_id;?>';

var startdate = '<?php echo $startdate;?>';

var enddate = '<?php echo $enddate;?>';

$('#example').dataTable({

"bProcessing": true,

"pagination":true,

"pagination":true,

"pageLength": 100,

"stateSave": true,

"sAjaxSource": "<?php echo page_url;?>Leads/upcomeingfollow_list/<?php echo $member_id?>/<?php echo $startdate;?>/<?php echo $enddate;?>",



"aoColumns": [

				

                { mData: 'sr_no' } ,

               

                { mData: 'unique' },

                { mData: 'customer_type' },

                { mData: 'leadsource' },

                { mData: 'company' },

                { mData: 'customer_name' },                

                { mData: 'alternatedetail' },

                { mData: 'lastremarks' },

                { mData: 'leadmanager'},

                { mData: 'followupschedule'},
                { mData: 'lastupdatedon'},

                 { mData: 'update'} 

				

				

		]

});  



        $(document).ready(function() {

             $('#start').datepicker({

                autoclose: true,

                todayHighlight: true,

                format: 'dd-mm-yyyy'

             });

             $('#end').datepicker({

                autoclose: true,

                todayHighlight: true,

                format: 'dd-mm-yyyy'

             });

        }); 



});



</script>

		

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

<script language="javascript" type="text/javascript">   

jQuery.noConflict();

$(document).ready(function() {

$("#save").click(function() {

var business_loc = $("#business_loc").val();

if(business_loc=='')

{

	$("#error_business_loc").html('Required!');

}

var department_name = $("#department_name").val();

if(department_name=='')

{

	

	$("#error_department_name").html('Required!');

}



var status = $("#status").val();

if(status=='')

{

	

	$("#error_status").html('Required!');

}





if(business_loc=='' || department_name==''|| status=='' )

{

	

	return false;

}



});

});

</script>



<script type="text/javascript">

    function getMemberList() {

        var team_members=$("#team_members").val();

        var startdate=$("#start").val();

        var enddate=$("#end").val();

        location.href = '<?php echo page_url;?>Leads/newleads/'+team_members+'/'+startdate+'/'+enddate;

            

    }

</script>

    </body>

</html>
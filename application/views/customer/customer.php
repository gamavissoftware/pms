 <?php 
 $CI =& get_instance();
 $CI->load->model('Salescrm_model');
 //$getCreditPeriod = $CI->Salescrm_model->getCreditPeriod();
 $max_customer_code=$CI->Salescrm_model->getHighestCustomerCode();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?> Customers List</title>

        <!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url; ?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
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

    <?PHP 

$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

foreach($q->result() as $LOGO);

?>
        <style>

              .loading
        {
        position: absolute;
        left:0px;
        top: -1px;
        }



        table.manglesh thead th {

                background: <?php echo $LOGO->colorcode;?>;

                color:#fff;

                font-weight:bold;

                text-align:center;

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
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
					
                               
                               <div class="col-md-6 pull-right">

                           
                        </div>
                            </div>
                           
                            <h4 class="page-title">Customer List</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
<span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

                 <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="example" class="table table-striped table-bordered manglesh">
                                <thead>
                                <tr>
                                    <th>SR NO</th>
                                   <th>Customer Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                    <th>Country</th>
                                     <th>Status</th>
                                    <th>Action</th>   
                                    <th>Email Delivery</th>                                
                                </tr>
                                </thead>
								<tbody></tbody>
                                
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
        <script>
        $( document ).ready(function() {
        $('#example').dataTable({
        "bProcessing": true,
        "pagination":true,
        dom: 'lBfrtip',
        buttons: [
        'excel'
        ],
        pageLength:50,
        "sAjaxSource": "<?php echo page_url;?>Customer/customer_list/<?php echo $this->uri->segment(3);?>",
        "aoColumns": [
        { mData: 'sr_no' } ,
        { mData: 'company_name' },
        { mData: 'contactperson' },
        { mData: 'mobile' },
        { mData: 'email' },
        { mData: 'country' },
        { mData: 'status' } ,
        { mData: 'edit' },
        { mData: 'emaildelivery' }


        ]
        });   
        });

        </script>
        <script>
        $(".allow_decimal").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));

        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
        evt.preventDefault();
        }
        });

        function getcity()
        {
        var stateid=$('#state').val();
        if (stateid !== '') {
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Master/Company_location/getcityname",
        data: "stateid=" + stateid,
        success: function(data) {
        //alert(data);
        $("#cityname").html(data);
        }
        });
        }

        }

        function checkIfDuplicateNoExists() {
        var company=$('#company').val();
        var company_name=$('#company option:selected').text();
        var mobile=$('#mobile').val();

        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Customer/checkIfDuplicateNoExists",
        data: { mobile: mobile,
        company: company
        },
        success: function(data) {
        if(data == 1) {
        alert('Duplicate Contact No. of '+company_name+' company found.');
        $("#mobile").val('');
        }
        }
        });
        }

        function validate_gst() {
        var gstinVal = $('#gst').val();
        var reggst = /^([0-9]){2}([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}([0-9]){1}([a-zA-Z]){1}([a-zA-Z0-9]){1}?$/;
        if(!reggst.test(gstinVal) && gstinVal!=''){
        alert('GST Identification Number is not valid. It should be in this "11AAAAA1111Z1A1" format');
        $('#gst').val('');
        }
        }
        </script>		
		
            <script language="javascript" type="text/javascript">   

            $(document).ready(function() {
            $("#save").click(function() {
            var vendor_name = $("#vendor_name").val();
            if(vendor_name=='')
            {
            $("#error_vendor_name").html('Required!');
            }
            var item_name = $("#item_name").val();
            if(item_name=='')
            {

            $("#error_item_name").html('Required!');
            }

            var status = $("#status").val();
            if(status=='')
            {

            $("#error_status").html('Required!');
            }


            if(vendor_name=='' || item_name==''|| status=='' )
            {

            return false;
            }

            });
            });

            function validate_pincode() {
            var pincode = $('#pincode').val();

            var zipRegex = /^\d{6}$/;

            if (!zipRegex.test(pincode))
            {
            alert('Invalid Pincode');
            $('#pincode').val('');
            }
            }

            function tds_applicable()
            {
            $(".tds_data").css('display','none');
            $("#tds_per").attr('required',false);

            var tds=$("#tds_appl").val();

            if(tds!='')
            {
            if(tds==1)
            {
                $(".tds_data").css('display','');
                $("#tds_per").attr('required',true);
            }else if(tds==0)
            {
                 $(".tds_data").css('display','none');
                $("#tds_per").attr('required',false);
            }
            }         

            }

            </script>

        <script type="text/javascript">
        function assign_to(id, tableid, user_id) {

        var employee_id = $("#employee_name" + tableid).val();
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Customer/customer_assignment",
        data: "customer_id=" + id + "&employee_id=" + employee_id + "&assigned_by=" + user_id,
        success: function(data) {

        $("#successmessage"+id).html(data);
        }
        });

        }

        function assign_to_trail(id, tableid, user_id){
        var employee_id = $("#employeename" + tableid).val();
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Customer/customer_assignment_for_trail",
        data: "customer_id=" + id + "&employee_id=" + employee_id + "&assigned_by=" + user_id,
        success: function(data) {

        $("#datasuccess"+id).html(data);
        }
        });
        }

        function add_balance(id){

        $("#response"+id).text('');
        var op_money = $("#op_money" + id).val();
        $.ajax({
        type: "post",
        url: "<?php echo page_url; ?>Customer/add_opening_balance",
        data: "id=" + id + "&op_money=" + op_money,
        success: function(data) {

        $("#response"+id).text('Updated');


        }
        });


        }


        function check_gstnew(gst)
        {
        var a=0;
        var statecode = gst.substring(0, 2);
        var pancarno = gst.substring(2, 12);
        var entityNumber = gst.substring(12, 13);
        var defaultZvalue =gst.substring(13, 14);
        var checksumdigit = gst.substring(14, 15);
        if (gst.length != 15) {
        alert('GST Number is invalid');

        var a=1;
        }
        if (pancarno.length != 10) {
        alert('GST number is invalid ');

        var a=1;

        }
        if (defaultZvalue !== 'Z') {
        alert('GST Number is invalid Z not in Entered Gst Number');

        var a=1;
        }

        if ($.isNumeric(statecode)) {

        } else {
        alert('Please Enter Valid State Code');

        var a=1;
        }

        // if ($.isNumeric(checksumdigit)) {

        // } else {
        //     alert('GST number is invalid last character must be digit');

        //     var a=1;

        // }



        if(a==0)
        {
        check_gst(gst);
        }else
        {
        $("#company_gstn").val('');
        }

        }
        function check_gst(gst){

        $("#loading").css('display','none');
        if(gst!='')
        {

        $.ajax({
        type:"post",
        url:"<?php echo page_url;?>Open_leads/getGSTData",
        data:{gst:gst},
        beforeSend: function() {
        $("#loading").css('display','');

        },
        success:function(data) {

        if(data!='NA')
        {
        $("#companyname").val(data);
        }else
        {
        alert('Invalid GST No. Provided');
        $("#companyname").val('');
        }
        $("#loading").css('display','none');

        }
        });
        }

        }
        </script>
    </body>
</html>
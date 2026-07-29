<?php
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$id=$this->uri->segment(3);

$row=$this->db->select('a.id,b.company_name,b.contact_no,b.email')
              ->from('customer_quotation a')
              ->join('customer_detail b', 'b.id=a.customer_id', 'left')
              ->where('a.id',$id)
              ->get();
if($row->num_rows()>0)
{
    foreach($row->result() as $rowss);

    $uniqueid='QUOTE'.$rowss->id;
    $companyname=$rowss->company_name;
    $contact_no=$rowss->contact_no;
    $email=$rowss->email;

}else
{
    echo "Invalid Request"; exit;
}

$checkIfProductIsApproved = $CI->master->checkIfProductIsApproved($id);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="NJ Media">
		<link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
		<title>PREVIEW PDF & SEND MAIL</title>
		<!-- Table Responsive css -->
		<script src="<?php echo assets_url;?>js/angular.min.js"></script>
		 <!-- DataTables -->
        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css">
        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
		<link href="<?php echo assets_url;?>plugins/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script>
		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
		<style>
table.manglesh thead th {
				background: #003366;
				color:#fff;
				font-size:11px;
				font-weight:bold;
			}
table tbody tr td {
  font-size: 11px;
  color:#000;
}
#pageloader
{
  background: rgba( 255, 255, 255, 0.8 );
  display: none;
  height: 100%;
  position: fixed;
  width: 100%;
  z-index: 9999;
}
#pageloader img
{
  left: 50%;
  margin-left: -32px;
  margin-top: -32px;
  position: absolute;
  top: 50%;
}
</style>
    </head>
    </head>


    <body>


        <!-- Navigation Bar-->
                <header id="topnav">
          <?php $this->load->view('common/nav-menu');?>
        </header>
        <!-- End Navigation Bar-->


        <div class="wrapper">
            <div class="container-fluid">

            
		<div class="row">
           
                    <div class="col-sm-12">
                        
                    
                        <div>
 <div class="col-md-12" style="color:red;"><h5 style="color:red;text-align:center;font-weight:bold;"><?php echo $this->session->flashdata('message');?></h5></div><br/>
                        <div class="col-md-8">
                        <?php
                        $aa=site_http_root.'quotation_pdf/'.$uniqueid.'_'.str_replace('/','',str_replace(' ','_',$companyname)).'.pdf';
                        // echo $aa;exit;
                        ?>

                        <iframe src="<?php echo $aa;?>" width="800" height="800"></iframe>
                        </div>


                        <div class="col-md-4">
                       <!--  <div class="col-md-12">
                        <a href="javascript:;" data-toggle="modal" data-target="#myModal" class="pull-right"><button class="btn btn-info btn-xs">View Mail History</button></a> -->
                        
                        <div class="col-md-12">
                            <?php if ($checkIfProductIsApproved == 0) { ?>
                            <form action="<?php echo page_url;?>Customer/sendclientintimation/<?php echo $id;?>" method="post">
                                <div class="col-md-12" style="border:1px solid #000;">

                                <div class="col-md-3"></div> 

                                <div class="col-md-3">              
                                <input type="checkbox" name="whatsapp" value="1" checked>&nbsp;Whatapp
                                </div>

                                <div class="col-md-3 pull-left"> 
                                <input type="checkbox" name="email" value="1" checked>&nbsp;Email
                                </div>
                                <div class="col-md-3"></div> 

                                <div class="col-md-12">
                                <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="aemail" id="aemail" class="form-control" value="<?php echo $email;?>">
                                </div>
                                </div>

                                <div class="col-md-12">
                                <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" name="amobile" id="amobile" value="<?php echo $contact_no;?>" class="form-control">
                                </div>
                                </div>
                                   <div class="col-md-12">
                                <input type="checkbox" value="1" name="profile"> Include Company Profile
                            </div>


                                <div class="col-md-12" style="margin-top:10px; text-align: center;">
                                <input type="submit" name="sub" id="sub" value="Send Quote" class="btn btn-success">
                                </div>

                                </div>
                          
                            <?php } else { ?>
                                <div class="col-md-4"></div>
                                <div class="col-md-6 text-center">  
                                    <span style="color: red; font-weight: bold;font-size: 16px; text-align:center;">One or more products are not Approved/Rejected. Hence, Quotation cannot be sent</span>
                                </div>
                            <?php } ?>
                        </div>
                     </form>

                     <div class="col-md-12" style="margin-top:40px;">
                            <table style="width: 100%; font-size:14px;" border="1">
                                 <tr>
                                    <th style="padding: 5px; background-color: #f5f5f5;text-align: center;" colspan="3">Quotation Send History</th>
                                 
                                </tr>

                                <tr>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent On</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Notification Type</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Send On</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent By</th>
                                </tr>
                                <?php
                                $sql1 = $this->db->select('a.type,a.mail_sent_on, b.first_name, b.last_name,a.send_to')
                                                 ->from('customer_quotation_mail_history a')
                                                 ->join('system_users b', 'b.user_id=a.mail_sent_by','left')
                                                 ->where('a.quotation_id', $id)
                                                 ->get();

                                if ($sql1->num_rows() > 0) {
                                    foreach ($sql1->result() as $row1) { ?>
                                        <tr>
                                            <td style="padding: 5px;"><?php echo date('d-m-Y H:i:s', strtotime($row1->mail_sent_on)); ?></td>
                                            <td style="padding: 5px;"><?php if($row1->type==1){?> Mail <?php }else{?> Whatsapp <?php } ?></td>
                                            <td style="padding: 5px;"><?php echo $row1->send_to; ?></td>
                                            <td style="padding: 5px;"><?php echo $row1->first_name . " " . $row1->last_name; ?></td>
                                        </tr>
                                <?php }
                                }else{ ?>

                                      <tr>
                                            <td style="padding: 5px;text-align: center;" colspan="4">No Intimation Sent</td>
                                          
                                        </tr>
                                <?php } ?>
                            </table>
                        </div>

                        </div>
                    </div>
                </div>


                <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Mail History of <?php echo $companyname; ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width: 100%; font-size:14px;" border="1">
                                <tr>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent On</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Notification Type</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent By</th>
                                </tr>
                                <?php
                                $sql1 = $this->db->select('a.type,a.mail_sent_on, b.first_name, b.last_name')
                                                 ->from('lead_quotation_mail_history a')
                                                 ->join('system_users b', 'b.user_id=a.mail_sent_by')
                                                 ->where('a.lead_id', $id)
                                                 ->get();

                                if ($sql1->num_rows() > 0) {
                                    foreach ($sql1->result() as $row1) { ?>
                                        <tr>
                                            <td style="padding: 5px;"><?php echo date('d-m-Y H:i:s', strtotime($row1->mail_sent_on)); ?></td>
                                            <td style="padding: 5px;"><?php if($row1->type==1){?> Mail <?php }else{?> Whatsapp <?php } ?></td>
                                            <td style="padding: 5px;"><?php echo $row1->first_name . " " . $row1->last_name; ?></td>
                                        </tr>
                                <?php }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        </form>
    </div><!-- /.modal -->


                 <!-- <a href="<?php //echo page_url;?>Store/sendmailtosupplierwithpdf/<?php //echo $id;?>"><span class="btn btn-warning">Send to Client</span></a> -->

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
        <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
<script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>
        <!-- Datatable init js -->
        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>

        <!-- App js -->
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
		

	 
 <script>
$( document ).ready(function() {
 
$('#example').dataTable({
 "bProcessing": false,
 "pagination":true,
fixedHeader: true,
   scrollCollapse: true,
   fixedColumns:   {
            leftColumns: 3
        },
          pageLength:50,
 "sAjaxSource": "<?php echo page_url;?>Reporting/yourcreated_indent/",
 "aoColumns": [
					{ mData: 'sr_no' },
					{ mData: 'indent_type'},
					{ mData: 'prno'},
					{ mData: 'itemdetail'},
					{ mData: 'createdby'},
					{ mData: 'createdon'},
					{ mData: 'markrecvd'}
						
						
                ]
        });   
});


</script>



<script src="<?php echo assets_url;?>plugins/select2/dist/js/select2.min.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
  $("#loginForm").on("submit", function(){
    $("#pageloader").fadeIn();
  });//submit
});//document ready
</script>
</body>
</html>
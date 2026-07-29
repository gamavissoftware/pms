<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">

        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

        <title><?php echo sitetitle; ?> Ticket History</title>
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
            <div class="container">

                <!-- Page-Title -->
                <div class="row" style="margin-top:20px;">
                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                        <div class="page-title-box">
						 <div class="btn-group pull-right">
						 
                           </div>
                           
                            <h4 class="page-title">Ticket ID #                       
                             <?php $query = $this->db->select(array('id','ticket_id','ticket','added_on'))->from('service_support')->where('id',$this->uri->segment(3))->get();
                            $res = $query->row_array();
                            echo $res['ticket_id'];
                            ?>
                            </h4>
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
                                    <th>Sr No.</th>
									<th>Business Location</th>
									<th>Department</th>
									<th>Remark</th>
                                    <th>Status</th>
                                    <th>Ticket Raise Timing</th>
                                    <th>Raised By</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
					
					<div class="col-sm-12">
                          <span id="levelsuccess"></span>
                           <?php $query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('service_support_ticket_progress a')->join('system_users b','a.added_by=b.user_id','left')->where('a.ticket_id',$this->uri->segment(3))->get();
                            $result = $query->result();
                            if(!empty($result)){
                            foreach($result as $element){
                            $supportflag[] = $element->level_flag;

                            }
                            }else{
                            $supportflag = array(0);    
                            }
                           // $implodesupportflag = implode(',',$supportflag);
                            if (in_array(1, $supportflag)){
                            if (in_array(2, $supportflag)){
                            $flag = 2;    
                            }else{    
                            $flag = 1; 
                            }   
                            }else{
                            $flag = 0;    
                            }


                            ?>
                            <?php 
                            $addedtime = strtotime($res['added_on']);
                            $levelonetime =  ($addedtime + 86400);
                            $leveltwotime =  ($addedtime + 86400 + 86400);
                            $currenttime = time();
                            ?>
                          <?php if(empty($result) && ($currenttime > $levelonetime)){?>
                          <div class="p-t-10 pull-right" style="padding-bottom: 10px;margin-right: 21px;">
                          <button type="button" class="sendlevelone btn btn-sm btn-warning" data-ticket_id="<?php echo $res['id'];?>" id="levelone">Level 1</button>
                            </div>
                          <?php }else if(!empty($result) && ($flag == 1) && ($currenttime > $leveltwotime)){?>
                          <div class="p-t-10 pull-right" style="padding-bottom: 10px;margin-right: 21px;">
                          <button type="submit" class="sendleveltwo btn btn-sm btn-info" data-ticket_id="<?php echo $res['id'];?>" id="leveltwo">Level 2</button>
                            </div>
                          <?php }?>

                        <form method="post" class="card-box" action="<?php echo page_url;?>Service_support/update_progress/<?php echo $this->uri->segment(3);?>">
                            <span class="input-icon icon-right">
							<span id="error_remarks" style="color:red;"></span>
                                <textarea rows="2" class="form-control" placeholder="Update Progress/Remarks" name="remarks" id="remarks"></textarea>
                            </span>
                            <div class="p-t-10 pull-right">
                                <input type="submit" class="btn btn-sm btn-primary" id="save" value="Send">
                            </div>
                           <div style="height:30px"></div>
                        </form>
                        <div class="card-box">
                            <?php $query = $this->db->select('a.*,b.user_id, b.first_name, b.last_name')->from('service_support_ticket_progress a')->join('system_users b','a.added_by=b.user_id','left')->where('a.ticket_id',$this->uri->segment(3))->get();
							foreach($query->result() as $history){?>
							
							<div class="comment">
                                <img src="<?php echo assets_url;?>images/techsupport.jpg" alt="" class="comment-avatar">
                                <div class="comment-body">
                                    <div class="comment-text">
                                        <div class="comment-header">
                                            <a href="#" title=""><?php echo $history->first_name." ".$history->last_name;?></a><span>about <?php echo $updateddate = date('Y-m-d', strtotime($history->added_on));
				$time = date('H:i:s', strtotime($history->added_on));
				echo $updatedtime = " ". date('g:i A', strtotime($time)); ?></span>
                                        </div>
                                       <?php echo $history->remarks;?>

                                    </div>

                                </div>

                           </div>
							<?php }?>
						   
						  
                          
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#save").click(function() {
	var remarks= $("#remarks").val();
if(remarks=='')
{
	$("#error_remarks").html('Required!');
}
if(remarks=='')
{
	
	return false;
}
});
});
</script>
<script>
$(".sendlevelone").click(function() {
     var ticket_id = jQuery(this).data('ticket_id'); 
    jQuery.ajax({
        url: '<?php echo page_url;?>/Service_support/sendlevelone/',
        type: 'post',
        data: {ticket_id: ticket_id},
        dataType: "json",
        success: function (data) {
        if(data!=''){
        jQuery('span#levelsuccess').html(data.success);
        setTimeout(function () {
                window.location.reload();
            }, 5000);    
        }             

        },  
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

});    

</script>

<script>
$(".sendleveltwo").click(function() {
     var ticket_id = jQuery(this).data('ticket_id'); 
    jQuery.ajax({
        url: '<?php echo page_url;?>/Service_support/sendleveltwo/',
        type: 'post',
        data: {ticket_id: ticket_id},
        dataType: "json",
        success: function (data) {      
        if(data!=''){
        jQuery('span#levelsuccess').html(data.success);
        setTimeout(function () {
                window.location.reload();
            }, 5000);    
        }   
        },  
        error: function (xhr, ajaxOptions, thrownError) {
            console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });

});    

</script>
 <script>
$( document ).ready(function() {
$('#example').dataTable({
"bProcessing": true,
"pagination":true,
"sAjaxSource": "<?php echo page_url;?>Service_support/raised_ticket_detail/<?php echo $this->uri->segment(3);?>",
"aoColumns": [
				{ mData: 'sr_no' } ,
				{ mData: 'business_loc' },
				{ mData: 'department' },
				{ mData: 'ticket' },
				{ mData: 'status' },
				{ mData: 'added_on' },
				{ mData: 'added_by' }
				
				
		]
});   
});

</script>
    </body>
</html>
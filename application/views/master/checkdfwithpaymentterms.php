<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');
?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright; ?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle; ?>Payment Terms</title>
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
                table.manglesh tbody td {
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
                         <div class="btn-group pull-right"></div>
                           <h4 class="text-center" style="padding:10px; 10px; 10px; 10px;">All Running DF</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">

                    <div class="col-sm-12">

                        <div class="card-box table-responsive">
                        <table id="example5" class="table manglesh table-striped table-bordered">
                        <thead>
                        <tr>
                        <th>S. NO.</th>
                        <th>DF NO.</th>
                        <th>DOWNLOAD</th>
                        <th>PO DATE</th>
                        <th>COMPANY NAME</th>
                        <th>MARKETING PERSON</th>
                        <th>DF RELEASE DATE</th>
                        <th>Payment Terms</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $m=1;
                        $reportid = $this->uri->segment(3);
                        $q = $this->db->select('id, df_no, added_on, df_upload, added_on')->from('df_release')->where('df_status',0)->get();
                        if($q->num_rows()>0){
                        foreach($q->result() as $rows){

                        $q1 = $this->db->select('MAX(end_date) as enddate')->from('task_department_wise_scheduling')->where('df_id',$rows->id)->get();
                        if($q1->num_rows()>0){
                        foreach($q1->result() as $r);
                        $planneddate = date('d-m-Y',strtotime($r->enddate));
                        }else{
                        $planneddate = '';
                        }

                        $q5 = $this->db->select('a.id, a.payment_term, a.company_name, a.podate, b.title, b.first_name, b.last_name')->from('poreceived a')->join('system_users b','a.added_by=b.user_id','left')->where('id',$rows->id)->get();

                        if($q5->num_rows()>0){
                        foreach($q5->result() as $row5);
                        $companyname = $row5->company_name;
                        $podate = date('d-m-Y',strtotime($row5->podate));
                        $dfowner = $row5->title." ".$row5->first_name." ".$row5->last_name;
                        $message = '';
                        $qq = $this->db->select('a.payment_percentage, b.task_name, b.task_id')->from('payment_terms_milestone a')->join('task_management b','a.milestone=b.task_id','left')->where('a.payment_term_id',$row5->payment_term)->get();
                        if($qq->num_rows()>0){
                            $message.= "<center><table border='1' style='width:600px !important; text-align:center'><tr style='background-color:#223010; text-align:center;'>
                            <th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold; color:#fff;'>DF ID</th>
                            <th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold; color:#fff;'>Task ID</th>
                            <th style='padding:2px 2px 2px 2px; text-align:center; font-weight:bold; color:#fff;'><b>(%)</b></th>
                            <th style='padding:2px 2px 2px 2px;  text-align:center; font-weight:bold; color:#fff;'><b>MILESTONE</b></th></tr>";
                            foreach($qq->result() as $milestonedata){
                               
                                $message.='<tr>
                                <td>'.$rows->id.'</td>
                                <td>'.$milestonedata->task_id.'</td>
                                <td>'.strtoupper($milestonedata->payment_percentage).'</td>
                                <td>'.strtoupper($milestonedata->task_name).'</td>
                               
                                </tr>';
                            }
                            $message.='</table></center>';
                        }



                        }else{
                        $companyname = "";
                        $podate = "";
                        $dfowner = "";
                        }

                        

                        
                       
                        $show=1;
                        
                        if($show==1){
                        ?>
                        <tr>
                        <td><?php echo $m;?></td>
                        <td><?php echo strtoupper($rows->df_no);?></td>
                        <td><a href="<?php echo sfdocument;?>Taskdocument/dfattachment/<?php echo $rows->df_upload;?>" download><span class="btn btn-primary btn-xs">Download DF</span></a></td>
                        <td><?php echo $podate;?></td>
                        <td><?php echo $companyname;?></td>
                        <td><?php echo $dfowner;?></td>
                        <td><?php echo date('d-m-Y',strtotime($rows->added_on));?></td>
                        <td><?php echo $message;?></td>
                        
                        </tr>


                        <?php $m++;} } }?>


                        </tbody>
                        </table>

                        </div>

                    </div>

                </div>
 

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
<script>
$(document).ready(function(){
       $("#teamupdate").attr('disabled',false);
       $("#teamupdate").val('Update');
  $("#loginForm").on("submit", function(){
   // $("#pageloader").fadeIn();
   $("#teamupdate").attr('disabled',true);
     $("#teamupdate").val('Please Wait...');
  });//submit
});//document ready
</script>

<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
$("#savedata").click(function() {
var uploaddf = $("#uploaddf").val();
if(uploaddf=='')
{
    $("#error_uploaddf").html('Required!');
    $("#uploaddf").css("border", "1px solid red");
}

var dfno = $("#dfno").val();
if(dfno=='')
{
    $("#error_dfno").html('Required!');
    $("#dfno").css("border", "1px solid red");
}

if(uploaddf=='' || dfno=='')
{
    
    return false;
}

});
});
</script>    
        <script>

$( document ).ready(function() {

$('#example5').dataTable({

"bProcessing": true,

    fixedHeader: true,

"pagination":true

});   

});



</script>

</body>
</html>
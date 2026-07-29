<?php 
$CIA =& get_instance();
$CIA->load->model('Task_model');
$runningdfno = $CIA->Task_model->getallrunningdf();
 $user_id =$this->session->userdata['logged_in']['user_id'];

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
         <title> Notification Panel</title>
        <!-- Table Responsive css -->
        <script src="<?php echo assets_url;?>js/angular.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
        <link href="<?php echo assets_url; ?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
        <style type="text/css">
            .notificationlist {
  background-color: white; /* Default background color */
  transition: background-color 0.5s; /* Optional: adds a smooth transition effect */
}

.notificationlist:hover {
  background-color: #f5f5f5; /* Background color on hover */
}

hr {
    margin-top: 6px;
    margin-bottom: 6px;
    border: 0;
    border-top: 1px solid #a08f8f;
}
p{
    color:#000 !important;
}
        </style>
    </head>
    <body>

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->



        <?php $this->load->view('common/info-section.php');?>

        <div class="wrapper" style="background-color:#ededed ;">

            <div class="container">



                <!-- Page-Title -->

                <div class="row">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">
                        <h4 class="page-title text-center">NOTIFICATIONS</h4>

                        </div>

                    </div>

                </div>

                 <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">

                        <div class="row card-box">

                            <div class="col-md-12">
                                <form>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Filter by DF No</label>
                                                <select class="form-control" name="filterbydfid" id="filterbydfid" onchange="getdynamicdata();">
                                                    <option value="ALL">ALL</option>
                                                        <?php if(count($runningdfno)>0){
                                                        foreach($runningdfno as $row){?>
                                                        <option value="<?php echo $row->id;?>"><?php echo $row->df_no;?></option>
                                                        <?php  }
                                                        }?>
                                                </select>
                                            </div>
                                        </div>

                                          <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-center">Filter Between Date</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="date" class="form-control" name="" value="<?php echo date('Y-01-01');?>" id="startdate" onchange="getdynamicdata();">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="date" class="form-control" name="" id="enddate" value="<?php echo date('Y-m-d');?>" onchange="getdynamicdata();">
                                                    </div>
                                            </div>
                                            </div>
                                            
                                          
                                        </div>
                                    <?php if($_SESSION['logged_in']['adminuser']==1){?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Filter by User</label>
                                                <select class="form-control" name="filterbyuser" id="filterbyuser" onchange="getdynamicdata();">
                                                    <option value="ALL">ALL</option>
                                                    <?php 
                                                            $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_status',1)->where('business_location',2)->get();
                                                            foreach($q->result() as $row){
                                                    ?>
                                                    <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } else if($_SESSION['logged_in']['adminuser']==2){
                                            $q = $this->db->select('team_id')->from('prestogroup_teams')->where('team_leader', $user_id)->get();
                                            if($q->num_rows()>0){

                                             ?>
                                             <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Filter by User</label>

                                                <select class="form-control" name="filterbyuser" id="filterbyuser" onchange="getdynamicdata();">
                                                    <?php    
                                                foreach($q->result() as $row1){
                                                    $teamid[]= $row1->team_id;
                                                }
                                            ?>
                                            <option value="ALL">ALL</option>
                                                <?php 
                                                $i=1;
                                                 $q1 = $this->db->select('b.user_id, b.first_name, b.last_name')->from('presto_team_members a')->join('system_users b','a.employee_id=b.user_id','left')->where_in('team_id',$teamid,false)->get();
                                                 if($q1->num_rows()>0){
                                                    foreach($q1->result() as $row){
                                                 ?>


                                            <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>

                                                <?php $i++;} 
                                                }?>
                                                </select>
                                            </div>
                                        </div>

                                        <?php  }
                                         }
                                         else{?>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Filter by User</label>
                                                <select class="form-control" name="filterbyuser" id="filterbyuser">
                                                    <?php $q = $this->db->select('user_id, first_name, last_name')->from('system_users')->where('user_id',$user_id)->where('business_location',2)->get();
                                                            foreach($q->result() as $row){ ?>
                                                            <option value="<?php echo $row->user_id;?>"><?php echo $row->first_name." ".$row->last_name;?></option>
                                        <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php }?>     

                                    </div><hr>
                                </form>
                            </div>

                               <div id="showalldynamicnotification"></div>

                        </div>

                    </div>
                        <div class="col-md-2"></div>
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
 <!-- App js -->

        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>

        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>
         

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

       <script type="text/javascript">
          $( document ).ready(function() {
            getdynamicdata();
           });
       </script>
       <?php 
       $activeusertype =  $_SESSION['logged_in']['adminuser'];
       ?>
       <script type="text/javascript">
           function getdynamicdata(){
            var activeusertype = '<?php echo $activeusertype;?>';
            var dfno = $("#filterbydfid").val();
            var startdate = $("#startdate").val();
            var enddate = $("#enddate").val();
            var filterbyuser = $("#filterbyuser").val();
            $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Task/allnotificationsdata/"+dfno+"/"+startdate+"/"+enddate+"/"+filterbyuser+"/"+activeusertype,
            success:function(data){
            $("#showalldynamicnotification").html(data);
            }
            });
           }
       </script>

       <script type="text/javascript">
           function getvalue(i){
            var recordid = i;
                 $.ajax({
            type:"post",
            url:"<?php echo page_url;?>Task/notificationmarkasread/"+recordid,
            success:function(data){
            $("#showalldynamicnotification").html(data);
            }
            });
           }
       </script>
        

        <script src="<?php echo assets_url; ?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
<script>
$( document ).ready(function() {
$('#filterbydfid').select2();
$('#filterbyuser').select2();
});
</script>



    </body>

</html>
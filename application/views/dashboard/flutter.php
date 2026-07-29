<?php $user_id =$this->session->userdata['logged_in']['user_id'];?>

<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php //echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php //echo sitetitle; ?>Dashboard</title>





        <!-- Table Responsive css -->


		<script src="<?php echo assets_url;?>js/angular.min.js"></script>


		 <!-- DataTables -->


        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />


		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />


		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->


        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->


        <!--[if lt IE 9]>


        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>


        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>


        <![endif]-->





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<?PHP 
//$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
//foreach($q->result() as $LOGO);
?>

    <style>

body{
    background-color:#f5f7f9;
    padding-bottom: 80px;
}

table.manglesh thead th {


				background: red;


				color:#fff;


				font-weight:bold;


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

.row-flex {
  display: flex;
  flex-wrap: wrap;
}

@media (max-width:576px){
    .row-flex{
        display:block;
    }
}




.flutter {
  height: 100%;
  padding: 10px;
  background-color:white;
box-shadow:1px 1px  10px lightgrey;
margin-top:20px;
border-radius:10px;
border-bottom: 2px solid lightseagreen;
}

.flutter p{
    font-size:11px;
    text-align:center;
}

.flutter h4{
    text-align:center;
    margin:0px;
    font-size:15px;
}
hr {
    margin-top: 10px;
    margin-bottom: 10px;
    border: 0;
    border-top: 1px solid #eee;
}

.blink_me {
    animation: blinker 6s linear infinite;
    color: #703bda;
    font-size: 15px;
    margin-left: 10px;
}

@keyframes blinker {
  50% {
    opacity: 0;
  }
}

.dailytaskcircle{
    border-radius: 50%;
    width: 45px;
    height: 45px;
    padding: 9px 0px 0px 0px;
    background: #fff;
    border: 2px solid #703bda52;
    color: #666;
    text-align: center;
    font-size: 14px;
    margin: auto;
    color:#703bda;
    font-weight:600;
}

.flutter i{
    color: #703bda;
}

[class*="col-"] {
  margin-bottom: 10px;
}


.delegation-box i {
    font-size: 20px;
    color: #f9ab00;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px;
    background-color: #fff1ea;
    padding: 12px;
}

.refer {
    font-size: 28px;
    background: #DBF3FA;
    color: #283043;
    text-align: center;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px;
}

.refer i{
  color:black;
}
</style>


	</head>
    <body>








        <!-- Navigation Bar-->


                <header id="topnav">


          <?php $this->load->view('common/nav-menu');?>


        </header>


        <!-- End Navigation Bar-->




        <body>
        <div class="wrapper" >
  <div class="container-fluid " >
   
    <div class="row row-flex">
      <div class="col-md-3 ">
        <div class="flutter">
        <h4>DAILY TASK <span class="blink_me">Take Action</span></h4><hr>
        <div class="row">
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle">1</div>
        <p>DELEGATED TASK</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle">2</div>
        <p>PO APPROVAL</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>HELP TICKET</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>ESCALATED TICKET</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>VIEW REPORT</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>VIEW BOGS</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>VIEW SEO BLOGS</p>
        </div></a>
        <a href="#"><div class="col-md-3 col-sm-3 col-xs-3">
        <div class="dailytaskcircle"></div>
        <p>APROVED SO</p>
        </div></a>
        
        </div>
        </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>STORE</h4><hr>
       <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">1</div>
        <p>DELEGATED TASK</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">2</div>
        <p>PO APPROVAL</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>HELP TICKET</p>
        </div></a>
        </div>
       </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
        <h4>ORDERS</h4><hr>
        <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">1</div>
        <p>LOT ORDERS</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">2</div>
        <p>PENDING</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>SALES PENDING</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>TODAYS ORDER</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>MONTHLY ORDER</p>
        </div></a>
        </div>
        </div>
        </div>
    
      <div class="col-md-3 ">
        <div class="flutter">
        <h4 > <span style="color:#703bda;"><i class="fa fa-bell-o" aria-hidden="true"></i></span> NOTIFICATION PANEL</h4><hr>
        <ul class="list-group m-b-0 user-list" style="height: 170px; overflow-y:auto" id="notificationdata"><li class="list-group-item">

<a href="#" class="user-list-item">

    <div class="avatar text-center"><i class="fa fa-caret-right "></i></div>

    <div class="user-desc">

       <span class="">HAVE A GOOD DAY!!</span>

    </div>

</a>

</li><li class="list-group-item">

<a href="#" class="user-list-item">

    <div class="avatar text-center"><i class="fa fa-caret-right "></i></div>

    <div class="user-desc">

       <span class="">PLEASE KEEP YOUR WORKSTATIONS CLEAN</span>

    </div>

</a>

</li><li class="list-group-item">

<a href="#" class="user-list-item">

    <div class="avatar text-center"><i class="fa fa-caret-right "></i></div>

    <div class="user-desc">

       <span class="">PLEASE PROPERLY SANITISE YOUR HANDS</span>

    </div>

</a>

</li></ul>
        </div>
      </div>
      <div class="col-md-3 ">
       <div class="row">
       <a href="#"><div class="col-md-6">
        <div class="flutter">
        <div class="  mis-score">

                                      <div class="text-center">
<i class="fa fa-wpforms" style="font-size:20px; color:#8484d7 ;
    border-radius: 50%;
    margin: auto;
    width: 45px;
    height: 45px; background-color:#e9e9ff; padding:12px; "></i>

                                <p > MIS SCORE </p>

                                

                            </div>


                        </div>
        </div>
        </div></a>
        <a href="#"><div class="col-md-6">
        <div class="flutter">
        <div class=" delegation-box " >
                             
                             
                             <div class="text-center">
                                 <div class="row">
                                 <i class="fa fa-users"></i>
                                     <p > DELEGATION </p>

                                       
                                    
                                 </div>                                
                   </div>
                  
                     
                   
               </div>
        </div>
        </div></a>
        </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>PURCHASE</h4><hr>
       <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">1</div>
        <p>PR VS PO</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">2</div>
        <p>PO VS DELIVERY</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>HELP TICKET</p>
        </div></a>
        </div>
       </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>PRODUCTION
</h4><hr>
       <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">1</div>
        <p>READY ORDERS</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">2</div>
        <p>UNPLANNED ORDERS</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"></div>
        <p>DISPATCH TOMORROW</p>
        </div></a>
        </div>
       </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>LMS</h4><hr>
       <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"><i class="fa fa-bar-chart" aria-hidden="true"></i></div>
        <p>SALES</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"><i class="fa fa-cog" aria-hidden="true"></i></div>
        <p>SERVICE</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle"><i class="fa fa-suitcase" aria-hidden="true"></i></div>
        <p>HR</p>
        </div></a>
        </div>
       </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>HELP DESK</h4><hr>
       <div class="row">
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">0</div>
        <p>ACCOUNTS</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">1</div>
        <p>SERVICE</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>IT</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>SALES</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>PRODUCTION</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>DISPATCH</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>E.A </p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>PURCHASE</p>
        </div></a>
        <a href="#"><div class="col-md-4 col-sm-4 col-xs-4">
        <div class="dailytaskcircle">6</div>
        <p>HR</p>
        </div></a>
        </div>
       </div>
      </div>
      <div class="col-md-3 ">
        <div class="flutter">
       <h4>REFERENCE</h4><hr>
       <div class="row">
        <a href="#">
        <div class="col-sm-3 col-xs-3">
     <div class="text-center refer">
        <!-- <i class="fa fa-file-pdf-o" aria-hidden="true"></i> -->
        <img src="<?php echo dashboard_icon;?>pdf_icon2.png" style="width:65%">
        </div>
        <p style="font-size:11px; padding-right:0px">SALE REFERENCE</p>
         </div></a>
        <a href="#"><div class="col-sm-3 col-xs-3">
       <div class="text-center refer">
        <!-- <i class="fa fa-envelope" aria-hidden="true"></i> -->
        <img src="<?php echo dashboard_icon;?>email_icon2.png" style="width:65%">
        </div>

        <p style="font-size:11px; padding-right:0px">EMAIL TEMPLATE</p>
         </div></a>
        <a href="#"><div class="col-sm-3 col-xs-3">
       <div class="text-center refer">
        <!-- <i class="fa fa-youtube" aria-hidden="true"></i> -->
        <img src="<?php echo dashboard_icon;?>video_icon2.png" style="width:65%">
        </div>
        <p style="font-size:11px; padding-right:0px">SALES VIDEOS</p>
         </div></a>
         <a href="#"><div class="col-sm-3 col-xs-3">
      <div class="text-center refer">
         <!-- <i class="fa fa-file-pdf-o" aria-hidden="true"></i> -->
         <img src="<?php echo dashboard_icon;?>pdf_icon2.png" style="width:65%">
        </div>

        <p style="font-size:11px; padding-right:0px">IT POLICY</p>
         </div></a>
        </div>
       </div>
      </div>
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


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


		


 


</body>


</html>
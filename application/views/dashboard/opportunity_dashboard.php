<?php $user_id = $this->session->userdata['logged_in']['user_id'];
$DI = &get_instance();
$DI->load->model('Dashboard_model');
$getAllLeadStages = $DI->Dashboard_model->getAllLeadStagesofleads();
$getAllLeadStagesforquotation = $DI->Dashboard_model->getAllLeadStagesofquotation();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright; ?>">
    <link rel="shortcut icon" href="<?php echo assets_url; ?>images/favicon.ico">
    <title><?php echo sitetitle; ?>
        Opportunity/Quotation </title>
    <!-- Table Responsive css -->

 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>


<style type="text/css">
     canvas {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
</style>


    <!-- DataTables -->





    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />





    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>





    <script type="text/javascript" src="<?php echo assets_url; ?>ckeditor/ckeditor.js"></script>





    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />

    <!-- <link href="<?php echo assets_url; ?>css/html_design.css" rel="stylesheet" type="text/css" /> -->





    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />





    <link href="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">





    <link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">





    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->





    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->





    <!--[if lt IE 9]>





        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>





        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>





        <![endif]-->


 <style>
        /* Inline CSS for Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>








    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

    <?PHP

    $q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();

    foreach ($q->result() as $LOGO);

    ?>




<style type="text/css">
    .tool i {
        position: absolute;
        right: 18px;
        color: orange;
    }
</style>




</head>

















<body>

















    <!-- Navigation Bar-->





    <header id="topnav">





        <?php $this->load->view('common/nav-menu'); ?>





    </header>





    <!-- End Navigation Bar-->



    <!-- <div class="wrapper">

        <div class="container-fluid">

            <div class="desc-box">

                <div class="row">

                    <div class="col-sm-2">

                        <img src="<?php echo dashboard_icon; ?>salestool_icon.jpg" style="width: 100%;">

                    </div>

                    <div class="col-sm-8">

                        <h6>

                            Sales Master </h6>

                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>

                    </div>

                    <div class="col-sm-2">

                    

                    </div>

                </div>

            </div>

        </div>

    </div> -->



    <div class="wrapper">

        <div class="container-fluid">
        <div class="dashboard-header"><h1>Opportunities/Quotation</h1><hr>

            </div>

            <div class="row">
            <div class="col-md-12">
                  <div class="col-sm-8 card-box" style="height:589px">
                    <div class="dash__box">
                       
                            <div class="col-md-12">
                                <h3>Opportunities Statistics&nbsp; &nbsp; &nbsp;<a href='<?php echo page_url;?>Leads/opportunity' class="btn btn-warning btn-xs"><i class="fa fa-plus"></i></a>
                                <div class="row">
                                    <div class="col-md-4"></div>

                                     <div class="col-md-4"><span style="padding-right:20px"><a href='<?php echo page_url;?>Customer/viewyourcustomers' class="btn btn-primary btn-xs pull-right">View Your Customer</a> </span></div>
                                <div class="col-md-4"> <a href='<?php echo page_url;?>Leads/all_opportunities' class="btn btn-primary btn-xs pull-right">All Opportunities</a></div>

                                </div>

                    </h3><hr>
                        </div>
                      

                        
                      <hr>
                      <div class="row">
                        <?php 
                        $kl=0;
                        foreach ($getAllLeadStages as $row1) {
                          $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id);
                            $color="border: 1px solid #fa5c50;";
                        
                           ?>

                          <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                         <!--  <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?> -->
                        <?php $kl++;
                     }
                     ?>
                     <?php if($user_id==139 || $user_id==161){?>
                     <a href="<?php echo page_url; ?>Dashboard/filteropportunitybysource/ALL">
                            <div class="col-md-4 col-sm-4 col-xs-4 text-center">
                              
                              <span class="btn btn-primary btn-xs">Source Wise Filter Report <i class="fa fa-list"></i></span>
                            </div>
                          </a>
                        <?php }?>

                        <a href="<?php echo page_url; ?>Customer/addnewcustomer">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              
                              <span class="btn btn-warning btn-xs">Add New Customer<i class="fa fa-list"></i></span>
                            </div>
                          </a>
                          <?php if($user_id==139 || $user_id==161){?>
                          <a href="<?php echo page_url; ?>Customer/customer_view">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center" style="padding-top: 20px;">
                              
                              <span class="btn btn-success btn-xs">View Indian Customer List<i class="fa fa-list"></i></span>
                            </div>
                          </a><?php }?>
                 </div><hr>
                 <div class="row">
                        <?php 
                        $kl=0;
                        foreach ($getAllLeadStagesforquotation as $row1) {
                          $datacount = $DI->Dashboard_model->lead_stage_counts($row1->lead_id);
                            $color="border: 1px solid #fa5c50;";
                        ?>
                          <a href="<?php echo page_url; ?>Leads/lead_stages/<?php echo $row1->lead_id; ?>">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><?php echo $datacount; ?></div>
                              <p><?php echo $row1->lead_name; ?></p>
                            </div>
                          </a>
                         <!--  <?php if ($kl % 4 == 0) { ?>
                            <div style="clear: both;height:5px;"></div>
                          <?php } ?> -->
                        <?php $kl++;
                     }
                     ?>
                     <?php
                     $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '15')->where('submoduleid', '38')->where('submodule_access', '1')->get();
                     if ($qry->num_rows() > 0) {
                        ?>
                          <a href="<?php echo page_url; ?>Master/User_management/triggeremail/1">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><i class="fa fa-envelope"></i></div>
                              <p>Trigger Intro Email to Exhibition Customer</p>
                            </div>
                          </a>

                      <?php }?>
                      <?php
                     $qry = $this->db->select('role_id, moduleid, submoduleid, submodule_access')->from('module_capablity')->where('role_id', $user_id)->where('moduleid', '15')->where('submoduleid', '39')->where('submodule_access', '1')->get();
                     if ($qry->num_rows() > 0) {
                        ?>
                            <a href="<?php echo page_url; ?>Master/User_management/triggeremail/2">
                            <div class="col-md-3 col-sm-3 col-xs-3 text-center">
                              <div class="again__circle" style="<?php echo $color;?>"><i class="fa fa-envelope"></i></div>
                              <p>Trigger Intro Email to Common Customer</p>
                            </div>
                          </a>
                      <?php }?>
                     
                 </div>
                    </div>
                  </div>
                 <div class="col-sm-4 card-box" style="height:589px">
                    <div class="dash__box">
                        <div class="col-md-12">
                            <div class="col-md-11">
                        <h3>Opportunities Followups</h3><hr>
                        </div>
                      

                        </div>
                      <hr>
                      <div class="row">
                        <div class="col-md-4">
                            <?php 
                            $b1 = $DI->Dashboard_model->FollowupCounts(1);
                            ?>
                            <a href="<?php echo page_url; ?>Leads/followups/1">
                            <div class="again__circle" style=""><?php echo $b1;?></div>
                            <p>Today's Followup(s)</p>
                            </a>
                        </div>
                            <?php
                            $b2 = $DI->Dashboard_model->FollowupCounts(2); 
                            ?>
                        <div class="col-md-4"> <a href="<?php echo page_url; ?>Leads/followups/2">
                            <div class="again__circle" style=""><?php echo $b2;?></div>
                            <p>Missed's Followup(s)</p>
                            </a></div>
                            <?php 
                             $b3 = $DI->Dashboard_model->FollowupCounts(3); 
                             ?>
                        <div class="col-md-4"> <a href="<?php echo page_url; ?>Leads/followups/3">
                            <div class="again__circle" style=""><?php echo $b3;?></div>
                            <p>Upcoming Followup(s)</p>
                            </a></div>
                            <?php 
                            if($_SESSION['logged_in']['user_id']==139 || $_SESSION['logged_in']['user_id']==61 || $_SESSION['logged_in']['user_id']==161)
                            {
                            ?>
                            <div class="col-md-12">
                                <div class="col-md-1"></div>
                                <div class="col-md-10">
                                    <table class="table table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                    <th colspan="3" style="text-align: center;">Followups Detail</th>
                                    </tr>

                                    <tr>
                                    <th>User</th>
                                    <th>Missed</th>
                                    <th>Today's</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php
                                    $this->db->select('title,user_id,first_name,last_name')->from('system_users')->where('marketing_person',1)->where('business_location',2)->where('user_status',1);
                                    $rest=$this->db->order_by('first_name')->get();
                                    if($rest->num_rows()>0)
                                    {
                                    foreach($rest->result() as $row)
                                    {
                                       $c=$this->Dashboard_model->FollowupCountsUsers(2,$row->user_id);
                                       $d=$this->Dashboard_model->FollowupCountsUsers(1,$row->user_id);
                                    ?>
                                    <tr>
                                    <td><?php echo ucwords(strtolower($row->title));?> <?php echo ucwords(strtolower($row->first_name));?> <?php echo ucwords(strtolower($row->last_name));?></td>
                                    <td><a href='<?php echo page_url;?>Leads/followups/2/<?php echo $row->user_id;?>'><?php echo $c;?></a></td>
                                    <td><a href='<?php echo page_url;?>Leads/followups/1/<?php echo $row->user_id;?>'><?php echo $d;?></a></td>
                                    </tr>
                                    <?php } } ?>
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>



                      </div>
                    </div>
                  </div>
                

            </div>

            </div>
            <?php 
if($this->session->userdata['logged_in']['role']==12 || $this->session->userdata['logged_in']['user_id']==139){
?>

            <div class="row card-box">
                <div class="col-md-6">
                     <h4 class="text-center">Sales Agent Performance for the Current Year</h4><hr>
    <canvas id="salesBarChart"></canvas>
                </div>
                <div class="col-md-6">
                     <h4 class="text-center">Top 5 Order in This Year <a href="<?php echo page_url;?>OrderController"><span class="btn btn-danger btn-xs">Brand Wise Report</span></a></h4>
                    <!--  <div class="form-group">
                         <label>Filter by Brand</label>
                         <select class="form-control" name="filterbybrand" id="filterbybrand">
                             
                         </select>
                     </div> -->
                     <hr>

                    <canvas id="orderChart" width="400" height="200"></canvas>
                </div>
                   
            </div>
        <?php }?>

            <!-- Footer -->

            <?php $this->load->view('common/footer'); ?>

            <!-- End Footer -->


        </div> <!-- end container -->





    </div>

<?php 
if($this->session->userdata['logged_in']['role']==12 || $this->session->userdata['logged_in']['user_id']==139){
?>
 <script>
        var ctx = document.getElementById('salesBarChart').getContext('2d');
        var salesData = {
            labels: [
                <?php foreach($sales_data as $data) { echo '"' . ucwords(strtolower($data->title." ".$data->first_name." ".$data->last_name)) . '",'; } ?>
            ],
            datasets: [{
                label: 'Total Sales',
                data: [
                    <?php foreach($sales_data as $data) { echo $data->total_sales . ','; } ?>
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        };

        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: salesData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>


<script>
    function toSentenceCase(str) {
        return str.toLowerCase().replace(/(^\w|\s\w)/g, m => m.toUpperCase());
    }

    var ctx = document.getElementById('orderChart').getContext('2d');
    var orderData = <?php echo json_encode($order_data_by_brand); ?>;
    var orderChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: orderData.map(order => toSentenceCase(order.name)),
            datasets: [
                {
                    label: 'Total Order Value',
                    data: orderData.map(order => order.total_order_value),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    yAxisID: 'y-axis-1'
                },
                {
                    label: 'Order Count',
                    data: orderData.map(order => order.order_count),
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1,
                    type: 'line',
                    yAxisID: 'y-axis-2'
                }
            ]
        },
        options: {
            scales: {
                yAxes: [{
                    id: 'y-axis-1',
                    position: 'left',
                    ticks: {
                        beginAtZero: true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Total Order Value'
                    }
                }, {
                    id: 'y-axis-2',
                    position: 'right',
                    ticks: {
                        beginAtZero: true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Order Count'
                    }
                }]
            },
            onClick: (evt, activeElements) => {
                if (activeElements.length > 0) {
                    const elementIndex = activeElements[0].index;
                    const brandName = orderChart.data.labels[elementIndex];
                    const orderValue = orderChart.data.datasets[0].data[elementIndex];
                    const orderCount = orderChart.data.datasets[1].data[elementIndex];
                    
                    // Populate modal content
                    document.getElementById('modalContent').innerText = 
                        `Brand: ${brandName}\nTotal Order Value: ${orderValue}\nOrder Count: ${orderCount}`;
                    
                    // Show modal
                    var modal = document.getElementById('orderModal');
                    modal.style.display = "block";
                }
            }
        }
    });

    // Get the modal
    var modal = document.getElementById('orderModal');

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName('close')[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>








    <!-- jQuery  -->

<?php }?>



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





    <script src="<?php echo assets_url; ?>plugins/timepicker/bootstrap-timepicker.min.js"></script>





    <!-- Datatable init js -->





    <script src="<?php echo assets_url; ?>pages/datatables.init.js"></script>





    <script src="<?php echo assets_url; ?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>





    <!-- App js -->





    <script src="<?php echo assets_url; ?>js/jquery.core.js"></script>





    <script src="<?php echo assets_url; ?>js/jquery.app.js"></script>

















</body>





</html>
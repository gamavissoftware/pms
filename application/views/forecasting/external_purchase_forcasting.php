<?php
$CI =& get_instance();
$CI->load->model('Forecasting_model','forecast');
$fgtype=$CI->forecast->getFG_type();
?>
<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <meta name="description" content="">

        <meta name="author" content="<?php echo copyright;?>">



        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">



        <title><?php echo sitetitle;?> Production Forcasting</title>



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

<style>
        .side-box {
            border: 1px solid black;
    background-color: white;
    height: 500px;
    height: 600px;
    margin-top: 5px;

        }

        .left {
            /* position: fixed; */
            /* float: left; */
            /* height: 100%; */
            /* width: 92%; */
           /* border: 1px solid black;
            background-color: white;*/
            /* margin-top: -8px; */
            /* z-index: 1; */
            padding-bottom: 20px;
        }

        .planning {
            background: rosybrown;
            padding: 10px;
        } 

        .planning p {
            color: white;
            font-weight: 600;
            margin: 0px;
            font-size: 15px;
        }

        .left select {
            width: 100%;
          /*  background-color: yellow;*/
            padding: 6px;
        }

        .left label {
            background: rosybrown;
            padding: 6px;
            margin: 0px;
            width: 100%;
        }
    </style>
    </head>





    <body>





        <!-- Navigation Bar-->

        <header id="topnav">

          <?php $this->load->view('common/nav-menu');?>

        </header>

        <!-- End Navigation Bar-->



        <?php $this->load->view('common/info-section.php');?>

        <div class="wrapper">

            <div class="container">

                 <div class="row">
            <div class="col-sm-12">
                <div class="">
                 
                    <form action="">
                        <div class="row">
                            <h3 class="text-center">External Purchase Items Forecasting Sheet</h3>

<!-- <div class="col-sm-2"></div> -->
    <div class="row left">
    <div class="col-sm-12">
                                <label for="" style="color:white;text-align: center;"> Today's Date <?php echo date('d-M-Y');?></label>
                              </div>
                                <div class="col-sm-3">
                            
                                <select name="baseyear" id="baseyear" class="form-control" onchange="getForecastdata();">
                                     <option value="">SELECT BASE YEAR</option>
                                     <?php
                                   $row=$this->db->query('SELECT EXTRACT(year from forecast_date) as year
                                    FROM fg_forecast_data
                                    GROUP BY year');
                                    if($row->num_rows()>0)
                                    {
                                        foreach($row->result() as $rows)
                                        {
                                            $nextyear=$rows->year+1;
                                    ?>
                                    <option value="<?php echo $rows->year;?>-<?php echo $nextyear;?>"><?php echo $rows->year;?>-<?php echo $nextyear;?></option>
                                    <?php
                                        }
                                    }
                                    ?>                                  
                                   
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="fgtype" id="fgtype" class="form-control" onchange="getForecastdata();">
                                    <option value="ALL">ALL</option>
                                    <?php  
                                    if(count($fgtype)>0)
                                    { 
                                        
                                        $i=0; 
                                    foreach($fgtype as $fgtype1)
                                    {
                                   
                                    ?>
                                    <option value="<?php echo $fgtype1->id;?>"><?php echo $fgtype1->type_name;?></option>
                                    <?php $i++;
                                        } } ?>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <select name="weeks" id="weeks" class="form-control" onchange="getForecastdata();">
                                    <option value="">Select Weeks</option>
                                    <?php 
                                    for($i=1;$i<55;$i++)
                                    {
                                    ?>
                                    <option value="<?php echo $i;?>"><?php echo $i;?> Weeks</option>
                                    <?php 
                                    } 
                                    ?>
                                   
                                </select>
                            </div>

                           


                                 <div class="col-sm-3">
                                    <select name="datatype" id="datatype" class="form-control" onchange="getForecastdata();">
                                    <option value="1" selected>Include Not Required Semi FG</option>
                                    <option value="2">Remove Not Required Semi FG</option>
                                   
                                    </select>
                                </div>
    </div>

    <div class="row">
    <div class="col-md-12" id="semifgdata">
        
    </div> 
                
               

</div>
<div class="col-sm-2"></div>


                              
                          

                        
                    </form>

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

        <script type="text/javascript">
            
            function getForecastdata()
            {
                var baseYear=$("#baseyear").val();
                var fGtype=$("#fgtype").val();
                var weeks=$("#weeks").val();
                var machine=$("#machine").val();
                var datatype=$("#datatype").val();

                if(baseYear!='' && fGtype !='' && weeks !='' && machine != '' && datatype!='')
                {
                        $.ajax({

                        type:"post",

                        url:"<?php echo page_url;?>Forecasting/getsemiforcastdata",

                        data:"baseyear="+baseYear+"&fgtype="+fGtype+"&weeks="+weeks+"&machine="+machine+"&datatype="+datatype,

                        success:function(data){

                            $("#semifgdata").html(data);

                        }

                        });

                }


            }

            function getSubPartCount() {
                var sub_part = $("#sub_part").val();
                var arr = [];
                $('.fin_id').each(function() {
                    arr.push($(this).val());
                });

                var fin_id = "'" + arr.join("', '") + "'";
                    
                    $.ajax({
                            type:"post",
                            url:"<?php echo page_url;?>Forecasting/getSubPartCount",
                            data:{sub_part: sub_part, fin_id: fin_id},

                            success:function(data){
                                // alert(data);
                                $("#subpartdata").html(data);
                                // console.log(data);
                            }
                    });
            }

        </script>


    </body>

</html>
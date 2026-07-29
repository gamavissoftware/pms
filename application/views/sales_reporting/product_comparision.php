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

        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />

        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/css/bootstrap-datepicker.min.css">
        <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

        <!--[if lt IE 9]>

        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

        <![endif]-->
            

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>   


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


.mycardbox
{
     padding: 10px;
    box-shadow: 1px 1px 10px lightgrey;
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-border-radius: 5px;
    background-clip: padding-box;
    margin-bottom: 20px;
    background-color: #f5f5f5;
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

            <div class="container-fluid">



                <!-- Page-Title -->

                <div class="row" style="margin-top:20px;">

                    <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">

                        <div class="page-title-box">

                         <!-- <div class="btn-group pull-right">

                          <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">ADD NEW DEPARTMENT</button>

                               

                            </div> -->

                           

                            <h4 class="page-title text-center">Product Comparision Report</h4><hr>
                        </div>

                    </div>

                </div>

                <!-- end page title end breadcrumb -->
                <?php 

                $prd=$this->uri->segment(3);
                $startdate=$this->uri->segment(4);
                $enddate=$this->uri->segment(5);
                $startdate1=$this->uri->segment(6);
                $enddate1=$this->uri->segment(7);
               
                if($startdate<>'' && $enddate<>'' &&  $startdate1<>'' && $enddate1<>'')
                {
                $dateone=date('d-m-Y',strtotime($startdate));
                $datetwo=date('d-m-Y',strtotime($enddate));
                $datethree=date('d-m-Y',strtotime($startdate1));
                $datefour=date('d-m-Y',strtotime($enddate1));
                }else
                {
                $dateone='';
                $datetwo='';
                $datethree='';
                $datefour='';
                }
        
                ?>
                <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
                <div class="row">
                  <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="card-box">
                        <form action="<?php echo page_url; ?>Sales_stats_reporting/filter_product_comparision" method="post">
                      <div class="row">
                         
                    
                        <div class="col-sm-6">
                        <div class="form-group">
                          <label for="field-1" class="control-label">PRODUCT</label>
                          <span style="color:red;">*</span>
                          <select name="product" id="product" class="form-control select2" required>
                            <option value=""></option>
                            <?php 
                          
                            $reste=$this->db->select('id,instruments_name,pack_size')->from('presto_instruments')->where('status',1)->get();
                            if($reste->num_rows()>0)
                            {
                                foreach($reste->result() as $roww)
                                {
                                    if($prd==$roww->id)
                                    {
                                        $a="selected";
                                    }else
                                    {
                                        $a='';
                                    }

                                ?>
                                <option value="<?php echo $roww->id;?>" <?php if($prd==$roww->id){?> selected <?php } ?>><?php echo $roww->instruments_name;?>-<?php echo $roww->pack_size;?></option>
                            <?php } } ?>
                          </select>
                        </div>
                      </div>


                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="startdate1" id="startdate1" value="<?php echo $dateone; ?>" class="form-control datepicker" required>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="enddate1" id="enddate1" value="<?php echo $datetwo; ?>" class="form-control datepicker" required >
                        </div>
                      </div>

                      <div class="col-md-6"></div>
                         <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">START DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="startdate2" id="startdate2" value="<?php echo $datethree; ?>" class="form-control datepicker" required >
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="form-group">
                          <label for="field-1" class="control-label">END DATE</label>
                          <span style="color:red;">*</span>
                          <input type="text" name="enddate2" id="enddate2" value="<?php echo $datefour; ?>" class="form-control datepicker" required >
                        </div>
                      </div>

                      
                      
                    </div>
                    <div class="row">
                      <div class="col-sm-12 text-center">
                          <div class="form-group">
                            <button type="submit" style="margin-top: 25px;"class="btn btn-info btn-sm">Submit</button>
                          </div>
                      </div>
                    </div>
                  </form>
                    </div>
                  </div>
                  
                </div>
                
                <div class="row">
            
  
  

 <div class="row">
<div class="card-box" style="min-height:2500px;"> 
<div class="col-md-12">
<h3 class="text-center">Product Comparison</h3>
</div>
<div class="col-md-4">
  <div class="mycardbox" style="height:500px; overflow:auto;">
  <div  id="buyeradd" >

</div>
  </div>

</div>

<div class="col-md-4 " >
<div class="mycardbox" id="buyersubtract" style="height:500px; overflow:auto;">

</div>
</div>


<div class="col-md-4 " >
<div class="mycardbox" id="qty_dec" style="height:500px; overflow-x:hidden;">

</div>
</div>

<div style="clear:both;height:40px;">
<div class="col-md-5 " >
<div class="mycardbox" id="qty_inc" style="height:500px; overflow-x:hidden;">

</div>
</div>



<div class="col-md-4" >
<div class="mycardbox" id="stablebuyer" style="height:500px; overflow-x:hidden;">

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



         <!-- jQuery  -->

         <script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.min.js"></script>
<script>

$( document ).ready(function() {
    getdata();
});

    $('.datepicker').datepicker({
     autoclose: true,
 format:'dd-mm-yyyy',
   // startView: "months", 
   //  minViewMode: "months",
     orientation: "bottom"
   });

    $('.select2').select2({ });



</script>
<?php 

    if($this->uri->segment(3)<>'' && $this->uri->segment(4)<>'' && $this->uri->segment(5)<>'' && $this->uri->segment(6)<>'' && $this->uri->segment(7)<>'' )
    {
?>
 <script type="text/javascript">
    
    function getdata()
    {

      $.ajax({
        url: "<?php echo page_url; ?>Sales_stats_reporting/get_customers_added/<?php echo $this->uri->segment(3);?>/<?php echo $this->uri->segment(4);?>/<?php echo $this->uri->segment(5);?>/<?php echo $this->uri->segment(6);?>/<?php echo $this->uri->segment(7);?>",
        method: "GET",
        success: function(data) {
              
        var d=data.split("|");
        $("#buyeradd").html(d[0]);
        $("#buyersubtract").html(d[1]);
         $("#stablebuyer").html(d[2]);
         $("#qty_dec").html(d[3]);
         $("#qty_inc").html(d[4]);
        }

        }); 
     
     }
 </script>
 <?php 
 }
?>




    </body>

</html>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="<?php echo copyright;?>">
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
    <title><?php echo sitetitle;?></title>
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

    <?php 
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

      </style>
</head>
<body>

      <header id="topnav">
        <?php $this->load->view('common/nav-menu');?>
      </header>

  <div class="wrapper">
    <div class="container">
        <div class="row" style="margin-top:20px;">
            <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                <div class="page-title-box">
                  <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button" style="background-color: ;"><i class="fa fa-arrow-left"></i>Back</button></a>
                  <div class="btn-group pull-right">
                      <button class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target="#con-close-modal" style="background-color: ;">Add Transporter Details</button>
                  </div>
                  <h4 class="page-title text-center">Transporter List</h4>
                </div>
            </div>
        </div>

        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

         <div class="row">
            <div class="col-sm-12">
                <div class="card-box table-responsive">
                    <table id="example" class="table table-striped table-bordered pretty" style="background-color: ;">
                        <thead>
                        <tr>
                          <th>SR. NO.</th>
                          <th>NAME</th>
                          <th>MOBILE NO</th>
                          <th>ADDRESS</th> 
                          <th>PAN</th>  
                          <th>GSTN</th>  
                          <th>TDS</th>
                          <th>TDS CERT</th>                                
                          <th>ACTION</th>                                 
                        </tr>
                        </thead>                               
                    </table>
                </div>
            </div>
        </div>

        <div id="con-close-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <form id="loginForm" method="post" action="<?php echo page_url;?>Master/Transporter_details/add_transporters" autocomplete="nope" enctype="multipart/form-data">
                <input type="hidden" name="mode" value="">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title">Add Transporter Details</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Name <span style="color:red">*</span></label>
                                      <input type="text" class="form-control" name="name" autocomplete="nope" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Mobile No<span style="color:red">*</span></label>
                                        <input type="text" class="form-control" name="mobile_no" id="mobile_no" autocomplete="nope" required onblur="checkIfMobileNoExists()">
                                    </div>
                                </div>
                              

                                <div class="col-md-3">
                                <div class="form-group">
                                <label for="field-1" class="control-label">GST Applicable<span style="color:red">*</span></label><br/>
                                <input type="checkbox" name="gst_appl" id="gst_appl" onchange="check_gstno();"  value="1">
                                </div>
                                </div>
                                <script type="text/javascript">
                                    function check_gstno(){
                                        if($('#gst_appl').is(":checked"))
                                        {
                                            $("#gstno").css('display','');
                                            $("#gst").attr('required',true);
                                        }else
                                        {
                                            $("#gstno").css('display','none');
                                            $("#gst").attr('required',false);
                                        }

                                    }
                                </script>


                                <div class="col-md-3" id="gstno" style="display:none;">
                                <div class="form-group">
                                <label for="field-1" class="control-label">GST No.<span style="color:red">*</span></label><br/>
                                <input type="text" name="gst" id="gst" class="form-control">
                                </div>
                                </div>


                                <div class="col-md-3">
                                <div class="form-group">
                                <label for="field-1" class="control-label">PAN No.<span style="color:red">*</span></label><br/>
                                <input type="text" name="pan" id="pan" class="form-control" required>
                                </div>
                                </div>



                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">TDS %<span style="color:red">*</span></label>
                                        <input type="number" class="form-control" step="any" name="tds" id="tds" required value="" onkeyup="check_tds();">
                                    </div>
                                </div>
                                <script>
                                    function check_tds()
                                    {
                                        var tds=$("#tds").val();
                                        $("#tds_declaration").css('display','none');
                                        $("#tds_dec").attr('required',false);

                                        if(tds==0)
                                        {
                                        $("#tds_declaration").css('display','');
                                        $("#tds_dec").attr('required',true);
                                        }

                                    } 
                                </script>


                                <div class="col-md-3" id="tds_declaration" style="display:none;">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">TDS Declaration<span style="color:red">*</span></label>
                                        <input type="file" class="form-control" name="tds_dec" id="tds_dec">
                                    </div>
                                </div>



                                  <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Address<span style="color:red">*</span></label>
                                        <textarea class="form-control" name="address" autocomplete="nope" required=""></textarea>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="submit" id="save" class="btn btn-info" value="Submit"> 
                        </div>
                    </div>
                </div>
            </form>
          </div><!-- /.modal -->


          <?php $this->load->view('common/footer');?>
            </div> <!-- end container -->
        </div>

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
        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>
        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>

        <script>
        $( document ).ready(function() {
          $('#example').dataTable({
              "bProcessing": true,
              "pagination":true,
              "sAjaxSource": "<?php echo page_url;?>Master/Transporter_details/transporters_listing",
              "aoColumns": [
                            { mData: 'sr_no' },
                            { mData: 'name' },
                            { mData: 'mobile_no' },
                            { mData: 'address' },
                            { mData: 'pan' },
                            { mData: 'gst' },
                            { mData: 'tds' },
                            { mData: 'tds_cert' },
                            { mData: 'edit' }
                          ]
                  });   
          });


        function checkIfMobileNoExists() {
                var mobile_no = $("#mobile_no").val();

                $.ajax({
                          type:"post",
                          url:"<?php echo page_url;?>Master/Transporter_details/checkIfMobileNoExists",
                          data:"mobile_no="+mobile_no,
                          success:function(data) {
                            if(data == 1) {
                              alert('Mobile no. already exists!');
                              $("#mobile_no").val('');
                            }
                          }
                    });
        }
      </script>
</body>
</html>

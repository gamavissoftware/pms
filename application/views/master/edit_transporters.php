<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="<?php echo copyright;?>">
        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">
        <title><?php echo sitetitle;?></title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />
        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
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
                            <a href="javascript:history.go(-1)"><button type="submit" class="btn btn-success" name="action_button"><i class="fa fa-arrow-left"></i>Back</button></a>                           
                            <h4 class="page-title">Edit Transporter</h4>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
        <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>

               <div class="row">
                    <div class="col-xs-12">
                        <div class="card-box">

                            <div class="row">
                                <div class="col-sm-12 col-xs-12 col-md-12">

                <?php
                $id= $this->uri->segment(4);
                $this->db->select('*')->from('transporter_details')->where('id',$id);
                $query =$this->db->get();
                $res = $query->result();
                foreach($res as $row);
                ?>
                
              <form method="post" action="<?php echo page_url;?>Master/Transporter_details/update_transporter/<?php echo $this->uri->segment(4);?>" enctype="multipart/form-data">
                <input type="hidden" name="old_image" value="<?php echo $row->tds_cert;?>">s
                  <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                      <label>Name <span style="color:red">*</span></label>
                                      <input type="text" class="form-control" name="name" autocomplete="nope" value="<?php echo $row->name;?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Mobile No<span style="color:red">*</span></label>
                                        <input type="text" class="form-control" name="mobile_no" id="mobile_no" value="<?php echo $row->mobile_no;?>" autocomplete="nope" required onblur="checkIfMobileNoExists()">
                                    </div>
                                </div>

                                  <div class="col-md-3">
                                <div class="form-group">
                                <label for="field-1" class="control-label">GST Applicable<span style="color:red">*</span></label><br/>
                                <input type="checkbox" name="gst_appl" id="gst_appl" onchange="check_gstno();"  value="1" <?php if($row->gst_appl==1){?> checked <?php } ?>>
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
                                <input type="text" name="gst" id="gst" class="form-control" value="<?php echo $row->gst;?>">
                                </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">Address<span style="color:red">*</span></label>
                                        <textarea class="form-control" name="address" autocomplete="nope" required=""><?php echo $row->address;?></textarea>
                                    </div>
                                </div>


                                    <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="field-1" class="control-label">PAN No.<span style="color:red">*</span></label><br/>
                                    <input type="text" name="pan" id="pan" value="<?php echo $row->pan;?>" class="form-control" required>
                                    </div>
                                    </div>

                        

                                 <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">TDS %<span style="color:red">*</span></label>
                                        <input type="number" class="form-control" step="any" name="tds" id="tds" required value="<?php echo $row->tds;?>" onkeyup="check_tds();">
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

                                <?php 
                                if($row->tds==0)
                                {
                                    $a="";

                                }else
                                {
                                     $a="display:none;";
                                }
                                ?>


                                <div class="col-md-3" id="tds_declaration" style="<?php echo $a;?>">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">TDS Declaration<span style="color:red">*</span></label>
                                        <input type="file" class="form-control" name="tds_dec" id="tds_dec">
                                        <?php if($row->tds_cert<>''){?>

                                        <a href='<?php echo page_url1;?>image_bank/tds_cert/<?php echo $row->tds_cert;?>' download>Download</a>
                                        <?php } ?>
                                    </div>
                                </div>





                            </div>
                  </div>
                    <div class="col-md-9"></div>
                    <div class="col-md-3">
                      <div class="form-group pull-right" style="padding-top:24px;">
                        <label>&nbsp;</label>
                        <input type="submit" class="btn btn-success" value="Update">
                      </div>
                    </div>
                  
                  </form>
                                   

                                </div>

                            </div>
                            <!-- end row -->
                        </div> <!-- end ard-box -->
                    </div><!-- end col-->

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
<script language="javascript" type="text/javascript">   
jQuery.noConflict();
$(document).ready(function() {
    check_gstno();
$("#save").click(function() {
var lead_type = $("#lead_type").val();
if(lead_type=='')
{
  $("#error_lead_type").html('Required!');
}
var color = $("#color").val();
if(color=='')
{
  
  $("#error_color").html('Required!');
}

var status = $("#status").val();
if(status=='')
{
  
  $("#error_status").html('Required!');
}



if(lead_type=='' || color=='' || status=='' )
{
  
  return false;
}

});
});
</script>

<script type="text/javascript">
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
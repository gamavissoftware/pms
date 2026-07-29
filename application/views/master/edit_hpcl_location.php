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
                            <h4 class="page-title">Edit HPCL Location</h4>
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
                $this->db->select('*')->from('hpcl_location')->where('id',$id);
                $query =$this->db->get();
                $res = $query->result();
                foreach($res as $row);
                ?>
                
              <form method="post" action="<?php echo page_url;?>Master/Hpcl_location/update_location_type/<?php echo $this->uri->segment(4);?>">
                  <div class="col-md-3">
                      <div class="form-group">
                        <label>Location Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?php echo $row->name;?>" required>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="form-group">
                          <label>Code <span style="color:red">*</span></label>
                      <input type="text" class="form-control" name="address" id="address" value="<?php echo $row->address;?>">
                  </div>
                  </div>
                  <div class="col-md-4">
                    <label for="field-2" class="control-label">Status</label>
                    <select class="form-control" id="status" name="status">
                    <option value="">--Select Status--</option>
                    <option value="1" <?php if($row->status == 1) { echo "selected";}?>>Active</option>
                    <option value="0" <?php if($row->status == 0) { echo "selected";}?>>Inactive</option>
                    </select>
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
    </body>
</html>
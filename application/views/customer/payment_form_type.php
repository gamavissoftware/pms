<?php 
if($this->uri->segment(3)<>'')
{
$user_id = base64_decode($this->uri->segment(3));

}else
{
    echo "You are trying to use an invalid link"; exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LEAD TYPE</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?php echo assets_url;?>js/angular.min.js"></script>
    <link href="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" >
    <link href="<?php echo assets_url;?>plugins/audio/manage-audio.css" rel="stylesheet" type="text/css">
    <link href="<?php echo assets_url;?>plugins/newselect2/select2.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single
        {
            height: 34px !important;
        }
        .mobile-form {
            background-color: whitesmoke;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid lightgray;
            margin: 20px 0px;
        }

        .mobile-form h1 {
            color: #17a2b8;
            font-size: 20px;
            text-align: center;
            margin: 8px 0px;
            font-weight: 700;
        }

        .mobile-form label {
            font-size: 14px;
        }

        .mobile-form select {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

        .mobile-form textarea {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

        .mobile-form input {
            width: 100%;
            border: 1px solid lightgray;
            border-radius: 2px;
            padding: 5px 10px;
            font-size: 14px;
            outline: #17a2b8;
            -webkit-appearance: none;
        }

        @media (max-width:576px) {
            .mobile-form input {
                font-size: 12px;
            }

            .mobile-form label {
                font-size: 12px;
            }

            .mobile-form select {
                font-size: 12px;
            }

            .mobile-form textarea {
                font-size: 12px;
            }

            .btn {
                margin-top: 29px !important;
                padding: 5px 10px !important;
                font-size: 12px !important;
            }

            .mobile-form img{
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="container ">
        <div class="mobile-form">
            <span style="color:red;"><?php echo $this->session->flashdata('message'); ?></span>
            <div class="row">
                <div class="col-sm-12 col-12">
                    <div class="text-center">
                        <img src="<?php echo sfdocument;?>hpcl_logo.png" style="width: 200px;">
                    </div>
                </div>
            </div>

              <hr>

             <div class="row">
                 <?php 
                    $r=$this->db->select('companyname,id')->from('store_rack_location')->where('status',1)->get();
                    if($r->num_rows()>0)
                    {
                        foreach($r->result() as $row)
                        {
                    ?>
                <div class="col-sm-12 col-md-6 col-lg-6 text-center" style="margin-bottom: 25px;">
                    
                   
                    <a href="<?php echo page_url;?>Open_leads/payment_form/<?php echo $this->uri->segment(3);?>/NA/<?php echo $row->id;?>" style="text-decoration: none;"> 
                    <div class="card" style="background-color:#FF6D6A;color:white;">
                    <div class="card-body">
                    <h5 class="card-title" style="font-weight:bold;"><?php echo $row->companyname;?></h5>


                    </div>
                    </div>
                    </a>
                   
                </div>

                 <?php 
                    }
                    }
                    ?>
              
             </div>
           
          
            
        </div>
    </div>

<script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>
<script src="<?php echo assets_url;?>js/detect.js"></script>
<script src="<?php echo assets_url;?>js/fastclick.js"></script>
<script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>
<script src="<?php echo assets_url;?>js/waves.js"></script>
<script src="<?php echo assets_url;?>js/wow.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>
<script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>
<script src="<?php echo assets_url;?>js/jquery.core.js"></script>
<script src="<?php echo assets_url;?>js/jquery.app.js"></script>
<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo assets_url;?>plugins/audio/manage-audio.js"></script>

<script src="<?php echo assets_url;?>plugins/newselect2/select2.min.js" type="text/javascript"></script>

</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sitetitle; ?> View Bills</title>
    <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>


body {
  width: 230mm;
  height: 100%;
  margin: 0 auto;
  padding: 0;
  font-size: 12pt;
 
}
* {
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}
.main-page {
  width: 210mm;
  min-height: 297mm;
  margin: 10mm auto;
  background: white;
  /* box-shadow: 0 0 0.5cm rgba(0,0,0,0.5); */
}

@page {
  size: A4;
  margin: 0;
}
@media print {
  html, body {
    width: 210mm;
    height: 297mm;        
  }
  .main-page {
    margin: 0;
    border: initial;
    border-radius: initial;
    width: initial;
    min-height: initial;
    box-shadow: initial;
    background: initial;
    page-break-after: always;
  }
}



     /* @page{
  size: auto A4 landscape;
  margin: 2mm;
}  */

        /* .packing {
            margin-top: 50px;
            margin-bottom: 50px;
            background-color: white;
            border: 1px solid lightgray;
            height: none;
            padding: 20px;
        } */
        
        table {
            width: 100%;
        }
        
        .packing td {
            padding: 5px;
            /* font-weight: 700; */
            /* width: 200px; */
            /* border: 1px solid black; */
            font-size: 18px;
        }
        
        .packing h5 {
            text-align: center;
            font-size: 30px;
            font-weight: 700;
        }
        
        .packing img {
            width: 150px;
            float: right;
        }
        
        .packing span {
            font-size: 15px;
        }
        
        .packing th {
            border: 1px solid black; 
            text-align: center;
            font-size: 18px;
        }
        
        .row-flex {
            display: flex;
            flex-wrap: wrap;
        }
        
        @media (max-width:576px) {
            .row-flex {
                display: block;
            }
        }
        
        .next {
            border: 1px solid black;
            height: 100%;
        }

        .left-line{
            width:2px;
            height:100px;
            background-color:black;
        }

        @media print {
            .hide
            {
                display: none;
            }
        }
    </style>

</head>

<body>
<div class="text-center">
                <span class="btn btn-success hide" onclick="printDiv();">Print</span>
                </div>
    


               
                    <div class="main-page packing">
                        <div class="sub-page">
                            <h4 style='text-align:center; color:red;'>Image will only we visible if it is uploaded </h4>
                            
                                <?php

                                $bill_id=$this->uri->segment(3);
                                // $job_id=$this->uri->segment(4);
                                // $order_id=$this->uri->segment(5);

                                $query = $this->db->select('a.conv_id,a.bill_attachment, a.tour_date, a.city, a.company_name, a.living_expense_id,a.foodexpense, a.living_exp_amount, a.travel_exp_id, a.travel_exp_amount, b.options, c.options as expensetype, a.livingexpensebill, a.travellingbill')->from('member_conveyance_brief a')->join('conveyance_type_options b','a.living_expense_id=b.id','left')->join('conveyance_type_options c','a.travel_exp_id=c.id','left')->where('a.conv_id',$bill_id)->get();
                                if($query->num_rows()>0){
                                foreach($query->result() as $record){


                                ?>
                                
                                <div class='col-md-12' >
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                    <img src="<?php echo tourbills;?><?php echo $record->bill_attachment ?>" alt="" style="width: 50%;" style='border: 1px solid lightgray;'>
                                    <img src="<?php echo tourbills;?><?php echo $record->livingexpensebill ?>" alt="" style="width: 50%;" style='border: 1px solid lightgray;'>
                                    <img src="<?php echo tourbills;?><?php echo $record->travellingbill ?>" alt="" style="width: 50%;" style='border: 1px solid lightgray;'>
                                        </div>
                                    <div class="col-md-2"></div>
                                 </div>
                                
                                <?php }}?>

        </div>
        </div>
</body>
<script type="text/javascript">
    function printDiv() 
{


  window.print();

}
    
</script>

</html>
<?php //echo $this->uri->segment(3);
$sql = $this->db->select('a.company_name, a.added_on, road_permit, pan_no, gst_no, a.delivery_date,  a.po_number, a.phone, a.internal_order_no, a.contact_person, a.email, a.mobile_number, a.address, a.ship_address, a.payment_terms, a.freight_type, a.packing_charges, a.remarks, a.packing_type, b.item_id')
                ->from('prestogroup_orders a')
                ->join('order_instruments b', 'b.order_id=a.order_id')
                ->where('a.order_id', $this->uri->segment(3))
                ->get();

    if ($sql->num_rows() > 0) {
        foreach ($sql->result() as $row);
        $email = $row->email;
        $mobile_number = $row->mobile_number;
        $address = $row->address;
        $po_no = $row->po_number;
        $phone = $row->phone;
        $internal_order_no = $row->internal_order_no;
        $contact_person = $row->contact_person;
        $ship_address = $row->ship_address;
        $delivery_schedule = $row->delivery_date;
        $payment_terms = $row->payment_terms;
        $freight = $row->freight_type;
        $packing_charges = $row->packing_charges;
        $packing_type = $row->packing_type;
        $remarks = $row->remarks;
        $item_id = $row->item_id;
        $added_on = $row->added_on;
        $road_permit = $row->road_permit;
        $pan_no = $row->pan_no;
        $gst_no = $row->gst_no;
        $company_name = $row->company_name;
    } else {
        $email = '';
        $mobile_number = '';
        $address = '';
        $po_no = '';
        $phone = '';
        $internal_order_no = '';
        $contact_person = '';
        $ship_address = '';
        $delivery_schedule = '';
        $payment_terms = '';
        $freight = '';
        $packing_charges = '';
        $item_id = '';
        $remarks = '';
        $packing_type = '';
        $added_on = '';
        $road_permit = '';
        $pan_no = '';
        $gst_no = '';
        $company_name = '';
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Slip</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <style>
        .slip-box {
            border: 1px solid lightgray;
            background-color: white;
            height: auto;
            margin-top: 50px;
            padding: 20px;
            margin-bottom: 50px;
        }
        
        .slip-box h3 {
            text-align: center;
            font-size: 20px;
        }
        
        td {
            padding: 3px !important;
        }
        
        .table-bordered td,
        .table-bordered th {
            border: 1px solid black !important;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
             <div class="text-center">
                <span class="btn btn-success" style="text-align:center" onclick="printDiv();">PRINT</span>
            </div>
                <div class="slip-box">
                     <div class="text-center">
                        <img src="<?php echo sfdocument;?>testronix/textronixlogo1.jpg" style="margin: auto;">
                    </div>
                    <h3>DISPATCH SLIP</h3>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 33%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Ref. No. : <?php echo $internal_order_no ;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 33%;"></td>
                            <td style=" width: 33%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Date : <?php echo date('d-m-Y H:i:s', strtotime($added_on));?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 67%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Client Name : <?php echo $company_name;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td style=" width: 33%; "></td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            <strong>Delivery Address:</strong><br> <?php echo $ship_address;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            <strong>Billing Address :</strong><br> <?php echo $address;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Contact Person : <?php echo $contact_person;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Mobile : <?php echo $mobile_number;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Phone : <?php echo $phone;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Fax :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Email : <?php echo $email;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Road Permit : <?php echo $road_permit;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            PAN No. : <?php echo $pan_no;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            GST No. : <?php echo $gst_no;?>

                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <h3 style="text-align: left;">ORDER DETAIL</h3>







                    <table rules="all" bordercolor="black" border="1" width="100%">


                        <tr style="border-collapse:collapse;">

                            <td width="50%">
                                <table rules="all" cellpadding="5" width="100%">

                                    <tbody>
                                        <tr>
                                            <td>PO No.</td>
                                            <td><?php echo $po_no;?> </td>
                                        </tr>
                                        <tr>
                                            <td>PO Date</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Payment Terms </td>
                                            <td><?php echo $payment_terms; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Payment Details</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Shipment by</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>


                            </td>

                            <td width="50%">
                                <table rules="all" cellpadding="5" width="100%">

                                    <tbody>
                                        <tr>
                                            <td>Delivery Schedule</td>
                                            <td><?php echo $delivery_schedule; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Warranty Period</td>
                                            <td> 12 Months</td>
                                        </tr>
                                        <tr>
                                            <td>Packing Charges </td>
                                            <td><?php echo $packing_charges; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Packing Type</td>
                                            <td><?php echo $packing_type; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Freight</td>
                                            <td><?php echo $freight; ?></td>
                                        </tr>
                                    </tbody>
                                </table>


                            </td>

                        </tr>





                    </table>


                    <h3 style="text-align: left; margin-top: 20px;">PRODUCT DETAIL</h3>

                    <table width="100%" border="1" bordercolor="black" rules="all">
                        <tr>
                            <td style="width: 72px; text-align: center;">Serial No</td>
                            <td style="text-align: center;">Product Name</td>
                            <td style="text-align: center;">Quantity</td>
                            <td style="text-align: center;">Product Code</td>
                        </tr>
                        <?php $query = $this->db->select('a.instruments_name, a.product_unique_code, b.qty')
                                                ->from('presto_instruments a')
                                                ->join('order_instruments b', 'b.item_id=a.id')
                                                ->where('b.order_id', $this->uri->segment(3))
                                                ->get();

                                if ($query->num_rows() > 0) {
                                    $i = 1;
                                    foreach ($query->result() as $rows) {
                                                                            
                            ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i;?></td>
                            <td style="text-align: center;"><?php echo $rows->instruments_name ;?></td>
                            <td style="text-align: center;"><?php echo $rows->qty;?></td>
                            <td style="text-align: center;"><?php echo $rows->product_unique_code ;?></td>
                        </tr>
                                <?php  $i++;} } ?>
                    </table>

                    <table width="100%" style="margin-top: 20px;">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 67%; padding: 0 !important;">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td style="margin-top: 20px;">
                                            Remarks : <?php echo $remarks;?>
                                        </td>
                                    </tr>
                                </table>
                            </td>

                            <td style=" width: 33%; "></td>
                        </tr>
                    </table>
                    <h3 style="text-align: left; font-size: 14px;">Dealing Manager: Prabhakar Kumar</h3>

                    <h3 style="text-align: left; ">DISPATCH DETAILS</h3>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Mode of Despatch :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Date of Despatch :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td style="width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Invoice No. :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 2%;"></td>
                            <td style=" width: 45%; ">
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Invoice Date :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td>
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Accounts Verified By (Name & Signature) :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table width="100%">
                        <tr style="border-collapse:collapse;">
                            <td>
                                <table class=" table table-bordered ">
                                    <tr>
                                        <td>
                                            Despatched By (Name & Signature) :
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-sm-1 "></div>
        </div>
    </div>

    <script>
        function printDiv() 
{

  window.print();

}
</script>

</body>

</html>
<?php
$uri = $this->uri->segment(3);
$CI = &get_instance();
$CI->load->model('Master_model', 'master');
$checkIfProductIsApproved = $CI->master->checkIfLeadProductIsApproved($uri);

if ($uri <> '') {
    $sql = $this->db->select('contact_person, company_name, customer_name, email_id, city, state, contact_no, postal_address, check_terms, validity_date, general_terms, bulk_terms, alt_contact_no, hpcl_company')
                    ->from('leads')
                    ->where('id', $uri)
                    ->get();

    if ($sql->num_rows() > 0) {
        foreach ($sql->result() as $row);
            $contact_person = $row->contact_person;
            $alt_contact_no = $row->alt_contact_no;
            $customer_name = $row->customer_name;
            $company_name = $row->company_name;
            $city = $row->city;
            $contact_no = $row->contact_no;
            $address = $row->postal_address;
            $general_terms = $row->general_terms;
            $bulk_terms = $row->bulk_terms;
            $email_id = $row->email_id;
            $hpcl_company = $row->hpcl_company;
            if($row->check_terms == 1) {
                $tnc = $row->bulk_terms;
            } else {
                $tnc = $row->general_terms;
            }
            if($row->validity_date == '0000-00-00') {
                  $validity_date = '';
              } else {
                  $validity_date = date('d-m-Y',strtotime($row->validity_date));
              }
        } else {
            $contact_person = '';
            $alt_contact_no = '';
            $customer_name = '';
            $company_name = '';
            $city = '';
            $contact_no = '';
            $address = '';
            $general_terms = '';
            $bulk_terms = '';
            $tnc = '';
            $email_id = '';
            $hpcl_company = 0;
            $check_terms=0;
            $validity_date = '';
        }
} else {
    redirect(page_url);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Quotation</title>
    <!-- Table Responsive css -->
    <script src="<?php echo assets_url; ?>js/angular.min.js"></script>
    <!-- DataTables -->
    <link href="<?php echo assets_url; ?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>

    <link href="<?php echo assets_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/core.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/components.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/pages.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/menu.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo assets_url; ?>css/responsive.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo assets_url; ?>js/modernizr.min.js"></script>

</head>

<body>
    <br><br>
    <table style="width:100%; padding:5px;">
        <tr>
            <td width="20%"></td>
            <td width="60%">
                <div class="card-box">
                <table style="width:100%;">
                    <tr>
                        <td width="33.33333333%" style="padding:5px;">
                            <div class="pull-right">
                                <a href="<?php echo page_url; ?>Leads/view_detail/<?php echo $this->uri->segment(3); ?>" target="_blank"><button class="btn btn-info">Edit Lead Quote</button></a>
                            </div>
                        </td>
                        <td width="33.33333333%" style="padding:5px;">
                            <div class="text-center">
                                <a href="javascript:;" data-toggle="modal" data-target="#myModal"><button class="btn btn-info btn-xs">View Mail History</button></a>
                            </div>
                        </td>
                        <td width="33.33333333%" style="padding:5px;">
                            <?php if ($checkIfProductIsApproved == 0) { ?>
                                <a href="<?php echo page_url; ?>Leads/send_mail_to_lead/<?php echo $uri; ?>">
                                    <button class="btn btn-success "> Confirm and send mail</button>
                                </a>
                            <?php } else { ?>
                                <span style="color: red; font-weight: bold;font-size: 16px; text-align:center;">One or more products are not approved. Hence, mail cannot be sent</span>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
                </div>
                
            </td>
            <td width="20%"></td>
        </tr>
    </table>
    <br>
    <table style="width: 100%; font-size:14px; ">
        <tr>
            <td width="20%"></td>
            <td width="60%" style=" padding:5px; box-shadow: 1px 1px 10px lightgray; background-color:white;">
                <table style="width: 100%; font-size:14px;">
                    <tr>
                        <td width="10%"></td>
                        <td width="80%">



                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="50%">To</td>
                                    <td width="50%" style="text-align:right;">Date: <?php echo date('d-m-Y'); ?></td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="40%"><?php echo $customer_name; ?><br>
                                        M/s <?php echo $company_name; ?>
                                        <br>
                                        <?php echo $address; ?>
                                    </td>
                                    <td width="30%"></td>
                                    <td width="30%"></td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="10%"></td>
                                    <td width="80%" style="text-align:center;">Sub: Quotation of Industrial Lubricants.
                                    </td>
                                    <td width="10%"></td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        Sir,<br>
                                        In reference to our meeting discussion held regarding Industrial Oil supply to
                                        your respective business units therefore, we hereby offer you Quotation for the
                                        products as required by yourself.
                                    </td>
                                </tr>
                            </table>
                            <br>
                            <table style="width: 100%; font-size:14px;" border="1">
                                <tr>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Product Name with HSN Code</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Pack Size</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Offered Price</th>
                                </tr>
                                <?php
                                $sql2 = $this->db->select('a.qty, a.price, b.instruments_name')
                                                 ->from('lead_products a')
                                                 ->join('presto_instruments b', 'b.id=a.product_id')
                                                 ->where('lead_id', $uri)
                                                 ->get();
                                if ($sql2->num_rows() > 0) {
                                    foreach ($sql2->result() as $row2) { ?>
                                    <tr>
                                        <td style="padding: 5px;"><?php echo $row2->instruments_name; ?></td>
                                        <td style="padding: 5px;"><?php echo $row2->qty; ?></td>
                                        <td style="padding: 5px;"><?php echo $row2->price; ?></td>
                                    </tr>
                                <?php } } ?>
                            </table>
                            <!----------------------------DRUMS--------------------------------->
                            <!-- <table style="width: 100%; font-size:14px; padding: 5px;">
                                <?php echo $general_terms; ?>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <i>Please feel free to contact us incase of any further queries. We shall be
                                            more than happy to assist/resolve all your queries.</i>
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td style="color: red;">
                                        Please Note: - We are the only authorized C&F Agents for Industrial lubricants
                                        for <b>M/s HINDUSTAN PETROLEUM CORPORATION LIMITED</b> in Faridabad district and
                                        that
                                        We/HPCL does not take any responsibility for any unauthorized product supplied
                                        by unauthorized/illegitimate supplier.
                                    </td>
                                </tr>
                            </table>
                                <?php
                            $sql3 = $this->db->select('*')->from('store_rack_location')->where('id', $hpcl_company)->get();
                            if ($sql3->num_rows() > 0) {
                                foreach ($sql3->result() as $company);
                            ?>
                                <table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td>
                                            <img src="<?php echo sfdocument; ?>hp.jpg"><b>with regards</b><br>
                                            <?php echo $company->contact_person; ?><br>
                                            M/S CFA//<?php echo $company->companyname; ?><br>
                                            AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>
                                            <?php echo $company->address; ?><br>
                                            Emails : <?php echo $company->email_id; ?><br>
                                            Office Landline No. <?php echo $company->landline_number; ?><br>
                                            MOBILE <?php echo $company->mobile; ?>,#<?php echo $company->alt_mobile; ?><br> Please view us on GoogleMap:-<?php echo $company->googlemap; ?><br>
                                            Please locate us on HPCL Website:<?php echo $company->locate_us; ?>

                                        </td>
                                    </tr>
                                </table>
                            <?php } ?> -->
                            <!----------------------------BULK--------------------------------->

                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <?php echo $tnc; ?>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <i>Please feel free to contact us incase of any further queries. We shall be
                                            more than happy to assist/resolve all your queries. </i>
                                    </td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td style="color: red;">
                                        We are the only authorized C&F Agents for industrial lubricants for HPCL in
                                        Faridabad district and that HPCL does not take any responsibility for any
                                        unauthorized product supplied by unauthorized/illegitimate supplier.
                                    </td>
                                </tr>
                            </table>
                            <?php
                        $sql4 = $this->db->select('*')->from('store_rack_location')->where('id', $hpcl_company)->get();
                            if ($sql4->num_rows() > 0) {
                                foreach ($sql4->result() as $company);
                            ?>
                                <table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td>
                                            <img src="<?php echo sfdocument; ?>hp.jpg"><b>with regards</b><br>
                                            <?php echo $company->contact_person; ?><br>
                                            M/S CFA//<?php echo $company->companyname; ?><br>
                                            AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>
                                            <?php echo $company->address; ?><br>
                                            Emails : <?php echo $company->email_id; ?><br>
                                            Office Landline No. <?php echo $company->landline_number; ?><br>
                                            MOBILE <?php echo $company->mobile; ?>,#<?php echo $company->alt_mobile; ?><br> Please view us on GoogleMap:-<?php echo $company->googlemap; ?><br>
                                            Please locate us on HPCL Website:<?php echo $company->locate_us; ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php } ?>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>

            </td>
            <td width="20%"></td>
        </tr>
    </table>

    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Mail History of <?php echo $company_name; ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table style="width: 100%; font-size:14px;" border="1">
                                <tr>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent On</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Mail Sent By</th>
                                </tr>
                                <?php
                                $sql1 = $this->db->select('a.mail_sent_on, b.first_name, b.last_name')
                                                 ->from('lead_quotation_mail_history a')
                                                 ->join('system_users b', 'b.user_id=a.mail_sent_by')
                                                 ->where('a.lead_id', $uri)
                                                 ->get();

                                if ($sql1->num_rows() > 0) {
                                    foreach ($sql1->result() as $row1) { ?>
                                        <tr>
                                            <td style="padding: 5px;"><?php echo date('d-m-Y H:i:s', strtotime($row1->mail_sent_on)); ?></td>
                                            <td style="padding: 5px;"><?php echo $row1->first_name . " " . $row1->last_name; ?></td>
                                        </tr>
                                <?php }
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        </form>
    </div><!-- /.modal -->

    <!-- jQuery  -->
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


</body>

</html>
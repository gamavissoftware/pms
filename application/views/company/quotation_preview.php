<?php 
$uri=$this->uri->segment(3);
if($uri<>'')
{
$res=$this->db->select('*')->from('customer_quotation1')->where('id',$uri)->get();
if($res->num_rows() >0)
{
    foreach($res->result() as $com);
    
        $companyid=$com->$company_id;
        $customerid=$com->$customer_id;

    $cu=$this->db->select('customer_name,city,state,contact_no,address,company_name')->from('customer_detail')->where('id',$customerid)->get();
    if($cu->num_rows() >0)
    {
        foreach($cu->result() as $cdetail);
        $company_name=$cdetail->company_name;
        $city=$cdetail->city;
        $contact_no=$cdetail->contact_no;
        $address=$cdetail->address;
    }
}
else
{
  $companyid='';
  $customerid=''; 
   $company_name='';
        $city='';
        $contact_no='';
        $address=''; 
}
}else{
    redirect(page_url);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature</title>
</head>

<body>
    <table style="width: 100%; font-size:14px; font-family: monospace;">
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
                                    <td width="50%" style="text-align:right;">Date: 01-06-2022</td>
                                </tr>
                            </table>
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td width="40%">The Purchase Manger <br>
                                        M/s Lakhani Armaan group
                                        Sec-24 Faridabad (HR)
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
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Product Name with
                                        HSN Code</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Pack size</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">List price per
                                        ltr</th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Discount Per Ltr
                                    </th>
                                    <th style="padding: 5px; background-color: lightgray;" width="20%">Net price per ltr
                                        (GST Extra) </th>
                                </tr>
                                <tr>
                                    <td style="padding: 5px;">HP TRANSFORMER OIL (27101980)</td>
                                    <td style="padding: 5px;">210 steel EP coated barrel</td>
                                    <td style="padding: 5px;">124</td>
                                    <td style="padding: 5px;">30</td>
                                    <td style="padding: 5px;">94.00</td>
                                </tr>
                            </table>
                            <!----------------------------DRUMS--------------------------------->
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        Terms and Conditions
                                        <ol>
                                            <li><i>Payment- 60 days (customer can avail 3% Cash discount on basic prices
                                                    only) </i></li>
                                            <li><i> Product will be supplied multiply 210/ Ltr 182/kg minimum supply
                                                    1050 Ltr (5 drums )with Tamper Proof seal HPC QC Dept. </i></li>
                                            <li><i>Product specs& test report will be provided with every load </i></li>
                                            <li><i>Discount firm on list price valid up to 31/05/2022, price will be
                                                    updated inline with ICIS International base Oil market. (We will
                                                    have full entitlement to revise prices without any prior notice time
                                                    subject to high fluctuation in base oil prices. </i></li>
                                            <li><i>Billing shall be carried out in the name of M/s CFA// Sunder
                                                    Industrial oil Authorized C& F Agent (Since 2010) for M/s Hindustan
                                                    Petroleum Corporation Limited, only in Faridabad district </i></li>
                                            <li><i>9+9=18% CGST/SGST will be levied as govt. regulation.</i></li>
                                        </ol>
                                    </td>
                                </tr>
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
                            <table style="width: 100%; font-size:14px; padding: 5px;">
                                <tr>
                                    <td>
                                        <img src="hp.jpg"><b>with regards</b><br>
                                        BALWINDER SINGH<br>
                                        M/S CFA//SUNDER INDUSTRIAL OIL<br>
                                        AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED<br>
                                        PLOT NO.B-21 FIT (Faridabad Industrial Town ) Sector 57 <br>
                                        Ballabgarh FARIDABAD (HR) 121004 #9891943003 <br>
                                        Emails : faridabad@hpclcfa.com<br>
                                        Office Landline No. 0129-2971007,1008<br>
                                        MOBILE 9891941007<br> Please view us on GoogleMap:-
                                        https://maps.app.goo.gl/TLeaLcj7L9kTWPKGA<br>
                                        Please locate us on HPCL Website:
                                        https://www.hplubricants.in/distributor/locate-cfa-distributor/faridabad/cfa-(industrial)#122-1781-2060-2061


                                    </td>
                                </tr>
                            </table>
                            <!----------------------------BULK--------------------------------->
                            <----------------------------BULK--------------------------------->
                                <table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td>
                                            Terms of business
                                            <ol>
                                                <li><i>Mentioned price are FOR (landed price)</i></li>
                                                <li><i>Payment- 60 days (customer can avail 2% Cash discount on basic
                                                        prices
                                                        only) </i></li>
                                                <li><i>Product will be supplied Auth./dedicated Tanker in Min vol.
                                                        22.5/23/24 kl with Tamper Proof seal HPC QC Dept. </i></li>
                                                <li><i>One time supply minimum one Tanker</i></li>
                                                <li><i>Product specs& test report will be provided if required </i></li>
                                                <li><i>Discount/price firmed valid up to 28/02/2021.</i></li>
                                                <li><i>Billing shall be carried out in the name of <b>M/s Hindustan
                                                            Petroleum
                                                            Corporation limited,</b> For drums supply: billing shall be
                                                        carried out
                                                        in the name of <b>CFA//Sunder Industrial Oil,</b> only in
                                                        Faridabad district
                                                    </i></li>
                                                <li><i> 9+9=18% CGST/SGST will be levied as govt. regulation.</i></li>
                                            </ol>
                                        </td>
                                    </tr>
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
                                <table style="width: 100%; font-size:14px; padding: 5px;">
                                    <tr>
                                        <td>
                                            <img src="hp.jpg"><b>with regards</b><br>
                                            BALWINDER SINGH<br>
                                            M/S CFA//SUNDER INDUSTRIAL OIL<br>
                                            AUTH. C& F AGENT FOR M/s HINDUSTAN PETROLEUM CORPORATION LIMITED <br>
                                            PLOT NO.B-21 FIT (Faridabad Industrial Town ) Sector 57 <br>
                                            Ballabgarh FARIDABAD (HR) 121004 #9891943003,<br>
                                            MOBILE  9891941007<br> Please view us on GoogleMap:- https://maps.app.goo.gl/TLeaLcj7L9kTWPKGA<br>
                                            Please locate us on HPCL Website: https://www.hplubricants.in/distributor/locate-cfa-distributor/faridabad/cfa-(industrial)#122-1781-2060-2061
                                            


                                        </td>
                                    </tr>
                                </table>
                        </td>
                        <td width="10%"></td>
                    </tr>
                </table>
            </td>
            <td width="20%"></td>
        </tr>
    </table>
</body>

</html>
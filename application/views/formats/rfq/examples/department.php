<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);





// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->SetMargins(5, 5, 5, true);

// $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 13, '', true);


//$pdf->SetFont('msungstdlight', '', 12);


// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage('P');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set some content to print
$html = '
<style>
    table{
        width:100%;
        }

        .table {
            width: 100%;
            font-size:11px;
            padding:5px;
        }


        .table th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
        }

        .table td {
            border: 1px solid #000;
            background-color: #fff;
            text-align: center;
        }

        .table1 {
            font-size:13px;
            width: 100%;
              padding: 5px 10px;
        }

        .table1 th {
            border: 1px solid #000;
            background-color: #e5e5e5;
            text-align: center;
          
        }

        .table1 td {
            border: 1px solid lightgrey;
            background-color: #fff;
            text-align: left;
        }
</style>


<table style="padding:5px;">
    <tr>
        <td style="text-align:center; font-size:20px; background-color:#d0edf7;">Department Wise Overdue Task</td>
    </tr>
</table>
<br>
<br>
<table style="padding:5px;" class="table1">
<tr>
<td width="80%" style="background-color:#049dd4; color:#fff;">Department Name</td>
<td width="20%" style="background-color:#049dd4; color:#fff;">Overall Task</td>
</tr>
</table>
<table style="padding:5px;" class="table1">
<tr>
<td style="background-color:#dff2f0;" width="80%">Marketing</td>
<td style="background-color:#dff2f0;" width="20%">2</td>
</tr>
<tr>
<td>Sandeep</td>
<td >2</td>
</tr>
</table>
';

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+

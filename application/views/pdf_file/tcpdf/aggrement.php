<?php
define('FILEPATH',$_SERVER['DOCUMENT_ROOT'].'/image_bank/supplierdocs/agreement/');
define('FILEPATH1',$_SERVER['DOCUMENT_ROOT'].'/image_bank/supplierdocs/terms/');
/** QUERY ENDS **/
include('mysqlconfig.php'); 

$qid=$_GET['uid'];

$today=date('d-M-Y');
$days= date('l');
$sel="SELECT supplier_id,company_name,company_registration_id,company_registered_address, supplier_type FROM `suppliers` WHERE `supplier_id` = '$qid'";
//echo $sel; exit;
$coures=mysqli_query($con,$sel);
 $row_cnt = mysqli_num_rows($coures);
 if($row_cnt==0)
 {
    echo "INVALID ACCESS"; exit;
 }
 else
 {
    $row=mysqli_fetch_array($coures);    
    $supplier_id=$row['supplier_id'];
    //echo $stid; exit;
    $supplier_type=$row['supplier_type'];
    $company_name=$row['company_name'];
    $company_registration_id=$row['company_registration_id'];
    $company_registered_address=str_replace('"', '', $row['company_registered_address']);
    
}

if($supplier_type == 1) {
  $term_id = 2;
} else {
  $term_id = 11;
}


$sel22="SELECT id,category,type,description FROM `termsandconditions` WHERE `id`=".$term_id;
// echo $sel; exit;
$coures22=mysqli_query($con,$sel22);
 $row_cnt22 = mysqli_num_rows($coures22);
 $row22=mysqli_fetch_array($coures22);    
 $description=$row22['description'];


/** QUERY ENDS **/


require_once('tcpdf/tcpdf_include.php');
class MYPDF extends TCPDF {

}
// create new PDF document
class MyCustomPDFWithWatermark extends TCPDF {
   public function Header() {
       // Get the current page break margin
       $bMargin = $this->getBreakMargin();

       // Get current auto-page-break mode
       $auto_page_break = $this->AutoPageBreak;

       // Disable auto-page-break
       $this->SetAutoPageBreak(false, 0);

       // Define the path to the image that you want to use as watermark.
       $img_file = '../tcpdf/tcpdf/images/water.jpg';

       // Render the image
       $this->Image($img_file, 0, 0, 220, 280, '', '', '', false, 300, '', false, false, 0);

       // Restore the auto-page-break status
       $this->SetAutoPageBreak($auto_page_break, $bMargin);

       // Set the starting point for the page content
       $this->setPageMark();
   }
}
// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);

// remove default header/footer
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>false, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

/** quert paet **/
 
$html='
<table style="padding:3px; width:100%; font-size:10px;">
<tr>
<td style="text-align:center; background-color:lightgrey; font-weight:bold; ">NON-DISCLOSURE AGREEMENT</td>
</tr>
</table>
<p style=" font-size:10px; text-align:center;">MUTUAL OBLIGATIONS</p>
<p style=" font-size:10px; text-align:center;">Between</p>

<table style="padding:3px; width:100%; font-size:10px;">
<tr>
<td >
<table style="padding:3px; width:100%; font-size:10px;">
<tr>
<td style="text-align:center;  font-weight:bold; ">
HONGYI JIG RAPID TECHOLOGIES CO. LIMITED</td>
</tr>
<tr>
<td style="text-align:center;">Company Registration Number 58638366-000-07-11-9</td></tr>
<tr>
<td style="text-align:center;">
Unit H1/F Mao Lam Commercial Building 16-18 Mau Lam Street Jordan KL</td></tr>
</table>
</td>



<td >
<table style="padding:3px; width:100%; font-size:10px;">
<tr>
<td style="text-align:center;  font-weight:bold; ">
'.$company_name.'</td>
</tr>
<tr>
<td style="text-align:center;">
'.$company_registration_id.'</td></tr>
<tr>
<td style="text-align:center;">
'.$company_registered_address.'</td></tr>
</table>
</td>

</tr>
</table>

<p style=" font-size:10px; text-align:left;">HONGYI JIG RAPID TECHOLOGIES CO. LIMITED and '.$company_name.'. wish to explore a business opportunity of mutual interest and in connection with this opportunity may disclose certain confidential technical and business information
</p>

<table style="padding:3px; width:100%; font-size:10px;">
<tr>
<td style="text-align:center; background-color:lightgrey; ">
NON-DISCLOSURE AGREEMENT FOR PRODUCT DEVELOPMENT</td>
</tr>
</table>
<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td>This Product Development Non-Disclosure Agreement, known as the “Agreement”, 
made this '.$days.', '.$today.' is by and between HONGYI JIG RAPID TECHOLOGIES CO. 
LIMITED the “Releasor”, and '.$company_name.'., the “Recipient”, 
and collectively “the Parties”.</td>
</tr>



<tr>
<td>WHEREAS, Releasor agrees to furnish certain confidential information relating 
to ideas, inventions, or products for the purposes of assistance in product development,
 patenting, licensing, and any other details about a product or service that is unique in nature to the Releasor.</td>
</tr>
</table>
<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">

'.$description.'

<!--<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
OBJECTS OF AGREEMENT (NDA)
</td>

</tr>
</table>
<p style=" font-size:10px; text-align:left;">This Agreement acknowledges that certain confidential information, trade secrets, and proprietary data (hereinafter defined and referred to as “Confidential Information”) of the Releasor. The provisions set forth in this Agreement define the circumstances in which the Recipient can and cannot disclose Confidential Information to the third party.
Both Parties agree that it is in their best interests to protect the Releasor’s Confidential Information, and that the
terms of this Agreement create a bond of trust and confidentiality between them.
In consideration of the subjected agreement, the Parties agree on the ground as follows:

</p>


<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">

CONFIDENTIAL INFORMATION</td>

</tr>
</table>
<p style=" font-size:10px; text-align:left;">Confidential Information is any material, knowledge, information and data (verbal, electronic, written or any other form) concerning the Company or its businesses not generally known to the public consisting of, but not limited to, inventions, discoveries, plans, concepts, designs, blueprints, drawings, models, devices, equipment, apparatus, products, prototypes, formulae, algorithms, techniques, research projects, computer programs, software, firmware, hardware, business, development and marketing plans, merchandising systems, financial and pricing data, information concerning investors, customers, suppliers, consultants and employees, and any other concepts, ideas or information involving or related to the business which, if misused or disclosed, could adversely affect the Releasor’s business.
</p>


<br>
<br>
<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">

MAINTENANCE OF CONFIDENTIALITY</td>

</tr>
</table>
<p style=" font-size:10px; text-align:left;">Recipient agrees that it shall take all reasonable measures to protect the secrecy of and avoid disclosure and unauthorized use of the confidential information. Without limiting the foregoing, recipient shall take at least those measures that recipient takes to protect its own most highly confidential information and shall have its employees who have access to confidential information sign a non-use and non-disclosure agreement, in content substantially similar to the provisions hereof, prior to any disclosure of confidential information to such employees. Recipient shall not make any copies of confidential information unless the same are previously approved in writing by the disclosing party. Recipient shall immediately notify the disclosing party in the event of any unauthorized use or disclosure of the confidential information.

</p>

<p style=" font-size:10px; text-align:left;">A. This Agreement shall govern the conditions of disclosure by Releasor to Recipient of certain Confidential Information.
"Confidential Information", as used herein, means all engineering and business information (including prototypes, drawings,
data, trade secrets and intellectual property) which includes: 
</p>
<ol style="font-size:10px;">
<li>This Agreement shall govern the conditions of disclosure by Releasor to Recipient of certain Confidential Information. "Confidential Information", as used herein, means all engineering and business information (including prototypes, drawings, data, trade secrets and intellectual property) which includes:
<ul style="list-style-type: circle;">
<li>If tangible, is identified in writing as confidential at the time of its disclosure to the recipient; or</li>
<li>If intangible, is identified at the time of disclosure to the recipient as confidential and is later promptly confirmed in</li>
<li>writing within one (1) month from the date of disclosure as being confidential.</li>
</ul>
</li>
<li>The term Confidential Information shall exclude information which:
<ul style="list-style-type: circle;">
<li>is known or possessed by the Recipient at the time of its disclosure to the recipient;</li>
<li>is publicly known at the time of disclosure to the recipient;</li>
<li>is subsequently received by the Recipient from a third party without restriction on disclosure;</li>
<li>subsequently becomes publicly known without violation of this Agreement;</li>
<li>is independently developed by the recipient without access to the Confidential Information; or</li>
<li>is disclosed by recipient pursuant to a requirement of a law, regulation, or legal process with regard to the</li>
<li>Confidential Information, Recipient hereby agrees:
<ul style="list-style-type: square;">
<li>to hold confidential or proprietary information or trade secrets ("confidential information") in trust and confidence and agrees that it shall be used only for the purpose of business product or idea development for Releasor and shall not be used for any other purpose, or disclosed to any third party;</li>
<li>safeguard and exercise reasonable precautions against disclosure of the Confidential Information to others;</li>
<li>to not disclose Confidential Information to any employee, consultant, or third party unless they agree to execute and be bound by the terms of this Agreement; and</li>
<li>6(d). that the secrecy obligations of Recipient with respect to the information shall continue for a period ending _________________ from the date hereof.</li>
</ul>
</li>
</ul>
</li>
</ol>




<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
PERIOD OF CONFIDENTIALITY</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">Recipient agrees not to use or disclose Confidential Information for their own personal benefit or the benefit of any other person, corporation or entity other than the Company for a period of _______________.
</p>

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
LIMITATIONS</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">Recipient shall limit access to Confidential Information to individuals on a strictly need-to-know basis, involving only those who are carrying out duties related to the Company and its business. Individuals under the Recipient’s command (affiliates, agents, consultants, representatives and other employees) are bound by and shall comply with the terms of this Agreement.
</p>



<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
OWNERSHIP</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">All repositories of information containing or in any way relating to Confidential Information is considered property of the Releasor. The removal of Confidential Information from the Releasor’s consent is prohibited unless prior written consent is provided. All such items made, compiled or used by the Recipient shall be delivered to the Releasor upon termination of the validity of this agreement or the achieved the subject of the Non Disclosure.
</p>


<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
SUBJECT OF NON DISCLOSURE
</td>
</tr>
</table><p style=" font-size:10px; text-align:left;">
<ul style="list-style-type: disc;">
<li>Releasor has conceptualized the invention for the purpose of the commercialization of the product / invention by the research & development team.</li>
<li>The Releasor has transmitted the invention technique to the recipient for the manufacturing of the product in the name and owned to Releasor and no other third party.</li>
</ul></p>


<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
RETURN OF CONFIDENTIAL MATERIALS
</td>
</tr>
</table><p style=" font-size:10px; text-align:left;">Within seven (7) days of a written request by the Releasor, the Recipient shall return/destroy (as may be requested in writing by the Releasor or upon expiry and or earlier termination) all originals, copies, reproductions and summaries of Confidential Information provided to the Recipientas Confidential Information. The Recipient shall certify to the Releasorin writing that it has satisfied its obligations under this paragraph.
<ul style="list-style-type: disc;">
<li>In the event either Party receives a summons or other validly issued administrative or judicial process requiring the disclosure of Confidential Information of the other Party, the Recipientshall promptly notify the Releasor. The Recipientmay disclose Confidential Information to the extent such disclosure is required by law, rule, regulation or legal process; provided however, that, to the extent practicable, the Recipientshall give prompt written notice of any such request for such information to the Releasor, and agrees to co-operate with the Releasor, at the Releasor’s expense, to the extent permissible and practicable, to challenge the request or limit the scope there of, as the Releasormay reasonably deem appropriate.
</li>
<li>11Neither Party shall use the other’s name, trademarks, proprietary words or symbols or disclose under this Agreement in any publication, press release, marketing material, or otherwise without the prior written approval of the other.
</li>
<li>Each Party agrees that the conditions in this Agreement and the Confidential Information disclosed pursuant to this Agreement are of a special, unique, and extraordinary character and that an impending or existing violation of any provision of this Agreement would cause the other Party irreparable injury for which it would have no adequate remedy at law and further agrees that the other Party shall be entitled to obtain immediately injunctive relief prohibiting such violation, in addition to any other rights and remedies available to it at law or in equity.
</li>
<li>The Recipientshall indemnify the Releasorfor all costs, expenses or damages that Releasor incurs as a result of any violation of any provisionsof this Agreement. This obligation shall include court, litigation expenses, and actual, reasonable attorney’s fees. The Parties acknowledge that as damages may not be a sufficient remedy for any breach under this Agreement, the non-breaching party is entitled to seek specific performance or injunctive relief (as appropriate) as a remedy for any breach or threatened breach, in addition to any other remedies at law or in equity.
</li>
<li>Both the Parties agree that this Agreement will be effective from the date of execution of this Agreement by both Parties and shall continue to be effective till the Proposed Transaction is terminated by either Party by giving a thirty (30)days notice, in case either Party foresees that the Proposed Transaction would not be achieved.
</li>
</ul></p>


<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
NON-USE AND NON-DISCLOSURE
</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">Recipient agrees not to use any confidential information for any purpose except to evaluate and engage in discussions concerning a potential business relationship between the parties. Recipient agrees not to contact or otherwise communicate with any third party directly related to the business opportunity that is the subject of the confidential information. Recipient agrees not to disclose any confidential information to third parties or to employees of recipient except to those employees who are required to have the information in order to evaluate or engage in discussions concerning contemplated business relationships.
</p>


<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
ENTIRE AGREEMENT
</td>
</tr>
</table><p style=" font-size:10px; text-align:left;">
<ul style="list-style-type: disc;">
<li>Previous Agreements. This Agreement constitutes the entire agreement and the signing thereof by both Parties nullifies any and all previous agreements made between Employer and Employee.
</li>
<li>Modifications and Amendments. No modifications, amendments, changes or alterations can be made to the Agreement unless in writing and signed by authorized representatives of both Parties.  
</li>
<li>Successors and Assigns This Agreement shall be binding upon the successors, subsidiaries, assigns and corporations controlling or controlled by the Parties. The Company may assign this Agreement to any party at any time, whereas Employee is prohibited from assigning any of their rights or obligations in the Agreement without prior written consent from Company.
</li>
</ul></p>

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
NATURE OF RELATIONSHIP
</td>
</tr>
</table><p style=" font-size:10px; text-align:left;">
<ul style="list-style-type: disc;">
<li>Non-contract. The Agreement does not constitute a contract of employment, nor does it guarantee continuing employment for the Employee.
</li>
<li>Non-partner. The Agreement does not create a partnership or joint venture between Company and Employees
</li>
<li>Any financial arrangements made between both Parties shall not be included in this Agreement but must be disclosed in a separate document.
</li>
</ul></p>

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
SEVERABILITY
</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">Any provision within the Agreement (or any portion thereof) deemed invalid, unlawful or otherwise unusable by a court of law shall be dissolved from the Agreement and the remainder of the Agreement shall continue to be enforceable. A severed provision shall not alter the integrity of the Agreement, and the terms set forth in any severed provision shall be construed in such a way as to interpret the purpose for which it was drafted.
</p>

<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
DISPUTE RESOLUTION & GOVERNING LAW
</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">This Agreement shall be governed by the laws of India. Both parties irrevocably submit to the exclusive jurisdiction of the Courts in Delhi, for any action or proceeding regarding this Agreement. Any dispute or claim arising out of or in connection herewith, or the breach, termination or invalidity thereof, shall be settled by arbitration in accordance with the provisions of Procedure of the Indian Arbitration & Conciliation Act, 1996, including any amendments thereof. The arbitration tribunal shall be composed of a sole arbitrator, and such arbitrator shall be appointed mutually by the Parties. The place of arbitration shall be Delhi, India and the arbitration proceedings shall take place in the English language.
</p>



<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
IMMUNITY
</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">Disclosing Confidential Information to an attorney, government representative or court official in confidence while assisting or taking part in a case involving a suspected violation of law is not considered a breach of this Agreement. Should the Employee be required to disclose Confidential Information by law, the Employee shall provide Employer with prompt notice of such request.
</p>


<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
BREACH OF AGREEMENT
</td>
</tr>
</table><p style=" font-size:10px; text-align:left;">
<ul style="list-style-type: disc;">
<li>Cause for Action. Employee understands that the use or disclosure of any Confidential Information may because for an action at law in an appropriate court of the State of Delhi or any State of the INDIA and that the Employer shall be entitled to an injunction prohibiting the use or disclosure of the Confidential Information.</li>
<li>Indemnification. Employee understands and agrees that if the use or disclosure of Confidential Information by them or any affiliate, employee or representative of the Employee causes damage, loss, cost or expense at actual or compensatory amount Rs. 10, 00, 000/- (Rupees Ten Lakh) whichever is higher to the Company, the Employee shall be held responsible and shall indemnify the Company.</li>
<li>Injunctive Relief. The Employee understands and agrees that the use or disclosure of Confidential Information could cause the Company irreparable harm and the Company has the right to pursue legal action beyond remedies of a monetary nature in the form of injunctive or equitable relief. This may be in addition to any other remedy, penalty or claim the law can provide.
</li>
<li>Notice of Unauthorized Use or Disclosure. Employee is bound by this Agreement to notify the Company in the event of a breach of agreement involving the dissemination of Confidential Information, either by the Employee or a third party, and will do everything possible to help the Company regain possession of the Confidential Information.
</li>
</ul></p>



<table style="width:100%; padding:3px; font-size:10px;">
<tr>
<td style="text-align:left; font-weight:bold; background-color:lightgrey; ">
PREVAILING PARTY
</td>
</tr>
</table>
<p style=" font-size:10px; text-align:left;">In a dispute arising out of or in relation to this Agreement, the prevailing party shall have the right to collect from the other party its reasonable attorney fees, costs and necessary expenditures besides the damages defined in Clause 21 (B).
</p> -->

<p style=" font-size:10px; text-align:left;">
IN WITNESS WHEREOF, the Parties hereto agree to the terms of this Agreement and signed on the dates written below.

</p>

<table style="width:100%; padding:3px; font-size:10px;" border="0">
   <tr>
      <td width="250">
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td width="50">Name</td>
               <td style="border-bottom:1px solid black;" width="170"></td>
            </tr>
         </table>
      </td>
      <td>
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td  width="130">Supplier company Name</td>
               <td style="border-bottom:1px solid black;"  width="240">  '.$company_name.'  </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
<br><br><br><br>
<table style="width:100%; padding:3px; font-size:10px;" border="0">
   <tr>
      <td width="250">
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td width="80">Designation</td>
               <td style="border-bottom:1px solid black;" width="140"></td>
            </tr>
         </table>
      </td>
      <td>
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td  width="110">Supplier Address</td>
               <td style="border-bottom:1px solid black;"  width="260">  '.$company_registered_address.'  </td>
            </tr>
         </table>
      </td>
   </tr>
</table>
<br><br><br><br>
<table style="width:100%; padding:3px; font-size:10px;" border="0">
   <tr>
      <td width="250">
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td width="80">Signature (Stamped)</td>
               <td  width="140">__________________________</td>
            </tr>
         </table>
      </td>
      <td>
         <table style="width:100%; padding:3px; font-size:10px;" >
            <tr>
               <td  width="60">Date</td>
               <td   width="150">____________________________</td>
            </tr>
         </table>
      </td>
   </tr>
</table>


';	


//echo $footer_logo_html; exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.



//$filelocation = $_SERVER['DOCUMENT_ROOT'].'image_bank/popdf/';
$fileNL1 = FILEPATH."agreement-".$supplier_id.".pdf"; //Linux
$pdf->Output($fileNL1,'F');


//============================================================+
// END OF FILE
//============================================================+



header("location:https://hongyijig.in/pdf_file/tcpdf/terms-conditions.php?uid=".$supplier_id);
//============================================================+
// END OF FILE
//============================================================+



<?php 
class Opportunity extends CI_Controller{
      function __construct() { 
 parent::__construct();

	$this->load->model('Daily_report_model');
	$this->load->model('Salescrm_model');
	$this->load->model('Task_model','task');
 } 

function quotation()
{

$html='<table width="100%">
<tr>
<td>
<img src="https://gamavis.com/PMS/assets/images/shubhampack.png" width="250px">
</td>
</tr>
</table>
<table width="100%" ruled="all" style=" padding:3px;">
<tr>
<td style="text-align:left">Ref. no. SPM/EXP/P/SR/0035/24-25 </td>
<td style="text-align:right;">Date: - 18th April 2024</td>
</tr>
</table>
<table style="padding-top:50px">
<tr>
<td>
<h2 style="text-align:center;">Quote for<br>
MULTI TRACK MACHINE MODEL SPM 1280P AU</h2>
</td></tr>
</table>
<table style="padding-top:50px">
<tr>
<td><i style="text-align:center; font-size:18px;">“Global Standards Unmatched Performance”</i></td>
</tr>
</table>

<table style="padding-top:150px">
<tr>
<td style="text-align:center;">Specially prepared for<br>
<h2>CUSTOMER NAME</h2>
</td>

</tr>
</table>

<table style="padding-top:200px">
<tr>
<td>
Submitted by:<br>
Swati Rao<br>
Projects<br>
Email: Projects@shubhampack.com<br>
Cell: +91-8130192022<br>
</td>
</tr>
</table>

<br pagebreak="true">

<table>
<tr>
<td><h2 style="text-align:center;">Index</h2></td></tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-I</td>
<td style="text-align:left; width:50%">Project Data</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-II</td>
<td style="text-align:left; width:50%">Technical Specifications</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-III</td>
<td style="text-align:left; width:50%">Exclusions</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-IV</td>
<td style="text-align:left; width:50%">Price Schedule & Optional Items</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-V</td>
<td style="text-align:left; width:50%">Commercial terms and conditions</td>
</tr>
</table>

<table style="padding-top:50px; padding-bottom:50px;">
<tr>
<td style="text-align:left; width:50%">Annexure-VI</td>
<td style="text-align:left; width:50%">Attachments<br>
- E-Brochure
 (on demand)</td>
</tr>
</table>
<br pagebreak="true">

<table>
<h2 style="text-align:center">Annexure-I<br>Project Data</h2>
</table>

<table border="1" style="text-align:center;">
<tr style="padding:20px;">
	<td style="padding: 10px;">1</td>
	<td style="padding: 10px;">Product to be Packed</td>
	<td style="padding: 10px;">Liquid/Powder /Granules</td>
	<td style="padding: 10px;">Powder</td>
</tr>

<tr style="padding-top:20px;">
	<td>2</td>
	<td>Product name</td>
	<td></td>
	<td>Seasoning Powder</td>
</tr>

<tr>
	<td>3</td>
	<td>Pouch Size & Type</td>
	<td>W x L</td>
	<td>80mm (W) X 77 mm (L)</td>
</tr>

<tr>
	<td>4</td>
	<td>Quantity to be packed</td>
	<td>ml / gm</td>
	<td>8 gm</td>
</tr>

<tr>
	<td>5</td>
	<td>Horizontal Sealing Width</td>
	<td>Mm</td>
	<td>7 mm + 7 mm</td>
</tr>
<tr>
	<td>6</td>
	<td>Vertical Sealing Width</td>
	<td>Mm</td>
	<td>10 mm + 10 mm</td>
</tr>

<tr>
	<td>7</td>
	<td>Perforation Pitch</td>
	<td>Mm</td>
	<td>2.5mm</td>
</tr>

<tr>
	<td>8</td>
	<td>Type of Sealing</td>
	<td>VLining/Butt/Knurling</td>
	<td>V – Lining</td>
</tr>

<tr>
	<td>9</td>
	<td>PLC Make</td>
	<td>Omron / AB</td>
	<td>Omron</td>
</tr>

<tr>
	<td>10</td>
	<td>Power Supply</td>
	<td>VAC/Ph/Hz</td>
	<td>220/380 V, 3 Phases, 50 Hz, Neutral</td>
</tr>
</table>
<br pagebreak="true">

<table>
<tr style="text-align:center;">
<td>Quotation Cum Technical Specification Of The Machine</td>
</tr>
</table>

<table border="1">
<tr>
<th style="width:20%; text-align:center; font-weight:bold;">Sr No.</th>
<th style="width:80%; text-align:center; font-weight:bold;">Description</th>
</tr>

<tr>
<td style="width:20%; text-align:center;">01</td>
<td style="width:80%"><b>Model SPM-1280P AU with 05 Axis</b>
<ul>
<li>Basic machine frame in welded structure.</li>
<li>Detachable Laminate reel mounting unit for two reels mounting with laminate
end detector sensor.</li>
<li>Electronics web aligner for laminate tracking with Dual sensing</li>
<li>Sealing station for longitudinal sealing.</li>
<li>Pre-determined laminate draw with Servo drive system along with eye mark
sensor. </li>
<li>Servo operated Batch cutter cum perforation Blade with servo driven horizontal
and vertical sealing jaws.</li>
<li>Electrical Equipment for standard machine with AC cooled panel</li>
<li>Standard front Polycarbonate Guarding with limit switches.</li>
<li>Pack ML Enabled machine </li>
<li>Smart HMI -15 inches</li>
<li>Elmedur Sealers </li>
<li>Temperature control module for heaters</li>
<li>Automatic Lubrication system</li>
<li>Downtime data and performance tracking</li>
</ul>
</td>
</tr>
<tr>
<td></td>
<td><strong>No. of Axis in Machine: 05 Servos<br>
1 servo for Vertical Sealing<br>
1 servo for Pulling<br>
1 servo for Horizontal Sealing<br>
1 servo for Perforation<br>
1 servo for Up & Down Movement</strong></td></tr>
</table>
<br pagebreak="true">

<table>
<tr>
	<td><h2 style="text-align:center;">Annexure-II<br>
	Technical Specifications</h2></td>
</tr>
<tr style="padding-top:30px">
<td></td>
</tr>
</table>

<table border="1">
<tbody style="padding-left:30px">
<tr>
<td style="width:40%; padding:20px">Machine Model </td>
<td style="width:60%; padding:20px"><b>SPM – 1280P AU </b></td>
</tr>

<tr>
<td>Sealing Style </td>
<td>4 side seal </td>
</tr>

<tr>
<td>Speed</td>
<td>140 to 150 Strokes / min<br>
(Depends upon the nature of product & filling qty.) </td>
</tr>

<tr>
<td>No. of Tracks</td>
<td>8 Tracks </td>
</tr>

<tr>
<td>Laminate specification </td>
<td>Width - 1280 mm Max.<br>
Max. Reel Dia – 800 mm<br>
Reel Core Dia – 152 mm </td>
</tr>

<tr>
<td>Product to be packed </td>
<td>Seasoning Powder</td>
</tr>

<tr>
<td>Filling capacity </td>
<td>8 gm</td>
</tr>

<tr>
<td>Pouch Size </td>
<td>80mm (W) X 77 mm (L)</td>
</tr>

<tr>
<td>Sealing drives</td>
<td>Individual Servo Driven. </td>
</tr>

<tr>
<td>Perforation and cutting </td>
<td>Pneumatic</td>
</tr>

<tr>
<td>Laminate Draw Off system </td>
<td>PLC based Servo driven. </td>
</tr>

<tr>
<td>Laminate tracking system</td>
<td>Web aligner system </td>
</tr>
<tr>
<td>Electrical Spec. </td>
<td>Connected load – 42 kw.<br>
Consumption – 37 kw.<br>
PLC controlled Operations<br>
RTD Module PLC.</td></tr>

<tr>
<td>Layout Dimensions </td>
<td>Length 4300 mm<br>
Width 5100 mm<br>
Height 3400 mm<br> </td>
</tr>

<tr>
<td>Machine Weight </td>
<td>Net Weight  4200 Kgs.<br>
Gross weight  4700 kgs.<br> </td>
</tr>

<tr>
<td style="padding: 8px;">Compressed Air </td>
<td style="padding: 8px;">Operating Pressure – 6 Bar (Compressor not in the scope of<br>
supply)<br>
Consumption – 4 CFM </td>
</tr>
</tbody>
</table>
<br pagebreak="true">

<table>
	<tr>
		<td style="text-align:center"><h2><u>Annexure-III<br>
Exclusion<br>
(To be provided by customer</u></h2></td>
	</tr>
</table>


<table>
<tr>
<td style="padding:40px">1.0 Foundation and any civil building work</td>
</tr>
<tr>
<td style="padding:40px">2.0 Dismantling of existing equipment, if any.</td>
</tr>
<tr style="padding:40px">
<td>3.0 Compressed air piping including compressor</td>
</tr>
<tr style="padding:40px">
<td>4.0 Incomer cable and power supply up to Shubham Pack control panel.</td>
</tr>
<tr style="padding:40px">
<td>5.0 Voltage stabilizer of suitable capacity in case voltage and frequency variation is more
than 10% & 3% respectively at site</td>
</tr>
<tr style="padding:40px">
<td>6.0 Bulk material/Laminate during Factory acceptance test (FAT)</td>
</tr>
<tr style="padding:40px">
<td>7.0 Special tools and tackles like cranes, lifts etc., at the time of installation.</td>
</tr>
<tr style="padding:40px">
<td>8.0 Skilled and unskilled man power at site along with qualified supervisor
to assist installation.</td>
</tr>


</table>
<br pagebreak="true">

<table>
<tr>
<h2 style="text-align:center;">Annexure-IV<br>
Price Schedule</h2>
</tr>
<tr>
<td><h3 style="text-align:center;">Line items below will be added as per the requirement</h3></td>
</tr>
</table>

<table style="width:100%; border-collapse: collapse;">
<thead>
<tr>
<th style="border: 1px solid black; padding: 8px; width:10%">S.no.</th>
<th style="border: 1px solid black; padding: 8px; width:60%">Item description</th>
<th style="border: 1px solid black; padding: 8px; width:10%">Qty</th>
<th style="border: 1px solid black; padding: 8px; width:20%">Price </th>
</tr>
</thead>
<tbody>
	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Model- SPM </td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">A</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Price of design, manufacturing, supply and installation
of machine as per technical specifications at
Annexure-II<br><br>
· Ladder and Platform.<br>
· Guarding with Door switches.</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		
		<td style="border: 1px solid black; padding: 8px; width:10%">B</td>
		
		<td style="border: 1px solid black; padding: 8px; width:60%">Auger Filling System for 08 Tracks-Product Filling
unit consist of:<br>
• Servo Auger Filler system for cleaning Powder with
individual as well overall weight adjustment system<br>
• Pulling - Servo driven from basic machine.<br>
• Designed Filling – 8 gm<br>
<br>
For Exact fill volumes/weights the following is
necessary:<br>
• Uniform density of products.<br>
• Continuous product feed into product Hopper.<br>
Steady Product level by controllable conveyor over
primary feeder. </td>
<td style="border: 1px solid black; padding: 8px; width:10%"></td>
<td style="border: 1px solid black; padding: 8px; width:20%"></td>
		
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">C</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Discharge Conveyor with Rejection system</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">D</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Auto Case Packer with Transfer Conveyor</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr style="background-color:#DFD5D9">
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Total Cost Basic Machine </td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>
	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">E</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Packing Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">F</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Forwarding Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">G</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Insurance Charges</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">H</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Freight until Jakarta Port (Two - 40 Ft )</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">I</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Total CIF Jakarta port</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">K</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Installation, Commissioning, Training at site by 2<br>
Engineers for 20 days <br><br>
(for details refer terms & conditions attached)
• To & Fro air Fare to be booked by customer<br>Hotels, meals and transfer to be taken care by
customer
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Lodging, Boarding and Local Conveyance to be borne
by customer. (All Local Taxes must be beard by
Customer)
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%">(Visa to be arranged by customer)
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:60%"><strong>Total Cost (CIF Jakarta Port) including Installation & commissioning</strong>
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		
		<td colspan="2" style="border: 1px solid black; padding: 8px; width:70%; text-align:center;"><strong>Optional:-</strong>
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%"></td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	<tr>
		<td style="border: 1px solid black; padding: 8px; width:10%">1</td>
		<td style="border: 1px solid black; padding: 8px; width:60%">Thermal Inkjet printer with Printer mount for 8 tracks with the required speed - Make USA Norwix
</td>
		<td style="border: 1px solid black; padding: 8px; width:10%">1</td>
		<td style="border: 1px solid black; padding: 8px; width:20%"></td>
	</tr>

	
	
</tbody>
</table>
<table>
<tr>
<td><h2 style="text-align:center"><u>Annexure-V<br>
Commercial Terms and Conditions</u></h2></td>
</tr>
<tr>
<td>
<h4><u>PRICE BASIS</u></h4>
</td></tr>
<tr>
<td>All prices are on CIF Port, Basis unless otherwise specified.<br><br></td>
</tr>
<tr><h4><u>TERMS OF PAYMENT</u></h4></tr>
<tr>
<td>
<ul>
<li>30% Advance against Order within 30 days.</li>
<li>You may present invoices to the Buyer following the delivery by you to the Buyer of all
products to the required location in respect of 60% of the price against commercial invoice on
Bill of Lading within 90 days.</li>
<li>You may present invoices to the Buyer following issuance of (i) an acceptance certificate (SAT)
of the products, and (ii) for services, completion of the same in accordance with the contract,
for 10% of the price with PBG valid for 13 weeks after delivery. </li>
</ul><br><br>
</td>
</tr>
<tr>
<td>The L/C must be advised and negotiable through Supplier’s Bankers, as per the below details:<br></td>
</tr>
<tr>
<td>Axis Bank Ltd.<br>
SCO-40, Sec-7 Market<br>
Ballabgarh, Faridabad 121004<br>
Haryana, India<br>
A/c holder name- Shubham Flexible Packaging Machines Pvt. Ltd.<br>
A/c no.- 039010200025364<br>
Swift code- AXISINBB039<br></td>
</tr>
<tr>
<td>The Letter of Credit must permit <b>partial shipment</b> and transshipment and should be valid for
negotiation for a period of 21 days beyond the last permissible date of shipment.<br><br></td></tr>
<tr>
<td><h4><strong>The Letter of Credit must accept Combined Transport Bill of Lading issued by the Shipping
Company in New Delhi as a negotiable document.</strong></h4><br><br><br><br></td>
</tr>
<tr>
<td><h4><strong><u>PACKING CHARGES: -</u></strong></h4><br>
In strong seaworthy wooden boxes. 1% of forwarding charges will be applicable in case of terms other
than Ex – Works.<br><br><br><br></td>
</tr>

<tr>
<td>
<h4><strong><u>INSURANCE: -</u></strong></h4><br>
Buyer’s responsibility to take suitable insurance for goods from seller’s warehouse in Ballabhgarh to
the port of discharge covering all risks including erection, installation and commissioning for 110 %
of CIF value. Documentary evidence of this insurance to be given to us at least 30 days before
shipment. Insurance will be applicable in case of terms other than Ex-works.<br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>FREIGHT: -</u></strong></h4><br>
CIF Jakarta Port<br><br></td>
</tr>

<tr>
<td><h4><strong><u>DELIVERY: -</u></strong></h4><br><br>
Standard delivery term is within 24th week for FAT from the date of PO</td>
</tr>

<tr>
<td><h4><strong><u>INSTALLATION / START- UP AND TRAINING</u></strong></h4><br><br/>
To be done by factory trained engineers of Shubham pack or its authorized sub suppliers. The
installation cost is indicated separately in the price schedule at Annexure-IV. <br><br></td>
</tr>
<tr>
<td><strong>Buyer has to provide Hotel, Food, local conveyance and medical expenses for all service
engineers at customer site.</strong><br><br></td>
</tr>

<tr>
<td>Service /installation charges are extra as per number of days required. Buyer has to bear expenses
for extra to and fro air fares, if any, stay in hotel, food, local transport and medical expenses for
deputing engineers and also reimburse out of pocket expenses <strong>@ € 275</strong> per day per including the
travel time and intervening holidays. <br><br></td>
</tr>

<tr>
<i>Shubham Pack will Provide Local Engineer Support in INDONESIA. Shubham Pack is planning for an
office in INDONESIA to cater demand of service and spares.<br><br></i>
</tr>

<tr>
<td>Local Labor, Power and other connected items including lifting, tackle, foundation and masonry work
during installation shall have to be provided by the Buyer.<br><br>
Buyer is advised to unload the machine from the truck/container and place it at designated place. It
is also advisable that inlet of bulk feed, air connection, power supply etc. should be made ready before
arrival of Installation engineer. However, all such connections as well as electrical power ON should
be done in presence of installation engineer only. <br><br></td>
</tr>

<tr>
<td><h4><strong><u>WARRANTY</u></strong></h4><br><br>
We warranty for a period of 24 months from the date of erection of products at Buyer’s site,
all products and parts thereof, when properly installed, adjusted, operated and maintained
as per our proposal and / or the applicable technical manuals. This will however not
include components made of rubber, plastic and electrical equipment and other parts /
components subject to normal wear and tear.<br><br>
Warranty does not cover consumables. These parts are considered as consumables and are required
to be paid for upon replacement.<br><br>
<p>Customer must buy critical and consumable spares for 24 months in order to avail comprehensive
warranty of 24 months.<br></p>
<p>Warranty does not cover damage of part due to poor preventive maintenance OR parts that are subject
to damage due to voltage fluctuation or improper voltage at buyer site.<br><br> </p>
<p>Warranty does not apply to any equipment which has been improperly installed, adjusted, operated,
maintained, repaired or altered by unauthorized persons. We reserve the right to inspect any claimed
defect prior to replacement.<br><br></p>
<p>We will replace/repair, any defective material or workmanship, provided that we are given
written notice of the claimed defects above. Unless caused by us, equipment damaged by
overloading, exposure to corrosive or abrasive substance of abnormal dampness or other
misuse, neglect or accident, shall not be subject to the warranty set forth above.<br><br></p>
<p>The Warranty as referred to above clause will cease to operate if:<br>
a. The buyer, within the Warranty period, sells or otherwise parts with possession of the products.<br><br>
 <p style="text-align:center">AND / OR</p><br>
b. Any local mechanic or electrician tampers with the Products without Shubham’ written
permission</p><br><br>

</td>
</tr>
<tr>
<td>
<h4><strong><u>LIABILITY</u></strong></h4><br><br>
<p>Liability of Shubham Flexible Packaging Machines Pvt. Ltd is limited to Warranty performance of
equipment and does not cover any aspect related to conversion of material. Shubham Flexible
Packaging Machines Pvt. Ltd undertakes no responsibility on performance of laminate/film converted
on the equipment. Customer must conduct trials at his own risk and cost, to evaluate and achieve
satisfactory results before commencing large-scale production. Machine speed and performance are
indicative and may vary with different substrates and raw material and may not be accurate. Liability of Shubham Flexible Packaging Machines Pvt. Ltd is only limited to replacement of defective parts and
does not cover any incidental or consequential loss to customer. <br><br></p><br><br>
</td>
</tr>
<tr>
<td><h4><strong><u>CANCELLATION</u></strong></h4><br><br>
<p>In the event of a request to stop work or to cancel any part of the order should be mutually discussed
between Supplier and Buyer.</p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>FORCE MAJEURE:</u></strong></h4><br><br>
<p>We will not be responsible for any delay in delivery or for non-delivery of our products by reasons of
Force Majeure, such as acts of God, war, riots, civil disturbances, acts of authorities, strikes, lockouts
or other labour difficulties or any other circumstances beyond our control which might affect us or
our suppliers and hinder, impede or prevent deliveries. We will send the notice of force majeure to
customer in writing.</p>
<br><br>

</td>
</tr>


</table>

<table>
<tr>
<td>
<h4><strong><u>APPROVAL</u></strong></h4>
<p>Shubham Flexible Packaging Machines Pvt. Ltd will fully assemble the equipment prior to shipping
and a representative of the customer must approve the Equipment in writing prior to shipment. Any
modification or change suggested at this point will invalidate the delivery date clause and reasonable
time will be allowed to incorporate the modification.<br><br></p><br><br>

</td>
</tr>

<tr>
<td><h4><strong><u>ARBITRATION</u></strong></h4><br><br>
<p>Any dispute or differences whatsoever arising between the parties out of or relating to the construction,
meaning and operation or effect of this contract or the breach thereof shall be settled by arbitration in
accordance with the Rules of Arbitration of the Indian Council of Arbitration and the Award made in
pursuance thereof shall be binding on the parties.<br><br></p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>VALIDITY:</u></strong></h4><br><br>
<p>This Contract and prices are valid for 30 days and supersedes all previous contracts. The terms and
conditions mentioned above shall supersede any terms and conditions agreed to earlier Performa
invoice if any.<br><br></p><br><br>
</td>
</tr>

<tr>
<td><h4><strong><u>GENERAL:</u></strong></h4><br><br>
<p>Continuous improvement is standard policy at Shubham. Accordingly, all specification and features
are subject to change without any prior notice.<br><br></p>
</td>
</tr>
</table>
';

//echo $html; exit;
$this->load->library('Pdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Shubham Pack');
$pdf->SetTitle("Quotation");
$pdf->SetSubject('Quotation');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
// $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));

$pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin('10');
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('dejavusans', '', 10, '', true);
$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');
ob_end_clean();
$pdf->Output('example_001.pdf', 'I');
// echo $_SERVER['DOCUMENT_ROOT'] . '/image_bank/daily_reports';exit;
$filelocation = $_SERVER['DOCUMENT_ROOT'] . '/image_bank/customerfile';
// $fileNL = $filelocation."/akash.pdf"; //Linux
$fileNL = $filelocation . "/CRM_Daily_Report_".date('d-m-Y') . ".pdf"; //Linux
$pdf->Output($fileNL, 'F');


}


public function addnewopportunity(){
	$this->load->view('opportunity/opportunity');
}

public function Opportunitywithaddition(){
	$this->load->view('leads/opportunitywithaddition');
}

function add_quote()
{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
		$this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
		$this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
		$this->form_validation->set_rules('cur', 'Currency', 'required|trim');
		$this->form_validation->set_rules('country', 'Country', 'required|trim');
		$this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
		$this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
		$this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('pouchsize', 'Pouch Size', 'required|trim');
		$this->form_validation->set_rules('qtytobepacked', 'Qty to be Packed', 'required|trim');
		$this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
		$this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
		$this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
		$this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
		$this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
		$this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');
		$this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
		$this->form_validation->set_rules('noofaxisinmachine', 'No of Axis in Machine', 'required|trim');
		$this->form_validation->set_rules('axisdetail', 'Axis Machine Detail', 'required|trim');
		$this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
		//$this->form_validation->set_rules('sealingstyle', 'Sealing Style', 'required|trim');
		$this->form_validation->set_rules('speed', 'Speed', 'required|trim');
		$this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
		$this->form_validation->set_rules('laminate_specification', 'Laminate specification', 'required|trim');
		$this->form_validation->set_rules('producttobepacked', 'Product to be Packed', 'required|trim');
		$this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
		$this->form_validation->set_rules('pouchsize', 'Pouch Size', 'required|trim');
		$this->form_validation->set_rules('sealingdrives', 'Sealing Drives', 'required|trim');
		$this->form_validation->set_rules('perforationandcutting', 'perforation and Cutting', 'required|trim');
		$this->form_validation->set_rules('laminatedrawoffsystem', 'Laminate Draw Off system', 'required|trim');
		$this->form_validation->set_rules('laminatetrackingsystem', 'Laminate tracking system', 'required|trim');
		$this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
		$this->form_validation->set_rules('layoutdimensions', 'Layout Dimensions', 'required|trim');
		$this->form_validation->set_rules('machineweight', 'Machine Weight', 'required|trim');
		$this->form_validation->set_rules('compressedair', 'Compressed Air', 'required|trim');
		$this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
		$this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
		$this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
		$this->form_validation->set_rules('auger_filling_system', 'Filling Style', 'required|trim');
		$this->form_validation->set_rules('auger_filling_system_qty', 'Filling Style Qty', 'required|trim');
		$this->form_validation->set_rules('auger_filling_system_price', 'Filling Style Price', 'required|trim');
		$this->form_validation->set_rules('discharge_qty', 'Discharge Qty', 'required|trim');
		$this->form_validation->set_rules('discharge_price', 'Discharge Price', 'required|trim');
		$this->form_validation->set_rules('autocasepacker_qty', 'Auto Case Packer Qty', 'required|trim');
		$this->form_validation->set_rules('autocasepacker_price', 'Auto Case Packer Price', 'required|trim');
		$this->form_validation->set_rules('packingcharges_qty', 'Packing Charges Qty', 'required|trim');
		$this->form_validation->set_rules('forwarding_charges_qty', 'Forwarding Charges Qty', 'required|trim');
		$this->form_validation->set_rules('forwarding_charges_price', 'Forwarding Charges Price', 'required|trim');
		$this->form_validation->set_rules('insurancecharges_qty', 'Insurance Charges Qty', 'required|trim');
		$this->form_validation->set_rules('insurancecharges_price', 'Insurance Charges Price', 'required|trim');
		$this->form_validation->set_rules('freightuntil', 'Freight until', 'required|trim');
		$this->form_validation->set_rules('freightuntil_qty', 'Freight until Qty', 'required|trim');
		$this->form_validation->set_rules('freightuntil_price', 'Freight until Price', 'required|trim');
		$this->form_validation->set_rules('totalcif', 'Total CIF', 'required|trim');
		$this->form_validation->set_rules('totalcif_qty', 'Total CIF Qty', 'required|trim');
		$this->form_validation->set_rules('totalcif_price', 'Total CIF Price', 'required|trim');
		$this->form_validation->set_rules('totalcostcif', 'Total Cost CIF', 'required|trim');
		$this->form_validation->set_rules('totalcostcif_qty', 'Total Cost CIF Qty', 'required|trim');
		$this->form_validation->set_rules('totalcostcif_price', 'Total Cost CIF Price', 'required|trim');
		$this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
		$this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
		$this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');
		$this->form_validation->set_rules('support_statement', 'Support Statement', 'required|trim');
		$this->form_validation->set_rules('followDate', 'Next Followup Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('opportunity/opportunity');
		}
		else
		{
			$rest=$this->db->select('product_id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$product_id=$row->product_id;
			}else
			{
				echo "Product Not Found"; exit;
			}

			$data = array('lead_id'=>$this->uri->segment(3),
				'product_id'=>$product_id,
				'ref_no'=>$this->input->post('refno'),
				'quotation_date'=>date('Y-m-d',strtotime($this->input->post('quote_date'))),
				'customer_id'=>$this->input->post('customername'),
				'currency'=>$this->input->post('cur'),
				'country'=>$this->input->post('country'),
				'machine_name'=>$this->input->post('machineName'),
				'machine_model_no'=>$this->input->post('machineModel'),
				'added_on'=>date('Y-m-d H:i:s'),
				'last_revision_date'=>date('Y-m-d'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_customer_data',$data);
			$recordid = $this->db->insert_id();

			$data1 = array('record_id'=>$recordid ,
				'product_to_be_packed'=>$this->input->post('producttobepacked'),
				'product_name'=>$this->input->post('productname'),
				'pouch_size_type'=>$this->input->post('pouchsize'),
				'qty_to_be_packed'=>$this->input->post('qtytobepacked'),
				'horizontal_sealing_width'=>$this->input->post('horizontalsealingwidth'),
				'vertical_sealing_width'=>$this->input->post('verticalsealingwidth'),
				'perforation_pitch'=>$this->input->post('perforationpitch'),
				'typeofsealing'=>$this->input->post('typeofsealing'),
				'plc_make'=>$this->input->post('plcmake'),
				'power_supply'=>$this->input->post('powersupply'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_annexture_1',$data1);

			$data2 = array('record_id'=>$recordid,
				'model'=>$this->input->post('machinemodelno'),
				'no_of_axis_in_machine'=>$this->input->post('noofaxisinmachine'),
				'axis_detail'=>$this->input->post('axisdetail'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_cum_tech_spec',$data2);

			$data3 = array('record_id'=>$recordid,
				'machinemodel'=>$this->input->post('machinemodel'),
				 'sealingstyle'=>$this->input->post('sealingstyle'),
				'speed'=>$this->input->post('speed'),
				'no_of_track'=>$this->input->post('nooftracks'),
				'leminate_specification'=>$this->input->post('laminate_specification'),
				'product_to_be_packed'=>$this->input->post('producttobepacked'),
				'filling_capacity'=>$this->input->post('fillingcapacity'),
				'pouch_size'=>$this->input->post('pouchsize'),
				'sealing_drives'=>$this->input->post('sealingdrives'),
				'perforation_and_cutting'=>$this->input->post('perforationstyle'),
				'perforationstyle'=>$this->input->post('perforationstyle'),
				'batchcut'=>$this->input->post('batchcut'),
				'laminate_draw'=>$this->input->post('laminatedrawoffsystem'),
				'laminate_tracking'=>$this->input->post('laminatetrackingsystem'),
				'electrical_spec'=>$this->input->post('electricalspec'),
				'layout_dimensions'=>$this->input->post('layoutdimensions'),
				'machine_weight'=>$this->input->post('machineweight'),
				'compressed_air'=>$this->input->post('compressedair'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);

			$this->db->insert('quotation_annexure_2',$data3);

			$data4 = array('record_id'=>$recordid,
				'description'=>$this->input->post('modelno'),
				'qty'=>$this->input->post('modelqty'),
				'price'=>$this->input->post('modelprice'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>1,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data4);

			$data5 = array('record_id'=>$recordid,
				'description'=>$this->input->post('auger_filling_system'),
				'qty'=>$this->input->post('auger_filling_system_qty'),
				'price'=>$this->input->post('auger_filling_system_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>2,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data5);

			$data6 = array('record_id'=>$recordid,
				'description'=>$this->input->post('discharge'),
				'qty'=>$this->input->post('discharge_qty'),
				'price'=>$this->input->post('discharge_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>3,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data6);

			$data7 = array('record_id'=>$recordid,
				'description'=>$this->input->post('autocasepacker'),
				'qty'=>$this->input->post('autocasepacker_qty'),
				'price'=>$this->input->post('autocasepacker_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>4,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data7);

			$data8 = array('record_id'=>$recordid,
				'description'=>$this->input->post('packingcharges'),
				'qty'=>$this->input->post('packingcharges_qty'),
				'price'=>$this->input->post('packingcharges_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>5,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data8);

			$data9 = array('record_id'=>$recordid,
				'description'=>$this->input->post('forwarding_charges'),
				'qty'=>$this->input->post('forwarding_charges_qty'),
				'price'=>$this->input->post('forwarding_charges_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>6,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data9);

			$data10 = array('record_id'=>$recordid,
				'description'=>$this->input->post('insurancecharges'),
				'qty'=>$this->input->post('insurancecharges_qty'),
				'price'=>$this->input->post('insurancecharges_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>7,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data10);

			$data11 = array('record_id'=>$recordid,
				'description'=>$this->input->post('freightuntil'),
				'qty'=>$this->input->post('freightuntil_qty'),
				'price'=>$this->input->post('freightuntil_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>8,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data11);

			$data11 = array('record_id'=>$recordid,
				'description'=>$this->input->post('totalcif'),
				'qty'=>$this->input->post('totalcif_qty'),
				'price'=>$this->input->post('totalcif_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>9,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data11);


			$data12 = array('record_id'=>$recordid,
				'description'=>$this->input->post('totalcostcif'),
				'qty'=>$this->input->post('totalcostcif_qty'),
				'price'=>$this->input->post('totalcostcif_price'),
				'addedOn'=>date('Y-m-d H:i:s'),
				'flag'=>10,
				'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data12);

			if(isset($_REQUEST['optionaldata'])){	
					$tags1=count($_REQUEST['optionaldata']);
					if($tags1>0)
					{
					$optionaldata = $_REQUEST['optionaldata'];
					$optionalqty=$_REQUEST['optionalqty'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($optionaldata[$x]!='')
						{
							
							
							$data=array('record_id'=>$recordid,
							'description'=>$optionaldata[$x],
							'value'=>$optionalqty[$x],
							'addedBy'=>$user_id,
							'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('quotation_optional',$data);
							
						}
					$i++;	
					}
					}
					}

			$data13 = array('record_id'=>$recordid,
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'support_statement_value'=>$this->input->post('support_statement'),
				'addedBy'=>$user_id,
				'addedOn'=>date('Y-m-d H:i:s'));

			$this->db->insert('quotation_other_information',$data13);


			/** UPDATE LEAD STATUS **/

			$d=array(
					'lead_id'=>$this->uri->segment(3),
					'lead_status'=>36,
					'next_follow_date'=>date('Y-m-d',strtotime($this->input->post('followDate'))),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$_SESSION['logged_in']['user_id']
				);
			$this->input->post('progress_remarks',$d);

			$this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');
				redirect(page_url.'Opportunity/addnewopportunity/'.$this->uri->segment(3));

		}
}

function GeneratedQuote()
{
	$this->load->view('formats/rfq/examples/shubham_quotation');

}

function newopportunity(){
	$this->load->view('opportunity/opportunity_new');
}

function feednewopportunity()
{
		// $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		// $this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
		// $this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
		// $this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
		// $this->form_validation->set_rules('cur', 'Currency', 'required|trim');
		// $this->form_validation->set_rules('country', 'Country', 'required|trim');
		// $this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
		// $this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
		// /*Validation till general information*/
		// $this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
		// $this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		// //$this->form_validation->set_rules('pouchsizew', 'Pouch Size W', 'required|trim');
		// //$this->form_validation->set_rules('pouchsizel', 'Pouch Size L', 'required|trim');
		// //$this->form_validation->set_rules('qtytobepacked', 'Qty to be Packed', 'required|trim');
		// //$this->form_validation->set_rules('qty_unit', 'Qty Unit', 'required|trim');
		// $this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
		// $this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
		// $this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
		// $this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
		// $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
		// $this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');


		// $this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
		// // $this->form_validation->set_rules('noofaxisinmachine', 'No of Axis in Machine', 'required|trim');
		// $this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
		// $this->form_validation->set_rules('sealingstyle', 'Sealing Style', 'required|trim');
		// $this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
		// $this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
		// $this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
		// $this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
		// $this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
		// $this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
		// $this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
		// $this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
		// $this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
		// // $this->form_validation->set_rules('pouchsizew1', 'Pouch Size W', 'required|trim');
		// // $this->form_validation->set_rules('pouchsizel1', 'Pouch Size L', 'required|trim');
		// // $this->form_validation->set_rules('pouchsizeh1', 'Pouch Size H', 'required|trim');
		// $this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
		// $this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
		// $this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
		// $this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
		// $this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
		// $this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
		// $this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
		// $this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
		// $this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
		// $this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
		// $this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
		// $this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');
		
		// $this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
		// $this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
		// $this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');
		 $this->form_validation->set_rules('followDate', 'Next Followup Date', 'required|trim');
		$user_id =$this->input->post('user_id');		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('opportunity/opportunity_new');
		}
		else
		{
			$rest=$this->db->select('product_id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$product_id=$row->product_id;
			}else
			{
				echo "Product Not Found"; exit;
			}


			$gst_actual=0;
			if($this->input->post('country')==101)
			{
			if($this->input->post('gstapplicable')==1)
			{
				$gst_actual=1;
			}
			}


			$data = array('lead_id'=>$this->uri->segment(3),
				'product_id'=>$product_id,
				'ref_no'=>$this->input->post('refno'),
				'quotation_date'=>date('Y-m-d',strtotime($this->input->post('quote_date'))),
				'customer_id'=>$this->input->post('customername'),
				'currency'=>$this->input->post('cur'),
				'country'=>$this->input->post('country'),
				'machine_name'=>$this->input->post('machineName'),
				'machine_model_no'=>$this->input->post('machineModel'),
				'cantilever'=>$this->input->post('cantilever'),
				'mach_model_no'=>$this->input->post('mach_model_no'),
				'added_on'=>date('Y-m-d H:i:s'),
				'last_revision_date'=>date('Y-m-d'),
				'special_notes'=>$this->input->post('specialNotes'),
				'gst_actual'=>$gst_actual,
				'validity'=>$this->input->post('validity'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_customer_data',$data);
			$recordid = $this->db->insert_id();

			/** ADD QTY TO BE PACKED & POUCH SIZE **/
			$qtytobepacked=$this->input->post('qtytobepacked');
			for($u=0;$u<count($qtytobepacked);$u++)
			{
				$packedqty=$qtytobepacked[$u];
				$unit=$this->input->post('qty_unit')[$u];
				$length=$this->input->post('pouchsizel')[$u];
				$width=$this->input->post('pouchsizew')[$u];
				$height=$this->input->post('pouchsizeh')[$u];
				if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName')=="HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName')=="HFFS MULTI TRACK MACHINE" || $this->input->post('machineName')=="HFFS PICK FILL SEAL")
				{
				$gusset=$this->input->post('gusset')[$u];
				$punchhole=$this->input->post('punch_hole')[$u];
				}else
				{
				$gusset='';
				$punchhole='';
				}


			$data=array('record_id'=>$recordid,'packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
			$this->db->insert('quotation_pouch_size',$data);
			}
			/** END **/

			// $pouchsizew = $this->input->post('pouchsizew');
			// $pouchsizel = $this->input->post('pouchsizel');
			// $pouchsize = $pouchsizew." X ".$pouchsizel;

			$data1 = array('record_id'=>$recordid ,
				'product_to_be_packed'=>$this->input->post('producttobepacked'),
				'liquid_option'=>$this->input->post('liquid_option'),
				'powder_option'=>$this->input->post('powder_option'),
				'non_viscous_option'=>$this->input->post('non_viscous_option'),
				'viscous_option'=>$this->input->post('viscous_option'),
				'piston_filler_option'=>$this->input->post('piston_filler_option'),
				'follow_meter_option'=>$this->input->post('follow_meter_option'),
				'free_flow_option'=>$this->input->post('free_flow_option'),
				'weigher_system_option'=>$this->input->post('weigher_system_option'),
				'liner_weigher_option'=>$this->input->post('liner_weigher_option'),
				'mult_head_weigher_option'=>$this->input->post('mult_head_weigher_option'),
				'volumetric_cap_option'=>$this->input->post('volumetric_cap_option'),
				'non_free_flow_option'=>$this->input->post('non_free_flow_option'),
				'product_name'=>$this->input->post('productname'),
				// 'pouch_size_type'=>$pouchsize,
				// 'qty_to_be_packed'=>$this->input->post('qtytobepacked')." ".$this->input->post('qty_unit'),
				'horizontal_sealing_width'=>$this->input->post('horizontalsealingwidth'),
				'vertical_sealing_width'=>$this->input->post('verticalsealingwidth'),
				'perforation_pitch'=>$this->input->post('perforationpitch'),
				'perforationstyle'=>$this->input->post('perforationstyle'),
				'batchcut'=>$this->input->post('batchcut'),
				'typeofsealing'=>$this->input->post('typeofsealing'),
				// 'plc_make'=>$this->input->post('plcmake'),
				'power_supply'=>$this->input->post('powersupply'),
				'liquidviscositydata'=>$this->input->post('liquidviscositydata'),
				'liquidconductivitydata'=>$this->input->post('liquidconductivitydata'),
				'powderdensitydata'=>$this->input->post('powderdensitydata'),
				'powderdfrdata'=>$this->input->post('powderdfrdata'),
				'powdermoisturecontentdata'=>$this->input->post('powdermoisturecontentdata'),
				'machine_type'=>$this->input->post('machtype'),
			
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_annexture_1',$data1);

			$data2 = array('record_id'=>$recordid,
				'model'=>$this->input->post('machinemodelno'),
				'flag'=>1,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_cum_tech_spec',$data2);


			/*Add Multiple Tech Spec*/
			if(isset($_REQUEST['technicalspec'])){	
					$tags1=count($_REQUEST['technicalspec']);
					if($tags1>0)
					{
					$technicalspec = $_REQUEST['technicalspec'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($technicalspec[$x]!='')
						{
					$data21 = array('record_id'=>$recordid,
					'model'=>$technicalspec[$x],
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id,
					'flag'=>$x);
					$this->db->insert('quotation_cum_tech_spec',$data21);
							
						}
					$i++;	
					}
					}
					}
			/*Add Multiple Tech Spec*/


			/*Add Multiple Axis Machine Input*/
			// if(isset($_REQUEST['technicalspecs'])){	
			// 		$tags1=count($_REQUEST['technicalspecs']);
			// 		if($tags1>0)
			// 		{
			// 		$technicalspecs = $_REQUEST['technicalspecs'];
			// 		$i=1;
			// 		for($x=0;$x<$tags1;$x++){
			// 		if($technicalspecs[$x]!='')
			// 			{
			// 		$data22 = array('record_id'=>$recordid,
			// 		'description'=>$technicalspecs[$x],
			// 		'added_on'=>date('Y-m-d H:i:s'),
			// 		'added_by'=>$user_id);
			// 		$this->db->insert('quotation_no_of_axis_in_machine',$data22);
							
			// 			}
			// 		$i++;	
			// 		}
			// 		}
			// 		}


			$noofaxisinmachine=$this->input->post('noofaxisinmachine');
			for($i=0;$i<count($noofaxisinmachine);$i++)
			{
				$data22 = array('record_id'=>$recordid,
			 		'description'=>$this->input->post('noofaxisinmachine')[$i],
			 		'axiscount'=>$this->input->post('noofaxisincount')[$i],
			 		'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
			 		$this->db->insert('quotation_no_of_axis_in_machine',$data22);
			}


			/** BRANDS DATA **/

			$brands=$this->input->post('brands');
			for($i=0;$i<count($brands);$i++)
			{
				$data22 = array('record_id'=>$recordid,
				'head_id'=>$brands[$i],
				'value_id'=>$this->input->post('brand_data')[$i],
				'addedOn'=>date('Y-m-d H::i:s'),
				'addedBy'=>$user_id);
				$this->db->insert('quotation_brand_data',$data22);
			}


			/** END **/



			/*Add Multiple Axis Machine Input*/
			$laminatewidth = $this->input->post('laminatewidth')."<br>";
			$laminatereeldia = $this->input->post('laminatereeldia')."<br>";
			$laminatereelcoredia = $this->input->post('laminatereelcoredia')."<br>";
			$laminatewidthcoredia = $laminatewidth." ".$laminatereeldia." ".$laminatereelcoredia;
			// $pouchsizew1 = $this->input->post('pouchsizew1');
			// $pouchsizel1 = $this->input->post('pouchsizel1');
			// $pouchsizeh1 = $this->input->post('pouchsizeh1');
			//$secondpouchsize = $pouchsizew1." X ".$pouchsizel1." X ".$pouchsizeh1;
			$layoutdimensionslength = $this->input->post('layoutdimensionslength');
			$layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
			$layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
			$layoutdimensionsof = $layoutdimensionslength."<br>".$layoutdimensionswidth."<br>".$layoutdimensionsheight."<br>";

			$data3 = array('record_id'=>$recordid,
				'machinemodel'=>$this->input->post('machinemodel'),
				 'sealingstyle'=>$this->input->post('sealingstyle'),
				'filling_accuracy'=>$this->input->post('fillingaccuracy'),
				'speed'=>$this->input->post('designspeed'),
				'actual_speed'=>$this->input->post('actualspeed'),
				'no_of_track'=>$this->input->post('nooftracks'),
				'leminate_specification'=>$laminatewidth,
				'laminatereeldia'=>$laminatereeldia,
				'laminatereelcoredia'=>$laminatereelcoredia ,
				'product_to_be_packed'=>$this->input->post('product_tobepacked'),
				'filling_capacity'=>$this->input->post('fillingcapacity'),
				//'pouch_size'=>$secondpouchsize,
				'electrical_spec'=>$this->input->post('electricalspec'),
				'layout_dimensions'=>$layoutdimensionsof,
				'machine_weight'=>$this->input->post('netweight'),
				'gross_weight'=>$this->input->post('grossweight'),
				'compressed_air'=>$this->input->post('compressedaircfa'),
				'compressedairbar'=>$this->input->post('compressedairbar'),
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);

			$this->db->insert('quotation_annexure_2',$data3);

			$data4 = array('record_id'=>$recordid,
			'description'=>$this->input->post('modelno'),
			'hsn'=>$this->input->post('modelhsn'),
			'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
			'qty'=>$this->input->post('modelqty'),
			'unit'=>$this->input->post('qtyunit'),
			'price'=>$this->input->post('modelprice'),
			'addedOn'=>date('Y-m-d H:i:s'),
			'flag'=>0,
			'addedBy'=>$user_id);
			$this->db->insert('quotation_annexture_4',$data4);


			// Check if the checkbox is set (checked)
			if ($this->input->post('machinefillingsystem')!='') {
			// Get the value of the checkbox
			$checkboxValue = $this->input->post('machinefillingsystem');
			$photo=$_FILES['machinefillingimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'.'.$cat_image;
			move_uploaded_file($_FILES['machinefillingimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			}else
			{
			$machineimage="";
			}

			$data = array('record_id'=>$recordid,
				'image'=>$machineimage,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_machine_filling_image',$data);

			} else {
				
			}





			if ($this->input->post('kld')!='') {
			// Get the value of the checkbox
			$checkboxValue = $this->input->post('kld');
			$photo=$_FILES['kldimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'12232.'.$cat_image;
			move_uploaded_file($_FILES['kldimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			}else
			{
			$machineimage="";
			}

			$data = array('record_id'=>$recordid,
				'image'=>$machineimage,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_kld_image',$data);

			} else {
				
			}



			
			/*Machine price section*/
			$grandtotalprice = $this->input->post('modelqty')*$this->input->post('modelprice');
			$machinepricedata = array('record_id'=>$recordid,
				'machine_model'=>$this->input->post('modelno'),
				'qty'=>$this->input->post('modelqty'),
				'unit_price'=>$this->input->post('modelprice'),
				'totalprice'=>$grandtotalprice,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_machine_price_info',$machinepricedata);
			/*Machine price section*/


			/*Quotation Freight Info*/
			if ($this->input->post('packagingapplicable')!='') {
				$packingingcharge = $this->input->post('packagingpercentage');
			}else{
				$packingingcharge = "";
			}

			if ($this->input->post('forwardingapplicable')!='') {
				$forwardingcharges = $this->input->post('forwardingpercentage');
			}else{
				$forwardingcharges = "";
			}
			if ($this->input->post('insuranceapplicable')!='') {
				$insurancecharges = $this->input->post('insurancepercentage');
			}else{
				$insurancecharges = "";
			}
			if($this->input->post('frightinfo')==3 || $this->input->post('frightinfo')==4){
				$freight_charges = $this->input->post('freightamount');
			}else{
				$freight_charges = "";
			}

			$freight_type=$this->input->post('freightType');
			$port_id = 0;
			if($freight_type=="FOB" || $freight_type=="CIF" || $freight_type=="CFR")
			{
				$port=$this->input->post('port');
				if (is_numeric($port) && !strpos($port, '.')) {

					$port_id=$port;
				}else
				{
					$dt=array('name'=>$port);
					$this->db->insert('ports',$dt);
					$port_id=$this->db->insert_id();
				}


			}


			if ($this->input->post('installationcommissioningapplicable')!='') {
				$installationcommissioningamount = $this->input->post('installationcommissioningamount');
			}else{
				$installationcommissioningamount = "";
			}


			$freightdata = array('record_id'=>$recordid,
				'freight'=>$this->input->post('frightinfo'),
				'freight_charges'=>$freight_charges,
				'installation_charges'=>$installationcommissioningamount,
				'freight_type'=>$freight_type,
				'packing_charges'=>$packingingcharge,
				'forwarding_charges'=>$forwardingcharges,
				'insurance'=>$insurancecharges,
				'port'=>$port_id,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_freight_packing_forwarding',$freightdata);
			/*Quotation Freight Info*/


			/*Add Technical Charges*/
		
			if(isset($_REQUEST['techdescriptioninfo'])){	
					$tags1=count($_REQUEST['techdescriptioninfo']);
					if($tags1>0)
					{
					$techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
					$techdescqty = $_REQUEST['techdescqty'];
					$techsn = $_REQUEST['techsn'];
					$techunit = $_REQUEST['techunit'];
					$techdescprice = $_REQUEST['techdescprice'];
					$techdesctotalprice = $_REQUEST['techdesctotalprice'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($techdescriptioninfo[$x]!='')
						{


							if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {

							$techdescitemid = $techdescriptioninfo[$x];

							// echo "String contains only numeric digits-".$techdescriptioninfo[$x];

							} else {
							// echo "String contains non-numeric characters-".$techdescriptioninfo[$x];

							$datass = array('instruments_name'=>$techdescriptioninfo[$x],'type'=>1,'status'=>1);
							$this->db->insert('presto_instruments',$datass);
							$techdescitemid = $this->db->insert_id();
							}

							
							// if(is_int($techdescriptioninfo[$x])){
							// 	$techdescitemid = $techdescriptioninfo[$x];
							// }else{
							// 	$datass = array('instruments_name'=>$techdescriptioninfo[$x],'type'=>1,'status'=>1);
							// 	$this->db->insert('presto_instruments',$datass);
							// 	$techdescitemid = $this->db->insert_id();
							// }


							$totalprice = $techdescqty[$x]*$techdescprice[$x];
							$data4 = array('record_id'=>$recordid,
							'description'=>$techdescitemid,
							'hsn'=>$techsn[$x],
							'qty'=>$techdescqty[$x],
							'unit'=>$techunit[$x],
							'price'=>$techdescprice[$x],
							'total_price'=>$totalprice,
							'addedOn'=>date('Y-m-d H:i:s'),
							'flag'=>$i,
							'category_type'=>1,
							'addedBy'=>$user_id);
							$this->db->insert('quotation_annexture_4',$data4);
							
						}
					$i++;	
					}
					}
					}

			

			if(isset($_REQUEST['optionalitem'])){	
					$tags1=count($_REQUEST['optionalitem']);
					if($tags1>0)
					{
					$optionalitem = $_REQUEST['optionalitem'];
					$optionalqty=$_REQUEST['optionalqty'];
					$optionalprice=$_REQUEST['optionalprice'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($optionalitem[$x]!='')
					{

						if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
						$itemid = $optionalitem[$x];
						} else {
						$datass = array('instruments_name'=>$optionalitem[$x],'type'=>1,
						'status'=>1);
						$this->db->insert('presto_instruments',$datass);
						$itemid = $this->db->insert_id();
						}					


							// if(is_int($optionalitem[$x])){
							// 	$itemid = $optionalitem[$x];
							// }else{
							// 	$datass = array('instruments_name'=>$optionalitem[$x],'type'=>1,
							// 'status'=>1);
							// 	$this->db->insert('presto_instruments',$datass);
							// 	$itemid = $this->db->insert_id();
							// }
							
							$totalprice = $optionalqty[$x]*$optionalprice[$x];
							$data=array('record_id'=>$recordid,
							'description'=>$itemid,
							'value'=>$optionalqty[$x],
							'price'=>$optionalprice[$x],
							'totalprice'=>$totalprice,
							'addedBy'=>$user_id,
							'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('quotation_optional',$data);
							
						}
					$i++;	
					}
					}
					}

			if ($this->input->post('consumablespare')!='') {
				$consumablespare = $this->input->post('consumablespare');
			}else{
				$consumablespare = 0;
			}
			$data13 = array('record_id'=>$recordid,
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'addedBy'=>$user_id,
				'consumable_spare'=>$consumablespare,
				'addedOn'=>date('Y-m-d H:i:s'));

			$this->db->insert('quotation_other_information',$data13);


			// Check if the checkbox is set (checked)
			if ($this->input->post('layoutapplicable')!=='') {
			// Get the value of the checkbox
			
			$photo1=$_FILES['uploadlayout']['name'];
			if($photo1<>'')
			{
			$image2=explode('.',$photo1);
			$cat_image1=end($image2);
			$layoutimage=time().'.'.$cat_image1;
			move_uploaded_file($_FILES['uploadlayout']["tmp_name"],UPLOADPATH.'opportunitydocs/layoutimg/' . $layoutimage);
			}else
			{
			$layoutimage="";
			}

			$data = array('record_id'=>$recordid,
				'image'=>$layoutimage,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_layout_img',$data);

			} else {
				
			}

		if ($this->input->post('consumablespare')!='') {
			/*Consumable Spares*/

			if(isset($_REQUEST['spareqty'])){	
					$tags1=count($_REQUEST['spareqty']);
					if($tags1>0)
					{
					//$partname = $_REQUEST['partname'];
					$spareqty=$_REQUEST['spareqty'];
					$spareprice=$_REQUEST['spareprice'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					// if($partname[$x]!='')
					// 	{

						/** MEDIA **/
							$photo1=$_FILES['spareFile']['name'];
							if($photo1<>'')
							{
							$image2=explode('.',$photo1);
							$cat_image1=end($image2);
							$spareFiles=time().'.'.$cat_image1;
							move_uploaded_file($_FILES['spareFile']["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
							}else
							{
							$spareFiles="";
							}

						/** END **/

							$data=array('record_id'=>$recordid,
							'qty'=>$spareqty[$x],
							'price'=>$spareprice[$x],
							'media'=>$spareFiles,
							'added_by'=>$user_id,
							'added_on'=>date('Y-m-d H:i:s'));
							$this->db->insert('quotation_consumable_spares',$data);
							
						//}
					$i++;	
					}
					}
					}


			/*Consumable Spares*/
		}


		/** UPDATE TERMS & CONDITIONS **/
		if($this->input->post('country')==101)
		{

		$late_delivery_applicable = $this->input->post('late_delivery_applicable');
		if($late_delivery_applicable==1)
		{
			$late_delivery_applicable=1;
		}else
		{
			$late_delivery_applicable=0;
		}


		$liquidated_applicable = $this->input->post('liquidated_clause_applicable');
		if($liquidated_applicable==1)
		{
			$liquidated_applicable=1;
		}else
		{
			$liquidated_applicable=0;
		}


		// get and xss_clean content fields (optional: keep html if you use editors)
		$liquidated_text  = $this->input->post('liquidated_clause');
		$late_delivery_text  = $this->input->post('late_delivery');
		$packing_charges  = $this->input->post('packing_charges');
		$insurance        = $this->input->post('insurance');
		$installation     = $this->input->post('installation');

		$payload = [
		'quotation_id' => $recordid,
		'liquidated_applicable' => $liquidated_applicable,
		'liquidated_text' => $liquidated_text ?: null,
		'late_delivery_applicable'=>$late_delivery_applicable,
		'late_delivery_text'=>$late_delivery_text ? : null,
		'packing_charges' => $packing_charges ?: null,
		'insurance' => $insurance ?: null,
		'installation' => $installation ?: null
		];

		$this->db->insert('quotation_custom_terms',$payload);
		}else
		{
			$this->db->where('quotation_id',$recordid);
			$this->db->delete('quotation_custom_terms');
		}



			/** UPDATE LEAD STATUS **/

			$d=array(
					'lead_id'=>$this->uri->segment(3),
					'lead_status'=>39,
					'next_follow_date'=>date('Y-m-d',strtotime($this->input->post('followDate'))),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id
				);
			$this->db->insert('progress_remarks',$d);


			// $this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Quote Generated Please review and share for approval.</span><br/>');

			//redirect(page_url.'Opportunity/previewquoteandsendforapproval/'.$recordid.'/'.$this->uri->segment(3));

			// $this->session->set_flashdata('message','<span style="color:red; float-left:20px;" class="alert alert-success">Thank you, Your record successfully added.</span><br/>');

			 redirect(page_url.'Opportunity/GeneratedQuote/'.$recordid);

		}
}


function edit_opportunity()
{
	$this->load->view('opportunity/edit_opportunity_quote');
}

function deletespecification()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('quotation_cum_tech_spec');

	echo true;

}

function deleteaxisspecification()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('quotation_no_of_axis_in_machine');
	echo true;

}

function getAxisCount()
{
	$q=$this->db->select('id')->from('quotation_no_of_axis_in_machine')->where('record_id',$this->input->post('record_id'))->get();
	echo $q->num_rows();

}

function deleteLineItem()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('quotation_annexture_4');
	echo true;
}

function delete_op_data()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('quotation_optional');
	echo true;
}



// function Update_newopportunity()
// {
// 		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
// 		$this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
// 		$this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
// 		$this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
// 		$this->form_validation->set_rules('cur', 'Currency', 'required|trim');
// 		$this->form_validation->set_rules('country', 'Country', 'required|trim');
// 		$this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
// 		$this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
// 		/*Validation till general information*/
// 		$this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
// 		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
// 		$this->form_validation->set_rules('machtype', 'Machine Type', 'required|trim');
// 		//$this->form_validation->set_rules('orientation', 'orientation', 'required|trim');
// 		// $this->form_validation->set_rules('pouchsizew', 'Pouch Size W', 'required|trim');
// 		// $this->form_validation->set_rules('pouchsizel', 'Pouch Size L', 'required|trim');
// 		// $this->form_validation->set_rules('qtytobepacked', 'Qty to be Packed', 'required|trim');
// 		// $this->form_validation->set_rules('qty_unit', 'Qty Unit', 'required|trim');
// 		$this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
// 		$this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
// 		$this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
// 		$this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
// 		// $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
// 		$this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');


// 		$this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
// 		// $this->form_validation->set_rules('noofaxisinmachine', 'No of Axis in Machine', 'required|trim');
// 		$this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
// 		//$this->form_validation->set_rules('sealingstyle', 'Sealing Style', 'required|trim');
// 		$this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
// 		$this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
// 		$this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
// 		$this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
// 		$this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
// 		$this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
// 		$this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
// 		$this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
// 		$this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
// 		// $this->form_validation->set_rules('pouchsizew1', 'Pouch Size W', 'required|trim');
// 		// $this->form_validation->set_rules('pouchsizel1', 'Pouch Size L', 'required|trim');
// 		// $this->form_validation->set_rules('pouchsizeh1', 'Pouch Size H', 'required|trim');
// 		$this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
// 		$this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
// 		$this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
// 		$this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
// 		$this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
// 		$this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
// 		$this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
// 		$this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
// 		$this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
// 		$this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
// 		$this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
// 		$this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');
		
// 		$this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
// 		$this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
// 		$this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');
// 		// $this->form_validation->set_rules('followDate', 'Next Followup Date', 'required|trim');
// 		$user_id =$this->session->userdata['logged_in']['user_id'];		
// 		if ($this->form_validation->run() == FALSE)
// 		{
// 			$this->load->view('opportunity/edit_opportunity_quote');
// 		}
// 		else
// 		{
// 			$rest=$this->db->select('product_id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
// 			if($rest->num_rows()>0)
// 			{
// 				foreach($rest->result() as $row);
// 				$product_id=$row->product_id;
// 			}else
// 			{
// 				echo "Product Not Found"; exit;
// 			}
// 			if($this->uri->segment(5)==1){
// 				$version=$this->getCurrentVersion($this->uri->segment(3));
// 				$newversion = $version+1;
// 			}else{
// 				$newversion=$this->getCurrentVersion($this->uri->segment(3));
// 			}
// 			$version=$this->getCurrentVersion($this->uri->segment(3));

// 			$gst_actual=0;
// 			if($this->input->post('country')==101)
// 			{
// 			if($this->input->post('gstapplicable')==1)
// 			{
// 				$gst_actual=1;
// 			}
// 			}


// 			$data = array(
// 				'currency'=>$this->input->post('cur'),
// 				'country'=>$this->input->post('country'),
// 				'machine_name'=>$this->input->post('machineName'),
// 				'machine_model_no'=>$this->input->post('machineModel'),
// 				'cantilever'=>$this->input->post('cantilever'),
// 				'version'=>$newversion,
// 				'updatedOn'=>date('Y-m-d H:i:s'),
// 				'last_revision_date'=>date('Y-m-d'),
// 				'special_notes'=>$this->input->post('specialNotes'),
// 				'gst_actual'=>$gst_actual,
// 				'validity'=>$this->input->post('validity'),
// 				'updatedBy'=>$user_id);
// 			$this->db->where('lead_id',$this->uri->segment(3));
// 			$this->db->update('quotation_customer_data',$data);

// 			$recordid = $this->uri->segment(4);

// 			// $pouchsizew = $this->input->post('pouchsizew');
// 			// $pouchsizel = $this->input->post('pouchsizel');
// 			// $pouchsize = $pouchsizew." X ".$pouchsizel;
// 			$data1 = array(
// 				'product_to_be_packed'=>$this->input->post('producttobepacked'),
// 				'liquid_option'=>$this->input->post('liquid_option'),
// 				'powder_option'=>$this->input->post('powder_option'),
// 				 'non_viscous_option' => $this->input->post('non_viscous_option'),
//    				 'viscous_option' => $this->input->post('viscous_option'),
//    				 'piston_filler_option' => $this->input->post('piston_filler_option'),
//     			'follow_meter_option' => $this->input->post('follow_meter_option'),
//    				 'free_flow_option' => $this->input->post('free_flow_option'),
//    				 'weigher_system_option' => $this->input->post('weigher_system_option'),
//    				 'liner_weigher_option' => $this->input->post('liner_weigher_option'),
//    				 'mult_head_weigher_option' => $this->input->post('mult_head_weigher_option'),
//   				  'volumetric_cap_option' => $this->input->post('volumetric_cap_option'),
//   				  'non_free_flow_option' => $this->input->post('non_free_flow_option'),
// 				'product_name'=>$this->input->post('productname'),
// 				// 'pouch_size_type'=>$pouchsize,
// 				// 'qty_to_be_packed'=>$this->input->post('qtytobepacked')." ".$this->input->post('qty_unit'),
// 				'horizontal_sealing_width'=>$this->input->post('horizontalsealingwidth'),
// 				'vertical_sealing_width'=>$this->input->post('verticalsealingwidth'),
// 				'perforation_pitch'=>$this->input->post('perforationpitch'),   
// 				'perforationstyle'=>$this->input->post('perforationstyle'),
// 				'batchcut'=>$this->input->post('batchcut'),
// 				'typeofsealing'=>$this->input->post('typeofsealing'),
// 				// 'plc_make'=>$this->input->post('plcmake'),
// 				'power_supply'=>$this->input->post('powersupply'),
// 				'liquidviscositydata'=>$this->input->post('liquidviscositydata'),
// 				'liquidconductivitydata'=>$this->input->post('liquidconductivitydata'),
// 				'powderdensitydata'=>$this->input->post('powderdensitydata'),
// 				'powderdfrdata'=>$this->input->post('powderdfrdata'),
// 				'powdermoisturecontentdata'=>$this->input->post('powdermoisturecontentdata'),
// 				'machine_type'=>$this->input->post('machtype')
// 				);
// 			$this->db->where('record_id',$recordid);
// 			$this->db->update('quotation_annexture_1',$data1);

// 			/** EXISTING PRODUCT TO BE PACKED**/
// 			for($i=0;$i<count($this->input->post('pack_id'));$i++)
// 			{
// 				$pckid=$this->input->post('pack_id')[$i];
// 				$packedqty=$this->input->post('editqtytobepacked'.$pckid);
// 				$unit=$this->input->post('editqty_unit'.$pckid);
// 				$length=$this->input->post('editpouchsizel'.$pckid);
// 				$width=$this->input->post('editpouchsizew'.$pckid);
// 				$height=$this->input->post('editpouchsizeh'.$pckid);
// 				if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName')=="HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName')=="HFFS MULTI TRACK MACHINE" || $this->input->post('machineName')=="HFFS PICK FILL SEAL")
// 				{
// 				$gusset=$this->input->post('editgusset'.$pckid);
// 				$punchhole=$this->input->post('editpunch_hole'.$pckid);
// 				}else
// 				{
// 				$gusset='';
// 				$punchhole='';
// 				}

// 			$data=array('packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
// 			$this->db->where('id',$pckid);
// 			$this->db->update('quotation_pouch_size',$data);
// 			}


// 			/** ADD QTY TO BE PACKED & POUCH SIZE **/
// 			if($this->input->post('addMorePRDPacked')==1)
// 			{
// 			$qtytobepacked=$this->input->post('qtytobepacked');
// 			for($u=0;$u<count($qtytobepacked);$u++)
// 			{
// 			$packedqty=$qtytobepacked[$u];
// 			$unit=$this->input->post('qty_unit')[$u];
// 			$length=$this->input->post('pouchsizel')[$u];
// 			$width=$this->input->post('pouchsizew')[$u];
// 			$height=$this->input->post('pouchsizeh')[$u];
// 			if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE")
// 			{
// 			$gusset=$this->input->post('gusset')[$u];
// 			$punchhole=$this->input->post('punch_hole')[$u];
// 			}else
// 			{
// 			$gusset='';
// 			$punchhole='';
// 			}


// 			$data=array('record_id'=>$recordid,'packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
// 			$this->db->insert('quotation_pouch_size',$data);
// 			}
// 			}
// 			/** END **/



// 			$data2 = array(
// 				'model'=>$this->input->post('machinemodelno'),'flag'=>1);
// 			$this->db->where('record_id',$recordid);
// 			$this->db->where('flag',1);
// 			$this->db->update('quotation_cum_tech_spec',$data2);

// 			/** UPDATE PREVIOUS TECH SPEC**/
// 			$technicalDataid=$this->input->post('technicalDataid');
// 			if(count($technicalDataid)>0)
// 			{
// 				for($y=0;$y<count($technicalDataid);$y++)
// 				{
// 					$dmodel=$this->input->post('technicalspecedit'.$technicalDataid[$y]);
// 					$data21 = array('model'=>$dmodel);
// 					$this->db->where('id',$technicalDataid[$y]);
// 					$this->db->update('quotation_cum_tech_spec',$data21);

// 				}
// 			}
// 			/* END **/


// 			/*Add Multiple Tech Spec*/

// 			if(isset($_REQUEST['technicalspec'])){	
				
// 					$tags1=count($_REQUEST['technicalspec']);
// 					if($tags1>0)
// 					{
// 					$technicalspec = $_REQUEST['technicalspec'];
// 					$i=1;
// 					for($x=0;$x<$tags1;$x++){
// 					if($technicalspec[$x]!='')
// 						{
// 					$data21 = array('record_id'=>$recordid,
// 					'model'=>$technicalspec[$x],
// 					'added_on'=>date('Y-m-d H:i:s'),
// 					'added_by'=>$user_id);
// 					$this->db->insert('quotation_cum_tech_spec',$data21);
							
// 						}
// 					$i++;	
// 					}
// 					}
// 					}
// 			/*Add Multiple Tech Spec*/



// 			/** UPDATE PREVIOUS TECH SPEC**/
// 			$axisid=$this->input->post('axis_id');
// 			if(count($axisid)>0)
// 			{
// 				for($y=0;$y<count($axisid);$y++)
// 				{
// 					$noofaxisinmachineinputedit=$this->input->post('editnoofaxisinmachine'.$axisid[$y]);
// 					$editnoofaxisincount=$this->input->post('editnoofaxisincount'.$axisid[$y]);
// 					$data21 = array('description'=>$noofaxisinmachineinputedit,'axiscount'=>$editnoofaxisincount);
// 					$this->db->where('id',$axisid[$y]);
// 					$this->db->update('quotation_no_of_axis_in_machine',$data21);

// 				}
// 			}
// 			/* END **/



// 			/*Add Multiple Axis Machine Input*/
// 			if($this->input->post('more_axis')==1)
// 			{
// 			if(isset($_REQUEST['noofaxisinmachine'])){	
// 					$tags1=count($_REQUEST['noofaxisinmachine']);
					
// 					if($tags1>0)
// 					{
// 					$technicalspecs = $_REQUEST['noofaxisinmachine'];
// 					$noofaxisincount = $_REQUEST['noofaxisincount'];
// 					$i=1;
// 					for($x=0;$x<$tags1;$x++){
// 					if($technicalspecs[$x]!='')
// 					{
// 					$data22 = array('record_id'=>$recordid,
// 					'description'=>$technicalspecs[$x],
// 					'axiscount'=>$noofaxisincount[$x],
// 					'added_on'=>date('Y-m-d H:i:s'),
// 					'added_by'=>$user_id);
// 					$this->db->insert('quotation_no_of_axis_in_machine',$data22);
							
// 					}
// 					$i++;	
// 					}
// 					}
// 					}
// 			}
// 			/*Add Multiple Axis Machine Input*/



// 			$laminatewidth = $this->input->post('laminatewidth')."<br>";
// 			$laminatereeldia = $this->input->post('laminatereeldia')."<br>";
// 			$laminatereelcoredia = $this->input->post('laminatereelcoredia')."<br>";
// 			$laminatewidthcoredia = $laminatewidth." ".$laminatereeldia." ".$laminatereelcoredia;
// 			// $pouchsizew1 = $this->input->post('pouchsizew1');
// 			// $pouchsizel1 = $this->input->post('pouchsizel1');
// 			// $pouchsizeh1 = $this->input->post('pouchsizeh1');
// 			// $secondpouchsize = $pouchsizew1." X ".$pouchsizel1." X ".$pouchsizeh1;
// 			$layoutdimensionslength = $this->input->post('layoutdimensionslength');
// 			$layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
// 			$layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
// 			$layoutdimensionsof = $layoutdimensionslength."<br>".$layoutdimensionswidth."<br>".$layoutdimensionsheight."<br>";

// 			$data3 = array(
// 				'machinemodel'=>$this->input->post('machinemodel'),
// 				'filling_accuracy'=>$this->input->post('fillingaccuracy'),
// 				 'sealingstyle'=>$this->input->post('sealingstyle'),
// 				'speed'=>$this->input->post('designspeed'),
// 				'actual_speed'=>$this->input->post('actualspeed'),
// 				'no_of_track'=>$this->input->post('nooftracks'),
// 				'leminate_specification'=>$laminatewidth,
// 				'laminatereeldia'=>$laminatereeldia,
// 				'laminatereelcoredia'=>$laminatereelcoredia ,
// 				'product_to_be_packed'=>$this->input->post('product_tobepacked'),
// 				'filling_capacity'=>$this->input->post('fillingcapacity'),
// 				// 'pouch_size'=>$secondpouchsize,
// 				'electrical_spec'=>$this->input->post('electricalspec'),
// 				'layout_dimensions'=>$layoutdimensionsof,
// 				'machine_weight'=>$this->input->post('netweight'),
// 				'gross_weight'=>$this->input->post('grossweight'),
// 				'compressed_air'=>$this->input->post('compressedaircfa'),
// 				'compressedairbar'=>$this->input->post('compressedairbar'));

// 			$this->db->where('record_id',$recordid);
// 			$this->db->update('quotation_annexure_2',$data3);



// 			$data4 = array(
// 			'description'=>$this->input->post('modelno'),
// 			'hsn'=>$this->input->post('modelhsn'),
// 			'unit'=>$this->input->post('qtyunit'),
// 			'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
// 			'qty'=>$this->input->post('modelqty'),
// 			'price'=>$this->input->post('modelprice'));
// 			$this->db->where('record_id',$recordid);
// 			$this->db->where('flag',0);
// 			$this->db->update('quotation_annexture_4',$data4);


// 			// Check if the checkbox is set (checked)
// 			if ($this->input->post('machinefillingsystem')!='') {
// 			// Get the value of the checkbox
// 			$checkboxValue = $this->input->post('machinefillingsystem');
// 			$photo=$_FILES['machinefillingimg']['name'];
// 			if($photo<>'')
// 			{
// 			$image1=explode('.',$photo);
// 			$cat_image=end($image1);
// 			$machineimage=time().'.'.$cat_image;
// 			move_uploaded_file($_FILES['machinefillingimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
// 			$data = array('record_id'=>$recordid,
// 			'image'=>$machineimage,
// 			'added_on'=>date('Y-m-d H:i:s'),
// 			'added_by'=>$user_id);
// 			$this->db->insert('quotation_machine_filling_image',$data);	
// 			}else
// 			{
// 			$machineimage="";
// 			}
// 			}else{
// 			$this->db->where('record_id',$recordid);
// 			$this->db->delete('quotation_machine_filling_image');					
// 			}
			

// 			// Check if the checkbox is set (checked)
// 			if ($this->input->post('kld')==1) {
// 			// Get the value of the checkbox
// 			$photo=$_FILES['kldimg']['name'];
// 			if($photo<>'')
// 			{
// 			$image1=explode('.',$photo);
// 			$cat_image=end($image1);
// 			$machineimage=time().'1234.'.$cat_image;
// 			move_uploaded_file($_FILES['kldimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
// 			$data = array('record_id'=>$recordid,
// 			'image'=>$machineimage,
// 			'added_on'=>date('Y-m-d H:i:s'),
// 			'added_by'=>$user_id);
// 			$this->db->insert('quotation_kld_image',$data);	
// 			}else
// 			{
// 			$machineimage="";
// 			}
// 			}else{
// 			$this->db->where('record_id',$recordid);
// 			$this->db->delete('quotation_kld_image');					
// 			}


// 			/** BRAND DATA **/

// 			for($i=0;$i<count($this->input->post('brands'));$i++)
// 			{
// 				$brand_id=$this->input->post('brands')[$i];
// 				$brand_data=$this->input->post('brand_data')[$i];

// 				$resty=$this->db->select('id')->from('quotation_brand_data')->where('record_id',$recordid)->where('head_id',$brand_id)->get();
// 				if($resty->num_rows()==0)
// 				{
// 					$data=array('record_id'=>$recordid,'head_id'=>$brand_id,'value_id'=>$brand_data,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
// 					$this->db->insert('quotation_brand_data',$data);
// 				}else
// 				{
// 					foreach($resty->result() as $row)
// 					{
// 						$id=$row->id;
// 						$data=array('value_id'=>$brand_data);
// 						$this->db->where('record_id',$recordid);
// 						$this->db->where('id',$id);
// 						$this->db->update('quotation_brand_data',$data);
// 					}
// 				}

// 			}

// 			/** END **/

// 			/*Machine price section*/
// 			$grandtotalprice = $this->input->post('modelqty')*$this->input->post('modelprice');
// 			$machinepricedata = array(
// 			'machine_model'=>$this->input->post('modelno'),
// 			'qty'=>$this->input->post('modelqty'),
// 			'unit_price'=>$this->input->post('modelprice'),
// 			'totalprice'=>$grandtotalprice);
// 			$this->db->where('record_id',$recordid);
// 			$this->db->update('quotation_machine_price_info',$machinepricedata);
// 			/*Machine price section*/


// 			/*Quotation Freight Info*/
// 		;
// 			if ($this->input->post('packagingapplicable')!='') {
// 				$packingingcharge = $this->input->post('packagingpercentage');
				
// 			}else{
// 				$packingingcharge = "";
// 			}

// 			if ($this->input->post('forwardingapplicable')!='') {
// 				$forwardingcharges = $this->input->post('forwardingpercentage');
// 			}else{
// 				$forwardingcharges = "";
// 			}
// 			if ($this->input->post('insuranceapplicable')!='') {
// 				$insurancecharges = $this->input->post('insurancepercentage');
// 			}else{
// 				$insurancecharges = "";
// 			}
// 			if($this->input->post('frightinfo')==3 || $this->input->post('frightinfo')==4){
// 				$freight_charges = $this->input->post('freightamount');
// 			}else{
// 				$freight_charges = "";
// 			}

// 			$freight_type=$this->input->post('freightType');
// 			$port_id = 0;
// 			if($freight_type=="FOB" || $freight_type=="CIF" || $freight_type=="CFR")
// 			{
// 				$port=$this->input->post('port');
// 				if (is_numeric($port) && !strpos($port, '.')) {

// 					$port_id=$port;
// 				}else
// 				{
// 					$dt=array('name'=>$port);
// 					$this->db->insert('ports',$dt);
// 					$port_id=$this->db->insert_id();
// 				}


// 			}


// 			if ($this->input->post('installationcommissioningapplicable')!='') {
// 				$installationcommissioningamount = $this->input->post('installationcommissioningamount');
// 			}else{
// 				$installationcommissioningamount = "";
// 			}



// 			$rswqs=$this->db->select('id')->from('quotation_freight_packing_forwarding')->where('record_id',$recordid)->get();
// 			if($rswqs->num_rows()>0)
// 			{
// 			$freightdata = array(
// 			'freight'=>$this->input->post('frightinfo'),
// 			'freight_charges'=>$freight_charges,
// 			'installation_charges'=>$installationcommissioningamount,
// 			'freight_type'=>$freight_type,
// 			'packing_charges'=>$packingingcharge,
// 			'forwarding_charges'=>$forwardingcharges,
// 			'port'=>$port_id,
// 			'insurance'=>$insurancecharges);
// 			$this->db->where('record_id',$recordid);
// 			$this->db->update('quotation_freight_packing_forwarding',$freightdata);
// 			}else
// 			{
// 				$freightdata = array(
// 			'record_id'=>$recordid,
// 			'freight'=>$this->input->post('frightinfo'),
// 			'freight_charges'=>$freight_charges,
// 			'installation_charges'=>$installationcommissioningamount,
// 			'freight_type'=>$freight_type,
// 			'packing_charges'=>$packingingcharge,
// 			'forwarding_charges'=>$forwardingcharges,
// 			'port'=>$port_id,
// 			'insurance'=>$insurancecharges);
			
// 			$this->db->insert('quotation_freight_packing_forwarding',$freightdata);
// 			}
// 			/*Quotation Freight Info*/


// 			// /** EDIT OPTION DATA **/
// 			// 	$existing_optional=$this->input->post('existing_optional');
// 			// 	if(count($existing_optional)>0)
// 			// 	{
// 			// 	for($y=0;$y<count($existing_optional);$y++)
// 			// 	{
// 			// 	$techdescriptioninfo=$this->input->post('techdescriptioninfo'.$existing_optional[$y]);
// 			// 	$techdescqty=$this->input->post('techdescqty'.$existing_optional[$y]);
// 			// 	$techdescprice=$this->input->post('techdescprice'.$existing_optional[$y]);
// 			// 	$techdesctotalprice=$this->input->post('techdesctotalprice'.$existing_optional[$y]);
// 			// 	$totalprice = $techdescqty*$techdescprice;
// 			// 	$data4 = array(
// 			// 	'description'=>$techdescriptioninfo,
// 			// 	'qty'=>$techdescqty,
// 			// 	'price'=>$techdescprice,
// 			// 	'total_price'=>$totalprice);
// 			// 	$this->db->where('id',$existing_optional[$y]);
// 			// 	$this->db->update('quotation_annexture_4',$data4);
// 			// 	}
// 			// 	}

// 				/** END **/

// 				// NEED TO START FROM HERE 

// 				$existing_optional=$this->input->post('existing_optional');

// 				for($r=0;$r<count($existing_optional);$r++)
// 				{
// 					$id=$existing_optional[$r];
// 					$techdescriptioninfo=$this->input->post('edittechdescriptioninfo'.$id);
// 					$techdescqty=$this->input->post('edittechdescqty'.$id);
// 					$techdescprice=$this->input->post('edittechdescprice'.$id);
// 					$edittechhsn=$this->input->post('edittechhsn'.$id);
// 					$edittechunit=$this->input->post('edittechunit'.$id);

// 					$totalprice = $techdescqty*$techdescprice;
// 							$data4 = array(
// 							'description'=>$techdescriptioninfo,
// 							'hsn'=>$edittechhsn,
// 							'unit'=>$edittechunit,
// 							'qty'=>$techdescqty,
// 							'price'=>$techdescprice,
// 							'total_price'=>$totalprice);
// 							$this->db->where('id',$id);
// 							$this->db->update('quotation_annexture_4',$data4);

// 				}

// 				/** END EXISTING **/




// 			/*Add Technical Charges OPTIONAL */
// 			if(isset($_REQUEST['techdescriptioninfo'])){	
// 					$tags1=count($_REQUEST['techdescriptioninfo']);
// 					if($tags1>0)
// 					{
// 					$techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
// 					$techdescqty = $_REQUEST['techdescqty'];
// 					$techdescprice = $_REQUEST['techdescprice'];
// 					$techdesctotalprice = $_REQUEST['techdesctotalprice'];
// 					$techsn = $_REQUEST['techsn'];
// 					$techunit = $_REQUEST['techunit'];
// 					$i=1;
// 					for($x=0;$x<$tags1;$x++){
// 					if($techdescriptioninfo[$x]!='')
// 						{

// 						if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {

// 						$techdescitemid = $techdescriptioninfo[$x];
// 						} else {
// 						$datass = array('instruments_name'=>$techdescriptioninfo[$x],'type'=>1,'status'=>1);
// 						$this->db->insert('presto_instruments',$datass);
// 						$techdescitemid = $this->db->insert_id();
// 						}



// 							$totalprice = $techdescqty[$x]*$techdescprice[$x];
// 							$data4 = array('record_id'=>$recordid,
// 							'description'=>$techdescitemid,
// 							'qty'=>$techdescqty[$x],
// 							'price'=>$techdescprice[$x],
// 							'hsn'=>$techsn[$x],
// 							'unit'=>$techunit[$x],
// 							'total_price'=>$totalprice,
// 							'addedOn'=>date('Y-m-d H:i:s'),
// 							'flag'=>$i,
// 							'category_type'=>1,
// 							'addedBy'=>$user_id);
// 							$this->db->insert('quotation_annexture_4',$data4);
							
// 						}
// 					$i++;	
// 					}
// 					}
// 					}

// 					/* end **/


// 					/*check discount if applicable*/
// 					$discountapplicable = $this->input->post('discountapplicable');
					
// 					//echo $discounttype; exit;
// 					if($discountapplicable==1){
// 						$discunttype = $this->input->post('discunttype');
// 						$discount = '';
// 						if($discunttype==1){
// 							$discount = $this->input->post('discountinpercent');
// 						}else{
// 							$discount = $this->input->post('discountinamount');
// 						}
						
// 						$q = $this->db->select('id')->from('quotation_discount_data')->where('record_id',$recordid)->get();
// 						if($q->num_rows()>0){
// 							foreach($q->result() as $existingdiscount);
// 							$discountdata= array('discount_type'=>$discunttype,
// 								'discountvalue'=>$discount,
// 								'added_on'=>date('Y-m-d H:i:s'),
// 								'added_by'=>$user_id);
// 							$this->db->where('id',$existingdiscount->id);
// 							$this->db->update('quotation_discount_data',$discountdata);
// 						}else{
// 							$discountdata= array(
// 								'record_id'=>$recordid,
// 								'discount_type'=>$discunttype,
// 								'discountvalue'=>$discount,
// 								'added_on'=>date('Y-m-d H:i:s'),
// 								'added_by'=>$user_id);
// 							$this->db->insert('quotation_discount_data',$discountdata);

// 						}
// 					}else{
// 						$this->db->where('record_id',$recordid);
// 						$this->db->delete('quotation_discount_data');
// 					}
// 					/*check discount if applicable*/

// 					/** OPTIONAL DATA ADD  EXIST**/
// 					/** EXISTING **/
// 					$optionaldatainfo=$this->input->post('optionaldatainfo');
// 					for($r=0;$r<count($optionaldatainfo);$r++)
// 					{
// 					$id=$optionaldatainfo[$r];
// 					$optionalitem=$this->input->post('optionalitem'.$id);
// 					$optionalqty=$this->input->post('optionalqty'.$id);
// 					$optionalprice=$this->input->post('optionalprice'.$id);
// 					if(is_numeric($optionalitem)){
// 					$itemid = $optionalitem;
// 					}else{
// 					$datass = array('instruments_name'=>$optionalitem);
// 					$this->db->insert('presto_instruments',$datass);
// 					$itemid = $this->db->insert_id();
// 					}
// 					$totalprice = $optionalqty*$optionalprice;
// 					$data=array(
// 					'description'=>$itemid,
// 					'value'=>$optionalqty,
// 					'price'=>$optionalprice,
// 					'totalprice'=>$totalprice);
// 					$this->db->where('id',$id);
// 					$this->db->update('quotation_optional',$data);
// 					}
// 					/** END **/
			

// 					if(isset($_REQUEST['optionalitem'])){	
// 					$tags1=count($_REQUEST['optionalitem']);
// 					if($tags1>0)
// 					{
// 					$optionalitem = $_REQUEST['optionalitem'];
// 					$optionalqty=$_REQUEST['optionalqty'];
// 					$optionalprice=$_REQUEST['optionalprice'];
					
// 					$i=1;
// 					for($x=0;$x<$tags1;$x++){
// 					if($optionalitem[$x]!='')
// 						{
// 							if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
// 							$itemid = $optionalitem[$x];
// 							} else {
// 							$datass = array('instruments_name'=>$optionalitem[$x],'type'=>1,
// 							'status'=>1);
// 							$this->db->insert('presto_instruments',$datass);
// 							$itemid = $this->db->insert_id();
// 							}		

// 							$totalprice = $optionalqty[$x]*$optionalprice[$x];
// 							$data=array('record_id'=>$recordid,
// 							'description'=>$itemid,
// 							'value'=>$optionalqty[$x],
// 							'price'=>$optionalprice[$x],
// 							'totalprice'=>$totalprice,
// 							'addedBy'=>$user_id,
// 							'addedOn'=>date('Y-m-d H:i:s'));
// 							$this->db->insert('quotation_optional',$data);
							
// 						}
// 					$i++;	
// 					}
// 					}
// 					}

// 					/** end **/

// 			if ($this->input->post('consumablespare')!='') {
// 				$consumablespare = $this->input->post('consumablespare');
// 			}else{
// 				$consumablespare = 0;
// 			}

// 			$restyu=$this->db->select('id')->from('quotation_other_information')->where('record_id',$recordid)->get();
// 			if($restyu->num_rows()>0)
// 			{
// 			$data13 = array(
// 				'terms_value'=>$this->input->post('paymentterms'),
// 				'delivery_value'=>$this->input->post('delivery'),
// 				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
// 				'addedBy'=>$user_id,
// 				'consumable_spare'=>$consumablespare);
// 			$this->db->where('record_id',$recordid);
// 			$this->db->update('quotation_other_information',$data13);
// 			}else
// 			{
// 				$data13 = array(
// 					'record_id'=>$recordid,
// 				'terms_value'=>$this->input->post('paymentterms'),
// 				'delivery_value'=>$this->input->post('delivery'),
// 				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
// 				'addedBy'=>$user_id,
// 				'consumable_spare'=>$consumablespare);
			
// 			$this->db->insert('quotation_other_information',$data13);
// 			}


// 			// Check if the checkbox is set (checked)
// 			if ($this->input->post('layoutapplicable')!='') {
// 			// Get the value of the checkbox
			
// 			$photo1=$_FILES['uploadlayout']['name'];
// 			if($photo1<>'')
// 			{
// 			$image2=explode('.',$photo1);
// 			$cat_image1=end($image2);
// 			$layoutimage=time().'.'.$cat_image1;
// 			move_uploaded_file($_FILES['uploadlayout']["tmp_name"],UPLOADPATH.'opportunitydocs/layoutimg/' . $layoutimage);
// 			$data = array('record_id'=>$recordid,
// 				'image'=>$layoutimage,
// 				'added_on'=>date('Y-m-d H:i:s'),
// 				'added_by'=>$user_id);
// 			$this->db->insert('quotation_layout_img',$data);
// 			}else
// 			{
// 			$layoutimage="";
// 			}		

// 			} else {
				
// 				$this->db->where('record_id',$recordid);
// 				$this->db->delete('quotation_layout_img');
// 			}


// 			if($consumablespare!=0)
// 			{
// 			if($this->input->post('consumalbleNew')==0)
// 			{
// 				$consumanbleid=$this->input->post('consumanbleid');
// 				for($r=0;$r<count($consumanbleid);$r++)
// 				{
// 					$id=$consumanbleid[$r];
// 					$spareqty=$this->input->post('spareqty'.$id);
// 					$spareprice=$this->input->post('spareprice'.$id);
// 					$spareFile_old=$this->input->post('spareFile_old'.$id);
// 					/** MEDIA **/
// 					$photo1=$_FILES['spareFile'.$id]['name'];
// 					if($photo1<>'')
// 					{
// 					$image2=explode('.',$photo1);
// 					$cat_image1=end($image2);
// 					$spareFiles=time().'.'.$cat_image1;
// 					move_uploaded_file($_FILES['spareFile'.$id]["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
// 					}else
// 					{
// 					$spareFiles=$spareFile_old;
// 					}

// 					/** END **/
// 					$data=array('qty'=>$spareqty,
// 						'media'=>$spareFiles,
// 					'price'=>$spareprice);
// 					$this->db->where('id',$id);
// 					$this->db->update('quotation_consumable_spares',$data);

// 				}
// 			}else
// 			{

// 					if(isset($_REQUEST['spareqty'])){	
// 					$tags1=count($_REQUEST['spareqty']);
// 					if($tags1>0)
// 					{
// 					//$partname = $_REQUEST['partname'];
// 					$spareqty=$_REQUEST['spareqty'];
// 					$spareprice=$_REQUEST['spareprice'];

// 					$i=1;
// 					for($x=0;$x<$tags1;$x++){
// 					// if($partname[$x]!='')
// 					// 	{
// 					/** MEDIA **/
// 					$photo1=$_FILES['spareFile']['name'];
// 					if($photo1<>'')
// 					{
// 					$image2=explode('.',$photo1);
// 					$cat_image1=end($image2);
// 					$spareFiles=time().'.'.$cat_image1;
// 					move_uploaded_file($_FILES['spareFile']["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
// 					}else
// 					{
// 					$spareFiles=$this->input->post('spareFile_old');
// 					}
// 					/** END **/
// 					$data=array('record_id'=>$recordid,
// 					'qty'=>$spareqty[$x],
// 					'price'=>$spareprice[$x],
// 					'added_by'=>$user_id,
// 					'media'=>$spareFiles,
// 					'added_on'=>date('Y-m-d H:i:s'));
// 					$this->db->insert('quotation_consumable_spares',$data);

// 					//}
// 					$i++;	
// 					}
// 					}
// 					}



// 			}
// 		}else
// 		{

// 			$this->db->where('record_id',$recordid);
// 			$this->db->delete('quotation_consumable_spares');	
// 		}


// 		// if ($this->input->post('consumablespare')!='') {
// 		// 	/*Consumable Spares*/

// 		// 	if(isset($_REQUEST['spareqty'])){	
// 		// 			$tags1=count($_REQUEST['spareqty']);
// 		// 			if($tags1>0)
// 		// 			{
// 		// 			//$partname = $_REQUEST['partname'];
// 		// 			$spareqty=$_REQUEST['spareqty'];
// 		// 			$spareprice=$_REQUEST['spareprice'];
					
// 		// 			$i=1;
// 		// 			for($x=0;$x<$tags1;$x++){
// 		// 			// if($partname[$x]!='')
// 		// 			// 	{
// 		// 					$data=array('record_id'=>$recordid,
// 		// 					'qty'=>$spareqty[$x],
// 		// 					'price'=>$spareprice[$x],
// 		// 					'added_by'=>$user_id,
// 		// 					'added_on'=>date('Y-m-d H:i:s'));
// 		// 					$this->db->insert('quotation_consumable_spares',$data);
							
// 		// 				//}
// 		// 			$i++;	
// 		// 			}
// 		// 			}
// 		// 			}


// 		// 	/*Consumable Spares*/
// 		// }


// 			/** UPDATE TERMS & CONDITIONS **/
// 		if($this->input->post('country')==101)
// 		{

// 		$liquidated_applicable = $this->input->post('liquidated_clause_applicable');
// 		if($liquidated_applicable==1)
// 		{
// 			$liquidated_applicable=1;
// 		}else
// 		{
// 			$liquidated_applicable=0;
// 		}


// 		$late_delivery_applicable = $this->input->post('late_delivery_applicable');
// 		if($late_delivery_applicable==1)
// 		{
// 			$late_delivery_applicable=1;
// 		}else
// 		{
// 			$late_delivery_applicable=0;
// 		}

// 		// get and xss_clean content fields (optional: keep html if you use editors)
// 		$liquidated_text  = $this->input->post('liquidated_clause');
// 		$packing_charges  = $this->input->post('packing_charges');
// 		$insurance        = $this->input->post('insurance');
// 		$late_delivery_text  = $this->input->post('late_delivery');
// 		$installation     = $this->input->post('installation');

// 		$payload = [
		
// 		'liquidated_applicable' => $liquidated_applicable,
// 		'liquidated_text' => $liquidated_text ?: null,
// 		'packing_charges' => $packing_charges ?: null,
// 		'late_delivery_applicable'=>$late_delivery_applicable,
// 		'late_delivery_text'=>$late_delivery_text ? : null,
// 		'insurance' => $insurance ?: null,
// 		'installation' => $installation ?: null
// 		];

// 		$this->db->where('quotation_id',$recordid);
// 		$this->db->update('quotation_custom_terms',$payload);
// 		}else
// 		{
// 			$this->db->where('quotation_id',$recordid);
// 			$this->db->delete('quotation_custom_terms');
// 		}





// 			/** UPDATE LEAD STATUS **/

// 			$d=array(
// 					'lead_id'=>$this->uri->segment(3),
// 					'lead_status'=>39,
// 					'next_follow_date'=> date('Y-m-d', strtotime("+1 day")),
// 					'added_on'=>date('Y-m-d H:i:s'),
// 					'added_by'=>$_SESSION['logged_in']['user_id']
// 				);
// 			$this->db->insert('progress_remarks',$d);


// 			$reasondata = array('lead_id'=>$this->uri->segment(3),
// 			'reason'=>$this->input->post('reasonforchange'),
// 			'added_on'=>date('Y-m-d H:i:s'),
// 			'added_by'=>$_SESSION['logged_in']['user_id']);
// 			$this->db->insert('quotation_change_reason',$reasondata);


//  				redirect(page_url.'Opportunity/GeneratedQuote/'.$recordid."/".$this->uri->segment(4));
					
// // $this->session->set_flashdata('message','<div style="color:red;" class="alert alert-success">Thank you, Your Quote Updated.</div>');

// // 			redirect(page_url.'Opportunity/edit_opportunity/'.$this->uri->segment(3));

// 		}

		
// }


function Update_newopportunity()
{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
		$this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
		$this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
		$this->form_validation->set_rules('cur', 'Currency', 'required|trim');
		$this->form_validation->set_rules('country', 'Country', 'required|trim');
		$this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
		$this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
		/*Validation till general information*/
		$this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		$this->form_validation->set_rules('machtype', 'Machine Type', 'required|trim');
		//$this->form_validation->set_rules('orientation', 'orientation', 'required|trim');
		// $this->form_validation->set_rules('pouchsizew', 'Pouch Size W', 'required|trim');
		// $this->form_validation->set_rules('pouchsizel', 'Pouch Size L', 'required|trim');
		// $this->form_validation->set_rules('qtytobepacked', 'Qty to be Packed', 'required|trim');
		// $this->form_validation->set_rules('qty_unit', 'Qty Unit', 'required|trim');
		$this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
		$this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
		$this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
		$this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
		// $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
		$this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');


		$this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
		// $this->form_validation->set_rules('noofaxisinmachine', 'No of Axis in Machine', 'required|trim');
		$this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
		//$this->form_validation->set_rules('sealingstyle', 'Sealing Style', 'required|trim');
		$this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
		$this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
		$this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
		$this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
		$this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
		$this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
		$this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
		$this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
		$this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
		// $this->form_validation->set_rules('pouchsizew1', 'Pouch Size W', 'required|trim');
		// $this->form_validation->set_rules('pouchsizel1', 'Pouch Size L', 'required|trim');
		// $this->form_validation->set_rules('pouchsizeh1', 'Pouch Size H', 'required|trim');
		$this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
		$this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
		$this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
		$this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
		$this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
		$this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
		$this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
		$this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
		$this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');
		
		$this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
		$this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
		$this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');
		// $this->form_validation->set_rules('followDate', 'Next Followup Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('opportunity/edit_opportunity_quote');
		}
		else
		{
			$rest=$this->db->select('product_id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$product_id=$row->product_id;
			}else
			{
				echo "Product Not Found"; exit;
			}
			if($this->uri->segment(5)==1){
				$version=$this->getCurrentVersion($this->uri->segment(3));
				$newversion = $version+1;
			}else{
				$newversion=$this->getCurrentVersion($this->uri->segment(3));
			}
			$version=$this->getCurrentVersion($this->uri->segment(3));

			$gst_actual=0;
			if($this->input->post('country')==101)
			{
			if($this->input->post('gstapplicable')==1)
			{
				$gst_actual=1;
			}
			}


			$data = array(
				'currency'=>$this->input->post('cur'),
				'country'=>$this->input->post('country'),
				'machine_name'=>$this->input->post('machineName'),
				'machine_model_no'=>$this->input->post('machineModel'),
				'cantilever'=>$this->input->post('cantilever'),
				'version'=>$newversion,
				'updatedOn'=>date('Y-m-d H:i:s'),
				'last_revision_date'=>date('Y-m-d'),
				'special_notes'=>$this->input->post('specialNotes'),
				'gst_actual'=>$gst_actual,
				'validity'=>$this->input->post('validity'),
				'updatedBy'=>$user_id);
			$this->db->where('lead_id',$this->uri->segment(3));
			$this->db->update('quotation_customer_data',$data);

			$recordid = $this->uri->segment(4);

			// $pouchsizew = $this->input->post('pouchsizew');
			// $pouchsizel = $this->input->post('pouchsizel');
			// $pouchsize = $pouchsizew." X ".$pouchsizel;



			$product_to_be_packed = $this->input->post('producttobepacked');

$liquid_option           = $this->input->post('liquid_option');
$powder_option           = $this->input->post('powder_option');
$non_viscous_option      = $this->input->post('non_viscous_option');
$viscous_option          = $this->input->post('viscous_option');
$piston_filler_option    = $this->input->post('piston_filler_option');
$follow_meter_option     = $this->input->post('follow_meter_option');
$free_flow_option        = $this->input->post('free_flow_option');
$weigher_system_option   = $this->input->post('weigher_system_option');
$liner_weigher_option    = $this->input->post('liner_weigher_option');
$mult_head_weigher_option= $this->input->post('mult_head_weigher_option');
$volumetric_cap_option   = $this->input->post('volumetric_cap_option');
$non_free_flow_option    = $this->input->post('non_free_flow_option');


/*
|--------------------------------------------------------------------------
| Product Hierarchy
|--------------------------------------------------------------------------
*/

if($product_to_be_packed == 1) // Liquid
{
    // Powder branch not applicable
    $powder_option            = '';
    $free_flow_option         = '';
    $non_free_flow_option     = '';
    $weigher_system_option    = '';
    $liner_weigher_option     = '';
    $mult_head_weigher_option = '';
    $volumetric_cap_option    = '';

    if($liquid_option == 'Non-Viscous')
    {
        $viscous_option       = '';
        $piston_filler_option = '';
        $follow_meter_option  = '';
    }
    elseif($liquid_option == 'Viscous')
    {
        $non_viscous_option = '';

        if($viscous_option == 'Piston Filler')
        {
            $follow_meter_option = '';
        }
        elseif($viscous_option == 'Flow meter')
        {
            $piston_filler_option = '';
        }
        else
        {
            $piston_filler_option = '';
            $follow_meter_option  = '';
        }
    }
    else
    {
        $non_viscous_option = '';
        $viscous_option = '';
        $piston_filler_option = '';
        $follow_meter_option = '';
    }
}
else // Powder
{
    // Liquid branch not applicable
    $liquid_option        = '';
    $non_viscous_option   = '';
    $viscous_option       = '';
    $piston_filler_option = '';
    $follow_meter_option  = '';

    if($powder_option == 'Free Flow')
    {
        $non_free_flow_option = '';

        if($free_flow_option == 'Weigher System')
        {
            $volumetric_cap_option = '';

            if($weigher_system_option == 'Liner Weigher')
            {
                $mult_head_weigher_option = '';
            }
            elseif($weigher_system_option == 'Multi Head Weigher')
            {
                $liner_weigher_option = '';
            }
            else
            {
                $liner_weigher_option = '';
                $mult_head_weigher_option = '';
            }
        }
        elseif($free_flow_option == 'Volumetric Cup Filler')
        {
            $weigher_system_option    = '';
            $liner_weigher_option     = '';
            $mult_head_weigher_option = '';
        }
        else
        {
            $weigher_system_option    = '';
            $liner_weigher_option     = '';
            $mult_head_weigher_option = '';
            $volumetric_cap_option    = '';
        }
    }
    elseif($powder_option == 'Non Free Flow')
    {
        $free_flow_option         = '';
        $weigher_system_option    = '';
        $liner_weigher_option     = '';
        $mult_head_weigher_option = '';
        $volumetric_cap_option    = '';
    }
    else
    {
        $free_flow_option         = '';
        $non_free_flow_option     = '';
        $weigher_system_option    = '';
        $liner_weigher_option     = '';
        $mult_head_weigher_option = '';
        $volumetric_cap_option    = '';
    }
}


			$data1 = array(
				'product_to_be_packed'   => $product_to_be_packed,
'liquid_option'          => $liquid_option,
'powder_option'          => $powder_option,
'non_viscous_option'     => $non_viscous_option,
'viscous_option'         => $viscous_option,
'piston_filler_option'   => $piston_filler_option,
'follow_meter_option'    => $follow_meter_option,
'free_flow_option'       => $free_flow_option,
'weigher_system_option'  => $weigher_system_option,
'liner_weigher_option'   => $liner_weigher_option,
'mult_head_weigher_option'=> $mult_head_weigher_option,
'volumetric_cap_option'  => $volumetric_cap_option,
'non_free_flow_option'   => $non_free_flow_option,
				'product_name'=>$this->input->post('productname'),
				// 'pouch_size_type'=>$pouchsize,
				// 'qty_to_be_packed'=>$this->input->post('qtytobepacked')." ".$this->input->post('qty_unit'),
				'horizontal_sealing_width'=>$this->input->post('horizontalsealingwidth'),
				'vertical_sealing_width'=>$this->input->post('verticalsealingwidth'),
				'perforation_pitch'=>$this->input->post('perforationpitch'),   
				'perforationstyle'=>$this->input->post('perforationstyle'),
				'batchcut'=>$this->input->post('batchcut'),
				'typeofsealing'=>$this->input->post('typeofsealing'),
				// 'plc_make'=>$this->input->post('plcmake'),
				'power_supply'=>$this->input->post('powersupply'),
				'liquidviscositydata'=>$this->input->post('liquidviscositydata'),
				'liquidconductivitydata'=>$this->input->post('liquidconductivitydata'),
				'powderdensitydata'=>$this->input->post('powderdensitydata'),
				'powderdfrdata'=>$this->input->post('powderdfrdata'),
				'powdermoisturecontentdata'=>$this->input->post('powdermoisturecontentdata'),
				'machine_type'=>$this->input->post('machtype')
				);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_annexture_1',$data1);

			/** EXISTING PRODUCT TO BE PACKED**/
			for($i=0;$i<count($this->input->post('pack_id'));$i++)
			{
				$pckid=$this->input->post('pack_id')[$i];
				$packedqty=$this->input->post('editqtytobepacked'.$pckid);
				$unit=$this->input->post('editqty_unit'.$pckid);
				$length=$this->input->post('editpouchsizel'.$pckid);
				$width=$this->input->post('editpouchsizew'.$pckid);
				$height=$this->input->post('editpouchsizeh'.$pckid);
				if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName')=="HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName')=="HFFS MULTI TRACK MACHINE" || $this->input->post('machineName')=="HFFS PICK FILL SEAL")
				{
				$gusset=$this->input->post('editgusset'.$pckid);
				$punchhole=$this->input->post('editpunch_hole'.$pckid);
				}else
				{
				$gusset='';
				$punchhole='';
				}

			$data=array('packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
			$this->db->where('id',$pckid);
			$this->db->update('quotation_pouch_size',$data);
			}


			/** ADD QTY TO BE PACKED & POUCH SIZE **/
			if($this->input->post('addMorePRDPacked')==1)
			{
			$qtytobepacked=$this->input->post('qtytobepacked');
			for($u=0;$u<count($qtytobepacked);$u++)
			{
			$packedqty=$qtytobepacked[$u];
			$unit=$this->input->post('qty_unit')[$u];
			$length=$this->input->post('pouchsizel')[$u];
			$width=$this->input->post('pouchsizew')[$u];
			$height=$this->input->post('pouchsizeh')[$u];
			if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE")
			{
			$gusset=$this->input->post('gusset')[$u];
			$punchhole=$this->input->post('punch_hole')[$u];
			}else
			{
			$gusset='';
			$punchhole='';
			}


			$data=array('record_id'=>$recordid,'packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
			$this->db->insert('quotation_pouch_size',$data);
			}
			}
			/** END **/



			$data2 = array(
				'model'=>$this->input->post('machinemodelno'),'flag'=>1);
			$this->db->where('record_id',$recordid);
			$this->db->where('flag',1);
			$this->db->update('quotation_cum_tech_spec',$data2);

			/** UPDATE PREVIOUS TECH SPEC**/
			$technicalDataid=$this->input->post('technicalDataid');
			if(count($technicalDataid)>0)
			{
				for($y=0;$y<count($technicalDataid);$y++)
				{
					$dmodel=$this->input->post('technicalspecedit'.$technicalDataid[$y]);
					$data21 = array('model'=>$dmodel);
					$this->db->where('id',$technicalDataid[$y]);
					$this->db->update('quotation_cum_tech_spec',$data21);

				}
			}
			/* END **/


			/*Add Multiple Tech Spec*/

			if(isset($_REQUEST['technicalspec'])){	
				
					$tags1=count($_REQUEST['technicalspec']);
					if($tags1>0)
					{
					$technicalspec = $_REQUEST['technicalspec'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($technicalspec[$x]!='')
						{
					$data21 = array('record_id'=>$recordid,
					'model'=>$technicalspec[$x],
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
					$this->db->insert('quotation_cum_tech_spec',$data21);
							
						}
					$i++;	
					}
					}
					}
			/*Add Multiple Tech Spec*/



			/** UPDATE PREVIOUS TECH SPEC**/
			$axisid=$this->input->post('axis_id');
			if(count($axisid)>0)
			{
				for($y=0;$y<count($axisid);$y++)
				{
					$noofaxisinmachineinputedit=$this->input->post('editnoofaxisinmachine'.$axisid[$y]);
					$editnoofaxisincount=$this->input->post('editnoofaxisincount'.$axisid[$y]);
					$data21 = array('description'=>$noofaxisinmachineinputedit,'axiscount'=>$editnoofaxisincount);
					$this->db->where('id',$axisid[$y]);
					$this->db->update('quotation_no_of_axis_in_machine',$data21);

				}
			}
			/* END **/



			/*Add Multiple Axis Machine Input*/
			if($this->input->post('more_axis')==1)
			{
			if(isset($_REQUEST['noofaxisinmachine'])){	
					$tags1=count($_REQUEST['noofaxisinmachine']);
					
					if($tags1>0)
					{
					$technicalspecs = $_REQUEST['noofaxisinmachine'];
					$noofaxisincount = $_REQUEST['noofaxisincount'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($technicalspecs[$x]!='')
					{
					$data22 = array('record_id'=>$recordid,
					'description'=>$technicalspecs[$x],
					'axiscount'=>$noofaxisincount[$x],
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
					$this->db->insert('quotation_no_of_axis_in_machine',$data22);
							
					}
					$i++;	
					}
					}
					}
			}
			/*Add Multiple Axis Machine Input*/



			$laminatewidth = $this->input->post('laminatewidth')."<br>";
			$laminatereeldia = $this->input->post('laminatereeldia')."<br>";
			$laminatereelcoredia = $this->input->post('laminatereelcoredia')."<br>";
			$laminatewidthcoredia = $laminatewidth." ".$laminatereeldia." ".$laminatereelcoredia;
			// $pouchsizew1 = $this->input->post('pouchsizew1');
			// $pouchsizel1 = $this->input->post('pouchsizel1');
			// $pouchsizeh1 = $this->input->post('pouchsizeh1');
			// $secondpouchsize = $pouchsizew1." X ".$pouchsizel1." X ".$pouchsizeh1;
			$layoutdimensionslength = $this->input->post('layoutdimensionslength');
			$layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
			$layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
			$layoutdimensionsof = $layoutdimensionslength."<br>".$layoutdimensionswidth."<br>".$layoutdimensionsheight."<br>";

			$data3 = array(
				'machinemodel'=>$this->input->post('machinemodel'),
				'filling_accuracy'=>$this->input->post('fillingaccuracy'),
				 'sealingstyle'=>$this->input->post('sealingstyle'),
				'speed'=>$this->input->post('designspeed'),
				'actual_speed'=>$this->input->post('actualspeed'),
				'no_of_track'=>$this->input->post('nooftracks'),
				'leminate_specification'=>$laminatewidth,
				'laminatereeldia'=>$laminatereeldia,
				'laminatereelcoredia'=>$laminatereelcoredia ,
				'product_to_be_packed'=>$this->input->post('product_tobepacked'),
				'filling_capacity'=>$this->input->post('fillingcapacity'),
				// 'pouch_size'=>$secondpouchsize,
				'electrical_spec'=>$this->input->post('electricalspec'),
				'layout_dimensions'=>$layoutdimensionsof,
				'machine_weight'=>$this->input->post('netweight'),
				'gross_weight'=>$this->input->post('grossweight'),
				'compressed_air'=>$this->input->post('compressedaircfa'),
				'compressedairbar'=>$this->input->post('compressedairbar'));

			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_annexure_2',$data3);



			$data4 = array(
			'description'=>$this->input->post('modelno'),
			'hsn'=>$this->input->post('modelhsn'),
			'unit'=>$this->input->post('qtyunit'),
			'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
			'qty'=>$this->input->post('modelqty'),
			'price'=>$this->input->post('modelprice'));
			$this->db->where('record_id',$recordid);
			$this->db->where('flag',0);
			$this->db->update('quotation_annexture_4',$data4);


			// Check if the checkbox is set (checked)
			if ($this->input->post('machinefillingsystem')!='') {
			// Get the value of the checkbox
			$checkboxValue = $this->input->post('machinefillingsystem');
			$photo=$_FILES['machinefillingimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'.'.$cat_image;
			move_uploaded_file($_FILES['machinefillingimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			$data = array('record_id'=>$recordid,
			'image'=>$machineimage,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			$this->db->insert('quotation_machine_filling_image',$data);	
			}else
			{
			$machineimage="";
			}
			}else{
			$this->db->where('record_id',$recordid);
			$this->db->delete('quotation_machine_filling_image');					
			}
			

			// Check if the checkbox is set (checked)
			if ($this->input->post('kld')==1) {
			// Get the value of the checkbox
			$photo=$_FILES['kldimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'1234.'.$cat_image;
			move_uploaded_file($_FILES['kldimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			$data = array('record_id'=>$recordid,
			'image'=>$machineimage,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			$this->db->insert('quotation_kld_image',$data);	
			}else
			{
			$machineimage="";
			}
			}else{
			$this->db->where('record_id',$recordid);
			$this->db->delete('quotation_kld_image');					
			}


			/** BRAND DATA **/

			for($i=0;$i<count($this->input->post('brands'));$i++)
			{
				$brand_id=$this->input->post('brands')[$i];
				$brand_data=$this->input->post('brand_data')[$i];

				$resty=$this->db->select('id')->from('quotation_brand_data')->where('record_id',$recordid)->where('head_id',$brand_id)->get();
				if($resty->num_rows()==0)
				{
					$data=array('record_id'=>$recordid,'head_id'=>$brand_id,'value_id'=>$brand_data,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
					$this->db->insert('quotation_brand_data',$data);
				}else
				{
					foreach($resty->result() as $row)
					{
						$id=$row->id;
						$data=array('value_id'=>$brand_data);
						$this->db->where('record_id',$recordid);
						$this->db->where('id',$id);
						$this->db->update('quotation_brand_data',$data);
					}
				}

			}

			/** END **/

			/*Machine price section*/
			$grandtotalprice = $this->input->post('modelqty')*$this->input->post('modelprice');
			$machinepricedata = array(
			'machine_model'=>$this->input->post('modelno'),
			'qty'=>$this->input->post('modelqty'),
			'unit_price'=>$this->input->post('modelprice'),
			'totalprice'=>$grandtotalprice);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_machine_price_info',$machinepricedata);
			/*Machine price section*/


			/*Quotation Freight Info*/
		;
			if ($this->input->post('packagingapplicable')!='') {
				$packingingcharge = $this->input->post('packagingpercentage');
				
			}else{
				$packingingcharge = "";
			}

			if ($this->input->post('forwardingapplicable')!='') {
				$forwardingcharges = $this->input->post('forwardingpercentage');
			}else{
				$forwardingcharges = "";
			}
			if ($this->input->post('insuranceapplicable')!='') {
				$insurancecharges = $this->input->post('insurancepercentage');
			}else{
				$insurancecharges = "";
			}
			if($this->input->post('frightinfo')==3 || $this->input->post('frightinfo')==4){
				$freight_charges = $this->input->post('freightamount');
			}else{
				$freight_charges = "";
			}

			$freight_type=$this->input->post('freightType');
			$port_id = 0;
			if($freight_type=="FOB" || $freight_type=="CIF" || $freight_type=="CFR")
			{
				$port=$this->input->post('port');
				if (is_numeric($port) && !strpos($port, '.')) {

					$port_id=$port;
				}else
				{
					$dt=array('name'=>$port);
					$this->db->insert('ports',$dt);
					$port_id=$this->db->insert_id();
				}


			}


			if ($this->input->post('installationcommissioningapplicable')!='') {
				$installationcommissioningamount = $this->input->post('installationcommissioningamount');
			}else{
				$installationcommissioningamount = "";
			}



			$rswqs=$this->db->select('id')->from('quotation_freight_packing_forwarding')->where('record_id',$recordid)->get();
			if($rswqs->num_rows()>0)
			{
			$freightdata = array(
			'freight'=>$this->input->post('frightinfo'),
			'freight_charges'=>$freight_charges,
			'installation_charges'=>$installationcommissioningamount,
			'freight_type'=>$freight_type,
			'packing_charges'=>$packingingcharge,
			'forwarding_charges'=>$forwardingcharges,
			'port'=>$port_id,
			'insurance'=>$insurancecharges);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_freight_packing_forwarding',$freightdata);
			}else
			{
				$freightdata = array(
			'record_id'=>$recordid,
			'freight'=>$this->input->post('frightinfo'),
			'freight_charges'=>$freight_charges,
			'installation_charges'=>$installationcommissioningamount,
			'freight_type'=>$freight_type,
			'packing_charges'=>$packingingcharge,
			'forwarding_charges'=>$forwardingcharges,
			'port'=>$port_id,
			'insurance'=>$insurancecharges);
			
			$this->db->insert('quotation_freight_packing_forwarding',$freightdata);
			}
			/*Quotation Freight Info*/


			// /** EDIT OPTION DATA **/
			// 	$existing_optional=$this->input->post('existing_optional');
			// 	if(count($existing_optional)>0)
			// 	{
			// 	for($y=0;$y<count($existing_optional);$y++)
			// 	{
			// 	$techdescriptioninfo=$this->input->post('techdescriptioninfo'.$existing_optional[$y]);
			// 	$techdescqty=$this->input->post('techdescqty'.$existing_optional[$y]);
			// 	$techdescprice=$this->input->post('techdescprice'.$existing_optional[$y]);
			// 	$techdesctotalprice=$this->input->post('techdesctotalprice'.$existing_optional[$y]);
			// 	$totalprice = $techdescqty*$techdescprice;
			// 	$data4 = array(
			// 	'description'=>$techdescriptioninfo,
			// 	'qty'=>$techdescqty,
			// 	'price'=>$techdescprice,
			// 	'total_price'=>$totalprice);
			// 	$this->db->where('id',$existing_optional[$y]);
			// 	$this->db->update('quotation_annexture_4',$data4);
			// 	}
			// 	}

				/** END **/

				// NEED TO START FROM HERE 

				$existing_optional=$this->input->post('existing_optional');

				for($r=0;$r<count($existing_optional);$r++)
				{
					$id=$existing_optional[$r];
					$techdescriptioninfo=$this->input->post('edittechdescriptioninfo'.$id);
					$techdescqty=$this->input->post('edittechdescqty'.$id);
					$techdescprice=$this->input->post('edittechdescprice'.$id);
					$edittechhsn=$this->input->post('edittechhsn'.$id);
					$edittechunit=$this->input->post('edittechunit'.$id);

					$totalprice = $techdescqty*$techdescprice;
							$data4 = array(
							'description'=>$techdescriptioninfo,
							'hsn'=>$edittechhsn,
							'unit'=>$edittechunit,
							'qty'=>$techdescqty,
							'price'=>$techdescprice,
							'total_price'=>$totalprice);
							$this->db->where('id',$id);
							$this->db->update('quotation_annexture_4',$data4);

				}

				/** END EXISTING **/




			/*Add Technical Charges OPTIONAL */
			if(isset($_REQUEST['techdescriptioninfo'])){	
					$tags1=count($_REQUEST['techdescriptioninfo']);
					if($tags1>0)
					{
					$techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
					$techdescqty = $_REQUEST['techdescqty'];
					$techdescprice = $_REQUEST['techdescprice'];
					$techdesctotalprice = $_REQUEST['techdesctotalprice'];
					$techsn = $_REQUEST['techsn'];
					$techunit = $_REQUEST['techunit'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($techdescriptioninfo[$x]!='')
						{

						if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {

						$techdescitemid = $techdescriptioninfo[$x];
						} else {
						$datass = array('instruments_name'=>$techdescriptioninfo[$x],'type'=>1,'status'=>1);
						$this->db->insert('presto_instruments',$datass);
						$techdescitemid = $this->db->insert_id();
						}



							$totalprice = $techdescqty[$x]*$techdescprice[$x];
							$data4 = array('record_id'=>$recordid,
							'description'=>$techdescitemid,
							'qty'=>$techdescqty[$x],
							'price'=>$techdescprice[$x],
							'hsn'=>$techsn[$x],
							'unit'=>$techunit[$x],
							'total_price'=>$totalprice,
							'addedOn'=>date('Y-m-d H:i:s'),
							'flag'=>$i,
							'category_type'=>1,
							'addedBy'=>$user_id);
							$this->db->insert('quotation_annexture_4',$data4);
							
						}
					$i++;	
					}
					}
					}

					/* end **/


					/*check discount if applicable*/
					$discountapplicable = $this->input->post('discountapplicable');
					
					//echo $discounttype; exit;
					if($discountapplicable==1){
						$discunttype = $this->input->post('discunttype');
						$discount = '';
						if($discunttype==1){
							$discount = $this->input->post('discountinpercent');
						}else{
							$discount = $this->input->post('discountinamount');
						}
						
						$q = $this->db->select('id')->from('quotation_discount_data')->where('record_id',$recordid)->get();
						if($q->num_rows()>0){
							foreach($q->result() as $existingdiscount);
							$discountdata= array('discount_type'=>$discunttype,
								'discountvalue'=>$discount,
								'added_on'=>date('Y-m-d H:i:s'),
								'added_by'=>$user_id);
							$this->db->where('id',$existingdiscount->id);
							$this->db->update('quotation_discount_data',$discountdata);
						}else{
							$discountdata= array(
								'record_id'=>$recordid,
								'discount_type'=>$discunttype,
								'discountvalue'=>$discount,
								'added_on'=>date('Y-m-d H:i:s'),
								'added_by'=>$user_id);
							$this->db->insert('quotation_discount_data',$discountdata);

						}
					}else{
						$this->db->where('record_id',$recordid);
						$this->db->delete('quotation_discount_data');
					}
					/*check discount if applicable*/

					/** OPTIONAL DATA ADD  EXIST**/
					/** EXISTING **/
					$optionaldatainfo=$this->input->post('optionaldatainfo');
					for($r=0;$r<count($optionaldatainfo);$r++)
					{
					$id=$optionaldatainfo[$r];
					$optionalitem=$this->input->post('optionalitem'.$id);
					$optionalqty=$this->input->post('optionalqty'.$id);
					$optionalprice=$this->input->post('optionalprice'.$id);
					if(is_numeric($optionalitem)){
					$itemid = $optionalitem;
					}else{
					$datass = array('instruments_name'=>$optionalitem);
					$this->db->insert('presto_instruments',$datass);
					$itemid = $this->db->insert_id();
					}
					$totalprice = $optionalqty*$optionalprice;
					$data=array(
					'description'=>$itemid,
					'value'=>$optionalqty,
					'price'=>$optionalprice,
					'totalprice'=>$totalprice);
					$this->db->where('id',$id);
					$this->db->update('quotation_optional',$data);
					}
					/** END **/
			

					if(isset($_REQUEST['optionalitem'])){	
					$tags1=count($_REQUEST['optionalitem']);
					if($tags1>0)
					{
					$optionalitem = $_REQUEST['optionalitem'];
					$optionalqty=$_REQUEST['optionalqty'];
					$optionalprice=$_REQUEST['optionalprice'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($optionalitem[$x]!='')
						{
							if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
							$itemid = $optionalitem[$x];
							} else {
							$datass = array('instruments_name'=>$optionalitem[$x],'type'=>1,
							'status'=>1);
							$this->db->insert('presto_instruments',$datass);
							$itemid = $this->db->insert_id();
							}		

							$totalprice = $optionalqty[$x]*$optionalprice[$x];
							$data=array('record_id'=>$recordid,
							'description'=>$itemid,
							'value'=>$optionalqty[$x],
							'price'=>$optionalprice[$x],
							'totalprice'=>$totalprice,
							'addedBy'=>$user_id,
							'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('quotation_optional',$data);
							
						}
					$i++;	
					}
					}
					}

					/** end **/

			if ($this->input->post('consumablespare')!='') {
				$consumablespare = $this->input->post('consumablespare');
			}else{
				$consumablespare = 0;
			}

			$restyu=$this->db->select('id')->from('quotation_other_information')->where('record_id',$recordid)->get();
			if($restyu->num_rows()>0)
			{
			$data13 = array(
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'addedBy'=>$user_id,
				'consumable_spare'=>$consumablespare);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_other_information',$data13);
			}else
			{
				$data13 = array(
					'record_id'=>$recordid,
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'addedBy'=>$user_id,
				'consumable_spare'=>$consumablespare);
			
			$this->db->insert('quotation_other_information',$data13);
			}


			// Check if the checkbox is set (checked)
			if ($this->input->post('layoutapplicable')!='') {
			// Get the value of the checkbox
			
			$photo1=$_FILES['uploadlayout']['name'];
			if($photo1<>'')
			{
			$image2=explode('.',$photo1);
			$cat_image1=end($image2);
			$layoutimage=time().'.'.$cat_image1;
			move_uploaded_file($_FILES['uploadlayout']["tmp_name"],UPLOADPATH.'opportunitydocs/layoutimg/' . $layoutimage);
			$data = array('record_id'=>$recordid,
				'image'=>$layoutimage,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_layout_img',$data);
			}else
			{
			$layoutimage="";
			}		

			} else {
				
				$this->db->where('record_id',$recordid);
				$this->db->delete('quotation_layout_img');
			}


			if($consumablespare!=0)
			{
			if($this->input->post('consumalbleNew')==0)
			{
				$consumanbleid=$this->input->post('consumanbleid');
				for($r=0;$r<count($consumanbleid);$r++)
				{
					$id=$consumanbleid[$r];
					$spareqty=$this->input->post('spareqty'.$id);
					$spareprice=$this->input->post('spareprice'.$id);
					$spareFile_old=$this->input->post('spareFile_old'.$id);
					/** MEDIA **/
					$photo1=$_FILES['spareFile'.$id]['name'];
					if($photo1<>'')
					{
					$image2=explode('.',$photo1);
					$cat_image1=end($image2);
					$spareFiles=time().'.'.$cat_image1;
					move_uploaded_file($_FILES['spareFile'.$id]["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
					}else
					{
					$spareFiles=$spareFile_old;
					}

					/** END **/
					$data=array('qty'=>$spareqty,
						'media'=>$spareFiles,
					'price'=>$spareprice);
					$this->db->where('id',$id);
					$this->db->update('quotation_consumable_spares',$data);

				}
			}else
			{

					if(isset($_REQUEST['spareqty'])){	
					$tags1=count($_REQUEST['spareqty']);
					if($tags1>0)
					{
					//$partname = $_REQUEST['partname'];
					$spareqty=$_REQUEST['spareqty'];
					$spareprice=$_REQUEST['spareprice'];

					$i=1;
					for($x=0;$x<$tags1;$x++){
					// if($partname[$x]!='')
					// 	{
					/** MEDIA **/
					$photo1=$_FILES['spareFile']['name'];
					if($photo1<>'')
					{
					$image2=explode('.',$photo1);
					$cat_image1=end($image2);
					$spareFiles=time().'.'.$cat_image1;
					move_uploaded_file($_FILES['spareFile']["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
					}else
					{
					$spareFiles=$this->input->post('spareFile_old');
					}
					/** END **/
					$data=array('record_id'=>$recordid,
					'qty'=>$spareqty[$x],
					'price'=>$spareprice[$x],
					'added_by'=>$user_id,
					'media'=>$spareFiles,
					'added_on'=>date('Y-m-d H:i:s'));
					$this->db->insert('quotation_consumable_spares',$data);

					//}
					$i++;	
					}
					}
					}



			}
		}else
		{

			$this->db->where('record_id',$recordid);
			$this->db->delete('quotation_consumable_spares');	
		}


		// if ($this->input->post('consumablespare')!='') {
		// 	/*Consumable Spares*/

		// 	if(isset($_REQUEST['spareqty'])){	
		// 			$tags1=count($_REQUEST['spareqty']);
		// 			if($tags1>0)
		// 			{
		// 			//$partname = $_REQUEST['partname'];
		// 			$spareqty=$_REQUEST['spareqty'];
		// 			$spareprice=$_REQUEST['spareprice'];
					
		// 			$i=1;
		// 			for($x=0;$x<$tags1;$x++){
		// 			// if($partname[$x]!='')
		// 			// 	{
		// 					$data=array('record_id'=>$recordid,
		// 					'qty'=>$spareqty[$x],
		// 					'price'=>$spareprice[$x],
		// 					'added_by'=>$user_id,
		// 					'added_on'=>date('Y-m-d H:i:s'));
		// 					$this->db->insert('quotation_consumable_spares',$data);
							
		// 				//}
		// 			$i++;	
		// 			}
		// 			}
		// 			}


		// 	/*Consumable Spares*/
		// }


			/** UPDATE TERMS & CONDITIONS **/
		if($this->input->post('country')==101)
		{

		$liquidated_applicable = $this->input->post('liquidated_clause_applicable');
		if($liquidated_applicable==1)
		{
			$liquidated_applicable=1;
		}else
		{
			$liquidated_applicable=0;
		}


		$late_delivery_applicable = $this->input->post('late_delivery_applicable');
		if($late_delivery_applicable==1)
		{
			$late_delivery_applicable=1;
		}else
		{
			$late_delivery_applicable=0;
		}

		// get and xss_clean content fields (optional: keep html if you use editors)
		$liquidated_text  = $this->input->post('liquidated_clause');
		$packing_charges  = $this->input->post('packing_charges');
		$insurance        = $this->input->post('insurance');
		$late_delivery_text  = $this->input->post('late_delivery');
		$installation     = $this->input->post('installation');

		$payload = [
		
		'liquidated_applicable' => $liquidated_applicable,
		'liquidated_text' => $liquidated_text ?: null,
		'packing_charges' => $packing_charges ?: null,
		'late_delivery_applicable'=>$late_delivery_applicable,
		'late_delivery_text'=>$late_delivery_text ? : null,
		'insurance' => $insurance ?: null,
		'installation' => $installation ?: null
		];

		$this->db->where('quotation_id',$recordid);
		$this->db->update('quotation_custom_terms',$payload);
		}else
		{
			$this->db->where('quotation_id',$recordid);
			$this->db->delete('quotation_custom_terms');
		}





			/** UPDATE LEAD STATUS **/

			$d=array(
					'lead_id'=>$this->uri->segment(3),
					'lead_status'=>39,
					'next_follow_date'=> date('Y-m-d', strtotime("+1 day")),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$_SESSION['logged_in']['user_id']
				);
			$this->db->insert('progress_remarks',$d);


			$reasondata = array('lead_id'=>$this->uri->segment(3),
			'reason'=>$this->input->post('reasonforchange'),
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('quotation_change_reason',$reasondata);


 				redirect(page_url.'Opportunity/GeneratedQuote/'.$recordid."/".$this->uri->segment(4));
					
// $this->session->set_flashdata('message','<div style="color:red;" class="alert alert-success">Thank you, Your Quote Updated.</div>');

// 			redirect(page_url.'Opportunity/edit_opportunity/'.$this->uri->segment(3));

		}

		
}


function getCurrentVersion($lead_id)
		{
			$version=0;

			$rest=$this->db->select('version')->from('quotation_customer_data')->where('lead_id',$lead_id)->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);

				$version=$row->version;
			}

			return $version;
		}



function GeneratedQuoteStatic()
{
	$this->load->view('formats/rfq/examples/shubham_quotation_static');

}

public function orderwon(){
	$this->load->view('opportunity/order_won_stage');
}

function previewquoteandsendforapproval()
{
	$this->load->view('leads/pdf_preview');
}


function leadmarkaswon(){

	$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
	$this->form_validation->set_rules('existingpaymentterms', 'Payment Term', 'required|trim');
	$this->form_validation->set_rules('companyname', 'Company Name', 'required|trim');
	$this->form_validation->set_rules('pono', 'Po Number', 'required|trim');
	$this->form_validation->set_rules('podate', 'PO Date', 'required|trim');
	//$this->form_validation->set_rules('order_value', 'Order Value', 'required|trim');
	$user_id =$this->session->userdata['logged_in']['user_id'];			
	if ($this->form_validation->run() == FALSE)
	{
	$this->load->view('opportunity/order_won_stage');
	}
	else
	{

//echo $dfnumber; exit;

	$photo=$_FILES['attachpo']['name'];
	if($photo<>'')
	{
	$image1=explode('.',$photo);
	$cat_image=end($image1);
	$poattachment=time().'.'.$cat_image;
	move_uploaded_file($_FILES['attachpo']["tmp_name"],UPLOADPATH.'Taskdocument/' . $poattachment);
	}else
	{
	$poattachment="";
	}
		date_default_timezone_set("Asia/Kolkata");
		$date =  date('Y-m-d H:i:s'); 
		$month = date('m');
		$year = date('Y');
		$st = $year."-".$month."-"."01 00:00:00";
		$startdate = date('Y-m-d H:i:s',strtotime($st));
		$et = $year."-".$month."-"."31 23:59:59";
		$enddate = date('Y-m-d H:i:s',strtotime($et));
		$tilldateorder = array();
		$tilldateorder[] = 0;
	      $q1 = $this->db->select('order_value')->from('poreceived')->where('added_on BETWEEN "'.$startdate. '" and "'.$enddate.'"')->get();

		if($q1->num_rows()>0){
			foreach($q1->result() as $rowssss){
				$tilldateorder[] = $rowssss->order_value;
			}
		}

	$thismonthordervalue = array_sum($tilldateorder);
	

	if(isset($_REQUEST['orderwonmachinename'])){
		$tags1=count($_REQUEST['orderwonmachinename']);
		if($tags1>0)
		{
			$order_value = $_REQUEST['order_value'];
			for($x=0;$x<$tags1;$x++){

				$total[]= $order_value[$x];
			}

		}
		$totalordervalue = array_sum($total);

		
	}

	$grandtotal = $thismonthordervalue+$totalordervalue;
	$comparevalue = "10000000000000";
	
	if($grandtotal <$comparevalue){

		if(isset($_REQUEST['orderwonmachinename'])){	
					$tags1=count($_REQUEST['orderwonmachinename']);
					if($tags1>0)
					{
					$machineinfo = $_REQUEST['orderwonmachinename'];
					$ordervalueincustomercurrency=$_REQUEST['ordervalueincustomercurrency'];
					$order_value = $_REQUEST['order_value'];
					for($x=0;$x<$tags1;$x++){
					if($machineinfo[$x]!='')
						{

						$dfnumber= $this->getdfno();	
						
						$data = array('company_name'=>$this->input->post('companyname'),
						'pono'=>$this->input->post('pono'),
						'podate'=>date('Y-m-d',strtotime($this->input->post('podate'))),
						'po_attachment'=>$poattachment,
						'payment_term'=>$this->input->post('existingpaymentterms'),
						'added_on'=>$date,
						'order_value'=>$order_value[$x],
						'customer_currency'=>$this->input->post('currency'),
						'penalityamount'=>$this->input->post('penalityamount'),
						'amount_in_customer_currency'=>$ordervalueincustomercurrency[$x],
						'lead_id'=>$this->uri->segment(3),
						'machine_model'=>$machineinfo[$x],
						'machinetype'=>$this->input->post('machineName'),
						'machine_punch_date'=>date('Y-m-d H:i:s'),
						'prof_inv_no'=>$this->input->post('prof_inv_no'),
						'df_number'=>$dfnumber,
						'basic_machine'=>$this->input->post('basicmachine'),
						'added_by'=>$user_id);
						$this->db->insert('poreceived',$data);
						$polastid = $this->db->insert_id();	
						
						$nexttaskid = 0;
						$departmentid = 0;
						$enddate = date('Y-m-d');

						/*PO Release entry in Task scheduling table*/
						$data1 = array('df_id'=>0,
						'taskid'=>1,
						'department_id'=>9,
						'start_date'=>date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						'added_on'=>date('Y-m-d H:i:s'),
						'added_by'=>$user_id,
						'po_id'=>$polastid,
						'task_status'=>1,
						'remarks'=>'',
						'task_completed_on'=>date('Y-m-d H:i:s'),
						'task_completed_by'=>$user_id,
						'assigned_user'=>$user_id, 
						'assigned_by'=>$user_id,
						'assigned_on'=>date('Y-m-d H:i:s'),
						'userid'=>$user_id);
			   	$this->db->insert('task_department_wise_scheduling',$data1);
				$q = $this->db->select('task_id, department_id, tat')->from('task_management')->where('task_id',3)->order_by('sortorder','ASC')->limit(1)->get();
				if($q->num_rows()>0){
				foreach($q->result() as $row1);

				$nexttaskid = $row1->task_id;
				$departmentid = $row1->department_id;

				$startdate = date('Y-m-d');
				$enddate = date('Y-m-d', strtotime('+'.$row1->tat.' days', strtotime($startdate)));

				$skipped_dates = $this->task->SKIP_holidays($startdate, $enddate);

				$startdate = $skipped_dates['start_date'];
				$enddate = $skipped_dates['end_date'];

				//echo $startdate."<br/>".$enddate; exit;
				// $data1 = array('df_id'=>0,
				// 'taskid'=>114,
				// 'department_id'=>$departmentid,
				// 'start_date'=>$startdate,
				// 'end_date'=>$enddate,
				// 'added_on'=>date('Y-m-d H:i:s'),
				// 'added_by'=>$user_id,
				// 'assigned_user'=>$user_id, 
				// 'assigned_by'=>$user_id,
				// 'assigned_on'=>date('Y-m-d H:i:s'),
				// 'po_id'=>$polastid);

			      //  $this->db->insert('task_department_wise_scheduling',$data1);

			      $data1 = array('df_id'=>0,
				'taskid'=>3,
				'department_id'=>$departmentid,
				'start_date'=>$startdate,
				'end_date'=>$enddate,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id,
				'assigned_user'=>$user_id, 
				'assigned_by'=>$user_id,
				'assigned_on'=>date('Y-m-d H:i:s'),
				'po_id'=>$polastid);

			       $this->db->insert('task_department_wise_scheduling',$data1);
			
				
				}	
				}

				}

			/*Notification of PO Release*/
			//$this->task->notificationofprocessdone(1);
			/*Notification of PO Release*/

			$title  = "Order Won";
			$wonremark = "This order has been won";
			$d=array(
			'lead_id'=>$this->uri->segment(3),
			'lead_status'=>35,
			'remarks'=>$wonremark,
			'remark_title'=>$title,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$_SESSION['logged_in']['user_id']
			);
			$this->db->insert('progress_remarks',$d);

			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
			redirect(page_url.'Dashboard');


					}
					}else{
						$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry!, There is some issue on process.</div>');
			redirect(page_url.'Opportunity/orderwon/'.$this->uri->segment(3));
					}
	}else{
		$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Sorry!, This Months order value has been exceeded 10 CR. Please plan this order in next Month.</div>');
			redirect(page_url.'Opportunity/orderwon/'.$this->uri->segment(3));
	}

	

			

	}


}

function getdfno(){
	$dfno = 1730;
	$q = $this->db->select('MAX(df_number) as dfnumber')->from('poreceived')->where('df_number!=',0)->get();
	if($q->num_rows()>0){
		foreach($q->result() as $row);
		if($row->dfnumber<>''){
			$dfno= $row->dfnumber+1;
		}
		
	}
	return $dfno;

}

function editnew()
{
	$this->load->view('opportunity/edit');
}

function updatenew()
{
		$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
		$this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
		$this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
		$this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
		$this->form_validation->set_rules('cur', 'Currency', 'required|trim');
		$this->form_validation->set_rules('country', 'Country', 'required|trim');
		$this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
		$this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
		/*Validation till general information*/
		$this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
		$this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
		// $this->form_validation->set_rules('pouchsizew', 'Pouch Size W', 'required|trim');
		// $this->form_validation->set_rules('pouchsizel', 'Pouch Size L', 'required|trim');
		// $this->form_validation->set_rules('qtytobepacked', 'Qty to be Packed', 'required|trim');
		// $this->form_validation->set_rules('qty_unit', 'Qty Unit', 'required|trim');
		$this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
		$this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
		$this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
		$this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
		$this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
		$this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');


		$this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
		// $this->form_validation->set_rules('noofaxisinmachine', 'No of Axis in Machine', 'required|trim');
		$this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
		//$this->form_validation->set_rules('sealingstyle', 'Sealing Style', 'required|trim');
		$this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
		$this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
		$this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
		$this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
		$this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
		$this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
		$this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
		$this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
		$this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
		// $this->form_validation->set_rules('pouchsizew1', 'Pouch Size W', 'required|trim');
		// $this->form_validation->set_rules('pouchsizel1', 'Pouch Size L', 'required|trim');
		// $this->form_validation->set_rules('pouchsizeh1', 'Pouch Size H', 'required|trim');
		$this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
		$this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
		$this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
		$this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
		$this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
		$this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
		$this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
		$this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
		$this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
		$this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');
		
		$this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
		$this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
		$this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');
		// $this->form_validation->set_rules('followDate', 'Next Followup Date', 'required|trim');
		$user_id =$this->session->userdata['logged_in']['user_id'];		
		if ($this->form_validation->run() == FALSE)
		{
			$this->load->view('opportunity/edit');
		}
		else
		{
			$rest=$this->db->select('product_id')->from('lead_products')->where('lead_id',$this->uri->segment(3))->get();
			if($rest->num_rows()>0)
			{
				foreach($rest->result() as $row);
				$product_id=$row->product_id;
			}else
			{
				echo "Product Not Found"; exit;
			}
			if($this->uri->segment(5)==1){
				$version=$this->getCurrentVersion($this->uri->segment(3));
				$newversion = $version+1;
			}else{
				$newversion=$this->getCurrentVersion($this->uri->segment(3));
			}
			$version=$this->getCurrentVersion($this->uri->segment(3));

			$data = array(
				'currency'=>$this->input->post('cur'),
				'country'=>$this->input->post('country'),
				'machine_name'=>$this->input->post('machineName'),
				'machine_model_no'=>$this->input->post('machineModel'),
				'version'=>$newversion,
				'updatedOn'=>date('Y-m-d H:i:s'),
				'last_revision_date'=>date('Y-m-d'),
				'updatedBy'=>$user_id);
			$this->db->where('lead_id',$this->uri->segment(3));
			$this->db->update('quotation_customer_data',$data);

			$recordid = $this->uri->segment(4);

			// $pouchsizew = $this->input->post('pouchsizew');
			// $pouchsizel = $this->input->post('pouchsizel');
			// $pouchsize = $pouchsizew." X ".$pouchsizel;
			$data1 = array(
				'product_to_be_packed'=>$this->input->post('producttobepacked'),
				'product_name'=>$this->input->post('productname'),
				// 'pouch_size_type'=>$pouchsize,
				// 'qty_to_be_packed'=>$this->input->post('qtytobepacked')." ".$this->input->post('qty_unit'),
				'horizontal_sealing_width'=>$this->input->post('horizontalsealingwidth'),
				'vertical_sealing_width'=>$this->input->post('verticalsealingwidth'),
				'perforation_pitch'=>$this->input->post('perforationpitch'),
				'perforationstyle'=>$this->input->post('perforationstyle'),
				'batchcut'=>$this->input->post('batchcut'),
				'typeofsealing'=>$this->input->post('typeofsealing'),
				'plc_make'=>$this->input->post('plcmake'),
				'power_supply'=>$this->input->post('powersupply'),
				'liquidviscositydata'=>$this->input->post('liquidviscositydata'),
				'liquidconductivitydata'=>$this->input->post('liquidconductivitydata'),
				'powderdensitydata'=>$this->input->post('powderdensitydata'),
				'powderdfrdata'=>$this->input->post('powderdfrdata'),
				'powdermoisturecontentdata'=>$this->input->post('powdermoisturecontentdata'));
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_annexture_1',$data1);

			/** EXISTING PRODUCT TO BE PACKED**/
			for($i=0;$i<count($this->input->post('pack_id'));$i++)
			{
				$pckid=$this->input->post('pack_id')[$i];
				$packedqty=$this->input->post('editqtytobepacked'.$pckid);
				$unit=$this->input->post('editqty_unit'.$pckid);
				$length=$this->input->post('editpouchsizel'.$pckid);
				$width=$this->input->post('editpouchsizew'.$pckid);
				$height=$this->input->post('editpouchsizeh'.$pckid);
				if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE")
				{
				$gusset=$this->input->post('editgusset'.$pckid);
				$punchhole=$this->input->post('editpunch_hole'.$pckid);
				}else
				{
				$gusset='';
				$punchhole='';
				}

			$data=array('packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
			$this->db->where('id',$pckid);
			$this->db->update('quotation_pouch_size',$data);
			}


			/** ADD QTY TO BE PACKED & POUCH SIZE **/
			if($this->input->post('addMorePRDPacked')==1)
			{
			$qtytobepacked=$this->input->post('qtytobepacked');
			for($u=0;$u<count($qtytobepacked);$u++)
			{
			$packedqty=$qtytobepacked[$u];
			$unit=$this->input->post('qty_unit')[$u];
			$length=$this->input->post('pouchsizel')[$u];
			$width=$this->input->post('pouchsizew')[$u];
			$height=$this->input->post('pouchsizeh')[$u];
			if($this->input->post('machineName')=="VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName')=="VFFS COLLAR TYPE MACHINE")
			{
			$gusset=$this->input->post('gusset')[$u];
			$punchhole=$this->input->post('punch_hole')[$u];
			}else
			{
			$gusset='';
			$punchhole='';
			}


			$data=array('record_id'=>$recordid,'packing_qty'=>$packedqty,'unit'=>$unit,'length'=>$length,'width'=>$width,'height'=>$height,'gusset'=>$gusset,'punchhole'=>$punchhole);
			$this->db->insert('quotation_pouch_size',$data);
			}
			}
			/** END **/



			$data2 = array(
				'model'=>$this->input->post('machinemodelno'),'flag'=>1);
			$this->db->where('record_id',$recordid);
			$this->db->where('flag',1);
			$this->db->update('quotation_cum_tech_spec',$data2);

			/** UPDATE PREVIOUS TECH SPEC**/
			$technicalDataid=$this->input->post('technicalDataid');
			if(count($technicalDataid)>0)
			{
				for($y=0;$y<count($technicalDataid);$y++)
				{
					$dmodel=$this->input->post('technicalspecedit'.$technicalDataid[$y]);
					$data21 = array('model'=>$dmodel);
					$this->db->where('id',$technicalDataid[$y]);
					$this->db->update('quotation_cum_tech_spec',$data21);

				}
			}
			/* END **/


			/*Add Multiple Tech Spec*/

			if(isset($_REQUEST['technicalspec'])){	
				
					$tags1=count($_REQUEST['technicalspec']);
					if($tags1>0)
					{
					$technicalspec = $_REQUEST['technicalspec'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($technicalspec[$x]!='')
						{
					$data21 = array('record_id'=>$recordid,
					'model'=>$technicalspec[$x],
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
					$this->db->insert('quotation_cum_tech_spec',$data21);
							
						}
					$i++;	
					}
					}
					}
			/*Add Multiple Tech Spec*/



			/** UPDATE PREVIOUS TECH SPEC**/
			$axisid=$this->input->post('axis_id');
			if(count($axisid)>0)
			{
				for($y=0;$y<count($axisid);$y++)
				{
					$noofaxisinmachineinputedit=$this->input->post('editnoofaxisinmachine'.$axisid[$y]);
					$editnoofaxisincount=$this->input->post('editnoofaxisincount'.$axisid[$y]);
					$data21 = array('description'=>$noofaxisinmachineinputedit,'axiscount'=>$editnoofaxisincount);
					$this->db->where('id',$axisid[$y]);
					$this->db->update('quotation_no_of_axis_in_machine',$data21);

				}
			}
			/* END **/



			/*Add Multiple Axis Machine Input*/
			if($this->input->post('more_axis')==1)
			{
			if(isset($_REQUEST['noofaxisinmachine'])){	
					$tags1=count($_REQUEST['noofaxisinmachine']);
					
					if($tags1>0)
					{
					$technicalspecs = $_REQUEST['noofaxisinmachine'];
					$noofaxisincount = $_REQUEST['noofaxisincount'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($technicalspecs[$x]!='')
					{
					$data22 = array('record_id'=>$recordid,
					'description'=>$technicalspecs[$x],
					'axiscount'=>$noofaxisincount[$x],
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);
					$this->db->insert('quotation_no_of_axis_in_machine',$data22);
							
					}
					$i++;	
					}
					}
					}
			}
			/*Add Multiple Axis Machine Input*/



			$laminatewidth = $this->input->post('laminatewidth')."<br>";
			$laminatereeldia = $this->input->post('laminatereeldia')."<br>";
			$laminatereelcoredia = $this->input->post('laminatereelcoredia')."<br>";
			$laminatewidthcoredia = $laminatewidth." ".$laminatereeldia." ".$laminatereelcoredia;
			// $pouchsizew1 = $this->input->post('pouchsizew1');
			// $pouchsizel1 = $this->input->post('pouchsizel1');
			// $pouchsizeh1 = $this->input->post('pouchsizeh1');
			// $secondpouchsize = $pouchsizew1." X ".$pouchsizel1." X ".$pouchsizeh1;
			$layoutdimensionslength = $this->input->post('layoutdimensionslength');
			$layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
			$layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
			$layoutdimensionsof = $layoutdimensionslength."<br>".$layoutdimensionswidth."<br>".$layoutdimensionsheight."<br>";

			$data3 = array(
				'machinemodel'=>$this->input->post('machinemodel'),
				'filling_accuracy'=>$this->input->post('fillingaccuracy'),
				 'sealingstyle'=>$this->input->post('sealingstyle'),
				'speed'=>$this->input->post('designspeed'),
				'actual_speed'=>$this->input->post('actualspeed'),
				'no_of_track'=>$this->input->post('nooftracks'),
				'leminate_specification'=>$laminatewidth,
				'laminatereeldia'=>$laminatereeldia,
				'laminatereelcoredia'=>$laminatereelcoredia ,
				'product_to_be_packed'=>$this->input->post('product_tobepacked'),
				'filling_capacity'=>$this->input->post('fillingcapacity'),
				// 'pouch_size'=>$secondpouchsize,
				'electrical_spec'=>$this->input->post('electricalspec'),
				'layout_dimensions'=>$layoutdimensionsof,
				'machine_weight'=>$this->input->post('netweight'),
				'gross_weight'=>$this->input->post('grossweight'),
				'compressed_air'=>$this->input->post('compressedaircfa'),
				'compressedairbar'=>$this->input->post('compressedairbar'));

			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_annexure_2',$data3);



			$data4 = array(
			'description'=>$this->input->post('modelno'),
			'qty'=>$this->input->post('modelqty'),
			'price'=>$this->input->post('modelprice'));
			$this->db->where('record_id',$recordid);
			$this->db->where('flag',0);
			$this->db->update('quotation_annexture_4',$data4);


			// Check if the checkbox is set (checked)
			if ($this->input->post('machinefillingsystem')!='') {
			// Get the value of the checkbox
			$checkboxValue = $this->input->post('machinefillingsystem');
			$photo=$_FILES['machinefillingimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'.'.$cat_image;
			move_uploaded_file($_FILES['machinefillingimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			$data = array('record_id'=>$recordid,
			'image'=>$machineimage,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			$this->db->insert('quotation_machine_filling_image',$data);	
			}else
			{
			$machineimage="";
			}
			}else{
			$this->db->where('record_id',$recordid);
			$this->db->delete('quotation_machine_filling_image');					
			}
			

			// Check if the checkbox is set (checked)
			if ($this->input->post('kld')==1) {
			// Get the value of the checkbox
			$photo=$_FILES['kldimg']['name'];
			if($photo<>'')
			{
			$image1=explode('.',$photo);
			$cat_image=end($image1);
			$machineimage=time().'1234.'.$cat_image;
			move_uploaded_file($_FILES['kldimg']["tmp_name"],UPLOADPATH.'opportunitydocs/' . $machineimage);
			$data = array('record_id'=>$recordid,
			'image'=>$machineimage,
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$user_id);
			$this->db->insert('quotation_kld_image',$data);	
			}else
			{
			$machineimage="";
			}
			}else{
			$this->db->where('record_id',$recordid);
			$this->db->delete('quotation_kld_image');					
			}


			/** BRAND DATA **/

			for($i=0;$i<count($this->input->post('brands'));$i++)
			{
				$brand_id=$this->input->post('brands')[$i];
				$brand_data=$this->input->post('brand_data')[$i];

				$resty=$this->db->select('id')->from('quotation_brand_data')->where('record_id',$recordid)->where('head_id',$brand_id)->get();
				if($resty->num_rows()==0)
				{
					$data=array('record_id'=>$recordid,'head_id'=>$brand_id,'value_id'=>$brand_data,'addedOn'=>date('Y-m-d'),'addedBy'=>$user_id);
					$this->db->insert('quotation_brand_data',$data);
				}else
				{
					foreach($resty->result() as $row)
					{
						$id=$row->id;
						$data=array('value_id'=>$brand_data);
						$this->db->where('record_id',$recordid);
						$this->db->where('id',$id);
						$this->db->update('quotation_brand_data',$data);
					}
				}

			}

			/** END **/

			/*Machine price section*/
			$grandtotalprice = $this->input->post('modelqty')*$this->input->post('modelprice');
			$machinepricedata = array(
			'machine_model'=>$this->input->post('modelno'),
			'qty'=>$this->input->post('modelqty'),
			'unit_price'=>$this->input->post('modelprice'),
			'totalprice'=>$grandtotalprice);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_machine_price_info',$machinepricedata);
			/*Machine price section*/


			/*Quotation Freight Info*/
		;
			if ($this->input->post('packagingapplicable')!='') {
				$packingingcharge = $this->input->post('packagingpercentage');
				
			}else{
				$packingingcharge = "";
			}

			if ($this->input->post('forwardingapplicable')!='') {
				$forwardingcharges = $this->input->post('forwardingpercentage');
			}else{
				$forwardingcharges = "";
			}
			if ($this->input->post('insuranceapplicable')!='') {
				$insurancecharges = $this->input->post('insurancepercentage');
			}else{
				$insurancecharges = "";
			}
			if($this->input->post('frightinfo')==3){
				$freight_charges = $this->input->post('freightamount');
			}else{
				$freight_charges = "";
			}

			$freight_type=$this->input->post('freightType');
			$port_id = 0;

			if($freight_type=="FOB" || $freight_type=="CIF" || $freight_type=="CFR")
			{
				$port=$this->input->post('port');
				if (is_numeric($port) && !strpos($port, '.')) {

					$port_id=$port;
				}else
				{
					$dt=array('name'=>$port);
					$this->db->insert('ports',$dt);
					$port_id=$this->db->insert_id();
				}


			}



			$rswqs=$this->db->select('id')->from('quotation_freight_packing_forwarding')->where('record_id',$recordid)->get();
			if($rswqs->num_rows()>0)
			{
			$freightdata = array(
			'freight'=>$this->input->post('frightinfo'),
			'freight_charges'=>$freight_charges,
			'freight_type'=>$freight_type,
			'packing_charges'=>$packingingcharge,
			'forwarding_charges'=>$forwardingcharges,
			'port'=>$port_id,
			'insurance'=>$insurancecharges);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_freight_packing_forwarding',$freightdata);
			}else
			{
				$freightdata = array(
					'record_id'=>$recordid,
			'freight'=>$this->input->post('frightinfo'),
			'freight_charges'=>$freight_charges,
			'freight_type'=>$freight_type,
			'packing_charges'=>$packingingcharge,
			'forwarding_charges'=>$forwardingcharges,
			'port'=>$port_id,
			'insurance'=>$insurancecharges);
			
			$this->db->insert('quotation_freight_packing_forwarding',$freightdata);
			}
			/*Quotation Freight Info*/


			// /** EDIT OPTION DATA **/
			// 	$existing_optional=$this->input->post('existing_optional');
			// 	if(count($existing_optional)>0)
			// 	{
			// 	for($y=0;$y<count($existing_optional);$y++)
			// 	{
			// 	$techdescriptioninfo=$this->input->post('techdescriptioninfo'.$existing_optional[$y]);
			// 	$techdescqty=$this->input->post('techdescqty'.$existing_optional[$y]);
			// 	$techdescprice=$this->input->post('techdescprice'.$existing_optional[$y]);
			// 	$techdesctotalprice=$this->input->post('techdesctotalprice'.$existing_optional[$y]);
			// 	$totalprice = $techdescqty*$techdescprice;
			// 	$data4 = array(
			// 	'description'=>$techdescriptioninfo,
			// 	'qty'=>$techdescqty,
			// 	'price'=>$techdescprice,
			// 	'total_price'=>$totalprice);
			// 	$this->db->where('id',$existing_optional[$y]);
			// 	$this->db->update('quotation_annexture_4',$data4);
			// 	}
			// 	}

				/** END **/

				// NEED TO START FROM HERE 

				$existing_optional=$this->input->post('existing_optional');

				for($r=0;$r<count($existing_optional);$r++)
				{
					$id=$existing_optional[$r];
					$techdescriptioninfo=$this->input->post('edittechdescriptioninfo'.$id);
					$techdescqty=$this->input->post('edittechdescqty'.$id);
					$techdescprice=$this->input->post('edittechdescprice'.$id);

					$totalprice = $techdescqty*$techdescprice;
							$data4 = array(
							'description'=>$techdescriptioninfo,
							'qty'=>$techdescqty,
							'price'=>$techdescprice,
							'total_price'=>$totalprice);
							$this->db->where('id',$id);
							$this->db->update('quotation_annexture_4',$data4);

				}

				/** END EXISTING **/




			/*Add Technical Charges OPTIONAL */
			if(isset($_REQUEST['techdescriptioninfo'])){	
					$tags1=count($_REQUEST['techdescriptioninfo']);
					if($tags1>0)
					{
					$techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
					$techdescqty = $_REQUEST['techdescqty'];
					$techdescprice = $_REQUEST['techdescprice'];
					$techdesctotalprice = $_REQUEST['techdesctotalprice'];
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($techdescriptioninfo[$x]!='')
						{

						if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {

						$techdescitemid = $techdescriptioninfo[$x];
						} else {
						$datass = array('instruments_name'=>$techdescriptioninfo[$x],'type'=>1,'status'=>1);
						$this->db->insert('presto_instruments',$datass);
						$techdescitemid = $this->db->insert_id();
						}



							$totalprice = $techdescqty[$x]*$techdescprice[$x];
							$data4 = array('record_id'=>$recordid,
							'description'=>$techdescitemid,
							'qty'=>$techdescqty[$x],
							'price'=>$techdescprice[$x],
							'total_price'=>$totalprice,
							'addedOn'=>date('Y-m-d H:i:s'),
							'flag'=>$i,
							'category_type'=>1,
							'addedBy'=>$user_id);
							$this->db->insert('quotation_annexture_4',$data4);
							
						}
					$i++;	
					}
					}
					}

					/* end **/


					/*check discount if applicable*/
					$discounttype = $this->input->post('discounttype');
					if($discounttype<>''){
						$discount = '';
						if($discounttype==1){
							$discount = $this->input->post('totaldiscount');
						}else{
							$discount = $this->input->post('totaldiscountinpercent');
						}
						
						$q = $this->db->select('id')->from('quotation_discount_data')->where('record_id',$this->uri->segment(3))->get();
						if($q->num_rows()>0){
							foreach($q->result() as $existingdiscount);
							$discountdata= array('discount_type'=>$discounttype,
								'discountvalue'=>$discount,
								'added_on'=>date('Y-m-d H:i:s'),
								'added_by'=>$user_id);
							$this->db->where('id',$existingdiscount->id);
							$this->db->update('quotation_discount_data',$discountdata);
						}else{
							$discountdata= array(
								'record_id'=>$this->uri->segment(3),
								'discount_type'=>$discounttype,
								'discountvalue'=>$discount,
								'added_on'=>date('Y-m-d H:i:s'),
								'added_by'=>$user_id);
							$this->db->insert('quotation_discount_data',$discountdata);

						}
					}
					/*check discount if applicable*/

					/** OPTIONAL DATA ADD  EXIST**/
					/** EXISTING **/
					$optionaldatainfo=$this->input->post('optionaldatainfo');
					for($r=0;$r<count($optionaldatainfo);$r++)
					{
					$id=$optionaldatainfo[$r];
					$optionalitem=$this->input->post('optionalitem'.$id);
					$optionalqty=$this->input->post('optionalqty'.$id);
					$optionalprice=$this->input->post('optionalprice'.$id);
					if(is_numeric($optionalitem)){
					$itemid = $optionalitem;
					}else{
					$datass = array('instruments_name'=>$optionalitem);
					$this->db->insert('presto_instruments',$datass);
					$itemid = $this->db->insert_id();
					}
					$totalprice = $optionalqty*$optionalprice;
					$data=array(
					'description'=>$itemid,
					'value'=>$optionalqty,
					'price'=>$optionalprice,
					'totalprice'=>$totalprice);
					$this->db->where('id',$id);
					$this->db->update('quotation_optional',$data);
					}
					/** END **/
			

					if(isset($_REQUEST['optionalitem'])){	
					$tags1=count($_REQUEST['optionalitem']);
					if($tags1>0)
					{
					$optionalitem = $_REQUEST['optionalitem'];
					$optionalqty=$_REQUEST['optionalqty'];
					$optionalprice=$_REQUEST['optionalprice'];
					
					$i=1;
					for($x=0;$x<$tags1;$x++){
					if($optionalitem[$x]!='')
						{
							if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
							$itemid = $optionalitem[$x];
							} else {
							$datass = array('instruments_name'=>$optionalitem[$x],'type'=>1,
							'status'=>1);
							$this->db->insert('presto_instruments',$datass);
							$itemid = $this->db->insert_id();
							}		

							$totalprice = $optionalqty[$x]*$optionalprice[$x];
							$data=array('record_id'=>$recordid,
							'description'=>$itemid,
							'value'=>$optionalqty[$x],
							'price'=>$optionalprice[$x],
							'totalprice'=>$totalprice,
							'addedBy'=>$user_id,
							'addedOn'=>date('Y-m-d H:i:s'));
							$this->db->insert('quotation_optional',$data);
							
						}
					$i++;	
					}
					}
					}

					/** end **/

			if ($this->input->post('consumablespare')!='') {
				$consumablespare = $this->input->post('consumablespare');
			}else{
				$consumablespare = 0;
			}

			$restyu=$this->db->select('id')->from('quotation_other_information')->where('record_id',$recordid)->get();
			if($restyu->num_rows()>0)
			{
			$data13 = array(
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'addedBy'=>$user_id,
				'consumable_spare'=>$consumablespare);
			$this->db->where('record_id',$recordid);
			$this->db->update('quotation_other_information',$data13);
			}else
			{
				$data13 = array(
					'record_id'=>$recordid,
				'terms_value'=>$this->input->post('paymentterms'),
				'delivery_value'=>$this->input->post('delivery'),
				'pocket_expense'=>$this->input->post('out_of_pocket_expense'),
				'addedBy'=>$user_id,
				'consumable_spare'=>$consumablespare);
			
			$this->db->insert('quotation_other_information',$data13);
			}


			// Check if the checkbox is set (checked)
			if ($this->input->post('layoutapplicable')!='') {
			// Get the value of the checkbox
			
			$photo1=$_FILES['uploadlayout']['name'];
			if($photo1<>'')
			{
			$image2=explode('.',$photo1);
			$cat_image1=end($image2);
			$layoutimage=time().'.'.$cat_image1;
			move_uploaded_file($_FILES['uploadlayout']["tmp_name"],UPLOADPATH.'opportunitydocs/layoutimg/' . $layoutimage);
			$data = array('record_id'=>$recordid,
				'image'=>$layoutimage,
				'added_on'=>date('Y-m-d H:i:s'),
				'added_by'=>$user_id);
			$this->db->insert('quotation_layout_img',$data);
			}else
			{
			$layoutimage="";
			}		

			} else {
				
				$this->db->where('record_id',$recordid);
				$this->db->delete('quotation_layout_img');
			}



			$consumanbleid=$this->input->post('consumanbleid');
			for($r=0;$r<count($consumanbleid);$r++)
			{
				$id=$consumanbleid[$r];
				$spareqty=$this->input->post('spareqty'.$id);
				$spareprice=$this->input->post('spareprice'.$id);
				$data=array('qty'=>$spareqty,
				'price'=>$spareprice);
				$this->db->where('id',$id);
				$this->db->update('quotation_consumable_spares',$data);

			}


		/*If Discount Given*/
		$discountinpercent = "";
		$discountinfixvalue = "";
		if ($this->input->post('discountapplicable')!='') {

			$discounttype = $this->input->post('discunttype');
			if($discounttype==1){
				$discountinpercent = $this->input->post('discountinpercent');
			}else if($discounttype==2){
				$discountinfixvalue =$this->input->post('discountinamount');
			}else{
				$discountinpercent = "";
				$discountinfixvalue = "";
			}

			if($discountinpercent<>'' || $discountinfixvalue<>''){

				$discountdata = array('record_id'=>$this->uri->segment(4),
					'discount_type'=>$discounttype,
					'discount_in_percent'=>$discountinpercent,
					'discount_in_value'=>$discountinfixvalue,
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$user_id);

				$q = $this->db->select('id')->from('quotation_discount_info')->where('record_id',$this->uri->segment(4))->get();
				if($q->num_rows()>0){
					foreach($q->result() as $disdata);

					$this->db->where('id',$disdata->id);
					$this->db->update('quotation_discount_info',$discountdata);
				}else{
					$this->db->insert('quotation_discount_info',$discountdata);
				}
			}

		}



		// if ($this->input->post('consumablespare')!='') {
		// 	/*Consumable Spares*/

		// 	if(isset($_REQUEST['spareqty'])){	
		// 			$tags1=count($_REQUEST['spareqty']);
		// 			if($tags1>0)
		// 			{
		// 			//$partname = $_REQUEST['partname'];
		// 			$spareqty=$_REQUEST['spareqty'];
		// 			$spareprice=$_REQUEST['spareprice'];
					
		// 			$i=1;
		// 			for($x=0;$x<$tags1;$x++){
		// 			// if($partname[$x]!='')
		// 			// 	{
		// 					$data=array('record_id'=>$recordid,
		// 					'qty'=>$spareqty[$x],
		// 					'price'=>$spareprice[$x],
		// 					'added_by'=>$user_id,
		// 					'added_on'=>date('Y-m-d H:i:s'));
		// 					$this->db->insert('quotation_consumable_spares',$data);
							
		// 				//}
		// 			$i++;	
		// 			}
		// 			}
		// 			}


		// 	/*Consumable Spares*/
		// }
			/** UPDATE LEAD STATUS **/

			$d=array(
					'lead_id'=>$this->uri->segment(3),
					'lead_status'=>39,
					'next_follow_date'=> date('Y-m-d', strtotime("+1 day")),
					'added_on'=>date('Y-m-d H:i:s'),
					'added_by'=>$_SESSION['logged_in']['user_id']
				);
			$this->db->insert('progress_remarks',$d);


			$reasondata = array('lead_id'=>$this->uri->segment(3),
			'reason'=>$this->input->post('reasonforchange'),
			'added_on'=>date('Y-m-d H:i:s'),
			'added_by'=>$_SESSION['logged_in']['user_id']);
			$this->db->insert('quotation_change_reason',$reasondata);


 				redirect(page_url.'Opportunity/GeneratedQuote/'.$recordid."/".$this->uri->segment(4));
					
// $this->session->set_flashdata('message','<div style="color:red;" class="alert alert-success">Thank you, Your Quote Updated.</div>');

// 			redirect(page_url.'Opportunity/edit_opportunity/'.$this->uri->segment(3));

		}

		
}


function delete_product_packed()
{
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->delete('quotation_pouch_size');

	echo true;
}

public function fetch_machine_axis_options() {
    $q70 = $this->db->select('axis_name')->from('machine_axis_master')->order_by('sort_order', 'ASC')->get();
    $options = "";
    foreach ($q70->result() as $axisrow) {
        $options .= '<option value="'.$axisrow->axis_name.'">'.$axisrow->axis_name.'</option>';
    }
    echo $options;
}


function CloneQuotation()
{
	$this->load->view('opportunity/clone_opportunity_quote');
}



// function Clone_newopportunity()
// {
// 	//echo $this->uri->segment(6); exit;
//     $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
//     $this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
//     $this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
//     $this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
//     $this->form_validation->set_rules('cur', 'Currency', 'required|trim');
//     $this->form_validation->set_rules('country', 'Country', 'required|trim');
//     $this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
//     $this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
//     /*Validation till general information*/
//     $this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
//     $this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
//     $this->form_validation->set_rules('machtype', 'Machine Type', 'required|trim');
//     $this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
//     $this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
//     $this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
//     $this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
//     // $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
//     $this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');

//     $this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
//     $this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
//     $this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
//     $this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
//     $this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
//     $this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
//     $this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
//     $this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
//     $this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
//     $this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
//     $this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
//     $this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
//     $this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
//     $this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
//     $this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
//     $this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
//     $this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
//     $this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
//     $this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
//     $this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');

//     $this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
//     $this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
//     $this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');

//     $user_id = $this->session->userdata['logged_in']['user_id'];

//     if ($this->form_validation->run() == FALSE)
//     {
//         $this->load->view('opportunity/edit_opportunity_quote');
//         return;
//     }

//     // --- find product for lead (same as before)
//     $rest = $this->db->select('product_id')->from('lead_products')->where('lead_id', $this->uri->segment(6))->get();
//     if ($rest->num_rows() > 0)
//     {
//         foreach ($rest->result() as $row);
//         $product_id = $row->product_id;
//     } else {
//         echo "Product Not Found"; exit;
//     }

//     // version handling same as before
//     // if ($this->uri->segment(5) == 1) {
//     //     $version = $this->getCurrentVersion($this->uri->segment(6));
//     //     $newversion = $version + 1;
//     // } else {
//     //     $newversion = $this->getCurrentVersion($this->uri->segment(6));
//     // }
//     $newversion = 1;

//     // -------------------------
//     // INSERT master quote row (instead of update)
//     // -------------------------

//     $gst_actual=0;
// 			if($this->input->post('country')==101)
// 			{
// 			if($this->input->post('gstapplicable')==1)
// 			{
// 				$gst_actual=1;
// 			}
// 			}

//     	$master_data = array('lead_id'=>$this->uri->segment(6),
// 				'product_id'=>$product_id,
// 				'ref_no'=>$this->input->post('refno'),
// 				'quotation_date'=>date('Y-m-d',strtotime($this->input->post('quote_date'))),
// 				'customer_id'=>$this->input->post('customername'),
// 				'currency'=>$this->input->post('cur'),
// 				'country'=>$this->input->post('country'),
// 				'machine_name'=>$this->input->post('machineName'),
// 				'machine_model_no'=>$this->input->post('machineModel'),
// 				'cantilever'=>$this->input->post('cantilever'),
// 				'mach_model_no'=>$this->input->post('mach_model_no'),
// 				'added_on'=>date('Y-m-d H:i:s'),
// 				'last_revision_date'=>date('Y-m-d'),
// 				'special_notes'=>$this->input->post('specialNotes'),
// 				'gst_actual'=>$gst_actual,
// 				'validity'=>$this->input->post('validity'),
// 				'added_by'=>$user_id);
//     $this->db->insert('quotation_customer_data', $master_data);
//     $new_recordid = $this->db->insert_id();
//     // Use this new record id for all child inserts
//     $recordid = $new_recordid;

//        // -------------------------
//     // ANNEXTURE 1 (insert new)
//     // -------------------------
//     $data1 = array(
//         'record_id' => $recordid,
//         'product_to_be_packed' => $this->input->post('producttobepacked'),
//         'liquid_option' => $this->input->post('liquid_option'),
//         'powder_option' => $this->input->post('powder_option'),
//         'non_viscous_option' => $this->input->post('non_viscous_option'),
//         'viscous_option' => $this->input->post('viscous_option'),
//         'piston_filler_option' => $this->input->post('piston_filler_option'),
//         'follow_meter_option' => $this->input->post('follow_meter_option'),
//         'free_flow_option' => $this->input->post('free_flow_option'),
//         'weigher_system_option' => $this->input->post('weigher_system_option'),
//         'liner_weigher_option' => $this->input->post('liner_weigher_option'),
//         'mult_head_weigher_option' => $this->input->post('mult_head_weigher_option'),
//         'volumetric_cap_option' => $this->input->post('volumetric_cap_option'),
//         'non_free_flow_option' => $this->input->post('non_free_flow_option'),
//         'product_name' => $this->input->post('productname'),
//         'horizontal_sealing_width' => $this->input->post('horizontalsealingwidth'),
//         'vertical_sealing_width' => $this->input->post('verticalsealingwidth'),
//         'perforation_pitch' => $this->input->post('perforationpitch'),
//         'perforationstyle' => $this->input->post('perforationstyle'),
//         'batchcut' => $this->input->post('batchcut'),
//         'typeofsealing' => $this->input->post('typeofsealing'),
//         'power_supply' => $this->input->post('powersupply'),
//         'liquidviscositydata' => $this->input->post('liquidviscositydata'),
//         'liquidconductivitydata' => $this->input->post('liquidconductivitydata'),
//         'powderdensitydata' => $this->input->post('powderdensitydata'),
//         'powderdfrdata' => $this->input->post('powderdfrdata'),
//         'powdermoisturecontentdata' => $this->input->post('powdermoisturecontentdata'),
//         'machine_type' => $this->input->post('machtype'),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_annexture_1', $data1);

//     // -------------------------
//     // PACKING SIZES: existing ones in form (pack_id[]) and new added ones
//     // For cloning, insert all pack entries as new rows using posted values.
//     // -------------------------
//     if ($this->input->post('pack_id')) {
//         // Some forms may send pack_id for existing items; for clone we insert new rows using posted edit values
//         for ($i = 0; $i < count($this->input->post('pack_id')); $i++)
//         {
//             $pckid = $this->input->post('pack_id')[$i];
//             // use posted edit fields (they contain original values loaded into form)
//             $packedqty = $this->input->post('editqtytobepacked' . $pckid);
//             $unit = $this->input->post('editqty_unit' . $pckid);
//             $length = $this->input->post('editpouchsizel' . $pckid);
//             $width = $this->input->post('editpouchsizew' . $pckid);
//             $height = $this->input->post('editpouchsizeh' . $pckid);
//             if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName') == "HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName') == "HFFS MULTI TRACK MACHINE" || $this->input->post('machineName') == "HFFS PICK FILL SEAL")
//             {
//                 $gusset = $this->input->post('editgusset' . $pckid);
//                 $punchhole = $this->input->post('editpunch_hole' . $pckid);
//             } else {
//                 $gusset = '';
//                 $punchhole = '';
//             }

//             $data = array(
//                 'record_id' => $recordid,
//                 'packing_qty' => $packedqty,
//                 'unit' => $unit,
//                 'length' => $length,
//                 'width' => $width,
//                 'height' => $height,
//                 'gusset' => $gusset,
//                 'punchhole' => $punchhole
//             );
//             $this->db->insert('quotation_pouch_size', $data);
//         }
//     }

//     // New appended pouch sizes (addMorePRDPacked)
//     if ($this->input->post('addMorePRDPacked') == 1)
//     {
//         $qtytobepacked = $this->input->post('qtytobepacked');
//         for ($u = 0; $u < count($qtytobepacked); $u++)
//         {
//             $packedqty = $qtytobepacked[$u];
//             $unit = $this->input->post('qty_unit')[$u];
//             $length = $this->input->post('pouchsizel')[$u];
//             $width = $this->input->post('pouchsizew')[$u];
//             $height = $this->input->post('pouchsizeh')[$u];
//             if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE")
//             {
//                 $gusset = $this->input->post('gusset')[$u];
//                 $punchhole = $this->input->post('punch_hole')[$u];
//             } else {
//                 $gusset = '';
//                 $punchhole = '';
//             }

//             $data = array(
//                 'record_id' => $recordid,
//                 'packing_qty' => $packedqty,
//                 'unit' => $unit,
//                 'length' => $length,
//                 'width' => $width,
//                 'height' => $height,
//                 'gusset' => $gusset,
//                 'punchhole' => $punchhole
//             );
//             $this->db->insert('quotation_pouch_size', $data);
//         }
//     }

//     // -------------------------
//     // CUM TECH SPEC: previous update -> insert new with flag=1
//     // -------------------------
//     $data2 = array('record_id' => $recordid, 'model' => $this->input->post('machinemodelno'), 'flag' => 1, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//     $this->db->insert('quotation_cum_tech_spec', $data2);

//     // Update previous technicalData entries posted as editable (they were existing ids in original)
//     $technicalDataid = $this->input->post('technicalDataid');
//     if (is_array($technicalDataid) && count($technicalDataid) > 0)
//     {
//         // For cloning, insert copies (use posted edit values) instead of updating original ids
//         for ($y = 0; $y < count($technicalDataid); $y++)
//         {
//             $dmodel = $this->input->post('technicalspecedit' . $technicalDataid[$y]);
//             $data21 = array('record_id' => $recordid, 'model' => $dmodel, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_cum_tech_spec', $data21);
//         }
//     }

//     // Add multiple technical specs (new ones)
//     if (isset($_REQUEST['technicalspec']))
//     {
//         $tags1 = count($_REQUEST['technicalspec']);
//         if ($tags1 > 0)
//         {
//             $technicalspec = $_REQUEST['technicalspec'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($technicalspec[$x] != '')
//                 {
//                     $data21 = array(
//                         'record_id' => $recordid,
//                         'model' => $technicalspec[$x],
//                         'added_on' => date('Y-m-d H:i:s'),
//                         'added_by' => $user_id
//                     );
//                     $this->db->insert('quotation_cum_tech_spec', $data21);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // AXIS: existing axis entries -> insert copies
//     // -------------------------
//     $axisid = $this->input->post('axis_id');
//     if (is_array($axisid) && count($axisid) > 0)
//     {
//         for ($y = 0; $y < count($axisid); $y++)
//         {
//             $noofaxisinmachineinputedit = $this->input->post('editnoofaxisinmachine' . $axisid[$y]);
//             $editnoofaxisincount = $this->input->post('editnoofaxisincount' . $axisid[$y]);
//             $data21 = array(
//                 'record_id' => $recordid,
//                 'description' => $noofaxisinmachineinputedit,
//                 'axiscount' => $editnoofaxisincount,
//                 'added_on' => date('Y-m-d H:i:s'),
//                 'added_by' => $user_id
//             );
//             $this->db->insert('quotation_no_of_axis_in_machine', $data21);
//         }
//     }

//     // New axes
//     if ($this->input->post('more_axis') == 1)
//     {
//         if (isset($_REQUEST['noofaxisinmachine']))
//         {
//             $tags1 = count($_REQUEST['noofaxisinmachine']);
//             if ($tags1 > 0)
//             {
//                 $technicalspecs = $_REQUEST['noofaxisinmachine'];
//                 $noofaxisincount = $_REQUEST['noofaxisincount'];
//                 for ($x = 0; $x < $tags1; $x++)
//                 {
//                     if ($technicalspecs[$x] != '')
//                     {
//                         $data22 = array(
//                             'record_id' => $recordid,
//                             'description' => $technicalspecs[$x],
//                             'axiscount' => $noofaxisincount[$x],
//                             'added_on' => date('Y-m-d H:i:s'),
//                             'added_by' => $user_id
//                         );
//                         $this->db->insert('quotation_no_of_axis_in_machine', $data22);
//                     }
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // ANNEXURE 2 (insert new)
//     // -------------------------
//     $laminatewidth = $this->input->post('laminatewidth') . "<br>";
//     $laminatereeldia = $this->input->post('laminatereeldia') . "<br>";
//     $laminatereelcoredia = $this->input->post('laminatereelcoredia') . "<br>";
//     $layoutdimensionslength = $this->input->post('layoutdimensionslength');
//     $layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
//     $layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
//     $layoutdimensionsof = $layoutdimensionslength . "<br>" . $layoutdimensionswidth . "<br>" . $layoutdimensionsheight . "<br>";

//     $data3 = array(
//         'record_id' => $recordid,
//         'machinemodel' => $this->input->post('machinemodel'),
//         'filling_accuracy' => $this->input->post('fillingaccuracy'),
//         'sealingstyle' => $this->input->post('sealingstyle'),
//         'speed' => $this->input->post('designspeed'),
//         'actual_speed' => $this->input->post('actualspeed'),
//         'no_of_track' => $this->input->post('nooftracks'),
//         'leminate_specification' => $laminatewidth,
//         'laminatereeldia' => $laminatereeldia,
//         'laminatereelcoredia' => $laminatereelcoredia,
//         'product_to_be_packed' => $this->input->post('product_tobepacked'),
//         'filling_capacity' => $this->input->post('fillingcapacity'),
//         'electrical_spec' => $this->input->post('electricalspec'),
//         'layout_dimensions' => $layoutdimensionsof,
//         'machine_weight' => $this->input->post('netweight'),
//         'gross_weight' => $this->input->post('grossweight'),
//         'compressed_air' => $this->input->post('compressedaircfa'),
//         'compressedairbar' => $this->input->post('compressedairbar'),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_annexure_2', $data3);

//     // -------------------------
//     // ANNEXURE 4 / optional items - existing ones posted -> insert copies
//     // -------------------------

//       $data4 = array('record_id'=>$recordid,
//             'description'=>$this->input->post('modelno'),
//             'hsn'=>$this->input->post('modelhsn'),
//             'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
//             'qty'=>$this->input->post('modelqty'),
//             'unit'=>$this->input->post('qtyunit'),
//             'price'=>$this->input->post('modelprice'),
//             'addedOn'=>date('Y-m-d H:i:s'),
//             'flag'=>0,
//             'addedBy'=>$user_id);
//             $this->db->insert('quotation_annexture_4',$data4);


            
//     $existing_optional = $this->input->post('existing_optional');
//     if (is_array($existing_optional) && count($existing_optional) > 0)
//     {
//         for ($r = 0; $r < count($existing_optional); $r++)
//         {
//             $id = $existing_optional[$r];
//             $techdescriptioninfo = $this->input->post('edittechdescriptioninfo' . $id);
//             $techdescqty = $this->input->post('edittechdescqty' . $id);
//             $techdescprice = $this->input->post('edittechdescprice' . $id);
//             $edittechhsn = $this->input->post('edittechhsn' . $id);
//             $edittechunit = $this->input->post('edittechunit' . $id);
//             $totalprice = $techdescqty * $techdescprice;

//             // If techdescriptioninfo is numeric and refers to instrument id, keep it. Else insert into presto_instruments first.
//             if (is_numeric($techdescriptioninfo) && !strpos($techdescriptioninfo, '.')) {
//                 $techdescitemid = $techdescriptioninfo;
//             } else {
//                 $datass = array('instruments_name' => $techdescriptioninfo, 'type' => 1, 'status' => 1);
//                 $this->db->insert('presto_instruments', $datass);
//                 $techdescitemid = $this->db->insert_id();
//             }

//             $data4 = array(
//                 'record_id' => $recordid,
//                 'description' => $techdescitemid,
//                 'qty' => $techdescqty,
//                 'price' => $techdescprice,
//                 'hsn' => $edittechhsn,
//                 'unit' => $edittechunit,
//                 'total_price' => $totalprice,
//                 'addedOn' => date('Y-m-d H:i:s'),
//                 'category_type' => 1,
//                 'addedBy' => $user_id
//             );
//             $this->db->insert('quotation_annexture_4', $data4);
//         }
//     }

//     // Add new technical charges optional
//     if (isset($_REQUEST['techdescriptioninfo']))
//     {
//         $tags1 = count($_REQUEST['techdescriptioninfo']);
//         if ($tags1 > 0)
//         {
//             $techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
//             $techdescqty = $_REQUEST['techdescqty'];
//             $techdescprice = $_REQUEST['techdescprice'];
//             $techsn = $_REQUEST['techsn'];
//             $techunit = $_REQUEST['techunit'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($techdescriptioninfo[$x] != '')
//                 {
//                     if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {
//                         $techdescitemid = $techdescriptioninfo[$x];
//                     } else {
//                         $datass = array('instruments_name' => $techdescriptioninfo[$x], 'type' => 1, 'status' => 1);
//                         $this->db->insert('presto_instruments', $datass);
//                         $techdescitemid = $this->db->insert_id();
//                     }

//                     $totalprice = $techdescqty[$x] * $techdescprice[$x];
//                     $data4 = array(
//                         'record_id' => $recordid,
//                         'description' => $techdescitemid,
//                         'qty' => $techdescqty[$x],
//                         'price' => $techdescprice[$x],
//                         'hsn' => $techsn[$x],
//                         'unit' => $techunit[$x],
//                         'total_price' => $totalprice,
//                         'addedOn' => date('Y-m-d H:i:s'),
//                         'flag' => ($x + 1),
//                         'category_type' => 1,
//                         'addedBy' => $user_id
//                     );
//                     $this->db->insert('quotation_annexture_4', $data4);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // OPTIONAL DATA: existing optional items (quotation_optional) - insert copies
//     // -------------------------
//     $optionaldatainfo = $this->input->post('optionaldatainfo');
//     if (is_array($optionaldatainfo) && count($optionaldatainfo) > 0)
//     {
//         for ($r = 0; $r < count($optionaldatainfo); $r++)
//         {
//             $id = $optionaldatainfo[$r];
//             $optionalitem = $this->input->post('optionalitem' . $id);
//             $optionalqty = $this->input->post('optionalqty' . $id);
//             $optionalprice = $this->input->post('optionalprice' . $id);

//             if (is_numeric($optionalitem)) {
//                 $itemid = $optionalitem;
//             } else {
//                 $datass = array('instruments_name' => $optionalitem);
//                 $this->db->insert('presto_instruments', $datass);
//                 $itemid = $this->db->insert_id();
//             }
//             $totalprice = $optionalqty * $optionalprice;
//             $data = array(
//                 'record_id' => $recordid,
//                 'description' => $itemid,
//                 'value' => $optionalqty,
//                 'price' => $optionalprice,
//                 'totalprice' => $totalprice,
//                 'addedBy' => $user_id,
//                 'addedOn' => date('Y-m-d H:i:s')
//             );
//             $this->db->insert('quotation_optional', $data);
//         }
//     }

//     // New optional items
//     if (isset($_REQUEST['optionalitem']))
//     {
//         $tags1 = count($_REQUEST['optionalitem']);
//         if ($tags1 > 0)
//        {
//             $optionalitem = $_REQUEST['optionalitem'];
//             $optionalqty = $_REQUEST['optionalqty'];
//             $optionalprice = $_REQUEST['optionalprice'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($optionalitem[$x] != '')
//                 {
//                     if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
//                         $itemid = $optionalitem[$x];
//                     } else {
//                         $datass = array('instruments_name' => $optionalitem[$x], 'type' => 1, 'status' => 1);
//                         $this->db->insert('presto_instruments', $datass);
//                         $itemid = $this->db->insert_id();
//                     }

//                     $totalprice = $optionalqty[$x] * $optionalprice[$x];
//                     $data = array(
//                         'record_id' => $recordid,
//                         'description' => $itemid,
//                         'value' => $optionalqty[$x],
//                         'price' => $optionalprice[$x],
//                         'totalprice' => $totalprice,
//                         'addedBy' => $user_id,
//                         'addedOn' => date('Y-m-d H:i:s')
//                     );
//                     $this->db->insert('quotation_optional', $data);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // OTHER INFORMATION (quotation_other_information) - insert new
//     // -------------------------
//     $consumablespare = ($this->input->post('consumablespare') != '') ? $this->input->post('consumablespare') : 0;
//     $data13 = array(
//         'record_id' => $recordid,
//         'terms_value' => $this->input->post('paymentterms'),
//         'delivery_value' => $this->input->post('delivery'),
//         'pocket_expense' => $this->input->post('out_of_pocket_expense'),
//         'addedBy' => $user_id,
//         'consumable_spare' => $consumablespare,
//         'addedOn' => date('Y-m-d H:i:s')
//     );
//     $this->db->insert('quotation_other_information', $data13);

//     // -------------------------
//     // LAYOUT IMAGE handling - insert new if uploaded
//     // -------------------------

//       if ($this->input->post('layoutapplicable') != '')
//     {
//         $photo1 = $_FILES['uploadlayout']['name'];
//         if ($photo1 <> '')
//         {
//             $image2 = explode('.', $photo1);
//             $cat_image1 = end($image2);
//             $layoutimage = time() . '.' . $cat_image1;
//             move_uploaded_file($_FILES['uploadlayout']["tmp_name"], UPLOADPATH . 'opportunitydocs/layoutimg/' . $layoutimage);
//             $data = array(
//                 'record_id' => $recordid,
//                 'image' => $layoutimage,
//                 'added_on' => date('Y-m-d H:i:s'),
//                 'added_by' => $user_id
//             );
//             $this->db->insert('quotation_layout_img', $data);
//         }else
//         {

//         	$old_path=$this->input->post('uploadlayout_path');
    		
//     		if($old_path!='')
//     		{
		
// 		$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 		$new = time()."67_clone.".$ext;
// 		copy($old_path, UPLOADPATH."opportunitydocs/layoutimg/".$new);

// 		$this->db->insert('quotation_layout_img', [
// 		'record_id'=>$recordid,
// 		'image'=>$new,
// 		'added_on'=>date('Y-m-d H:i:s'),
// 		'added_by'=>$user_id
// 		]);

// 		}


//         }
//     }


//     // MACHINE FILLING image
//     if ($this->input->post('machinefillingsystem') != '')
//     {
//         $photo = $_FILES['machinefillingimg']['name'];
//         $machineimage = "";
//         if ($photo <> '')
//         {
//             $image1 = explode('.', $photo);
//             $cat_image = end($image1);
//             $machineimage = time() . '.' . $cat_image;
//             move_uploaded_file($_FILES['machinefillingimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
//             $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_machine_filling_image', $data);
//         }else{

//         		$old_path=$this->input->post('machinefillingimg_path');
// 		if($old_path!='')
// 		{
// 			$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 			$new = time()."34_clone.".$ext;
// 			copy($old_path, UPLOADPATH."opportunitydocs/".$new);
// 			$this->db->insert('quotation_machine_filling_image', [
// 			'record_id'=>$recordid,
// 			'image'=>$new,
// 			'added_on'=>date('Y-m-d H:i:s'),
// 			'added_by'=>$user_id
// 			]);
// 		}
//         }
//     }

//     // KLD image
//     if ($this->input->post('kld') == 1)
//     {
//         $photo = $_FILES['kldimg']['name'];
//         if ($photo <> '')
//         {
//             $image1 = explode('.', $photo);
//             $cat_image = end($image1);
//             $machineimage = time() . '1234.' . $cat_image;
//             move_uploaded_file($_FILES['kldimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
//             $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_kld_image', $data);
//         }else
//         {

//         	$old_path=$this->input->post('kldimg_path');
// 		if($old_path!='')
// 		{
// 		$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 		$new = time()."1_clone.".$ext;
// 		copy($old_path, UPLOADPATH."opportunitydocs/".$new);
// 		$this->db->insert('quotation_kld_image', [
// 		'record_id'=>$recordid,
// 		'image'=>$new,
// 		'added_on'=>date('Y-m-d H:i:s'),
// 		'added_by'=>$user_id
// 		]);
// 		}

//         }

//     }

//     // -------------------------
//     // BRAND DATA: insert new rows for each posted brand selection
//     // -------------------------
//     if ($this->input->post('brands'))
//     {
//         for ($i = 0; $i < count($this->input->post('brands')); $i++)
//         {
//             $brand_id = $this->input->post('brands')[$i];
//             $brand_data = $this->input->post('brand_data')[$i];
//             $data = array('record_id' => $recordid, 'head_id' => $brand_id, 'value_id' => $brand_data, 'addedOn' => date('Y-m-d'), 'addedBy' => $user_id);
//             $this->db->insert('quotation_brand_data', $data);
//         }
//     }

//     // -------------------------
//     // MACHINE PRICE INFO - insert new
//     // -------------------------
//     $grandtotalprice = $this->input->post('modelqty') * $this->input->post('modelprice');
//     $machinepricedata = array(
//         'record_id' => $recordid,
//         'machine_model' => $this->input->post('modelno'),
//         'qty' => $this->input->post('modelqty'),
//         'unit_price' => $this->input->post('modelprice'),
//         'totalprice' => $grandtotalprice,
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_machine_price_info', $machinepricedata);

//     // -------------------------
//     // FREIGHT / PACKING / FORWARDING - insert new
//     // -------------------------
//     $packingingcharge = ($this->input->post('packagingapplicable') != '') ? $this->input->post('packagingpercentage') : "";
//     $forwardingcharges = ($this->input->post('forwardingapplicable') != '') ? $this->input->post('forwardingpercentage') : "";
//     $insurancecharges = ($this->input->post('insuranceapplicable') != '') ? $this->input->post('insurancepercentage') : "";
//     $freight_charges = ($this->input->post('frightinfo') == 3 || $this->input->post('frightinfo') == 4) ? $this->input->post('freightamount') : "";
//     $freight_type = $this->input->post('freightType');
//     $port_id = 0;
//     if ($freight_type == "FOB" || $freight_type == "CIF" || $freight_type == "CFR")
//     {
//         $port = $this->input->post('port');
//         if (is_numeric($port) && !strpos($port, '.')) {
//             $port_id = $port;
//         } else {
//             $dt = array('name' => $port);
//             $this->db->insert('ports', $dt);
//             $port_id = $this->db->insert_id();
//         }
//     }

//     if ($this->input->post('installationcommissioningapplicable')!='') {
// 				$installationcommissioningamount = $this->input->post('installationcommissioningamount');
// 			}else{
// 				$installationcommissioningamount = "";
// 			}

//     $freightdata = array(
//         'record_id' => $recordid,
//         'freight' => $this->input->post('frightinfo'),
//         'freight_charges' => $freight_charges,
//         'freight_type' => $freight_type,
//         'packing_charges' => $packingingcharge,
//         'forwarding_charges' => $forwardingcharges,
//         'port' => $port_id,
//         'insurance' => $insurancecharges,
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_freight_packing_forwarding', $freightdata);

//     // -------------------------
//     // CONSUMABLE SPARES - existing posted entries: insert copies
//     // -------------------------
// 	$consumanbleid = $this->input->post('consumanbleid');
// 	if($this->input->post('consumalbleNew')==0)
// 	{
// 	if (is_array($consumanbleid) && count($consumanbleid) > 0)
// 	{
// 	for ($r = 0; $r < count($consumanbleid); $r++)
// 	{
// 	$id = $consumanbleid[$r];
// 	$spareqty = $this->input->post('spareqty' . $id);
// 	$spareprice = $this->input->post('spareprice' . $id);
// 	$data = array('record_id' => $recordid, 'qty' => $spareqty, 'price' => $spareprice, 'added_by' => $user_id, 'added_on' => date('Y-m-d H:i:s'));
// 	$this->db->insert('quotation_consumable_spares', $data);
// 	}
// 	}
// 	}else
// 	{
// 		if (isset($_REQUEST['spareqty'])) {
// 		$tags1 = count($_REQUEST['spareqty']);
// 		for ($x=0;$x<$tags1;$x++){
// 		$data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
// 		$this->db->insert('quotation_consumable_spares',$data);
// 		}
// 		}

// 	}
//     // Optionally, if new spare fields were posted as arrays, insert them too (uncomment if used)
//     /*
//     if (isset($_REQUEST['spareqty'])) {
//         $tags1 = count($_REQUEST['spareqty']);
//         for ($x=0;$x<$tags1;$x++){
//             $data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
//             $this->db->insert('quotation_consumable_spares',$data);
//         }
//     }
//     */


//     /** UPDATE TERMS & CONDITIONS **/
// 		if($this->input->post('country')==101)
// 		{
// 		$liquidated_applicable = $this->input->post('liquidated_clause_applicable');
// 		if($liquidated_applicable==1)
// 		{
// 			$liquidated_applicable=1;
// 		}else
// 		{
// 			$liquidated_applicable=0;
// 		}

// 		$late_delivery_applicable=$this->input->post('late_delivery_applicable');
// 		if($late_delivery_applicable==1)
// 		{
// 			$late_delivery_applicable=1;
// 		}else
// 		{
// 			$late_delivery_applicable=0;
// 		}

// 		// get and xss_clean content fields (optional: keep html if you use editors)
// 		$liquidated_text  = $this->input->post('liquidated_clause');
// 		$packing_charges  = $this->input->post('packing_charges');
// 		$insurance        = $this->input->post('insurance');
// 		$installation     = $this->input->post('installation');
// 		$late_delivery_text  = $this->input->post('late_delivery');

// 		$payload = [
// 		'quotation_id' => $recordid,
// 		'liquidated_applicable' => $liquidated_applicable,
// 		'liquidated_text' => $liquidated_text ?: null,
// 		'packing_charges' => $packing_charges ?: null,
// 		'late_delivery_applicable'=>$late_delivery_applicable,
// 		'late_delivery_text'=>$late_delivery_text ? : null,
// 		'insurance' => $insurance ?: null,
// 		'installation' => $installation ?: null
// 		];

// 		$this->db->insert('quotation_custom_terms',$payload);
// 		}else
// 		{
// 			$this->db->where('quotation_id',$recordid);
// 			$this->db->delete('quotation_custom_terms');
// 		}

// 	/** END **/



//     // -------------------------
//     // QUOTATION PROGRESS REMARK
//     // -------------------------
//     $d = array(
//         'lead_id' => $this->uri->segment(6),
//         'lead_status' => 39,
//         'next_follow_date' => date('Y-m-d', strtotime("+1 day")),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $_SESSION['logged_in']['user_id']
//     );
//     $this->db->insert('progress_remarks', $d);

//     // QUOTATION CHANGE REASON
//     // $reasondata = array(
//     //     'lead_id' => $this->uri->segment(6),
//     //     'record_id' => $recordid,
//     //     'reason' => $this->input->post('reasonforchange'),
//     //     'added_on' => date('Y-m-d H:i:s'),
//     //     'added_by' => $_SESSION['logged_in']['user_id']
//     // );
//     // $this->db->insert('quotation_change_reason', $reasondata);

//     // Redirect to generated quote (use new record id)
//     redirect(page_url . 'Opportunity/GeneratedQuote/' . $recordid . "/" . $this->uri->segment(4));
// }


function Clone_newopportunity()
{
    //echo $this->uri->segment(6); exit;
    $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
    $this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
    $this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
    $this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
    $this->form_validation->set_rules('cur', 'Currency', 'required|trim');
    $this->form_validation->set_rules('country', 'Country', 'required|trim');
    $this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
    $this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
    /*Validation till general information*/
    $this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
    $this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
    $this->form_validation->set_rules('machtype', 'Machine Type', 'required|trim');
    $this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
    $this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
    $this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
    $this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
    // $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
    $this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');

    $this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
    $this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
    $this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
    $this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
    $this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
    $this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
    $this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
    $this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
    $this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
    $this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
    $this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
    $this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
    $this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
    $this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
    $this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
    $this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
    $this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
    $this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
    $this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
    $this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
    $this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
    $this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
    $this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');

    $this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
    $this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
    $this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');

    $user_id = $this->session->userdata['logged_in']['user_id'];

    if ($this->form_validation->run() == FALSE)
    {
        $this->load->view('opportunity/edit_opportunity_quote');
        return;
    }

    // --- find product for lead (same as before)
    $rest = $this->db->select('product_id')->from('lead_products')->where('lead_id', $this->uri->segment(6))->get();
    if ($rest->num_rows() > 0)
    {
        foreach ($rest->result() as $row);
        $product_id = $row->product_id;
    } else {
        echo "Product Not Found"; exit;
    }

    // version handling same as before
    // if ($this->uri->segment(5) == 1) {
    //     $version = $this->getCurrentVersion($this->uri->segment(6));
    //     $newversion = $version + 1;
    // } else {
    //     $newversion = $this->getCurrentVersion($this->uri->segment(6));
    // }
    $newversion = 1;

    // -------------------------
    // INSERT master quote row (instead of update)
    // -------------------------

    $gst_actual=0;
            if($this->input->post('country')==101)
            {
            if($this->input->post('gstapplicable')==1)
            {
                $gst_actual=1;
            }
            }

        $master_data = array('lead_id'=>$this->uri->segment(6),
                'product_id'=>$product_id,
                'ref_no'=>$this->input->post('refno'),
                'quotation_date'=>date('Y-m-d',strtotime($this->input->post('quote_date'))),
                'customer_id'=>$this->input->post('customername'),
                'currency'=>$this->input->post('cur'),
                'country'=>$this->input->post('country'),
                'machine_name'=>$this->input->post('machineName'),
                'machine_model_no'=>$this->input->post('machineModel'),
                'cantilever'=>$this->input->post('cantilever'),
                'mach_model_no'=>$this->input->post('mach_model_no'),
                'added_on'=>date('Y-m-d H:i:s'),
                'last_revision_date'=>date('Y-m-d'),
                'special_notes'=>$this->input->post('specialNotes'),
                'gst_actual'=>$gst_actual,
                'validity'=>$this->input->post('validity'),
                'added_by'=>$user_id);
    $this->db->insert('quotation_customer_data', $master_data);
    $new_recordid = $this->db->insert_id();
    // Use this new record id for all child inserts
    $recordid = $new_recordid;

       // -------------------------
    // ANNEXTURE 1 (insert new)
    // -------------------------

    $product_to_be_packed = $this->input->post('producttobepacked');

$liquid_option            = $this->input->post('liquid_option');
$powder_option            = $this->input->post('powder_option');
$non_viscous_option       = $this->input->post('non_viscous_option');
$viscous_option           = $this->input->post('viscous_option');
$piston_filler_option     = $this->input->post('piston_filler_option');
$follow_meter_option      = $this->input->post('follow_meter_option');
$free_flow_option         = $this->input->post('free_flow_option');
$weigher_system_option    = $this->input->post('weigher_system_option');
$liner_weigher_option     = $this->input->post('liner_weigher_option');
$mult_head_weigher_option = $this->input->post('mult_head_weigher_option');
$volumetric_cap_option    = $this->input->post('volumetric_cap_option');
$non_free_flow_option     = $this->input->post('non_free_flow_option');

/* -------------------------------------------------
   Apply Product Hierarchy
--------------------------------------------------*/

if($product_to_be_packed == 1) // Liquid
{
    // Reset complete Powder hierarchy
    $powder_option = '';
    $free_flow_option = '';
    $non_free_flow_option = '';
    $weigher_system_option = '';
    $liner_weigher_option = '';
    $mult_head_weigher_option = '';
    $volumetric_cap_option = '';

    if($liquid_option == 'Non-Viscous')
    {
        // Reset Viscous hierarchy
        $viscous_option = '';
        $piston_filler_option = '';
        $follow_meter_option = '';
    }
    elseif($liquid_option == 'Viscous')
    {
        // Reset Non Viscous hierarchy
        $non_viscous_option = '';

        if($viscous_option == 'Piston Filler')
        {
            $follow_meter_option = '';
        }
        elseif($viscous_option == 'Flow meter')
        {
            $piston_filler_option = '';
        }
        else
        {
            $piston_filler_option = '';
            $follow_meter_option = '';
        }
    }
    else
    {
        // No Liquid option selected
        $non_viscous_option = '';
        $viscous_option = '';
        $piston_filler_option = '';
        $follow_meter_option = '';
    }
}
else // Powder
{
    // Reset complete Liquid hierarchy
    $liquid_option = '';
    $non_viscous_option = '';
    $viscous_option = '';
    $piston_filler_option = '';
    $follow_meter_option = '';

    if($powder_option == 'Free Flow')
    {
        // Reset Non Free Flow
        $non_free_flow_option = '';

        if($free_flow_option == 'Weigher System')
        {
            // Reset Volumetric Cup branch
            $volumetric_cap_option = '';

            if($weigher_system_option == 'Liner Weigher')
            {
                $mult_head_weigher_option = '';
            }
            elseif($weigher_system_option == 'Multi Head Weigher')
            {
                $liner_weigher_option = '';
            }
            else
            {
                $liner_weigher_option = '';
                $mult_head_weigher_option = '';
            }
        }
        elseif($free_flow_option == 'Volumetric Cup Filler')
        {
            // Reset Weigher hierarchy
            $weigher_system_option = '';
            $liner_weigher_option = '';
            $mult_head_weigher_option = '';
        }
        else
        {
            // Nothing selected under Free Flow
            $weigher_system_option = '';
            $liner_weigher_option = '';
            $mult_head_weigher_option = '';
            $volumetric_cap_option = '';
        }
    }
    elseif($powder_option == 'Non Free Flow')
    {
        // Reset Free Flow hierarchy
        $free_flow_option = '';
        $weigher_system_option = '';
        $liner_weigher_option = '';
        $mult_head_weigher_option = '';
        $volumetric_cap_option = '';
    }
    else
    {
        // No Powder option selected
        $free_flow_option = '';
        $non_free_flow_option = '';
        $weigher_system_option = '';
        $liner_weigher_option = '';
        $mult_head_weigher_option = '';
        $volumetric_cap_option = '';
    }
}

    $data1 = array(
        'record_id' => $recordid,
       'product_to_be_packed' => $product_to_be_packed,
'liquid_option' => $liquid_option,
'powder_option' => $powder_option,
'non_viscous_option' => $non_viscous_option,
'viscous_option' => $viscous_option,
'piston_filler_option' => $piston_filler_option,
'follow_meter_option' => $follow_meter_option,
'free_flow_option' => $free_flow_option,
'weigher_system_option' => $weigher_system_option,
'liner_weigher_option' => $liner_weigher_option,
'mult_head_weigher_option' => $mult_head_weigher_option,
'volumetric_cap_option' => $volumetric_cap_option,
'non_free_flow_option' => $non_free_flow_option,
        'product_name' => $this->input->post('productname'),
        'horizontal_sealing_width' => $this->input->post('horizontalsealingwidth'),
        'vertical_sealing_width' => $this->input->post('verticalsealingwidth'),
        'perforation_pitch' => $this->input->post('perforationpitch'),
        'perforationstyle' => $this->input->post('perforationstyle'),
        'batchcut' => $this->input->post('batchcut'),
        'typeofsealing' => $this->input->post('typeofsealing'),
        'power_supply' => $this->input->post('powersupply'),
        'liquidviscositydata' => $this->input->post('liquidviscositydata'),
        'liquidconductivitydata' => $this->input->post('liquidconductivitydata'),
        'powderdensitydata' => $this->input->post('powderdensitydata'),
        'powderdfrdata' => $this->input->post('powderdfrdata'),
        'powdermoisturecontentdata' => $this->input->post('powdermoisturecontentdata'),
        'machine_type' => $this->input->post('machtype'),
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id
    );
    $this->db->insert('quotation_annexture_1', $data1);

    // -------------------------
    // PACKING SIZES: existing ones in form (pack_id[]) and new added ones
    // For cloning, insert all pack entries as new rows using posted values.
    // -------------------------
    if ($this->input->post('pack_id')) {
        // Some forms may send pack_id for existing items; for clone we insert new rows using posted edit values
        for ($i = 0; $i < count($this->input->post('pack_id')); $i++)
        {
            $pckid = $this->input->post('pack_id')[$i];
            // use posted edit fields (they contain original values loaded into form)
            $packedqty = $this->input->post('editqtytobepacked' . $pckid);
            $unit = $this->input->post('editqty_unit' . $pckid);
            $length = $this->input->post('editpouchsizel' . $pckid);
            $width = $this->input->post('editpouchsizew' . $pckid);
            $height = $this->input->post('editpouchsizeh' . $pckid);
            if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName') == "HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName') == "HFFS MULTI TRACK MACHINE" || $this->input->post('machineName') == "HFFS PICK FILL SEAL")
            {
                $gusset = $this->input->post('editgusset' . $pckid);
                $punchhole = $this->input->post('editpunch_hole' . $pckid);
            } else {
                $gusset = '';
                $punchhole = '';
            }

            $data = array(
                'record_id' => $recordid,
                'packing_qty' => $packedqty,
                'unit' => $unit,
                'length' => $length,
                'width' => $width,
                'height' => $height,
                'gusset' => $gusset,
                'punchhole' => $punchhole
            );
            $this->db->insert('quotation_pouch_size', $data);
        }
    }

    // New appended pouch sizes (addMorePRDPacked)
    if ($this->input->post('addMorePRDPacked') == 1)
    {
        $qtytobepacked = $this->input->post('qtytobepacked');
        for ($u = 0; $u < count($qtytobepacked); $u++)
        {
            $packedqty = $qtytobepacked[$u];
            $unit = $this->input->post('qty_unit')[$u];
            $length = $this->input->post('pouchsizel')[$u];
            $width = $this->input->post('pouchsizew')[$u];
            $height = $this->input->post('pouchsizeh')[$u];
            if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE")
            {
                $gusset = $this->input->post('gusset')[$u];
                $punchhole = $this->input->post('punch_hole')[$u];
            } else {
                $gusset = '';
                $punchhole = '';
            }

            $data = array(
                'record_id' => $recordid,
                'packing_qty' => $packedqty,
                'unit' => $unit,
                'length' => $length,
                'width' => $width,
                'height' => $height,
                'gusset' => $gusset,
                'punchhole' => $punchhole
            );
            $this->db->insert('quotation_pouch_size', $data);
        }
    }

    // -------------------------
    // CUM TECH SPEC: previous update -> insert new with flag=1
    // -------------------------
    $data2 = array('record_id' => $recordid, 'model' => $this->input->post('machinemodelno'), 'flag' => 1, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
    $this->db->insert('quotation_cum_tech_spec', $data2);

    // Update previous technicalData entries posted as editable (they were existing ids in original)
    $technicalDataid = $this->input->post('technicalDataid');
    if (is_array($technicalDataid) && count($technicalDataid) > 0)
    {
        // For cloning, insert copies (use posted edit values) instead of updating original ids
        for ($y = 0; $y < count($technicalDataid); $y++)
        {
            $dmodel = $this->input->post('technicalspecedit' . $technicalDataid[$y]);
            $data21 = array('record_id' => $recordid, 'model' => $dmodel, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
            $this->db->insert('quotation_cum_tech_spec', $data21);
        }
    }

    // Add multiple technical specs (new ones)
    if (isset($_REQUEST['technicalspec']))
    {
        $tags1 = count($_REQUEST['technicalspec']);
        if ($tags1 > 0)
        {
            $technicalspec = $_REQUEST['technicalspec'];
            for ($x = 0; $x < $tags1; $x++)
            {
                if ($technicalspec[$x] != '')
                {
                    $data21 = array(
                        'record_id' => $recordid,
                        'model' => $technicalspec[$x],
                        'added_on' => date('Y-m-d H:i:s'),
                        'added_by' => $user_id
                    );
                    $this->db->insert('quotation_cum_tech_spec', $data21);
                }
            }
        }
    }

    // -------------------------
    // AXIS: existing axis entries -> insert copies
    // -------------------------
    $axisid = $this->input->post('axis_id');
    if (is_array($axisid) && count($axisid) > 0)
    {
        for ($y = 0; $y < count($axisid); $y++)
        {
            $noofaxisinmachineinputedit = $this->input->post('editnoofaxisinmachine' . $axisid[$y]);
            $editnoofaxisincount = $this->input->post('editnoofaxisincount' . $axisid[$y]);
            $data21 = array(
                'record_id' => $recordid,
                'description' => $noofaxisinmachineinputedit,
                'axiscount' => $editnoofaxisincount,
                'added_on' => date('Y-m-d H:i:s'),
                'added_by' => $user_id
            );
            $this->db->insert('quotation_no_of_axis_in_machine', $data21);
        }
    }

    // New axes
    if ($this->input->post('more_axis') == 1)
    {
        if (isset($_REQUEST['noofaxisinmachine']))
        {
            $tags1 = count($_REQUEST['noofaxisinmachine']);
            if ($tags1 > 0)
            {
                $technicalspecs = $_REQUEST['noofaxisinmachine'];
                $noofaxisincount = $_REQUEST['noofaxisincount'];
                for ($x = 0; $x < $tags1; $x++)
                {
                    if ($technicalspecs[$x] != '')
                    {
                        $data22 = array(
                            'record_id' => $recordid,
                            'description' => $technicalspecs[$x],
                            'axiscount' => $noofaxisincount[$x],
                            'added_on' => date('Y-m-d H:i:s'),
                            'added_by' => $user_id
                        );
                        $this->db->insert('quotation_no_of_axis_in_machine', $data22);
                    }
                }
            }
        }
    }

    // -------------------------
    // ANNEXURE 2 (insert new)
    // -------------------------
    $laminatewidth = $this->input->post('laminatewidth') . "<br>";
    $laminatereeldia = $this->input->post('laminatereeldia') . "<br>";
    $laminatereelcoredia = $this->input->post('laminatereelcoredia') . "<br>";
    $layoutdimensionslength = $this->input->post('layoutdimensionslength');
    $layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
    $layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
    $layoutdimensionsof = $layoutdimensionslength . "<br>" . $layoutdimensionswidth . "<br>" . $layoutdimensionsheight . "<br>";

    $data3 = array(
        'record_id' => $recordid,
        'machinemodel' => $this->input->post('machinemodel'),
        'filling_accuracy' => $this->input->post('fillingaccuracy'),
        'sealingstyle' => $this->input->post('sealingstyle'),
        'speed' => $this->input->post('designspeed'),
        'actual_speed' => $this->input->post('actualspeed'),
        'no_of_track' => $this->input->post('nooftracks'),
        'leminate_specification' => $laminatewidth,
        'laminatereeldia' => $laminatereeldia,
        'laminatereelcoredia' => $laminatereelcoredia,
        'product_to_be_packed' => $this->input->post('product_tobepacked'),
        'filling_capacity' => $this->input->post('fillingcapacity'),
        'electrical_spec' => $this->input->post('electricalspec'),
        'layout_dimensions' => $layoutdimensionsof,
        'machine_weight' => $this->input->post('netweight'),
        'gross_weight' => $this->input->post('grossweight'),
        'compressed_air' => $this->input->post('compressedaircfa'),
        'compressedairbar' => $this->input->post('compressedairbar'),
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id
    );
    $this->db->insert('quotation_annexure_2', $data3);

    // -------------------------
    // ANNEXURE 4 / optional items - existing ones posted -> insert copies
    // -------------------------

      $data4 = array('record_id'=>$recordid,
            'description'=>$this->input->post('modelno'),
            'hsn'=>$this->input->post('modelhsn'),
            'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
            'qty'=>$this->input->post('modelqty'),
            'unit'=>$this->input->post('qtyunit'),
            'price'=>$this->input->post('modelprice'),
            'addedOn'=>date('Y-m-d H:i:s'),
            'flag'=>0,
            'addedBy'=>$user_id);
            $this->db->insert('quotation_annexture_4',$data4);


            
    $existing_optional = $this->input->post('existing_optional');
    if (is_array($existing_optional) && count($existing_optional) > 0)
    {
        for ($r = 0; $r < count($existing_optional); $r++)
        {
            $id = $existing_optional[$r];
            $techdescriptioninfo = $this->input->post('edittechdescriptioninfo' . $id);
            $techdescqty = $this->input->post('edittechdescqty' . $id);
            $techdescprice = $this->input->post('edittechdescprice' . $id);
            $edittechhsn = $this->input->post('edittechhsn' . $id);
            $edittechunit = $this->input->post('edittechunit' . $id);
            $totalprice = $techdescqty * $techdescprice;

            // If techdescriptioninfo is numeric and refers to instrument id, keep it. Else insert into presto_instruments first.
            if (is_numeric($techdescriptioninfo) && !strpos($techdescriptioninfo, '.')) {
                $techdescitemid = $techdescriptioninfo;
            } else {
                $datass = array('instruments_name' => $techdescriptioninfo, 'type' => 1, 'status' => 1);
                $this->db->insert('presto_instruments', $datass);
                $techdescitemid = $this->db->insert_id();
            }

            $data4 = array(
                'record_id' => $recordid,
                'description' => $techdescitemid,
                'qty' => $techdescqty,
                'price' => $techdescprice,
                'hsn' => $edittechhsn,
                'unit' => $edittechunit,
                'total_price' => $totalprice,
                'addedOn' => date('Y-m-d H:i:s'),
                'category_type' => 1,
                'addedBy' => $user_id
            );
            $this->db->insert('quotation_annexture_4', $data4);
        }
    }

    // Add new technical charges optional
    if (isset($_REQUEST['techdescriptioninfo']))
    {
        $tags1 = count($_REQUEST['techdescriptioninfo']);
        if ($tags1 > 0)
        {
            $techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
            $techdescqty = $_REQUEST['techdescqty'];
            $techdescprice = $_REQUEST['techdescprice'];
            $techsn = $_REQUEST['techsn'];
            $techunit = $_REQUEST['techunit'];
            for ($x = 0; $x < $tags1; $x++)
            {
                if ($techdescriptioninfo[$x] != '')
                {
                    if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {
                        $techdescitemid = $techdescriptioninfo[$x];
                    } else {
                        $datass = array('instruments_name' => $techdescriptioninfo[$x], 'type' => 1, 'status' => 1);
                        $this->db->insert('presto_instruments', $datass);
                        $techdescitemid = $this->db->insert_id();
                    }

                    $totalprice = $techdescqty[$x] * $techdescprice[$x];
                    $data4 = array(
                        'record_id' => $recordid,
                        'description' => $techdescitemid,
                        'qty' => $techdescqty[$x],
                        'price' => $techdescprice[$x],
                        'hsn' => $techsn[$x],
                        'unit' => $techunit[$x],
                        'total_price' => $totalprice,
                        'addedOn' => date('Y-m-d H:i:s'),
                        'flag' => ($x + 1),
                        'category_type' => 1,
                        'addedBy' => $user_id
                    );
                    $this->db->insert('quotation_annexture_4', $data4);
                }
            }
        }
    }

    // -------------------------
    // OPTIONAL DATA: existing optional items (quotation_optional) - insert copies
    // -------------------------
    $optionaldatainfo = $this->input->post('optionaldatainfo');
    if (is_array($optionaldatainfo) && count($optionaldatainfo) > 0)
    {
        for ($r = 0; $r < count($optionaldatainfo); $r++)
        {
            $id = $optionaldatainfo[$r];
            $optionalitem = $this->input->post('optionalitem' . $id);
            $optionalqty = $this->input->post('optionalqty' . $id);
            $optionalprice = $this->input->post('optionalprice' . $id);

            if (is_numeric($optionalitem)) {
                $itemid = $optionalitem;
            } else {
                $datass = array('instruments_name' => $optionalitem);
                $this->db->insert('presto_instruments', $datass);
                $itemid = $this->db->insert_id();
            }
            $totalprice = $optionalqty * $optionalprice;
            $data = array(
                'record_id' => $recordid,
                'description' => $itemid,
                'value' => $optionalqty,
                'price' => $optionalprice,
                'totalprice' => $totalprice,
                'addedBy' => $user_id,
                'addedOn' => date('Y-m-d H:i:s')
            );
            $this->db->insert('quotation_optional', $data);
        }
    }

    // New optional items
    if (isset($_REQUEST['optionalitem']))
    {
        $tags1 = count($_REQUEST['optionalitem']);
        if ($tags1 > 0)
       {
            $optionalitem = $_REQUEST['optionalitem'];
            $optionalqty = $_REQUEST['optionalqty'];
            $optionalprice = $_REQUEST['optionalprice'];
            for ($x = 0; $x < $tags1; $x++)
            {
                if ($optionalitem[$x] != '')
                {
                    if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
                        $itemid = $optionalitem[$x];
                    } else {
                        $datass = array('instruments_name' => $optionalitem[$x], 'type' => 1, 'status' => 1);
                        $this->db->insert('presto_instruments', $datass);
                        $itemid = $this->db->insert_id();
                    }

                    $totalprice = $optionalqty[$x] * $optionalprice[$x];
                    $data = array(
                        'record_id' => $recordid,
                        'description' => $itemid,
                        'value' => $optionalqty[$x],
                        'price' => $optionalprice[$x],
                        'totalprice' => $totalprice,
                        'addedBy' => $user_id,
                        'addedOn' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert('quotation_optional', $data);
                }
            }
        }
    }

    // -------------------------
    // OTHER INFORMATION (quotation_other_information) - insert new
    // -------------------------
    $consumablespare = ($this->input->post('consumablespare') != '') ? $this->input->post('consumablespare') : 0;
    $data13 = array(
        'record_id' => $recordid,
        'terms_value' => $this->input->post('paymentterms'),
        'delivery_value' => $this->input->post('delivery'),
        'pocket_expense' => $this->input->post('out_of_pocket_expense'),
        'addedBy' => $user_id,
        'consumable_spare' => $consumablespare,
        'addedOn' => date('Y-m-d H:i:s')
    );
    $this->db->insert('quotation_other_information', $data13);

    // -------------------------
    // LAYOUT IMAGE handling - insert new if uploaded
    // -------------------------

      if ($this->input->post('layoutapplicable') != '')
    {
        $photo1 = $_FILES['uploadlayout']['name'];
        if ($photo1 <> '')
        {
            $image2 = explode('.', $photo1);
            $cat_image1 = end($image2);
            $layoutimage = time() . '.' . $cat_image1;
            move_uploaded_file($_FILES['uploadlayout']["tmp_name"], UPLOADPATH . 'opportunitydocs/layoutimg/' . $layoutimage);
            $data = array(
                'record_id' => $recordid,
                'image' => $layoutimage,
                'added_on' => date('Y-m-d H:i:s'),
                'added_by' => $user_id
            );
            $this->db->insert('quotation_layout_img', $data);
        }else
        {

            $old_path=$this->input->post('uploadlayout_path');
            
            if($old_path!='')
            {
        
        $ext = pathinfo($old_path, PATHINFO_EXTENSION);
        $new = time()."67_clone.".$ext;
        copy($old_path, UPLOADPATH."opportunitydocs/layoutimg/".$new);

        $this->db->insert('quotation_layout_img', [
        'record_id'=>$recordid,
        'image'=>$new,
        'added_on'=>date('Y-m-d H:i:s'),
        'added_by'=>$user_id
        ]);

        }


        }
    }


    // MACHINE FILLING image
    if ($this->input->post('machinefillingsystem') != '')
    {
        $photo = $_FILES['machinefillingimg']['name'];
        $machineimage = "";
        if ($photo <> '')
        {
            $image1 = explode('.', $photo);
            $cat_image = end($image1);
            $machineimage = time() . '.' . $cat_image;
            move_uploaded_file($_FILES['machinefillingimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
            $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
            $this->db->insert('quotation_machine_filling_image', $data);
        }else{

                $old_path=$this->input->post('machinefillingimg_path');
        if($old_path!='')
        {
            $ext = pathinfo($old_path, PATHINFO_EXTENSION);
            $new = time()."34_clone.".$ext;
            copy($old_path, UPLOADPATH."opportunitydocs/".$new);
            $this->db->insert('quotation_machine_filling_image', [
            'record_id'=>$recordid,
            'image'=>$new,
            'added_on'=>date('Y-m-d H:i:s'),
            'added_by'=>$user_id
            ]);
        }
        }
    }

    // KLD image
    if ($this->input->post('kld') == 1)
    {
        $photo = $_FILES['kldimg']['name'];
        if ($photo <> '')
        {
            $image1 = explode('.', $photo);
            $cat_image = end($image1);
            $machineimage = time() . '1234.' . $cat_image;
            move_uploaded_file($_FILES['kldimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
            $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
            $this->db->insert('quotation_kld_image', $data);
        }else
        {

            $old_path=$this->input->post('kldimg_path');
        if($old_path!='')
        {
        $ext = pathinfo($old_path, PATHINFO_EXTENSION);
        $new = time()."1_clone.".$ext;
        copy($old_path, UPLOADPATH."opportunitydocs/".$new);
        $this->db->insert('quotation_kld_image', [
        'record_id'=>$recordid,
        'image'=>$new,
        'added_on'=>date('Y-m-d H:i:s'),
        'added_by'=>$user_id
        ]);
        }

        }

    }

    // -------------------------
    // BRAND DATA: insert new rows for each posted brand selection
    // -------------------------
    if ($this->input->post('brands'))
    {
        for ($i = 0; $i < count($this->input->post('brands')); $i++)
        {
            $brand_id = $this->input->post('brands')[$i];
            $brand_data = $this->input->post('brand_data')[$i];
            $data = array('record_id' => $recordid, 'head_id' => $brand_id, 'value_id' => $brand_data, 'addedOn' => date('Y-m-d'), 'addedBy' => $user_id);
            $this->db->insert('quotation_brand_data', $data);
        }
    }

    // -------------------------
    // MACHINE PRICE INFO - insert new
    // -------------------------
    $grandtotalprice = $this->input->post('modelqty') * $this->input->post('modelprice');
    $machinepricedata = array(
        'record_id' => $recordid,
        'machine_model' => $this->input->post('modelno'),
        'qty' => $this->input->post('modelqty'),
        'unit_price' => $this->input->post('modelprice'),
        'totalprice' => $grandtotalprice,
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id
    );
    $this->db->insert('quotation_machine_price_info', $machinepricedata);

    // -------------------------
    // FREIGHT / PACKING / FORWARDING - insert new
    // -------------------------
    $packingingcharge = ($this->input->post('packagingapplicable') != '') ? $this->input->post('packagingpercentage') : "";
    $forwardingcharges = ($this->input->post('forwardingapplicable') != '') ? $this->input->post('forwardingpercentage') : "";
    $insurancecharges = ($this->input->post('insuranceapplicable') != '') ? $this->input->post('insurancepercentage') : "";
    $freight_charges = ($this->input->post('frightinfo') == 3 || $this->input->post('frightinfo') == 4) ? $this->input->post('freightamount') : "";
    $freight_type = $this->input->post('freightType');
    $port_id = 0;
    if ($freight_type == "FOB" || $freight_type == "CIF" || $freight_type == "CFR")
    {
        $port = $this->input->post('port');
        if (is_numeric($port) && !strpos($port, '.')) {
            $port_id = $port;
        } else {
            $dt = array('name' => $port);
            $this->db->insert('ports', $dt);
            $port_id = $this->db->insert_id();
        }
    }

    if ($this->input->post('installationcommissioningapplicable')!='') {
                $installationcommissioningamount = $this->input->post('installationcommissioningamount');
            }else{
                $installationcommissioningamount = "";
            }
            
    $freightdata = array(
        'record_id' => $recordid,
        'freight' => $this->input->post('frightinfo'),
        'freight_charges' => $freight_charges,
        'installation_charges'=>$installationcommissioningamount,
        'freight_type' => $freight_type,
        'packing_charges' => $packingingcharge,
        'forwarding_charges' => $forwardingcharges,
        'port' => $port_id,
        'insurance' => $insurancecharges,
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $user_id
    );
    $this->db->insert('quotation_freight_packing_forwarding', $freightdata);

    // -------------------------
    // CONSUMABLE SPARES - existing posted entries: insert copies
    // -------------------------
    if($this->input->post('consumablespare')==1)
    {
    $consumanbleid = $this->input->post('consumanbleid');
    if($this->input->post('consumalbleNew')==0)
    {
    if (is_array($consumanbleid) && count($consumanbleid) > 0)
    {
    for ($r = 0; $r < count($consumanbleid); $r++)
    {
    $id = $consumanbleid[$r];
    $spareqty = $this->input->post('spareqty' . $id);
    $spareprice = $this->input->post('spareprice' . $id);
    $spareFile_old=$this->input->post('spareFile_old'.$id);
                    /** MEDIA **/
                    $photo1=$_FILES['spareFile'.$id]['name'];
                    if($photo1<>'')
                    {
                    $image2=explode('.',$photo1);
                    $cat_image1=end($image2);
                    $spareFiles=time().'.'.$cat_image1;
                    move_uploaded_file($_FILES['spareFile'.$id]["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
                    }else
                    {
                    $spareFiles=$spareFile_old;
                    }

                    /** END **/

    $data = array('record_id' => $recordid, 'qty' => $spareqty, 'price' => $spareprice, 'media'=>$spareFiles,'added_by' => $user_id, 'added_on' => date('Y-m-d H:i:s'));
    $this->db->insert('quotation_consumable_spares', $data);
    }
    }
    }else
    {
        if (isset($_REQUEST['spareqty'])) {
        $tags1 = count($_REQUEST['spareqty']);
        for ($x=0;$x<$tags1;$x++){

                                /** MEDIA **/
                    $photo1=$_FILES['spareFile']['name'];
                    if($photo1<>'')
                    {
                    $image2=explode('.',$photo1);
                    $cat_image1=end($image2);
                    $spareFiles=time().'.'.$cat_image1;
                    move_uploaded_file($_FILES['spareFile']["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
                    }else
                    {
                    $spareFiles=$this->input->post('spareFile_old');
                    }
                    /** END **/


        $data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'media'=>$spareFiles,'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
        $this->db->insert('quotation_consumable_spares',$data);
        }
        }

    }

    }


    // Optionally, if new spare fields were posted as arrays, insert them too (uncomment if used)
    /*
    if (isset($_REQUEST['spareqty'])) {
        $tags1 = count($_REQUEST['spareqty']);
        for ($x=0;$x<$tags1;$x++){
            $data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
            $this->db->insert('quotation_consumable_spares',$data);
        }
    }
    */


    /** UPDATE TERMS & CONDITIONS **/
        if($this->input->post('country')==101)
        {
        $liquidated_applicable = $this->input->post('liquidated_clause_applicable');
        if($liquidated_applicable==1)
        {
            $liquidated_applicable=1;
        }else
        {
            $liquidated_applicable=0;
        }

        $late_delivery_applicable=$this->input->post('late_delivery_applicable');
        if($late_delivery_applicable==1)
        {
            $late_delivery_applicable=1;
        }else
        {
            $late_delivery_applicable=0;
        }

        // get and xss_clean content fields (optional: keep html if you use editors)
        $liquidated_text  = $this->input->post('liquidated_clause');
        $packing_charges  = $this->input->post('packing_charges');
        $insurance        = $this->input->post('insurance');
        $installation     = $this->input->post('installation');
        $late_delivery_text  = $this->input->post('late_delivery');

        $payload = [
        'quotation_id' => $recordid,
        'liquidated_applicable' => $liquidated_applicable,
        'liquidated_text' => $liquidated_text ?: null,
        'packing_charges' => $packing_charges ?: null,
        'late_delivery_applicable'=>$late_delivery_applicable,
        'late_delivery_text'=>$late_delivery_text ? : null,
        'insurance' => $insurance ?: null,
        'installation' => $installation ?: null
        ];

        $this->db->insert('quotation_custom_terms',$payload);
        }else
        {
            $this->db->where('quotation_id',$recordid);
            $this->db->delete('quotation_custom_terms');
        }

    /** END **/



    // -------------------------
    // QUOTATION PROGRESS REMARK
    // -------------------------
    $d = array(
        'lead_id' => $this->uri->segment(6),
        'lead_status' => 39,
        'next_follow_date' => date('Y-m-d', strtotime("+1 day")),
        'added_on' => date('Y-m-d H:i:s'),
        'added_by' => $_SESSION['logged_in']['user_id']
    );
    $this->db->insert('progress_remarks', $d);

    // QUOTATION CHANGE REASON
    // $reasondata = array(
    //     'lead_id' => $this->uri->segment(6),
    //     'record_id' => $recordid,
    //     'reason' => $this->input->post('reasonforchange'),
    //     'added_on' => date('Y-m-d H:i:s'),
    //     'added_by' => $_SESSION['logged_in']['user_id']
    // );
    // $this->db->insert('quotation_change_reason', $reasondata);

    // Redirect to generated quote (use new record id)
    redirect(page_url . 'Opportunity/GeneratedQuote/' . $recordid . "/" . $this->uri->segment(4));
}


// function Clone_newopportunity()
// {
// 	//echo $this->uri->segment(6); exit;
//     $this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
//     $this->form_validation->set_rules('refno', 'Ref No', 'required|trim');
//     $this->form_validation->set_rules('quote_date', 'Quotation Date', 'required|trim');
//     $this->form_validation->set_rules('customername', 'Customer Name', 'required|trim');
//     $this->form_validation->set_rules('cur', 'Currency', 'required|trim');
//     $this->form_validation->set_rules('country', 'Country', 'required|trim');
//     $this->form_validation->set_rules('machineName', 'Machine Name', 'required|trim');
//     $this->form_validation->set_rules('machineModel', 'Machine Model', 'required|trim');
//     /*Validation till general information*/
//     $this->form_validation->set_rules('producttobepacked', 'Product To be Packed', 'required|trim');
//     $this->form_validation->set_rules('productname', 'Product Name', 'required|trim');
//     $this->form_validation->set_rules('machtype', 'Machine Type', 'required|trim');
//     $this->form_validation->set_rules('horizontalsealingwidth', 'Horizontal Sealing Width', 'required|trim');
//     $this->form_validation->set_rules('verticalsealingwidth', 'Vertical Sealing Width', 'required|trim');
//     $this->form_validation->set_rules('perforationpitch', 'Perforation Pitch', 'required|trim');
//     $this->form_validation->set_rules('typeofsealing', 'Type of Sealing', 'required|trim');
//     // $this->form_validation->set_rules('plcmake', 'PLC Make', 'required|trim');
//     $this->form_validation->set_rules('powersupply', 'Powder Supply', 'required|trim');

//     $this->form_validation->set_rules('machinemodelno', 'Machine Model No', 'required|trim');
//     $this->form_validation->set_rules('machinemodel', 'Machine Model', 'required|trim');
//     $this->form_validation->set_rules('fillingaccuracy', 'Filling Accuracy', 'required|trim');
//     $this->form_validation->set_rules('designspeed', 'Design Speed', 'required|trim');
//     $this->form_validation->set_rules('actualspeed', 'Actual Speed', 'required|trim');
//     $this->form_validation->set_rules('nooftracks', 'No of Tracks', 'required|trim');
//     $this->form_validation->set_rules('laminatewidth', 'Laminate Width', 'required|trim');
//     $this->form_validation->set_rules('laminatereeldia', 'Max Reel Dia', 'required|trim');
//     $this->form_validation->set_rules('laminatereelcoredia', 'Reel Core Dia', 'required|trim');
//     $this->form_validation->set_rules('product_tobepacked', 'Product to be Packed', 'required|trim');
//     $this->form_validation->set_rules('fillingcapacity', 'Filling Capacity', 'required|trim');
//     $this->form_validation->set_rules('electricalspec', 'Electrical Spec.', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionslength', 'Layout Dimensions Length', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionswidth', 'Layout Dimensions Width', 'required|trim');
//     $this->form_validation->set_rules('layoutdimensionsheight', 'Layout Dimensions Height', 'required|trim');
//     $this->form_validation->set_rules('netweight', 'Machine Net Weight', 'required|trim');
//     $this->form_validation->set_rules('grossweight', 'Machine Gross Weight', 'required|trim');
//     $this->form_validation->set_rules('compressedaircfa', 'Compressed Air CFA', 'required|trim');
//     $this->form_validation->set_rules('compressedairbar', 'Compressed Air BAR', 'required|trim');
//     $this->form_validation->set_rules('modelno', 'Model No', 'required|trim');
//     $this->form_validation->set_rules('modelqty', 'Model Qty ', 'required|trim');
//     $this->form_validation->set_rules('modelprice', 'Model Price ', 'required|trim');
//     $this->form_validation->set_rules('totalmodelprice', 'Model Price ', 'required|trim');

//     $this->form_validation->set_rules('paymentterms', 'Payment Terms', 'required|trim');
//     $this->form_validation->set_rules('delivery', 'Delivery', 'required|trim');
//     $this->form_validation->set_rules('out_of_pocket_expense', 'Out of Pocket expenses', 'required|trim');

//     $user_id = $this->session->userdata['logged_in']['user_id'];

//     if ($this->form_validation->run() == FALSE)
//     {
//         $this->load->view('opportunity/edit_opportunity_quote');
//         return;
//     }

//     // --- find product for lead (same as before)
//     $rest = $this->db->select('product_id')->from('lead_products')->where('lead_id', $this->uri->segment(6))->get();
//     if ($rest->num_rows() > 0)
//     {
//         foreach ($rest->result() as $row);
//         $product_id = $row->product_id;
//     } else {
//         echo "Product Not Found"; exit;
//     }

//     // version handling same as before
//     // if ($this->uri->segment(5) == 1) {
//     //     $version = $this->getCurrentVersion($this->uri->segment(6));
//     //     $newversion = $version + 1;
//     // } else {
//     //     $newversion = $this->getCurrentVersion($this->uri->segment(6));
//     // }
//     $newversion = 1;

//     // -------------------------
//     // INSERT master quote row (instead of update)
//     // -------------------------

//     $gst_actual=0;
// 			if($this->input->post('country')==101)
// 			{
// 			if($this->input->post('gstapplicable')==1)
// 			{
// 				$gst_actual=1;
// 			}
// 			}

//     	$master_data = array('lead_id'=>$this->uri->segment(6),
// 				'product_id'=>$product_id,
// 				'ref_no'=>$this->input->post('refno'),
// 				'quotation_date'=>date('Y-m-d',strtotime($this->input->post('quote_date'))),
// 				'customer_id'=>$this->input->post('customername'),
// 				'currency'=>$this->input->post('cur'),
// 				'country'=>$this->input->post('country'),
// 				'machine_name'=>$this->input->post('machineName'),
// 				'machine_model_no'=>$this->input->post('machineModel'),
// 				'cantilever'=>$this->input->post('cantilever'),
// 				'mach_model_no'=>$this->input->post('mach_model_no'),
// 				'added_on'=>date('Y-m-d H:i:s'),
// 				'last_revision_date'=>date('Y-m-d'),
// 				'special_notes'=>$this->input->post('specialNotes'),
// 				'gst_actual'=>$gst_actual,
// 				'validity'=>$this->input->post('validity'),
// 				'added_by'=>$user_id);
//     $this->db->insert('quotation_customer_data', $master_data);
//     $new_recordid = $this->db->insert_id();
//     // Use this new record id for all child inserts
//     $recordid = $new_recordid;

//        // -------------------------
//     // ANNEXTURE 1 (insert new)
//     // -------------------------
//     $data1 = array(
//         'record_id' => $recordid,
//         'product_to_be_packed' => $this->input->post('producttobepacked'),
//         'liquid_option' => $this->input->post('liquid_option'),
//         'powder_option' => $this->input->post('powder_option'),
//         'non_viscous_option' => $this->input->post('non_viscous_option'),
//         'viscous_option' => $this->input->post('viscous_option'),
//         'piston_filler_option' => $this->input->post('piston_filler_option'),
//         'follow_meter_option' => $this->input->post('follow_meter_option'),
//         'free_flow_option' => $this->input->post('free_flow_option'),
//         'weigher_system_option' => $this->input->post('weigher_system_option'),
//         'liner_weigher_option' => $this->input->post('liner_weigher_option'),
//         'mult_head_weigher_option' => $this->input->post('mult_head_weigher_option'),
//         'volumetric_cap_option' => $this->input->post('volumetric_cap_option'),
//         'non_free_flow_option' => $this->input->post('non_free_flow_option'),
//         'product_name' => $this->input->post('productname'),
//         'horizontal_sealing_width' => $this->input->post('horizontalsealingwidth'),
//         'vertical_sealing_width' => $this->input->post('verticalsealingwidth'),
//         'perforation_pitch' => $this->input->post('perforationpitch'),
//         'perforationstyle' => $this->input->post('perforationstyle'),
//         'batchcut' => $this->input->post('batchcut'),
//         'typeofsealing' => $this->input->post('typeofsealing'),
//         'power_supply' => $this->input->post('powersupply'),
//         'liquidviscositydata' => $this->input->post('liquidviscositydata'),
//         'liquidconductivitydata' => $this->input->post('liquidconductivitydata'),
//         'powderdensitydata' => $this->input->post('powderdensitydata'),
//         'powderdfrdata' => $this->input->post('powderdfrdata'),
//         'powdermoisturecontentdata' => $this->input->post('powdermoisturecontentdata'),
//         'machine_type' => $this->input->post('machtype'),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_annexture_1', $data1);

//     // -------------------------
//     // PACKING SIZES: existing ones in form (pack_id[]) and new added ones
//     // For cloning, insert all pack entries as new rows using posted values.
//     // -------------------------
//     if ($this->input->post('pack_id')) {
//         // Some forms may send pack_id for existing items; for clone we insert new rows using posted edit values
//         for ($i = 0; $i < count($this->input->post('pack_id')); $i++)
//         {
//             $pckid = $this->input->post('pack_id')[$i];
//             // use posted edit fields (they contain original values loaded into form)
//             $packedqty = $this->input->post('editqtytobepacked' . $pckid);
//             $unit = $this->input->post('editqty_unit' . $pckid);
//             $length = $this->input->post('editpouchsizel' . $pckid);
//             $width = $this->input->post('editpouchsizew' . $pckid);
//             $height = $this->input->post('editpouchsizeh' . $pckid);
//             if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE" || $this->input->post('machineName') == "HFFS SINGLE TRACK MACHINE" || $this->input->post('machineName') == "HFFS MULTI TRACK MACHINE" || $this->input->post('machineName') == "HFFS PICK FILL SEAL")
//             {
//                 $gusset = $this->input->post('editgusset' . $pckid);
//                 $punchhole = $this->input->post('editpunch_hole' . $pckid);
//             } else {
//                 $gusset = '';
//                 $punchhole = '';
//             }

//             $data = array(
//                 'record_id' => $recordid,
//                 'packing_qty' => $packedqty,
//                 'unit' => $unit,
//                 'length' => $length,
//                 'width' => $width,
//                 'height' => $height,
//                 'gusset' => $gusset,
//                 'punchhole' => $punchhole
//             );
//             $this->db->insert('quotation_pouch_size', $data);
//         }
//     }

//     // New appended pouch sizes (addMorePRDPacked)
//     if ($this->input->post('addMorePRDPacked') == 1)
//     {
//         $qtytobepacked = $this->input->post('qtytobepacked');
//         for ($u = 0; $u < count($qtytobepacked); $u++)
//         {
//             $packedqty = $qtytobepacked[$u];
//             $unit = $this->input->post('qty_unit')[$u];
//             $length = $this->input->post('pouchsizel')[$u];
//             $width = $this->input->post('pouchsizew')[$u];
//             $height = $this->input->post('pouchsizeh')[$u];
//             if ($this->input->post('machineName') == "VFFS COLLAR TYPE TWIN HEAD MACHINE" || $this->input->post('machineName') == "VFFS COLLAR TYPE MACHINE")
//             {
//                 $gusset = $this->input->post('gusset')[$u];
//                 $punchhole = $this->input->post('punch_hole')[$u];
//             } else {
//                 $gusset = '';
//                 $punchhole = '';
//             }

//             $data = array(
//                 'record_id' => $recordid,
//                 'packing_qty' => $packedqty,
//                 'unit' => $unit,
//                 'length' => $length,
//                 'width' => $width,
//                 'height' => $height,
//                 'gusset' => $gusset,
//                 'punchhole' => $punchhole
//             );
//             $this->db->insert('quotation_pouch_size', $data);
//         }
//     }

//     // -------------------------
//     // CUM TECH SPEC: previous update -> insert new with flag=1
//     // -------------------------
//     $data2 = array('record_id' => $recordid, 'model' => $this->input->post('machinemodelno'), 'flag' => 1, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//     $this->db->insert('quotation_cum_tech_spec', $data2);

//     // Update previous technicalData entries posted as editable (they were existing ids in original)
//     $technicalDataid = $this->input->post('technicalDataid');
//     if (is_array($technicalDataid) && count($technicalDataid) > 0)
//     {
//         // For cloning, insert copies (use posted edit values) instead of updating original ids
//         for ($y = 0; $y < count($technicalDataid); $y++)
//         {
//             $dmodel = $this->input->post('technicalspecedit' . $technicalDataid[$y]);
//             $data21 = array('record_id' => $recordid, 'model' => $dmodel, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_cum_tech_spec', $data21);
//         }
//     }

//     // Add multiple technical specs (new ones)
//     if (isset($_REQUEST['technicalspec']))
//     {
//         $tags1 = count($_REQUEST['technicalspec']);
//         if ($tags1 > 0)
//         {
//             $technicalspec = $_REQUEST['technicalspec'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($technicalspec[$x] != '')
//                 {
//                     $data21 = array(
//                         'record_id' => $recordid,
//                         'model' => $technicalspec[$x],
//                         'added_on' => date('Y-m-d H:i:s'),
//                         'added_by' => $user_id
//                     );
//                     $this->db->insert('quotation_cum_tech_spec', $data21);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // AXIS: existing axis entries -> insert copies
//     // -------------------------
//     $axisid = $this->input->post('axis_id');
//     if (is_array($axisid) && count($axisid) > 0)
//     {
//         for ($y = 0; $y < count($axisid); $y++)
//         {
//             $noofaxisinmachineinputedit = $this->input->post('editnoofaxisinmachine' . $axisid[$y]);
//             $editnoofaxisincount = $this->input->post('editnoofaxisincount' . $axisid[$y]);
//             $data21 = array(
//                 'record_id' => $recordid,
//                 'description' => $noofaxisinmachineinputedit,
//                 'axiscount' => $editnoofaxisincount,
//                 'added_on' => date('Y-m-d H:i:s'),
//                 'added_by' => $user_id
//             );
//             $this->db->insert('quotation_no_of_axis_in_machine', $data21);
//         }
//     }

//     // New axes
//     if ($this->input->post('more_axis') == 1)
//     {
//         if (isset($_REQUEST['noofaxisinmachine']))
//         {
//             $tags1 = count($_REQUEST['noofaxisinmachine']);
//             if ($tags1 > 0)
//             {
//                 $technicalspecs = $_REQUEST['noofaxisinmachine'];
//                 $noofaxisincount = $_REQUEST['noofaxisincount'];
//                 for ($x = 0; $x < $tags1; $x++)
//                 {
//                     if ($technicalspecs[$x] != '')
//                     {
//                         $data22 = array(
//                             'record_id' => $recordid,
//                             'description' => $technicalspecs[$x],
//                             'axiscount' => $noofaxisincount[$x],
//                             'added_on' => date('Y-m-d H:i:s'),
//                             'added_by' => $user_id
//                         );
//                         $this->db->insert('quotation_no_of_axis_in_machine', $data22);
//                     }
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // ANNEXURE 2 (insert new)
//     // -------------------------
//     $laminatewidth = $this->input->post('laminatewidth') . "<br>";
//     $laminatereeldia = $this->input->post('laminatereeldia') . "<br>";
//     $laminatereelcoredia = $this->input->post('laminatereelcoredia') . "<br>";
//     $layoutdimensionslength = $this->input->post('layoutdimensionslength');
//     $layoutdimensionswidth = $this->input->post('layoutdimensionswidth');
//     $layoutdimensionsheight = $this->input->post('layoutdimensionsheight');
//     $layoutdimensionsof = $layoutdimensionslength . "<br>" . $layoutdimensionswidth . "<br>" . $layoutdimensionsheight . "<br>";

//     $data3 = array(
//         'record_id' => $recordid,
//         'machinemodel' => $this->input->post('machinemodel'),
//         'filling_accuracy' => $this->input->post('fillingaccuracy'),
//         'sealingstyle' => $this->input->post('sealingstyle'),
//         'speed' => $this->input->post('designspeed'),
//         'actual_speed' => $this->input->post('actualspeed'),
//         'no_of_track' => $this->input->post('nooftracks'),
//         'leminate_specification' => $laminatewidth,
//         'laminatereeldia' => $laminatereeldia,
//         'laminatereelcoredia' => $laminatereelcoredia,
//         'product_to_be_packed' => $this->input->post('product_tobepacked'),
//         'filling_capacity' => $this->input->post('fillingcapacity'),
//         'electrical_spec' => $this->input->post('electricalspec'),
//         'layout_dimensions' => $layoutdimensionsof,
//         'machine_weight' => $this->input->post('netweight'),
//         'gross_weight' => $this->input->post('grossweight'),
//         'compressed_air' => $this->input->post('compressedaircfa'),
//         'compressedairbar' => $this->input->post('compressedairbar'),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_annexure_2', $data3);

//     // -------------------------
//     // ANNEXURE 4 / optional items - existing ones posted -> insert copies
//     // -------------------------

//       $data4 = array('record_id'=>$recordid,
//             'description'=>$this->input->post('modelno'),
//             'hsn'=>$this->input->post('modelhsn'),
//             'additionalinformationwiththemachine'=>$this->input->post('additionalinformationwiththemachine'),
//             'qty'=>$this->input->post('modelqty'),
//             'unit'=>$this->input->post('qtyunit'),
//             'price'=>$this->input->post('modelprice'),
//             'addedOn'=>date('Y-m-d H:i:s'),
//             'flag'=>0,
//             'addedBy'=>$user_id);
//             $this->db->insert('quotation_annexture_4',$data4);


            
//     $existing_optional = $this->input->post('existing_optional');
//     if (is_array($existing_optional) && count($existing_optional) > 0)
//     {
//         for ($r = 0; $r < count($existing_optional); $r++)
//         {
//             $id = $existing_optional[$r];
//             $techdescriptioninfo = $this->input->post('edittechdescriptioninfo' . $id);
//             $techdescqty = $this->input->post('edittechdescqty' . $id);
//             $techdescprice = $this->input->post('edittechdescprice' . $id);
//             $edittechhsn = $this->input->post('edittechhsn' . $id);
//             $edittechunit = $this->input->post('edittechunit' . $id);
//             $totalprice = $techdescqty * $techdescprice;

//             // If techdescriptioninfo is numeric and refers to instrument id, keep it. Else insert into presto_instruments first.
//             if (is_numeric($techdescriptioninfo) && !strpos($techdescriptioninfo, '.')) {
//                 $techdescitemid = $techdescriptioninfo;
//             } else {
//                 $datass = array('instruments_name' => $techdescriptioninfo, 'type' => 1, 'status' => 1);
//                 $this->db->insert('presto_instruments', $datass);
//                 $techdescitemid = $this->db->insert_id();
//             }

//             $data4 = array(
//                 'record_id' => $recordid,
//                 'description' => $techdescitemid,
//                 'qty' => $techdescqty,
//                 'price' => $techdescprice,
//                 'hsn' => $edittechhsn,
//                 'unit' => $edittechunit,
//                 'total_price' => $totalprice,
//                 'addedOn' => date('Y-m-d H:i:s'),
//                 'category_type' => 1,
//                 'addedBy' => $user_id
//             );
//             $this->db->insert('quotation_annexture_4', $data4);
//         }
//     }

//     // Add new technical charges optional
//     if (isset($_REQUEST['techdescriptioninfo']))
//     {
//         $tags1 = count($_REQUEST['techdescriptioninfo']);
//         if ($tags1 > 0)
//         {
//             $techdescriptioninfo = $_REQUEST['techdescriptioninfo'];
//             $techdescqty = $_REQUEST['techdescqty'];
//             $techdescprice = $_REQUEST['techdescprice'];
//             $techsn = $_REQUEST['techsn'];
//             $techunit = $_REQUEST['techunit'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($techdescriptioninfo[$x] != '')
//                 {
//                     if (is_numeric($techdescriptioninfo[$x]) && !strpos($techdescriptioninfo[$x], '.')) {
//                         $techdescitemid = $techdescriptioninfo[$x];
//                     } else {
//                         $datass = array('instruments_name' => $techdescriptioninfo[$x], 'type' => 1, 'status' => 1);
//                         $this->db->insert('presto_instruments', $datass);
//                         $techdescitemid = $this->db->insert_id();
//                     }

//                     $totalprice = $techdescqty[$x] * $techdescprice[$x];
//                     $data4 = array(
//                         'record_id' => $recordid,
//                         'description' => $techdescitemid,
//                         'qty' => $techdescqty[$x],
//                         'price' => $techdescprice[$x],
//                         'hsn' => $techsn[$x],
//                         'unit' => $techunit[$x],
//                         'total_price' => $totalprice,
//                         'addedOn' => date('Y-m-d H:i:s'),
//                         'flag' => ($x + 1),
//                         'category_type' => 1,
//                         'addedBy' => $user_id
//                     );
//                     $this->db->insert('quotation_annexture_4', $data4);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // OPTIONAL DATA: existing optional items (quotation_optional) - insert copies
//     // -------------------------
//     $optionaldatainfo = $this->input->post('optionaldatainfo');
//     if (is_array($optionaldatainfo) && count($optionaldatainfo) > 0)
//     {
//         for ($r = 0; $r < count($optionaldatainfo); $r++)
//         {
//             $id = $optionaldatainfo[$r];
//             $optionalitem = $this->input->post('optionalitem' . $id);
//             $optionalqty = $this->input->post('optionalqty' . $id);
//             $optionalprice = $this->input->post('optionalprice' . $id);

//             if (is_numeric($optionalitem)) {
//                 $itemid = $optionalitem;
//             } else {
//                 $datass = array('instruments_name' => $optionalitem);
//                 $this->db->insert('presto_instruments', $datass);
//                 $itemid = $this->db->insert_id();
//             }
//             $totalprice = $optionalqty * $optionalprice;
//             $data = array(
//                 'record_id' => $recordid,
//                 'description' => $itemid,
//                 'value' => $optionalqty,
//                 'price' => $optionalprice,
//                 'totalprice' => $totalprice,
//                 'addedBy' => $user_id,
//                 'addedOn' => date('Y-m-d H:i:s')
//             );
//             $this->db->insert('quotation_optional', $data);
//         }
//     }

//     // New optional items
//     if (isset($_REQUEST['optionalitem']))
//     {
//         $tags1 = count($_REQUEST['optionalitem']);
//         if ($tags1 > 0)
//        {
//             $optionalitem = $_REQUEST['optionalitem'];
//             $optionalqty = $_REQUEST['optionalqty'];
//             $optionalprice = $_REQUEST['optionalprice'];
//             for ($x = 0; $x < $tags1; $x++)
//             {
//                 if ($optionalitem[$x] != '')
//                 {
//                     if (is_numeric($optionalitem[$x]) && !strpos($optionalitem[$x], '.')) {
//                         $itemid = $optionalitem[$x];
//                     } else {
//                         $datass = array('instruments_name' => $optionalitem[$x], 'type' => 1, 'status' => 1);
//                         $this->db->insert('presto_instruments', $datass);
//                         $itemid = $this->db->insert_id();
//                     }

//                     $totalprice = $optionalqty[$x] * $optionalprice[$x];
//                     $data = array(
//                         'record_id' => $recordid,
//                         'description' => $itemid,
//                         'value' => $optionalqty[$x],
//                         'price' => $optionalprice[$x],
//                         'totalprice' => $totalprice,
//                         'addedBy' => $user_id,
//                         'addedOn' => date('Y-m-d H:i:s')
//                     );
//                     $this->db->insert('quotation_optional', $data);
//                 }
//             }
//         }
//     }

//     // -------------------------
//     // OTHER INFORMATION (quotation_other_information) - insert new
//     // -------------------------
//     $consumablespare = ($this->input->post('consumablespare') != '') ? $this->input->post('consumablespare') : 0;
//     $data13 = array(
//         'record_id' => $recordid,
//         'terms_value' => $this->input->post('paymentterms'),
//         'delivery_value' => $this->input->post('delivery'),
//         'pocket_expense' => $this->input->post('out_of_pocket_expense'),
//         'addedBy' => $user_id,
//         'consumable_spare' => $consumablespare,
//         'addedOn' => date('Y-m-d H:i:s')
//     );
//     $this->db->insert('quotation_other_information', $data13);

//     // -------------------------
//     // LAYOUT IMAGE handling - insert new if uploaded
//     // -------------------------

//       if ($this->input->post('layoutapplicable') != '')
//     {
//         $photo1 = $_FILES['uploadlayout']['name'];
//         if ($photo1 <> '')
//         {
//             $image2 = explode('.', $photo1);
//             $cat_image1 = end($image2);
//             $layoutimage = time() . '.' . $cat_image1;
//             move_uploaded_file($_FILES['uploadlayout']["tmp_name"], UPLOADPATH . 'opportunitydocs/layoutimg/' . $layoutimage);
//             $data = array(
//                 'record_id' => $recordid,
//                 'image' => $layoutimage,
//                 'added_on' => date('Y-m-d H:i:s'),
//                 'added_by' => $user_id
//             );
//             $this->db->insert('quotation_layout_img', $data);
//         }else
//         {

//         	$old_path=$this->input->post('uploadlayout_path');
    		
//     		if($old_path!='')
//     		{
		
// 		$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 		$new = time()."67_clone.".$ext;
// 		copy($old_path, UPLOADPATH."opportunitydocs/layoutimg/".$new);

// 		$this->db->insert('quotation_layout_img', [
// 		'record_id'=>$recordid,
// 		'image'=>$new,
// 		'added_on'=>date('Y-m-d H:i:s'),
// 		'added_by'=>$user_id
// 		]);

// 		}


//         }
//     }


//     // MACHINE FILLING image
//     if ($this->input->post('machinefillingsystem') != '')
//     {
//         $photo = $_FILES['machinefillingimg']['name'];
//         $machineimage = "";
//         if ($photo <> '')
//         {
//             $image1 = explode('.', $photo);
//             $cat_image = end($image1);
//             $machineimage = time() . '.' . $cat_image;
//             move_uploaded_file($_FILES['machinefillingimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
//             $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_machine_filling_image', $data);
//         }else{

//         		$old_path=$this->input->post('machinefillingimg_path');
// 		if($old_path!='')
// 		{
// 			$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 			$new = time()."34_clone.".$ext;
// 			copy($old_path, UPLOADPATH."opportunitydocs/".$new);
// 			$this->db->insert('quotation_machine_filling_image', [
// 			'record_id'=>$recordid,
// 			'image'=>$new,
// 			'added_on'=>date('Y-m-d H:i:s'),
// 			'added_by'=>$user_id
// 			]);
// 		}
//         }
//     }

//     // KLD image
//     if ($this->input->post('kld') == 1)
//     {
//         $photo = $_FILES['kldimg']['name'];
//         if ($photo <> '')
//         {
//             $image1 = explode('.', $photo);
//             $cat_image = end($image1);
//             $machineimage = time() . '1234.' . $cat_image;
//             move_uploaded_file($_FILES['kldimg']["tmp_name"], UPLOADPATH . 'opportunitydocs/' . $machineimage);
//             $data = array('record_id' => $recordid, 'image' => $machineimage, 'added_on' => date('Y-m-d H:i:s'), 'added_by' => $user_id);
//             $this->db->insert('quotation_kld_image', $data);
//         }else
//         {

//         	$old_path=$this->input->post('kldimg_path');
// 		if($old_path!='')
// 		{
// 		$ext = pathinfo($old_path, PATHINFO_EXTENSION);
// 		$new = time()."1_clone.".$ext;
// 		copy($old_path, UPLOADPATH."opportunitydocs/".$new);
// 		$this->db->insert('quotation_kld_image', [
// 		'record_id'=>$recordid,
// 		'image'=>$new,
// 		'added_on'=>date('Y-m-d H:i:s'),
// 		'added_by'=>$user_id
// 		]);
// 		}

//         }

//     }

//     // -------------------------
//     // BRAND DATA: insert new rows for each posted brand selection
//     // -------------------------
//     if ($this->input->post('brands'))
//     {
//         for ($i = 0; $i < count($this->input->post('brands')); $i++)
//         {
//             $brand_id = $this->input->post('brands')[$i];
//             $brand_data = $this->input->post('brand_data')[$i];
//             $data = array('record_id' => $recordid, 'head_id' => $brand_id, 'value_id' => $brand_data, 'addedOn' => date('Y-m-d'), 'addedBy' => $user_id);
//             $this->db->insert('quotation_brand_data', $data);
//         }
//     }

//     // -------------------------
//     // MACHINE PRICE INFO - insert new
//     // -------------------------
//     $grandtotalprice = $this->input->post('modelqty') * $this->input->post('modelprice');
//     $machinepricedata = array(
//         'record_id' => $recordid,
//         'machine_model' => $this->input->post('modelno'),
//         'qty' => $this->input->post('modelqty'),
//         'unit_price' => $this->input->post('modelprice'),
//         'totalprice' => $grandtotalprice,
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_machine_price_info', $machinepricedata);

//     // -------------------------
//     // FREIGHT / PACKING / FORWARDING - insert new
//     // -------------------------
//     $packingingcharge = ($this->input->post('packagingapplicable') != '') ? $this->input->post('packagingpercentage') : "";
//     $forwardingcharges = ($this->input->post('forwardingapplicable') != '') ? $this->input->post('forwardingpercentage') : "";
//     $insurancecharges = ($this->input->post('insuranceapplicable') != '') ? $this->input->post('insurancepercentage') : "";
//     $freight_charges = ($this->input->post('frightinfo') == 3 || $this->input->post('frightinfo') == 4) ? $this->input->post('freightamount') : "";
//     $freight_type = $this->input->post('freightType');
//     $port_id = 0;
//     if ($freight_type == "FOB" || $freight_type == "CIF" || $freight_type == "CFR")
//     {
//         $port = $this->input->post('port');
//         if (is_numeric($port) && !strpos($port, '.')) {
//             $port_id = $port;
//         } else {
//             $dt = array('name' => $port);
//             $this->db->insert('ports', $dt);
//             $port_id = $this->db->insert_id();
//         }
//     }

//     if ($this->input->post('installationcommissioningapplicable')!='') {
// 				$installationcommissioningamount = $this->input->post('installationcommissioningamount');
// 			}else{
// 				$installationcommissioningamount = "";
// 			}
			
//     $freightdata = array(
//         'record_id' => $recordid,
//         'freight' => $this->input->post('frightinfo'),
//         'freight_charges' => $freight_charges,
//         'installation_charges'=>$installationcommissioningamount,
//         'freight_type' => $freight_type,
//         'packing_charges' => $packingingcharge,
//         'forwarding_charges' => $forwardingcharges,
//         'port' => $port_id,
//         'insurance' => $insurancecharges,
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $user_id
//     );
//     $this->db->insert('quotation_freight_packing_forwarding', $freightdata);

//     // -------------------------
//     // CONSUMABLE SPARES - existing posted entries: insert copies
//     // -------------------------
// 	$consumanbleid = $this->input->post('consumanbleid');
// 	if($this->input->post('consumalbleNew')==0)
// 	{
// 	if (is_array($consumanbleid) && count($consumanbleid) > 0)
// 	{
// 	for ($r = 0; $r < count($consumanbleid); $r++)
// 	{
// 	$id = $consumanbleid[$r];
// 	$spareqty = $this->input->post('spareqty' . $id);
// 	$spareprice = $this->input->post('spareprice' . $id);
//     $spareFile_old=$this->input->post('spareFile_old'.$id);
//                     /** MEDIA **/
//                     $photo1=$_FILES['spareFile'.$id]['name'];
//                     if($photo1<>'')
//                     {
//                     $image2=explode('.',$photo1);
//                     $cat_image1=end($image2);
//                     $spareFiles=time().'.'.$cat_image1;
//                     move_uploaded_file($_FILES['spareFile'.$id]["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
//                     }else
//                     {
//                     $spareFiles=$spareFile_old;
//                     }

//                     /** END **/

// 	$data = array('record_id' => $recordid, 'qty' => $spareqty, 'price' => $spareprice, 'media'=>$spareFiles,'added_by' => $user_id, 'added_on' => date('Y-m-d H:i:s'));
// 	$this->db->insert('quotation_consumable_spares', $data);
// 	}
// 	}
// 	}else
// 	{
// 		if (isset($_REQUEST['spareqty'])) {
// 		$tags1 = count($_REQUEST['spareqty']);
// 		for ($x=0;$x<$tags1;$x++){

//                                 /** MEDIA **/
//                     $photo1=$_FILES['spareFile']['name'];
//                     if($photo1<>'')
//                     {
//                     $image2=explode('.',$photo1);
//                     $cat_image1=end($image2);
//                     $spareFiles=time().'.'.$cat_image1;
//                     move_uploaded_file($_FILES['spareFile']["tmp_name"],UPLOADPATH.'opportunitydocs/sparemedia/' . $spareFiles);
//                     }else
//                     {
//                     $spareFiles=$this->input->post('spareFile_old');
//                     }
//                     /** END **/


// 		$data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'media'=>$spareFiles,'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
// 		$this->db->insert('quotation_consumable_spares',$data);
// 		}
// 		}

// 	}
//     // Optionally, if new spare fields were posted as arrays, insert them too (uncomment if used)
//     /*
//     if (isset($_REQUEST['spareqty'])) {
//         $tags1 = count($_REQUEST['spareqty']);
//         for ($x=0;$x<$tags1;$x++){
//             $data = array('record_id'=>$recordid,'qty'=>$_REQUEST['spareqty'][$x],'price'=>$_REQUEST['spareprice'][$x],'added_by'=>$user_id,'added_on'=>date('Y-m-d H:i:s'));
//             $this->db->insert('quotation_consumable_spares',$data);
//         }
//     }
//     */


//     /** UPDATE TERMS & CONDITIONS **/
// 		if($this->input->post('country')==101)
// 		{
// 		$liquidated_applicable = $this->input->post('liquidated_clause_applicable');
// 		if($liquidated_applicable==1)
// 		{
// 			$liquidated_applicable=1;
// 		}else
// 		{
// 			$liquidated_applicable=0;
// 		}

// 		$late_delivery_applicable=$this->input->post('late_delivery_applicable');
// 		if($late_delivery_applicable==1)
// 		{
// 			$late_delivery_applicable=1;
// 		}else
// 		{
// 			$late_delivery_applicable=0;
// 		}

// 		// get and xss_clean content fields (optional: keep html if you use editors)
// 		$liquidated_text  = $this->input->post('liquidated_clause');
// 		$packing_charges  = $this->input->post('packing_charges');
// 		$insurance        = $this->input->post('insurance');
// 		$installation     = $this->input->post('installation');
// 		$late_delivery_text  = $this->input->post('late_delivery');

// 		$payload = [
// 		'quotation_id' => $recordid,
// 		'liquidated_applicable' => $liquidated_applicable,
// 		'liquidated_text' => $liquidated_text ?: null,
// 		'packing_charges' => $packing_charges ?: null,
// 		'late_delivery_applicable'=>$late_delivery_applicable,
// 		'late_delivery_text'=>$late_delivery_text ? : null,
// 		'insurance' => $insurance ?: null,
// 		'installation' => $installation ?: null
// 		];

// 		$this->db->insert('quotation_custom_terms',$payload);
// 		}else
// 		{
// 			$this->db->where('quotation_id',$recordid);
// 			$this->db->delete('quotation_custom_terms');
// 		}

// 	/** END **/



//     // -------------------------
//     // QUOTATION PROGRESS REMARK
//     // -------------------------
//     $d = array(
//         'lead_id' => $this->uri->segment(6),
//         'lead_status' => 39,
//         'next_follow_date' => date('Y-m-d', strtotime("+1 day")),
//         'added_on' => date('Y-m-d H:i:s'),
//         'added_by' => $_SESSION['logged_in']['user_id']
//     );
//     $this->db->insert('progress_remarks', $d);

//     // QUOTATION CHANGE REASON
//     // $reasondata = array(
//     //     'lead_id' => $this->uri->segment(6),
//     //     'record_id' => $recordid,
//     //     'reason' => $this->input->post('reasonforchange'),
//     //     'added_on' => date('Y-m-d H:i:s'),
//     //     'added_by' => $_SESSION['logged_in']['user_id']
//     // );
//     // $this->db->insert('quotation_change_reason', $reasondata);

//     // Redirect to generated quote (use new record id)
//     redirect(page_url . 'Opportunity/GeneratedQuote/' . $recordid . "/" . $this->uri->segment(4));
// }



function deleteconsumablefile()
{
	$data=array('media'=>'');
	$id=$this->input->post('id');
	$this->db->where('id',$id);
	$this->db->update('quotation_consumable_spares',$data);
	echo true;
}




}
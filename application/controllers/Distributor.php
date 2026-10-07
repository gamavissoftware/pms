<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distributor extends CI_Controller {

	private function sync_customer_to_sap($source, $customer_id)
	{
		$customer_id = (int) $customer_id;
		if ($customer_id <= 0) {
			return;
		}

		$this->load->library('Sap_service');
		$result = $source === 'spares'
			? $this->sap_service->sync_spares_customer($customer_id)
			: $this->sap_service->sync_marketing_customer($customer_id);

		if (empty($result['success']) && empty($result['skipped'])) {
			log_message('error', 'SAP customer sync failed for ' . $source . ' customer ' . $customer_id . ': ' . (isset($result['message']) ? $result['message'] : 'Unknown error'));
		}
	}

public function __construct()
{
parent::__construct();



$this->load->model('User_model','user');
$config = array();  
$config['protocol'] = 'smtp';  
$config['smtp_host'] = 'smtp.googlemail.com';  
$config['smtp_user'] = 'sundarindustrialsoftware@gmail.com';  
$config['smtp_pass'] = 'SundarIndst@323';   
$config['smtp_port'] = 465;  
$config['smtp_auth'] = true;  
$config['smtp_crypto'] = 'ssl';  
$this->email->initialize($config);  
$this->email->set_newline("\r\n");  
$this->load->library('email', $config); 
$this->load->model('Store_model');
$user_id =$this->session->userdata['logged_in']['user_id'];
if(empty($user_id))
{
redirect(site_url(),'refresh');
}

}

public function index()
{

$this->load->view('distributor/form');
}
public function add_new_distributor()
{

$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
$this->form_validation->set_rules('firm_name', 'Firm name', 'required|trim');
$this->form_validation->set_rules('owner_name', 'Owner name', 'required|trim');
$this->form_validation->set_rules('office_address', 'office_address', 'required|trim');
$this->form_validation->set_rules('city', 'city', 'required|trim');
$this->form_validation->set_rules('district', 'district', 'required|trim');
$this->form_validation->set_rules('state', 'state', 'required|trim');
$this->form_validation->set_rules('phone_no', 'phone_no', 'required|trim');

$user_id =$this->session->userdata['logged_in']['user_id'];		
if ($this->form_validation->run() == FALSE)
{
$this->load->view('distributor/form');
}else
{

date_default_timezone_set("Asia/Kolkata");
$date =  date('Y-m-d H:i:s'); 
$table = "distributor";

$data = array('firm_name'=>$this->input->post('firm_name'),
'owner_name'=>$this->input->post('owner_name'),
'create_date'=>date('Y-m-d'),
'office_address'=>$this->input->post('office_address'),
'city'=>$this->input->post('city'),
'district'=>$this->input->post('district'),
'state'=>$this->input->post('state'),
'pincode'=>$this->input->post('pincode'),
'phone_no'=>$this->input->post('phone_no'),
'mobile_no'=>$this->input->post('mobile_no'),
'email_id'=>$this->input->post('emailid'),
'delivery_address'=>$this->input->post('delivery_address'),
'warehouse_contact_person'=>$this->input->post('warehouse_contact_person'),
'warehouse_contact_person_contact_no'=>$this->input->post('warehouse_contact_person_no'),
'gstn'=>$this->input->post('gstn'),
'firm_type'=>$this->input->post('firm_type'),
'business_type'=>$this->input->post('business_type'),
'have_any_other_company_products'=>$this->input->post('doyouhaveanyotherproduct'),
'other_experience_info'=>$this->input->post('other_experience'),
'working_area_location'=>$this->input->post('business_related_area'),
'added_on'=>$date,
'status'=>$this->input->post('useful'),
'added_by'=>$_SESSION['logged_in']['user_id']);

$result  = $this->db->insert($table,$data);
$lastid = $this->db->insert_id();

if($result)
{

if(isset($_REQUEST['company_name'])){	
$tags1=count($_REQUEST['company_name']);
if($tags1>0)
{
$company_name=$_REQUEST['company_name'];
$product_name = $_REQUEST['product_name'];
$area = $_REQUEST['area'];
$turnover = $_REQUEST['turnover'];
$distributor_since = $_REQUEST['distributor_since'];

$i=1;
for($x=0;$x<$tags1;$x++){
if($company_name[$x]!='')
{
$data = array('distributor_id'=>$lastid,
'company_name'=>$company_name[$x],
'product_name'=>$product_name[$x],
'area'=>$area[$x],
'turnover'=>$turnover[$x],
'distributior_since'=>$distributor_since[$x],
'added_on'=>date('Y-m-d H:i:s'),
'added_by'=>$_SESSION['logged_in']['user_id']);	

$this->db->insert('distributor_competitor_products',$data);

}
$i++;	
}
}
}


/*Add Distributor as Customer*/

if($this->input->post('useful')==1){
$title = "Mr/Mrs.";

$data = array('company_id'=>1,
'title'=>$title,
'customer_name'=>$this->input->post('owner_name'),
'email'=>$this->input->post('emailid'),
'contact_no'=>$this->input->post('mobile_no'),
'country'=>101,
'gst'=>$this->input->post('gstn'),
'address'=>$this->input->post('office_address'),
'status'=>1,
'added_on'=>date('Y-m-d H:i:s'),
'added_by'=>$_SESSION['logged_in']['user_id'],
'company_name'=>$this->input->post('firm_name'),
'alt_contact'=>$this->input->post('phone_no'),
'ship_address'=>$this->input->post('office_address'),
'ship_city'=>$this->input->post('city'),
'ship_pincode'=>$this->input->post('pincode'),
'ship_email'=>$this->input->post('emailid'),
'bill_address'=>$this->input->post('office_address'),
'bill_city'=>$this->input->post('city'),
'bill_pincode'=>$this->input->post('pincode'),
'bill_email'=>$this->input->post('emailid'),
'pincode'=>$this->input->post('pincode'),
'assigned_to'=>$_SESSION['logged_in']['user_id'],
'customer_distributor'=>1,
'distributor_form_id'=>$lastid);

$this->db->insert('customer_detail',$data);
$lastid = $this->db->insert_id();

$data1 = array('customer_ref_no'=>$lastid);
$this->db->Where('id',$lastid);
$this->db->update('customer_detail',$data1);
$this->sync_customer_to_sap('marketing', $lastid);




}

/*Add Distributor as Customer*/


$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
redirect(page_url.'Distributor');

}  
}  





}



public function distributor_list(){
$this->load->view('distributor/dictributor_list.php');
}


public function list_all_distributor()
{
$i=1;
$role =$this->session->userdata['logged_in']['role'];
$user_id =$this->session->userdata['logged_in']['user_id'];
$vendor_data= array();
$this->db->select('*')->from('distributor')->Where('status','1');
if($role==1){

}else{
$this->db->where('added_by',$user_id);
}
$query = $this->db->get();
$res = $query->result();
foreach($res as $row){


$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>COMPANY NAME</th><th style='padding:2px 2px 2px 2px'>PRODUCT NAME</th><th style='padding:2px 2px 2px 2px'>AREA</th><th style='padding:2px 2px 2px 2px'>MONTHLY TURNOVER</th><th style='padding:2px 2px 2px 2px'>DISTRIBUTOR SINCE</th></tr>";

$Q = $this->db->select('*')->from('distributor_competitor_products')->where('distributor_id',$row->id)->get();

foreach($Q->result() as $detail){

$html.="<tr>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->company_name)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->product_name)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->area)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->turnover)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->distributior_since)."</td>";
$html.="</tr>";
}

$html.="</table>";

if($row->firm_type==1){
$firmtype = "Proprietorship";
}else if($row->firm_type==2){
$firmtype = "Partnership";
}else if($row->firm_type==3){
$firmtype = "Pvt. Ltd.";
}else if($row->firm_type==4){
$firmtype = "Ltd.";
}else if($row->firm_type==5){
$firmtype = "Individual";
}else{
$firmtype = "";
}

if($row->business_type==1){
$businesstype = "Agency";
}else if($row->business_type==2){
$businesstype = "Wholesale";
}else if($row->business_type==3){
$businesstype = "Retail";
}else if($row->business_type==4){
$businesstype = "Others";
}else{
$businesstype = "";
}

$edit = '<a href="'.page_url.'Distributor/edit_distributor/'.$row->id.'"><i class="fa fa-pencil-square-o"></i></a>';
$delete = '<a href="'.page_url.'Distributor/deletedistributor/'.$row->id.'"><i class="fa fa-trash"></i></a>';

$vendor_data[] = array('sr_no'=>$i,
'firm_name'=>$row->firm_name,
'owner_name'=>$row->owner_name,
'office_address'=>$row->office_address."<br>".$row->city.", <br>".$row->district.",<br>".$row->state.",<br>".$row->pincode,
'contactdetail'=>$row->phone_no."<br>".$row->mobile_no."<br>".$row->email_id,
'delivery_address'=>$row->delivery_address,
'warehouse_contact_person'=>$row->warehouse_contact_person."<br>".$row->warehouse_contact_person_contact_no,
'gstn'=>$row->gstn,
'firmtype'=>$firmtype."<br>".$businesstype,
'other_experience_info'=>$row->other_experience_info,
'working_area_location'=>$row->working_area_location,
'html'=>$html,
'edit'=>$edit." | ".$delete,
'addedon'=>date('d-m-Y h:i A',strtotime($row->added_on)));
$i++;


}
$results = array(
"sEcho" => 1,
"iTotalRecords" => count($vendor_data),
"iTotalDisplayRecords" => count($vendor_data),
"aaData"=>$vendor_data);

echo json_encode($results);
}


public function inactive_distributor_list(){
$this->load->view('distributor/inactive_distributor_list.php');
}


public function list_of_inactive_distributor()
{
$i=1;
$vendor_data= array();
$role =$this->session->userdata['logged_in']['role'];
$user_id =$this->session->userdata['logged_in']['user_id'];
$this->db->select('*')->from('distributor')->Where('status','0');
if($role==1){

}else{
$this->db->where('added_by',$user_id);
}
$query = $this->db->get();
$res = $query->result();
foreach($res as $row){


$html = "<table border='1' style='width:500px;'><tr style='background-color:yellow;'><th style='padding:2px 2px 2px 2px'>COMPANY NAME</th><th style='padding:2px 2px 2px 2px'>PRODUCT NAME</th><th style='padding:2px 2px 2px 2px'>AREA</th><th style='padding:2px 2px 2px 2px'>MONTHLY TURNOVER</th><th style='padding:2px 2px 2px 2px'>DISTRIBUTOR SINCE</th></tr>";

$Q = $this->db->select('*')->from('distributor_competitor_products')->where('distributor_id',$row->id)->get();

foreach($Q->result() as $detail){

$html.="<tr>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->company_name)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->product_name)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->area)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->turnover)."</td>";
$html.="<td style='padding:2px 2px 2px 2px; text-align:center;'>".strtoupper($detail->distributior_since)."</td>";
$html.="</tr>";
}

$html.="</table>";

if($row->firm_type==1){
$firmtype = "Proprietorship";
}else if($row->firm_type==2){
$firmtype = "Partnership";
}else if($row->firm_type==3){
$firmtype = "Pvt. Ltd.";
}else if($row->firm_type==4){
$firmtype = "Ltd.";
}else if($row->firm_type==5){
$firmtype = "Individual";
}else{
$firmtype = "";
}

if($row->business_type==1){
$businesstype = "Agency";
}else if($row->business_type==2){
$businesstype = "Wholesale";
}else if($row->business_type==3){
$businesstype = "Retail";
}else if($row->business_type==4){
$businesstype = "Others";
}else{
$businesstype = "";
}

$active = '<a href="'.page_url.'Distributor/activate_distributor/'.$row->id.'"><span class="btn btn-warning btn-xs">Click to Activate</span></a>';

$vendor_data[] = array('sr_no'=>$i,
'firm_name'=>$row->firm_name,
'owner_name'=>$row->owner_name,
'office_address'=>$row->office_address."<br>".$row->city.", <br>".$row->district.",<br>".$row->state.",<br>".$row->pincode,
'contactdetail'=>$row->phone_no."<br>".$row->mobile_no."<br>".$row->email_id,
'delivery_address'=>$row->delivery_address,
'warehouse_contact_person'=>$row->warehouse_contact_person."<br>".$row->warehouse_contact_person_contact_no,
'gstn'=>$row->gstn,
'firmtype'=>$firmtype."<br>".$businesstype,
'other_experience_info'=>$row->other_experience_info,
'working_area_location'=>$row->working_area_location,
'html'=>$html,
'activate'=>$active,
'addedon'=>date('d-m-Y h:i A',strtotime($row->added_on)));
$i++;


}
$results = array(
"sEcho" => 1,
"iTotalRecords" => count($vendor_data),
"iTotalDisplayRecords" => count($vendor_data),
"aaData"=>$vendor_data);

echo json_encode($results);
}


function activate_distributor(){
$id = $this->uri->segment(3);
$data = array('status'=>1);
$this->db->where('id',$id);
$this->db->update('distributor',$data);

$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
redirect(page_url.'Distributor/distributor_list');

}

public function edit_distributor()
{

$this->load->view('distributor/edit_form');
}

public function deletedistributorproduct(){
$id = $this->uri->segment(3);
$uri = $this->uri->segment(4);

$this->db->where('id',$id);
$this->db->where('distributor_id',$uri);
$this->db->delete('distributor_competitor_products');
$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully deleted.</div>');
redirect(page_url.'Distributor/edit_distributor/'.$uri);
}

public function update_distributorinfo()
{

$this->form_validation->set_error_delimiters('<div style="color:red;">', '</div>');
$this->form_validation->set_rules('firm_name', 'Firm name', 'required|trim');
$this->form_validation->set_rules('owner_name', 'Owner name', 'required|trim');
$this->form_validation->set_rules('office_address', 'office_address', 'required|trim');
$this->form_validation->set_rules('city', 'city', 'required|trim');
$this->form_validation->set_rules('district', 'district', 'required|trim');
$this->form_validation->set_rules('state', 'state', 'required|trim');
$this->form_validation->set_rules('phone_no', 'phone_no', 'required|trim');

$user_id =$this->session->userdata['logged_in']['user_id'];		
if ($this->form_validation->run() == FALSE)
{
$this->load->view('distributor/form');
}else
{

date_default_timezone_set("Asia/Kolkata");
$date =  date('Y-m-d H:i:s'); 
$table = "distributor";

$data = array('firm_name'=>$this->input->post('firm_name'),
'owner_name'=>$this->input->post('owner_name'),
'create_date'=>date('Y-m-d'),
'office_address'=>$this->input->post('office_address'),
'city'=>$this->input->post('city'),
'district'=>$this->input->post('district'),
'state'=>$this->input->post('state'),
'pincode'=>$this->input->post('pincode'),
'phone_no'=>$this->input->post('phone_no'),
'mobile_no'=>$this->input->post('mobile_no'),
'email_id'=>$this->input->post('emailid'),
'delivery_address'=>$this->input->post('delivery_address'),
'warehouse_contact_person'=>$this->input->post('warehouse_contact_person'),
'warehouse_contact_person_contact_no'=>$this->input->post('warehouse_contact_person_no'),
'gstn'=>$this->input->post('gstn'),
'firm_type'=>$this->input->post('firm_type'),
'business_type'=>$this->input->post('business_type'),
'have_any_other_company_products'=>$this->input->post('doyouhaveanyotherproduct'),
'other_experience_info'=>$this->input->post('other_experience'),
'working_area_location'=>$this->input->post('business_related_area'),
'added_on'=>$date,
'status'=>$this->input->post('useful'),
'added_by'=>$_SESSION['logged_in']['user_id']);
$this->db->where('id',$this->uri->segment(3));
$result  = $this->db->update($table,$data);
$lastid = $this->uri->segment(3);

if($result)
{

if(isset($_REQUEST['company_name'])){	
$tags1=count($_REQUEST['company_name']);
if($tags1>0)
{
$company_name=$_REQUEST['company_name'];
$product_name = $_REQUEST['product_name'];
$area = $_REQUEST['area'];
$turnover = $_REQUEST['turnover'];
$distributor_since = $_REQUEST['distributor_since'];
$recordid = $_REQUEST['recordid'];

$i=1;
for($x=0;$x<$tags1;$x++){
if($company_name[$x]!='')
{

$data = array('distributor_id'=>$lastid,
'company_name'=>$company_name[$x],
'product_name'=>$product_name[$x],
'area'=>$area[$x],
'turnover'=>$turnover[$x],
'distributior_since'=>$distributor_since[$x],
'added_on'=>date('Y-m-d H:i:s'),
'added_by'=>$_SESSION['logged_in']['user_id']);	

if($recordid[$x]<>''){
$this->db->where('id',$recordid[$x]);
$this->db->update('distributor_competitor_products',$data);
}else{
$this->db->insert('distributor_competitor_products',$data);
}



}
$i++;	
}
}
}


/*Add Distributor as Customer*/

if($this->input->post('useful')==1){
$title = "Mr/Mrs.";

$data = array('company_id'=>1,
'title'=>$title,
'customer_name'=>$this->input->post('owner_name'),
'email'=>$this->input->post('emailid'),
'contact_no'=>$this->input->post('mobile_no'),
'country'=>101,
'gst'=>$this->input->post('gstn'),
'address'=>$this->input->post('office_address'),
'status'=>1,
'added_on'=>date('Y-m-d H:i:s'),
'added_by'=>$_SESSION['logged_in']['user_id'],
'company_name'=>$this->input->post('firm_name'),
'alt_contact'=>$this->input->post('phone_no'),
'ship_address'=>$this->input->post('office_address'),
'ship_city'=>$this->input->post('city'),
'ship_pincode'=>$this->input->post('pincode'),
'ship_email'=>$this->input->post('emailid'),
'bill_address'=>$this->input->post('office_address'),
'bill_city'=>$this->input->post('city'),
'bill_pincode'=>$this->input->post('pincode'),
'bill_email'=>$this->input->post('emailid'),
'pincode'=>$this->input->post('pincode'),
'assigned_to'=>$_SESSION['logged_in']['user_id'],
'customer_distributor'=>1);


$q = $this->db->select('customer_name')->from('customer_detail')->where('contact_no',$this->input->post('mobile_no'))->where('customer_name',$this->input->post('owner_name'))->where('email',$this->input->post('emailid'))->get();

if($q->num_rows()>0){

}else{
$this->db->insert('customer_detail',$data);
$lastid = $this->db->insert_id();

$data1 = array('customer_ref_no'=>$lastid);
$this->db->Where('id',$lastid);
$this->db->update('customer_detail',$data1);
$this->sync_customer_to_sap('marketing', $lastid);
}

}

/*Add Distributor as Customer*/


$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissable">Thank you, record successfully added.</div>');
redirect(page_url.'Distributor');

}  
}  





}

public function deletedistributor(){
$id = $this->uri->segment(3);
$this->db->where('id',$id);
$this->db->delete('distributor');

$this->db->where('distributor_id',$id);
$this->db->delete('distributor_competitor_products');

$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissable">Thank you, record successfully deleted.</div>');
redirect(page_url.'Distributor/distributor_list/');

}
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forecasting extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Forecasting_model','forecast');
	}


	function index()
	{
		$this->load->view('forecasting/forecasting');
	}
		

	function getforcastdata()
	{

		$getAllLocations=$this->forecast->getAllLocations();

		$baseyear=$this->input->post('baseyear');
		$fgtype=$this->input->post('fgtype');
		$weeks=$this->input->post('weeks');
		$datatype=$this->input->post('datatype');

		$baseYears=explode('-',$baseyear);

		$baseyear_startDate=$baseYears[0]."-04-21";
		$baseyear_endDate=$baseYears[1]."-04-20";
		$getWeekDays=$weeks*7;
		$forecast_StartDate=date('Y-m-d',strtotime($baseyear_startDate));
		$forecast_EndDate=date('Y-m-d', strtotime($forecast_StartDate. ' + '.$getWeekDays.' days'));
		$data=$this->forecast->getfgItem($fgtype);
		$u=0;
		if($getAllLocations != '') {
		$u=count($getAllLocations);
		} 

		$u=$u+6;

		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th style="text-align:center;">Sr. No.</th>
		<th style="text-align:center;">FG</th>
		<th style="text-align:center;">Available Qty (in Carton).</th>
			<th style="text-align:center;">'.$baseyear.' Sale Qty (in Carton).</th>
		<th style="text-align:center;">Required Qty (in Carton).</th>
		<th style="text-align:center;">Weeks</th>';
		if($getAllLocations != '') {
			foreach($getAllLocations as $row2) {
				$html .= '<th>'.$row2->rack_location.'</th>';
		} }
		$html .= '</tr>
		</thead>
		<tbody>';
      
		
	$data_for_sort=array();
		$total_pieces = array();
		$total_cartons = array();
		$x = '';
		if(count($data)>0)
		{
		
		$i=1;
		$fg_itemid = array();
		foreach($data as $datas)
		{

		$fgcount = $this->forecast->getfgcount($datas->id, $forecast_StartDate, $forecast_EndDate);
		$dozenwisesale=$fgcount;

		$saleType = $this->forecast->getSaleType($datas->id, $forecast_StartDate, $forecast_EndDate);

		if($datas->dozen_special_condition==0)
		{
			if($saleType==1)
			{ 
				$divideby=12;
			}else
			{
				$divideby=1;
			}

		}else
		{
			if($saleType==1)
			{
			$divideby=$datas->dozen_special_condition_data;
			}else
			{
			$divideby=1;
			}
		}


		


		/** AVAILABLE QTY FROM DOZEN TO CARTON **/

		if($datas->stock>0)
		{
		$dozenpcs=$datas->stock*$divideby;
		$dozenavailable=ceil($dozenpcs/$datas->carton_qty);
		}else
		{
			$dozenavailable=0;
		}

		/** SALE IN PREVIOUS YEAR**/
if($fgcount>0)
{
	if($datas->carton_qty>0)
	{

		$fgcount=$fgcount*$divideby;
		$fgcount=ceil($fgcount/$datas->carton_qty);
	}else
	{
		$fgcount=$fgcount;
	}
}else
{
	$fgcount=0;
}


/** PCS REQUIRED **/

if($dozenavailable>$fgcount)
{
$required=0;
}else
{
	$required=($fgcount-$dozenavailable);
}	

/** END **/
/** GET WEEK AVAILABLE QTY WILL RUN **/
$weeks=0;
$color="background-color:#ff6961;color:white;";
$u="Week(s)";
$stockindozen=$datas->stock;
$days=$this->forecast->getWeekAvailable($stockindozen,$datas->id, $forecast_StartDate, $forecast_EndDate);
if($days>0)
{
	$weeks=$days/7;
	if($weeks>12)
	{
	$weeks=$weeks/4;
	$u="Month(s)";
	$color="background-color:#A7C7E7;color:white;";
	}else if($weeks>5 && $weeks<12)
	{
		$color="background-color:#77DD77;color:white;";
		$u="Week(s)";
	}else
	{
		$color="background-color:#ff6961;color:white;";
		$u="Week(s)";

	}
	
}

$show=1;
if($datatype==2)
{
	if($required==0)
	{
		$show=0;
	}

}	
/** END **/

if($show==1)
{

/** Create Array and sort **/
$data_for_sort[]=array('name'=>'<input type="hidden" class="fin_id" value="'.$datas->id.'">'.$datas->instruments_name,
'available'=>$dozenavailable,
'base_year_sales'=>$fgcount,
'required'=>$required,
'weeklast'=>floor($weeks),
'fg_itemid'=>$datas->id,
'color'=>$color,
'days'=>$days,
'unit'=>$u);
/** end **/

}

}


		$scheduler_data=array();
		$price = array_column($data_for_sort, 'days');
		array_multisort($price, SORT_ASC, $data_for_sort);

		if(count($data_for_sort)>0)
		{
			$k=1;
			foreach($data_for_sort as $data_after_sort)
			{
				

					if($data_after_sort['weeklast']>12)
					{
					$last_till_week=$last_till_week/4;
					$u="Month(s)";
					$color="background-color:#A7C7E7;color:white;";
					}else
					{
						$last_till_week=$data_after_sort['weeklast'];
						$u=$data_after_sort['unit'];
						$color=$data_after_sort['color'];
					}


				$html.='<tr class="rowsfor'.$data_after_sort['required'].'">
				<td style="text-align:center;">'.$k.'</td>
				<td style="text-align:center;">'.$data_after_sort['name'].'</td>
				<td style="text-align:center;">'.$data_after_sort['available'].'</td>
				<td style="text-align:center;">'.$data_after_sort['base_year_sales'].'</td>
				<td style="text-align:center;">'.$data_after_sort['required'].'</td>

				<td style="text-align:center;'.$data_after_sort['color'].'">'.$data_after_sort['weeklast'].' '.$data_after_sort['unit'].'</td>';

				if($getAllLocations != '') {
				foreach($getAllLocations as $row2) {
				$html .= '<td></td>';
				} }
				$html .= '</tr>';

				$total_cartons[] = $data_after_sort['required'];
				$fg_itemid[] = $data_after_sort['fg_itemid'];

			$k++;
		}

		}



		$uq=0;
		if($getAllLocations != '') {
		$uq=count($getAllLocations);
		}

		$ud=$uq+2;
		$html .= '<tr>
		<th colspan=3 style="text-align:right;background-color:#DBDBD0;font-size:14px;">Total Cartons</th>
		<th style="text-align:center;background-color:#DBDBD0;font-size:14px;">'.array_sum($total_cartons).'</th>
		<th colspan="'.$ud.'" style="text-align:right;background-color:#DBDBD0;"></th>
		</tr>';
		}else
		{
		$html.='<tr>
		<td colspan="'.$ud.'" style="text-align:center;">No Data Found</td>
		</tr>';
		}


	$html.='</tbody>
	</table>';

		echo $html;
	}

	function getSubPartCount() {
		$html = '';
		$sub_part = $this->input->post('sub_part');
		$fin_id = $this->input->post('fin_id');

		// $sql1 = $this->db->select('carton_qty')
		// 				 ->from('presto_instruments')
		// 				 ->where_in('id', $fin_id, false)
		// 				 ->get();

		// if($sql1->num_rows() > 0) {
			
		// }

		$sql = $this->db->select('a.qty, b.part, b.current_stock, c.name')
						->from('machine_bom a')
						->join('machine_parts_with_picture b', 'b.id=a.partid')
						->join('units c', 'c.id=a.unit')
						->where('a.subpartid', $sub_part)
						->where_in('a.mid', $fin_id, false)
						->get();

		if($sql->num_rows() > 0) {
		$i=1;
		foreach($sql->result() as $data1)
		{

		$html.='<tr>
		<td>'.$data1->part.'</td>		
		<td>'.$data1->qty.' '.$data1->name.'</td>		
		<td>'.$data1->current_stock.'</td>		
		</tr>';
		}

		} else {
		$html.='<tr>
		<td colspan="3">No Data Found</td>
		</tr>';
		}

		echo $html;
	}



	function semi_fg_forcasting()
	{
		$this->load->view('forecasting/semi_forecasting');
	}

	function getsemiforcastdata() {
		$baseyear=$this->input->post('baseyear');
		$fgtype=$this->input->post('fgtype');
		$weeks=$this->input->post('weeks');
		$machine=$this->input->post('machine');
		$datatype=$this->input->post('datatype');

		$baseYears=explode('-',$baseyear);
		$baseyear_startDate=$baseYears[0]."-04-21";
		$baseyear_endDate=$baseYears[1]."-04-20";

		$getWeekDays=$weeks*7;
		$forecast_StartDate=date('Y-m-d',strtotime($baseyear_startDate));
		$forecast_EndDate=date('Y-m-d', strtotime($forecast_StartDate. ' + '.$getWeekDays.' days'));

		$data_for_sort=array();
		$html='<table class="table table-bordered">
		<thead>
		<tr>
		<th style="text-align:center;width:100px;">Sr. No.</th>
		<th style="text-align:center;width:200px;">Machine</th>	
		<th style="text-align:center;width:200px;">Semi FG</th>
		<th style="text-align:center;width:200px;">Semi FG Code</th>
		<th style="text-align:center;width:200px;">Lose Stock (In Bags)</th>
		<th style="text-align:center;width:200px;">Current Stock (In Bags)</th>
		<th style="text-align:center;width:100px;">Total Stock Available (In Bags)</th>	
		<th style="text-align:center;width:100px;">Sale Qty (In Bags)</th>	
		<th style="text-align:center;width:100px;">Required (In Bags)</th>
		<th style="text-align:center;width:100px;">Will Run</th>';
		$html .= '</tr>
		</thead>
		<tbody>';

		$i=1;

		$getSemiFgDetails = $this->forecast->getSemiFgDetails($machine,$fgtype);
		if($getSemiFgDetails != '') {
			foreach($getSemiFgDetails as $row) {

				$getrequiredSemiFG=$this->forecast->getRequiredBagQty($row->id, $forecast_StartDate, $forecast_EndDate,$row->per_pcs_bag);

				$per_pcs_bag=$row->per_pcs_bag;
				$fgstock=$this->forecast->getfgstockavailable($row->id,$per_pcs_bag);
				$current_stock=floor($row->current_stock/$per_pcs_bag);

				$overallstock=$fgstock+$current_stock;

				if($overallstock>0)
				{
					if($per_pcs_bag>0)
					{
						$available_stocks_in_bag=$overallstock;
					}else
					{
						$available_stocks_in_bag=0;
					}
				}else
				{
						$available_stocks_in_bag=0;
				}


				if($overallstock>0)
				{
					if($available_stocks_in_bag>=$getrequiredSemiFG)
					{
						$requiredqtyforproduction=0;
					}else
					{
				$requiredqtyforproduction=ceil($getrequiredSemiFG-$available_stocks_in_bag);
					}
				}else
				{
					$requiredqtyforproduction=$getrequiredSemiFG;
				}
				/** END **/

				$weeks=0;
				$color="background-color:#ff6961;color:white;";
				$u="Week(s)";
				$days=$this->forecast->getWeekAvailableforsemifg($available_stocks_in_bag,$row->id, $forecast_StartDate, $forecast_EndDate,$per_pcs_bag);

				if($days>0)
				{
					$weeks=$days/7;
						if($weeks>12)
						{
							$weeks=$weeks/4;
							$u="Month(s)";
							$color="background-color:#A7C7E7;color:white;";
						}else if($weeks>5 && $weeks<12)
						{
							$color="background-color:#77DD77;color:white;";
							$u="Week(s)";
						}else
						{
							$color="background-color:#ff6961;color:white;";
							$u="Week(s)";

						}

				}


				$show=1;
if($datatype==2)
{
	if($requiredqtyforproduction==0)
	{
		$show=0;
	}

}	
/** END **/

if($show==1)
{

				$data_for_sort[]=array('machinename'=>$row->machine_name,
				'name'=>$row->semi_fg_name,
				'code'=>$row->fincode,
				'loosestock'=>$current_stock,
				'fgstock'=>$fgstock,
				'available'=>$available_stocks_in_bag,
				'base_year_sales'=>$getrequiredSemiFG,
				'required'=>$requiredqtyforproduction,
				'weeklast'=>floor($weeks),
				'color'=>$color,
				'unit'=>$u);

				
						
				$i++;

}
			}

		}


		if(count($data_for_sort)>0)
		{
		$scheduler_data=array();
		$price = array_column($data_for_sort, 'weeklast');
		array_multisort($price, SORT_ASC, $data_for_sort);

		if(count($data_for_sort)>0)
		{
			$k=1;
			foreach($data_for_sort as $data_after_sort)
			{
				

					if($data_after_sort['weeklast']>12)
					{
					$last_till_week=$last_till_week/4;
					$u="Month(s)";
					$color="background-color:#A7C7E7;color:white;";
					}else
					{
						$last_till_week=$data_after_sort['weeklast'];
						$u=$data_after_sort['unit'];
						$color=$data_after_sort['color'];
					}



					$html.='<tr>
						<td style="text-align:center;">'.$k.'</td>
						<td style="text-align:center;">'.$data_after_sort['machinename'].'</td>
						<td style="text-align:center;">'.$data_after_sort['name'].'</td>
						<td style="text-align:center;">'.$data_after_sort['code'].'</td>
						<td style="text-align:center;">'.$data_after_sort['loosestock'].'</td>
						<td style="text-align:center;">'.$data_after_sort['fgstock'].'</td>
						<td style="text-align:center;">'.$data_after_sort['available'].'</td>
						<td style="text-align:center;">'.$data_after_sort['base_year_sales'].'</td>
						<td style="text-align:center;">'.$data_after_sort['required'].'</td>
						<td style="text-align:center;'.$color.'">'.$last_till_week." ".$u.'</td>
						</tr>';


				$k++;
			}


		}

	}else
	{

		$html.='<tr>
						<td style="text-align:center;" colspan="5">No Data Available</td>
						
						</tr>';
	}



		$html .= '</tbody>
				  </table>';


		echo $html;

	}

	function external_purchase_forcasting()
	{
		$this->load->view('forecasting/external_purchase_forcasting');
	}
	
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Forecasting_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();

	}

	function getFG_type()
	{
		$fg=array();
		$r=$this->db->select('id,type_name')->from('finished_goods_type')->where('status',1)->get();

		return $r->result();
	}

	
	

	function getfgItem($fgtype)
	{
		$this->db->select('id,instruments_name,model_number,stock, carton_qty,dozen_special_condition,dozen_special_condition_data')->from('presto_instruments');
		if($fgtype<>'ALL')
		{
			$this->db->where('type',$fgtype);
		}

		$query=$this->db->get();

		return $query->result();
	}

	function getfgcount($id, $start_date, $end_date) {
		$total_qty = 0;
		$sql = $this->db->select('SUM(qty) as total_qty')
						->from('fg_forecast_data')
						->where('item_id', $id)
						->where('forecast_date >=', $start_date)
						->where('forecast_date <=', $end_date)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
				if($row->total_qty != '') {
					$total_qty = $row->total_qty;
				} else {
					$total_qty = 0;
				}
			}
		}

		return $total_qty;
	}


	function getSaleType($id,$start_date, $end_date) {
		$saletype = 2;
		$sql = $this->db->select('sale_type')
						->from('fg_forecast_data')
						->where('item_id', $id)
						->where('forecast_date >=', $start_date)
						->where('forecast_date <=', $end_date)
						->get();

		if($sql->num_rows() > 0) {
			foreach($sql->result() as $row) {
			
					$saletype = $row->sale_type;
				
			}
		}

		return $saletype;
	}

	function getSubParts($fg_itemid) {
		$res = '';
		$sql = $this->db->select('b.id, b.name')
						->from('machine_bom a')
						->join('sub_parts b', 'b.id=a.subpartid')
						->where_in('a.mid', $fg_itemid)
						->group_by('a.subpartid')
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}
	

	function getAllLocations() {
		$res = '';
		$sql = $this->db->select('id, rack_location')
						->from('store_rack_location')
						->get();

		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getSemiFgDetails($machine,$fgtype) {
		$res = '';
		 $this->db->select('b.id,b.part as semi_fg_name,b.current_stock,c.machine_name,a.per_pcs_bag,b.fincode')
						->from('semi_fg_bom_mould_wise_variant a')
						->join('machine_parts_with_picture b', 'b.fincode=a.code')
						->join('machine c','a.machine=c.id')
						->where('a.machine', $machine);
						if($fgtype<>'ALL')
						{
						$this->db->where('b.category_id',$fgtype);
						}
						$this->db->group_by('a.code');
					$sql =$this->db->get();


		if($sql->num_rows() > 0) {
			$res = $sql->result();
		}

		return $res;
	}

	function getRequiredBagQty($semifgid,$forecast_StartDate, $forecast_EndDate,$bagpcs)
	{
		$bags=0;
		$finalqty=array();
		$finalqty[]=0;
		$row=$this->db->select('mid,qty')->from('machine_bom')->where('partid',$semifgid)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $fg)
			{
				// IN DOZEN 
				$totalqty=$this->getfgcount($fg->mid,$forecast_StartDate,$forecast_EndDate);
				$saleType = $this->forecast->getSaleType($fg->mid, $forecast_StartDate, $forecast_EndDate);
				if($saleType==1)
				{
					$mul=12;
				}else
				{
					$mul=1;

				}

				$pcsconvert=$totalqty*$mul;
				$pcsbomqty=$pcsconvert*$fg->qty;
				$finalqty[]=ceil($pcsbomqty/$bagpcs);
			}

		}

		return array_sum($finalqty);



	}


	function getWeekAvailable($stockindozen,$fgid,$forecast_StartDate,$forecast_EndDate)
	{
	
		$array_index='';
		$cumarray=array();
		$cumdate=array();
		$restey=$this->db->select('forecast_date,qty')->from('fg_forecast_data')->where('item_id',$fgid)->where('forecast_date>=',$forecast_StartDate)->where('forecast_date<=',$forecast_EndDate)->get();
		if($restey->num_rows()>0)
		{
			$i=0;
			foreach($restey->result() as $restey1)
			{
			
				$da[]=$restey1->qty;
				$cumdate[]=$restey1->forecast_date;


			$i++;
			}

/** CREATE CUMULATIVE ARRAY **/
			if(count($da)>0)
			{
				$k=0;
				foreach($da as $d1)
				{
					if($k==0)
					{

						$cumarray[]=$d1;
						
					}else
					{
						$cumarray[]=$cumarray[$k-1]+$d1;
					}



				$k++;
				}
			}

/** END **/


/* GET CLOSEST DATA AND DATE **/

$closest = array_reduce($cumarray, function($carry, $item) use($stockindozen) {
return (abs($item - $stockindozen) < abs($carry - $stockindozen) ? $item : $carry);
}, reset($cumarray));
/** GET KEY **/
$key=array_search($closest,$cumarray,true);		
$closestdate=$cumdate[$key];
/** END **/

/** NOW COMPARE THE DATES **/
$earlier = new DateTime($forecast_StartDate);
$later = new DateTime($closestdate);
$abs_diff = $later->diff($earlier)->format("%a");
return $abs_diff;
/** END **/
		
			
		}


	}


	function getfgstockavailable($semifgid,$per_pcs_bag)
	{
		$availablestock=array();
		$availablestock[]=0;
		$row=$this->db->select('mid,qty')->from('machine_bom')->where('partid',$semifgid)->get();
		if($row->num_rows()>0)
		{
			foreach($row->result() as $fg)
			{
				$fgstock=$this->getfgstock($fg->mid);

				if($fg->qty=='')
				{
					$qty=0;
				}else
				{
					$qty=$fg->qty;
				}

				$availablestock[]=floor(($fgstock*$qty)/$per_pcs_bag);

			}

		}

		return array_sum($availablestock);

	}


		function getfgstock($fgid)
		{
			$stock=0;
			$resteyu=$this->db->select('stock')->from('presto_instruments')->where('id',$fgid)->get();
			if($resteyu->num_rows()>0)
			{
				foreach($resteyu->result() as $row)
				
				$stock=$row->stock*12;

			}


				return $stock;
		}



		function getWeekAvailableforsemifg($stockindozen,$fgid,$forecast_StartDate,$forecast_EndDate,$perpcsbag)
	{
	
		/** Convert stock available into pcs **/
		$stockinpcs=$stockindozen*$perpcsbag;
		$saleType = $this->getSaleType($fgid,$forecast_StartDate, $forecast_EndDate);
			if($saleType==1)
			{ 
				$divideby=12;
			}else
			{
				$divideby=1;
			}

			$stockindozen=$stockinpcs/$divideby;
		
/** END **/

		$array_index='';
		$cumarray=array();
		$cumdate=array();
		$restey=$this->db->select('forecast_date,qty,sale_type')->from('fg_forecast_data')->where('item_id',$fgid)->where('forecast_date>=',$forecast_StartDate)->where('forecast_date<=',$forecast_EndDate)->get();
		if($restey->num_rows()>0)
		{
			$i=0;
			foreach($restey->result() as $restey1)
			{
			
				$da[]=$restey1->qty;
				$cumdate[]=$restey1->forecast_date;


			$i++;
			}

/** CREATE CUMULATIVE ARRAY **/
			if(count($da)>0)
			{
				$k=0;
				foreach($da as $d1)
				{
					if($k==0)
					{

						$cumarray[]=$d1;
						
					}else
					{
						$cumarray[]=$cumarray[$k-1]+$d1;
					}



				$k++;
				}
			}

/** END **/

/* GET CLOSEST DATA AND DATE **/

$closest = array_reduce($cumarray, function($carry, $item) use($stockindozen) {
return (abs($item - $stockindozen) < abs($carry - $stockindozen) ? $item : $carry);
}, reset($cumarray));
/** GET KEY **/
$key=array_search($closest,$cumarray,true);		
$closestdate=$cumdate[$key];
/** END **/
/** NOW COMPARE THE DATES **/
$earlier = new DateTime($forecast_StartDate);
$later = new DateTime($closestdate);
$abs_diff = $later->diff($earlier)->format("%a");
return $abs_diff;
/** END **/
		
			
		}


	}


	
}

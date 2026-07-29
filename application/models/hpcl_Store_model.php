<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model {

	public function __construct()
	{
		
		parent::__construct();
		
	}


function generateprOldd($pr,$jobcardid)
{
	$selecteditem=$this->input->post('itemselected');
	$userid=$_SESSION['logged_in']['user_id'];
	for($r=0;$r<count($selecteditem);$r++)
	{
		$itemid=$selecteditem[$r];
		$qtyss=$this->input->post('qtyparts'.$itemid);
		$unit=$this->input->post('unit'.$itemid);
		
		$dataya=array('itemid'=>$itemid,'qty'=>$qtyss,'prno'=>$pr,'jobcardid'=>$jobcardid,'unit'=>$unit,'addedBy'=>$userid,'addedOn'=>date('Y-m-d H:i:s'));
		
		$this->db->insert('purchase_request',$dataya);
		
		
	}
	return true;
	
	
	
}


function generateproLDDDDDD($pr,$jobcardid)
{
	$selecteditem=$this->input->post('itemselected');
	$userid=$_SESSION['logged_in']['user_id'];
	for($r=0;$r<count($selecteditem);$r++)
	{
		$itemid=$selecteditem[$r];
		
		$qtyss=$this->input->post('qtyparts'.$itemid);
		$currstock=$this->input->post('currentstock'.$itemid);
		$unit=$this->input->post('unit'.$itemid);
		if($currstock>0)
		{
			$prreason=$this->input->post('prreason'.$itemid);
			
		}else{ $prreason=''; }
		
		$dataya=array('itemid'=>$itemid,'qty'=>$qtyss,'prno'=>$pr,'jobcardid'=>$jobcardid,'unit'=>$unit,'addedBy'=>$userid,'addedOn'=>date('Y-m-d H:i:s'),'stockattimeofpr'=>$currstock,'prraisereason'=>$prreason,'source'=>'1');
		
		$this->db->insert('purchase_request',$dataya);
		
		
	}
	return true;
	
	
	
}

function generateprOldddd($pr,$jobcardid)
{
	$selecteditem=$this->input->post('itemselected');
	$userid=$_SESSION['logged_in']['user_id'];
	for($r=0;$r<count($selecteditem);$r++)
	{
		$itemid=$selecteditem[$r];
		
		$qtyss=$this->input->post('qtyparts'.$itemid);
		$currstock=$this->input->post('currentstock'.$itemid);
		$unit=$this->input->post('unit'.$itemid);
		$masterid=$this->input->post('masterid'.$itemid);
		if($currstock>0)
		{
			$prreason=$this->input->post('prreason'.$itemid);
			
		}else{ $prreason=''; }
		
		$dataya=array('masterid'=>$masterid,'itemid'=>$itemid,'qty'=>$qtyss,'prno'=>$pr,'jobcardid'=>$jobcardid,'unit'=>$unit,'addedBy'=>$userid,'addedOn'=>date('Y-m-d H:i:s'),'stockattimeofpr'=>$currstock,'prraisereason'=>$prreason,'source'=>'1');
		
		$this->db->insert('purchase_request',$dataya);
		
		
	}
	return $pr;
	
	
	
}


function generatepr($pr,$jobcardid)
{
	$selecteditem=$this->input->post('itemselected');
	$prnumonly=preg_replace('/[^0-9]/', '', $pr);
	$userid=$_SESSION['logged_in']['user_id'];
	for($r=0;$r<count($selecteditem);$r++)
	{
		$itemid=$selecteditem[$r];
		
		$qtyss=$this->input->post('qtyparts'.$itemid);
		$currstock=$this->input->post('currentstock'.$itemid);
		$unit=$this->input->post('unit'.$itemid);
		$masterid=$this->input->post('masterid'.$itemid);
		if($currstock>0)
		{
			$prreason=$this->input->post('prreason'.$itemid);
			
		}else{ $prreason=''; }
		
		$dataya=array('masterid'=>$masterid,'itemid'=>$itemid,'qty'=>$qtyss,'prno'=>$pr,'jobcardid'=>$jobcardid,'unit'=>$unit,'addedBy'=>$userid,'addedOn'=>date('Y-m-d H:i:s'),'stockattimeofpr'=>$currstock,'prraisereason'=>$prreason,'source'=>'1','purno'=>$prnumonly);
		
		$this->db->insert('purchase_request',$dataya);
		
		
	}
	return $pr;
	
	
	
}




	function getIndianCurrency($number)
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');

    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }

    $rupees = implode('', array_reverse($str));
    $paise = '';

    if ($decimal) {
        $paise = 'and ';
        $decimal_length = strlen($decimal);

        if ($decimal_length == 2) {
            if ($decimal >= 20) {
                $dc = $decimal % 10;
                $td = $decimal - $dc;
                $ps = ($dc == 0) ? '' : '-' . $words[$dc];

                $paise .= $words[$td] . $ps;
            } else {
                $paise .= $words[$decimal];
            }
        } else {
            $paise .= $words[$decimal % 10];
        }

        $paise .= ' paise';
    }

    return ($rupees ? $rupees . 'rupees ' : '') . $paise ;
}


function getcategoryname($id)
{
	$category='';
	$prescat=$this->db->select('category')->from('presto_machine_part_category')->where('id',$id)->get();
	if($prescat->num_rows()>0)
	{
		foreach($prescat->result() as $prescat1);
		
		$category=$prescat1->category;
		
		
	}
	
	return $category;
	
	
	
}




function getlocationname($id)
{
	$category='';
	$prescat=$this->db->select('rack_location')->from('store_rack_location')->where('id',$id)->get();
	if($prescat->num_rows()>0)
	{
		foreach($prescat->result() as $prescat1);
		
		$category=$prescat1->rack_location;
		
		
	}
	
	return $category;
	
	
	
}



function getunit($id)
{
	$category='';
	$prescat=$this->db->select('shortname')->from('units')->where('id',$id)->get();
	if($prescat->num_rows()>0)
	{
		foreach($prescat->result() as $prescat1);
		
		$category=$prescat1->shortname;
		
		
	}
	
	return $category;
	
	
	
}


function getallsupplier($itemid,$masterid)
{
	$suppdata=array();
	$suplo=$this->db->select('b.name,b.id')->from('vendors_price a')->join('vendors b','a.vendorid=b.id')->where('a.itemid',$itemid)->where('masterid',$masterid)->get();
	
	if($suplo->num_rows()>0)
	{
		
		
		
		$suppdata=$suplo->result();
		
	}
	
	
	
	return $suppdata;
	
}


function checkifanypreviousqtyisinwarded($itemid,$po)
{
	
	$resty=$this->db->select('sum(recqty) as inwardedqty')->from('mrn')->where('pono',$po)->where('itemid',$itemid)->get();
	if($resty->num_rows()>0)
	{
	foreach($resty->result() as $resty1);
	return  floatval($resty1->inwardedqty);
	}else{
		
		return 0;
		
	}
	
}


function getgeneralitemsupplier($itemid,$masterid)
{
	$suppdata=array();
	$suplo=$this->db->select('b.name,b.id')->from('vendorwise_house_keeping_item_price a')->join('vendors b','a.vendor_id=b.id')->where('a.item_id',$itemid)->get();
	
	if($suplo->num_rows()>0)
	{
		
		
		
		$suppdata=$suplo->result();
		
	}
	
	
	
	return $suppdata;
	
}


function getvendorname($vname)
{
	$suplo=$this->db->select('name')->from('vendors')->where('id',$vname)->get();
	
	if($suplo->num_rows()>0)
	{
		foreach($suplo->result() as $suplo1);
		$suppdata=$suplo1->name;
		
	}else{
		
		$suppdata='';
	}
	
	
	
	return $suppdata;
	
}



function getnextgetentryno()
	{
		$prnos=$this->db->select('id')->from('mrn')->group_by('gateentryno')->get();
	$num=$prnos->num_rows();
	if($num==0)
	{
		$num1=1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESGE'.$num_padded;
	}else{
		$num1=$num+1;
		$num_padded = sprintf("%02d", $num1);
		$code='PRESGE'.$num_padded;
		
	}
		
		return $code;
		
	}





function getmachineitemname($itemid)
{
	$iname='';
	$resty=$this->db->select('part')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->part;
	}
	
	return $iname;
	
}


function getgeneralitemname($itemid)
{
	$iname='';
	$resty=$this->db->select('item_name')->from('house_keeping_items')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->item_name;
	}
	
	return $iname;
	
}



function getmachineotherdetails($itemid)
{
	
	$iname=array();
	$resty=$this->db->select('conversion_unit,conversion_weight,part,fincode,hsn,specification,size_in_mm,unit,material')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		$iname['fincode']=$resty1->fincode;
		$iname['specialization']=$resty1->specification;
		$iname['name']=$resty1->part;
		$iname['size']=$resty1->size_in_mm;
		$iname['hsn']=$resty1->hsn;
		$iname['unit']=$resty1->unit;
		$iname['material']=$resty1->material;
		$iname['conunit']=$resty1->conversion_unit;
		$iname['conweight']=$resty1->conversion_weight;
	}
	
	return $iname;
	
}



function getvendodetails($vname)
{
	$vendordetail=array();
	$suplo=$this->db->select('name,phone,email,address,gst')->from('vendors')->where('id',$vname)->get();
	
	if($suplo->num_rows()>0)
	{
		foreach($suplo->result() as $suplo1);
		$vendordetail['name']=$suplo1->name;
		$vendordetail['phone']=$suplo1->phone;
		$vendordetail['address']=$suplo1->email;
		$vendordetail['email']=$suplo1->address;
		$vendordetail['gst']=$suplo1->gst;
		
	}
	
	
	
	return $vendordetail;
	
}


function getsusername($userid)
	{
		$alluser='';
		$rest=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->order_by('first_name','ASC')->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $rest1)
			{
				//$alluser[]=$rest1->first_name." ".$rest1->last_name;
				$alluser=strtoupper($rest1->first_name);
			}
			
			
			return $alluser;
		}else
		{
			return $alluser;
		}
		
	}



function getusersdata()
	{
		$guser=array();
		
		$resty=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->get();
		if($resty->num_rows()>0)
		{
			
			return $resty->result();
			
			
			
		}else{
			
			return $guser;
			
		}
	}
	
	
	function checkifpreviousissueismade($itemid,$blockedid)
	{
		$resty=$this->db->select('sum(stock) as issuedstock')->from('issuestocktousers')->where('itemid',$itemid)->where('blockedid',$blockedid)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $resty1);
			$isstock=$resty1->issuedstock;
			
		}else{
			
			$isstock=0;
		}
		
		return $isstock;
		
	}
	
	function getpoinfo($pono)
	{
		
		$resytyuue=$this->db->select('vendor,unit,source,potype')->from('purchase_order')->where('pono',$pono)->get();
		if($resytyuue->num_rows()>0)
		{
			return $resytyuue->result();
			
		}else{
			
			echo "INVALID PO";EXIT;
		}
		
		
	}



function getmachineid($jobcardid)
	{
		
		$rety=$this->db->select('item_id')->from('order_instruments')->where('id',$jobcardid)->get();
		if($rety->num_rows()>0)
		{
			foreach($rety->result() as $resty1);
			$insid=$resty1->item_id;
			return $insid;
		}else{
			
			echo "NO JOBCARD FOUND";exit;
		}
		
	}
	
	function blockallitems($machineid,$jobcardid,$selitem,$gprono)
	{
		
		$resty=$this->db->select('partid,qty')->from('machine_bom')->where('mid',$machineid)->get();
		if($resty->num_rows()>0)
		{
			
			foreach($resty->result() as $resty1)
			{
				if(count($selitem)>0)
				{
				if(in_array($resty1->partid,$selitem))
				{
					$pr=1;
					$gpono=$gprono;
				}else{
					$pr=0;
					$gpono='';
				}
					
				}else{ $pr=0;  $gpono=''; }
				
				$getcurrentstock=$this->getstock($resty1->partid);
				$data=array('itemid'=>$resty1->partid,'machineid'=>$machineid,'jobcardid'=>$jobcardid,'stock'=>$resty1->qty,'stockthattime'=>$getcurrentstock,'active'=>'1','issued'=>'0','pr'=>$pr,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'prno'=>$gpono);
				
				$this->db->insert('blockedstock',$data);
				
				
			}
			
			
			
		}
		
		
		return true;
		
	}


			function getstock($pid)
			{
				$restyu=$this->db->select('current_stock')->from('machine_parts_with_picture')->where('id',$pid)->get();
				if($restyu->num_rows()>0)
				{
					
					foreach($restyu->result() as $restyu1);
					
					$currstock=$restyu1->current_stock;
					
				}else{
					
					$currstock=0;
					
				}
				return $currstock;
				
			}
 


function getissueditem($jobcardid,$partid)
			{
				$issued=0;
			 $restyu=$this->db->select('sum(stock) as issuedstock')->from('issuestocktousers')->where('jobcardid',$jobcardid)->where('itemid',$partid)->get();
			 if($restyu->num_rows()>0)
			 {
				 foreach($restyu->result() as $restyu1);
				 
				 $issued=$restyu1->issuedstock;
				 
			 }
			 
			 return $issued;
			 }


 function getallusers()
			 {
				 $abc=array();
				$restyui=$this->db->select('user_id,first_name,last_name')->from('system_users')->order_by('first_name','ASC')->get(); /**->where('user_status','1')->where('hide_profile','0')**/ 
				if($restyui->num_rows()>0)
				{
					
					
					return $restyui->result();
					
				}else{
					 return $abc;
					
				}
				 
			 }
			 
			 
			 
			 function housekeepitemdetails($id)
			 {
				 $abc=array();
				$restyui=$this->db->select('a.qty,a.item_name,c.shortname')->from('house_keeping_items a')->join('units c','a.unit=c.id','left')->where('a.id',$id)->get();
				if($restyui->num_rows()>0)
				{
					return $restyui->result();
				}else{
					
					return $abc;
				}
				 
			 } 


function getmachinename($in)
{
	$ins='';
	$query = $this->db->select('instruments_name')->from('presto_instruments')->where('id',$in)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->instruments_name;
			
		}
		
		return $ins;

}


function getbom($id)
{
	$ins='';
	$query = $this->db->select('part')->from('machine_parts_with_picture')->where('id',$id)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->part;
			
		}
		
		return $ins;

}

function getudata($userid)
	{
		
		$resty=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $resty1);
			
			return $resty1->first_name." ".$resty1->last_name;
		}else{
			
		return '';
			
		}
	}


function getcolorcountforims($cols)
{
    
        $this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
            $query = $this->db->get();
            $res = $query->result();
            $machinepart_data=array();
            $machinepart_data[]=0;
            foreach($res as $row){
		    
		    /** GET STOCK WISE COLOR **/
			$curst=$row->current_stock;
			$min=$row->min_stock;
			if($cols=='BLACK')
			{
			if($curst<=0.00)
			{
				$machinepart_data[]=1;
			}
			}
			
			/** 33 PER **/
			
			$thr33=floor($min*0.33);
			
			/** 62 PER **/
			$thr62=floor($min*0.62);
			
			
			/** 100 PER **/
			$thr100=$min*1;
			
			if($cols=='RED')
			{
			if($curst>0.00 && $curst<=$thr33)
			{
			  $is=$this->checkifprraised($row->id);
			  if($is==0)
			  {
			$machinepart_data[]=1;	
			  }
			}
			
			}
			
			
			if($cols=='YELLOW')
			{
			 $is=$this->checkifprraised($row->id);
			 if($is==0)
			  {
			if($curst>$thr33 && $curst<=$thr62)
			{
			$machinepart_data[]=1;
			}
			  }
			
			}
			
			
			if($cols=='GREEN')
			{
			if($curst>$thr62 && $curst<=$thr100)
			{
				$machinepart_data[]=1;
			}
			}
			
			
				if($cols=='BLUE')
			{
			if($curst>$row->min_stock)
			{
				$machinepart_data[]=1;
			}
			}
			
			
			
			/** END **/
		    
		    
		}
		
		
	    	return array_sum($machinepart_data);
}


	function checkifprraised($itemid)
	{
        $restu=$this->db->select('id,prno')->from('purchase_request')->where('source','3')->where('itemid',$itemid)->where('approvalstatus','0')->get();
        $resrt=$restu->num_rows();
        
        $restu1=$this->db->select('id,pono')->from('purchase_order')->where('source','3')->where('itemid',$itemid)->get();
        $resrt1=$restu1->num_rows();
        
        if($resrt=='1' || $resrt1=='1' )
        {
            return true;
        }else
        {
             return false;
        }
	    
	}



function getprforims($cols)
{
    
        $this->db->select('a.id,a.current_stock,a.min_stock')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left');
            $query = $this->db->get();
            $res = $query->result();
            $machinepart_data=array();
            $machinepart_data[]=0;
            foreach($res as $row){
		    
		    /** GET STOCK WISE COLOR **/
			$curst=$row->current_stock;
			$min=$row->min_stock;
		
			
			/** 33 PER **/
			
			$thr33=$min*0.33;
			
			/** 62 PER **/
			$thr62=$min*0.62;
			
			
			/** 100 PER **/
			$thr100=$min*1;
			
			if($cols=='RED')
			{
			if($curst>0.00 && $curst<=$thr33)
			{
            $is=$this->checkifprraised($row->id);
            
            if($is==1)
            {
            //echo $row->id;exit;
            $machinepart_data[]=1;	
            }
			}
			
			}
			
			
			if($cols=='YELLOW')
			{
			 
			if($curst>$thr33 && $curst<=$thr62)
			{
                $is=$this->checkifprraised($row->id);
                
                if($is==1)
                {  
                $machinepart_data[]=1;
                }
			}
			  
			
			}
			
			
			/** END **/
		    
		}
		
			return array_sum($machinepart_data);
}


  function criticalitemrecord()
            {
                $restsys=$this->db->select('a.id')->from('machine_parts_with_picture  a')->where('a.current_stock<a.min_stock')->where('a.critical','1')->get();
                return $restsys->num_rows();
               
            } 


            function pendingmrn()
            {
                	$rest=$this->db->select('a.*')->from('mrn a')->where('a.mrndone','0')->group_by('a.gateentryno')->order_by('a.addedOn','DESC')->get();
                	return $rest->num_rows();
                
            }
            
            function rgpchallannotclosedcount()
            {
                 	$cha=$this->db->select('a.id')->from('outwardchallan a')->where('a.open','0')->group_by('a.id')->order_by('a.id','DESC')->get(); 
                 	
                 	return $cha->num_rows();
                
                
            }
            
            function bompr()
            {
                $rest=$this->db->select('a.jobcardid')->from('purchase_request a')->where('a.approvalstatus','0')->where('a.source','1')->group_by('a.prno')->get();
                
                return $rest->num_rows();
                
            }
            
            
             function imspr()
            {
                $rest=$this->db->select('a.jobcardid')->from('purchase_request a')->where('a.approvalstatus','0')->where('a.source','3')->group_by('a.prno')->get();
                
                return $rest->num_rows();
                
            }
            
            
            function blockallserviceitems($machine,$jobcardid,$gprono,$qty)
	{
			
			$fincode=$this->getfincodebyinstrument($machine);
			if($fincode<>'')
			{
		
		$resty=$this->db->select('id as partid')->from('machine_parts_with_picture')->where('fincode',$fincode)->get();
		if($resty->num_rows()>0)
		{
			
		foreach($resty->result() as $resty1);

		if($gprono=='')
		{
		$pr=0;
		$gpono='';
		}else{
		$pr=1;
		$gpono=$gprono;
		}

		$getcurrentstock=$this->getstock($resty1->partid);
		$data=array('itemid'=>$resty1->partid,'machineid'=>'0','jobcardid'=>$jobcardid,'stock'=>$qty,'stockthattime'=>$getcurrentstock,'active'=>'1','issued'=>'0','pr'=>$pr,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'prno'=>$gpono,'servicepart'=>'1');

		//echo "<pre>"; print_r($data);exit;
		$this->db->insert('blockedstock',$data);
				
				
			
			
			
		}
		

		}
		
		return true;
		
	}



function getvendornameotherdetails($vname)
{
	$suppdata=array();
	$suplo=$this->db->select('name,contactperson,email,phone')->from('vendors')->where('id',$vname)->get();
	
	if($suplo->num_rows()>0)
	{
		foreach($suplo->result() as $suplo1);
		$suppdata['name']=$suplo1->name;
		$suppdata['contactperson']=$suplo1->contactperson;
		$suppdata['email']=$suplo1->email;
		$suppdata['phone']=$suplo1->phone;
		
	}
	
	
	
	return $suppdata;
	
}



function checkforgreensupplier($itemid,$masterid)
{
	$suppdata=array();
	$suplo=$this->db->select('a.id')->from('vendors_price a')->where('a.itemid',$itemid)->where('a.green_supplier','1')->get();
	
	if($suplo->num_rows()>0)
	{
		foreach($suplo->result() as $suplo1);
		
		return $suplo1->id;
	
		
	}else{
		return false;
	}
	
	
}




function checkforgreensupplierforgeneralitem($itemid,$masterid)
{
	$suppdata=array();
	$suplo=$this->db->select('a.id')->from('vendorwise_house_keeping_item_price a')->where('a.item_id',$itemid)->where('a.green_supplier','1')->get();
	
	if($suplo->num_rows()>0)
	{
		foreach($suplo->result() as $suplo1);
		
		return $suplo1->id;
	
		
	}else{
		return false;
	}
	
	
}

function getqcrejectedcount()
{	$rest=$this->db->select('a.*,a.pono,a.itemid,a.qty as reject_qty')->from('item_rejection_request
 a')->join('mrn_history b','a.mrnhistoryid=b.id','left')->order_by('a.approved','ASC')->order_by('a.addedOn','DESC')->get();
		return $rest->num_rows();
    
    
}

function autoprcount()
{
    
    $ass=array();
    $ass[]=0;
   	$resty=$this->db->select('id')->from('machine_parts_with_picture')->where('current_stock<min_stock')->where('min_stock!=','0')->group_by('part','ASC')->get();
		if($resty->num_rows()>0)
		{
			$i=1;  
			foreach($resty->result() as $restyui1)
			{
			    $por=$this->checkifpoisraised($restyui1->id);
			    
			    if($por==0)
			    {
			    $ass[]=1;
			    }
			
			$i++;
			}
			
		}
		
		return array_sum($ass);
    
}

function checkifpoisraised($itemid)
{
    
    $Rete=$this->db->select('id')->from('purchase_order')->where('itemid',$itemid)->where('source','3')->where('approved','1')->where('completed','0')->get();
    
    return $Rete->num_rows();
    
    
}


function vendorforreviewcount()
{
   	$this->db->select('id')->from('vendor_for_review')->Where('status','0')->group_by('item_id');
		$query = $this->db->get(); 
		
		return $query->num_rows();
}




function getpototalOldd($pono)
{
	$poval=0;
	$povalue[]=0;
	$resyteyur=$this->db->select('itemid,price,qty,vendor,potype')->from('purchase_order')->where('pono',$pono)->get();
	if($resyteyur->num_rows()>0)
	{
		foreach($resyteyur->result() as $resyteyur1123);
		$gst=$this->checkisupplierhasgst($resyteyur1123->vendor);
		foreach($resyteyur->result() as $resyteyur1)
		{
			/** ITEM MUL QTY **/
			if($gst>0)
			{

		$gstnva=$this->checkforitemgst($resyteyur1->itemid,$resyteyur1->potype);
		if($gst>0)
		{
			$gww=$gstnva/100;
			$singleqtygst=$resyteyur1->price*$gww;
			$finalgst=$singleqtygst*$resyteyur1->qty;
			$itemval=$resyteyur1->price*$resyteyur1->qty+$finalgst;

			}else{

			$gstval=0;
			$itemval=$resyteyur1->price*$resyteyur1->qty;
			}
			
			
			$povalue[]=$itemval;
			
			}
		}
		
		
		
		
	}
	
	return array_sum($povalue);
	
	
}


function getpototal($pono)
{
	$poval=0;
	$povalue[]=0;
	$resyteyur=$this->db->select('itemid,price,qty,vendor,potype')->from('purchase_order')->where('pono',$pono)->get();
	if($resyteyur->num_rows()>0)
	{
		foreach($resyteyur->result() as $resyteyur1123);
		$gst=$this->checkisupplierhasgst($resyteyur1123->vendor);
		foreach($resyteyur->result() as $resyteyur1)
		{
			/** ITEM MUL QTY **/
			
				$converted=$this->checkforconversionapplicable($resyteyur1->itemid);
				if($converted>0)
				{
					$finalqty=$converted*$resyteyur1->qty;

				}else
				{
					$finalqty=$resyteyur1->qty;	
				}
				$gstnva=$this->checkforitemgst($resyteyur1->itemid,$resyteyur1->potype);
				if($gstnva>0 && $gst>0)
				{
				$gww=$gstnva/100;
				$singleqtygst=$resyteyur1->price*$gww;
				$finalgst=$singleqtygst*$finalqty;
				$itemval=$resyteyur1->price*$finalqty+$finalgst;

				}else{

				$gstval=0;
				$itemval=$resyteyur1->price*$finalqty;
				}


				$povalue[]=$itemval;
			
			
		}
		
		
		
		
	}
	
	return array_sum($povalue);
	
	
}


function checkisupplierhasgst($vendor)
{
	$g=0;
	$Resteyre=$this->db->select('gst')->from('vendors')->where('id',$vendor)->get();
	if($Resteyre->num_rows()>0)
	{
		foreach($Resteyre->result() as $Resteyre1);
		
		if($Resteyre1->gst!='')
		{
			$g=1;
		}
		
		
	}
	
	return $g;
	
	
}

function checkforitemgst($itemid,$potype)
{
	$gstn=0;
	if($potype==0)
	{
		$Restrur=$this->db->select('gst')->from('machine_parts_with_picture')->where('id',$itemid)->get();
		if($Restrur->num_rows()>0)
		{
			foreach($Restrur->result() as $Restrur1);
			
			$gstn=$Restrur1->gst;
			
		}
		
	}else{

		$Restrur=$this->db->select('gst')->from('house_keeping_items')->where('id',$itemid)->get();
		if($Restrur->num_rows()>0)
		{
			foreach($Restrur->result() as $Restrur1);
			
			$gstn=$Restrur1->gst;
			
		}

	}
	
	
	return $gstn;
	
	
}

function getindentno($prno)
{
    $resteur=$this->db->select('sourceid')->from('purchase_request')->where('prno',$prno)->get();
    if($resteur->num_rows()>0)
    {
        foreach($resteur->result() as $resteur1);
        
        return $resteur1->sourceid;
    }else
    {
        return '';
    }
}


function getpoinstruction()
{
	
	
    $resteur=$this->db->select('script')->from('poinstructions')->get();
    if($resteur->num_rows()>0)
    {
        foreach($resteur->result() as $resteur1);
        
        return $resteur1->script;
    }else
    {
        return '';
    }
}



function checktheissuename($userid,$type)
{
	$fullname='';
	if($type==1)
	{
		
		$quwery=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$userid)->get();	
		if($quwery->num_rows()>0)
		{
		foreach($quwery->result() as $quwery1);
		$fullname=$quwery1->first_name.' '.$quwery1->last_name;
		}
		
		
	}else{
		
		$quwery=$this->db->select('employee_name')->from('prestogroup_employees')->where('id',$userid)->get();	
		if($quwery->num_rows()>0)
		{
		foreach($quwery->result() as $quwery1);
		$fullname=$quwery1->employee_name;
		
		}
		
		
	}
	
	
	return $fullname;
	
}


function getstoreusersdata($userid,$type)
	{
		$guser=array();
		
		if($type==1)
		{
		$quwery=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_id',$userid)->get();	
		if($quwery->num_rows()>0)
		{
		foreach($quwery->result() as $quwery1);
		$guser['username']=$quwery1->first_name.' '.$quwery1->last_name;
		$guser['userid']=$quwery1->user_id;
		}
		
		}else{
			
			$quwery=$this->db->select('employee_name,id')->from('prestogroup_employees')->where('id',$userid)->get();	
			if($quwery->num_rows()>0)
			{
			foreach($quwery->result() as $quwery1);
			$guser['username']=$quwery1->employee_name;
			$guser['userid']=$quwery1->id;

		}
			
			
		}
		
		return $guser;exit;
	}
	
	
	function checkifanyitemisissued($jobcardid,$itemid,$blockedflag,$itemtype)
	{
		$issuedstock=0;
		$restey=$this->db->select('sum(stock) as issuedstock')->from('issuestocktousers')->where('itemtype',$itemtype)->where('jobcardid',$jobcardid)->where('itemid',$itemid)->where('issuetype',$blockedflag)->get();
		if($restey->num_rows()>0)
		{
			foreach($restey->result() as $restey1);
			
			$issuedstock=floatval($restey1->issuedstock);
			
			
		}
		
		return $issuedstock;
		
	}
	
	function getnoncrmuser()
	{
	
		$noncrm=array();
		
		$restyue=$this->db->select('employee_name,id')->from('prestogroup_employees')->where('system_user','0')->get();
		if($restyue->num_rows()>0)
		{
			return $restyue->result();
		}else{
			
			return $noncrm;
		}
		
		
	}
	
	
	 function machineitemdetails($id)
			 {
				 $abc=array();
				$restyui=$this->db->select('a.current_stock,a.part,c.shortname,a.fincode,a.specification')->from('machine_parts_with_picture a')->join('units c','a.unit=c.id','left')->where('a.id',$id)->get();
				if($restyui->num_rows()>0)
				{
					return $restyui->result();
				}else{
					
					return $abc;
				}
				 
			 } 
			 
			 
			 
			  function checkwhomitisissued($itemtype,$itemid,$jobcardid)
			 {
				 $issuedto='';
				 $Restey=$this->db->select('issuedto,usertype')->from('issuestocktousers')->where('itemtype','1')->where('jobcardid',$jobcardid)->where('itemid',$itemid)->get();
				if($Restey->num_rows()>0)
				{
				foreach($Restey->result() as $Restey1);

				$issuedto=$Restey1->issuedto;
				$usertype=$Restey1->usertype;

				if($usertype=='1')
				{
				$Resty=$this->db->select('first_name,last_name')->from('system_users')->where('user_id',$issuedto)->get();

				if($Resty->num_rows()>0)
				{
				foreach($Resty->result() as $Resty1);

				return $Resty1->first_name." ".$Resty1->last_name;

				}else{ return $issuedto;  }

				}else
				{
				$Resty=$this->db->select('employee_name')->from('prestogroup_employees')->where('id',$issuedto)->get();

				if($Resty->num_rows()>0)
				{
				foreach($Resty->result() as $Resty1);

				return $Resty1->employee_name; 
				}else{  return $issuedto;  }


				}
				}else{
				return $issuedto;
				}

				 
			 }
			 
			 
			 
	function getprstatus($prno)
	{
	$prstatus='';
	$poraisedate='';
	$resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn')->from('purchase_order')->where('prno',$prno)->get();
	if($resteyur->num_rows()>0)
	{
		foreach($resteyur->result() as $resteyur1);
		
		if($resteyur1->approved=='1')
		{
			$prstatus= "PO APPROVED"; 
		}else{
			$prstatus= "UNAPPROVED PO";
		}
		
		
		if($resteyur1->gateentrycomplete=='1')
		{
			$prstatus= "QC MRN"; 
		}
		
		
		if($resteyur1->completed=='1')
		{
			$prstatus= "COMPLETED"; 
		}
		
		
		
		
		
		
	
	}else{
		
		$prstatus= "PO YET TO BE RAISED";
	}



        return $prstatus;

	
	
}
  


function getporaisedate($addedOn,$vendor,$prno)
{
    
    $enddate='';
    $Restrey=$this->db->select('deliverytime,otherdeliverytime')->from('vendors')->where('id',$vendor)->get();
    
    if($Restrey->num_rows()>0)
    {
        foreach($Restrey->result() as $Restrey1);
        if($Restrey1->deliverytime=='Other')
        {
        $deliday=$Restrey1->otherdeliverytime;
        }else
        {
        $deliday=$Restrey1->deliverytime;   
        }
    
   
        $addedOn=date('Y-m-d',strtotime($addedOn));
     

         $enddate =date('d-m-Y',strtotime($addedOn . ' +'.$deliday.' day'));
       
        
    }
    
        return $enddate;
    
    
}


function checkdeliveryday($prno)
{
    $poraisedate='';
    $resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn')->from('purchase_order')->where('prno',$prno)->get();
    if($resteyur->num_rows()>0)
    {
        foreach($resteyur->result() as $resteyur1);
        $poraisedate=$this->getporaisedate($resteyur1->addedOn,$resteyur1->vendor,$prno);
        
    }
    
        return $poraisedate;
		  
}



	
	function getnoncrmusername($userid)
	{
		
	$noncrm=array();
	$restyue=$this->db->select('employee_name')->from('prestogroup_employees')->where('id',$userid)->get();
	if($restyue->num_rows()>0)
	{
	foreach($restyue->result() as $restyue1);
	return $restyue1->employee_name;
	}else{

	return '';
	}

	}
	


function getjobcardno($jobcard)
{
    $restey=$this->db->select('job_card_no')->from('order_instruments')->where('id',$jobcard)->get();
    if($restey->num_rows()>0)
    {
        foreach($restey->result() as $restey12);
        
        return $restey12->job_card_no;
        
    }else
    {
        return '';
    }
    

}	


function getmachinewithotherdetails($itemid)
{
	
	$iname=array();
	$resty=$this->db->select('a.fincode,a.size_in_mm,a.material,a.specification,b.rack_location,a.part,a.unit')->from('machine_parts_with_picture a')->join('store_rack_location b','a.location_id=b.id')->where('a.id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		$iname['name']=$resty1->part;
		$iname['fincode']=$resty1->fincode;
		$iname['specialization']=$resty1->specification;
		$iname['rack_location']=$resty1->rack_location;
		$iname['unit']=$resty1->unit;
		$iname['size']=$resty1->size_in_mm;
		$iname['material']=$resty1->material;
	}
	
	return $iname;
	
}


function getgeneralitemunit($itemid)
{
	$iname='';
	$resty=$this->db->select('a.unit,c.shortname')->from('house_keeping_items a')->join('units c','a.unit=c.id','left')->where('a.id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->shortname;
	}
	
	return $iname;
	
}


function getinstrumentname($id)
{
    
    $restye=$this->db->select('instruments_name')->from('presto_instruments')->where('id',$id)->get();
    if($restye->num_rows()>0)
    {
        foreach($restye->result() as $restye1);
        
        return $restye1->instruments_name;
    }else
    {
        return '';
    }
    
}



function getgeneralitemdetails($itemid)
{
	$iname=array();
	$resty=$this->db->select('item_name,unit')->from('house_keeping_items')->where('id',$itemid)->get();
    if($resty->num_rows()>0)
    {
    	foreach($resty->result() as $resty1);
    	$iname['name']=$resty1->item_name;
    	$iname['unit']=$resty1->unit;
    }
	
	return $iname;
	
}

	function checkifstorehasaccepted($itemid,$blockedid)
	{
	    $restsyeee=array();
		$resty=$this->db->select('a.storeaccept,b.first_name,b.last_name')->from('issuestocktousers a')->join('system_users b','a.acceptedBy=b.user_id','left')->where('a.itemid',$itemid)->where('a.blockedid ',$blockedid)->where('a.storeaccept','1')->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $resty1);
            if($resty1->storeaccept=='1')
            {
            $stca="Yes";
            }else
            {
            $stca="No";
            }
            
            $restsyeee['storeaccept']=$stca;
            $restsyeee['accby']=$resty1->first_name." ".$resty1->last_name;
			
		}
		
		return $restsyeee;
		
	}
	
	
	function getexpecteddays($prno,$itemid)
	{
	    $fda='';
	    $resteyue=$this->db->select('pono')->from('purchase_order')->where('prno',$prno)->get();
	    if($resteyue->num_rows()>0)
	    {
	        foreach($resteyue->result() as $resteyue1);
	        
	        $Restye=$this->db->select('expected_date')->from('vendor_followup')->where('itemid',$itemid)->where('po_no',$resteyue1->pono)->where('expected_date !=','0000-00-00')->order_by('id','ASC')->limit(1)->get();
	        if($Restye->num_rows()>0)
	        {
	            foreach($Restye->result() as $Restye111);
	            
	            $fda= date('d-m-Y',strtotime($Restye111->expected_date));
	        }
	        
	       
	    }
	    
	    return $fda;
	    
	}
	
	  function withoutgstcount()
{
	$restyey=$this->db->select('id')->from('machine_parts_with_picture')->where('gst','0.00')->get();
	return $restyey->num_rows();
	
} 


function withoutvendor()
{
		$restyey=$this->db->select('b.itemid')->from('machine_parts_with_picture a')->join('vendors_price b','a.id=b.itemid','left')->where('b.itemid',NULL)->get();
		return $restyey->num_rows();
		
		
		
	
}


function withoutfincode()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture')->where('fincode',NULL)->or_where('fincode','')->get();
		return $restyey->num_rows();
	

	
}



function withoutracklocation()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture')->where('location_id','0')->or_where('location_id',NULL)->get();
		return $restyey->num_rows();
	

	
}


function withoutimage()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture')->where('picture',NULL)->or_where('picture','')->get();
		return $restyey->num_rows();
}


function getitemprice($potype,$vendor,$itemid)
{
$price='';
if($potype==0)
{

$tweyew=$this->db->select('price')->from('vendors_price')->where('itemid',$itemid)->where('vendorid',$vendor)->get();
if($tweyew->num_rows()>0)
{
foreach($tweyew->result() as $tweyew1);

$price=$tweyew1->price;

}

}else
{

$tweyew=$this->db->select('price')->from('vendorwise_house_keeping_item_price')->where('item_id',$itemid)->where('vendor_id',$vendor)->get();
if($tweyew->num_rows()>0)
{
foreach($tweyew->result() as $tweyew1);

$price=$tweyew1->price;

}


}


return floatval($price);

}


function getlistprice($itemid,$vendor)
{

$restyu=$this->db->select('listprice')->from('vendors_price')->where('vendorid',$vendor)->where('itemid',$itemid)->get();
if($restyu->num_rows()>0)
{
foreach($restyu->result() as $restyu1);

return $restyu1->listprice;

}else
{
return '';
}

}



function checkispoismade($jobcardid)
{


   	$htmldata= "<table border='1' style='width:200px;'><tr style=''><th style='padding:2px 2px 2px 2px; text-align:center'>PO Number</th></tr><tbody>";

$resteueiu=$this->db->select('pono')->from('purchase_order')->where('jobcard',$jobcardid)->group_by('pono')->get();
if($resteueiu->num_rows()>0)
{
	$r=1;
foreach($resteueiu->result() as $resteueiu1)
{
		$htmldata.="<tr><td style='padding:2px 2px 2px 2px; text-align:center'><a href='".page_url."Store/deliverystatuspo/".$resteueiu1->pono."' target='_blank'	>".$resteueiu1->pono."</a></td></tr>";

$r++;
}
}else
{


		$htmldata="";

}



return $htmldata;

}

function getapprovaldateanddeliverytime($id)
{
$resty=$this->db->select('approved,approvedOn,vendor')->from('purchase_order')->where('id',$id)->get();
if($resty->num_rows()>0)
{
	foreach($resty->result() as $resty1);
	
	if($resty1->approved==1)
	{
	$app="Approved";
	$appdate=date('d-M-Y',strtotime($resty1->approvedOn));
	$delidate=$this->getdeliverydate($resty1->vendor,$resty1->approvedOn);
	}else if($resty1->approved==2)
	{
	$app="Rejected";
	$appdate='';
	$delidate='';
	}else
	{
	$app="Action Pending";
	$appdate='';
	$delidate='';
	}
	$data=array('approvalstatus'=>$app,'approvedon'=>$appdate,'deliverydate'=>$delidate);
	
	return $data;



}else
{
	return $data=array();
}


}


function getdeliverydate($vendortime,$approcedon)
{	
$Restye=$this->db->select('deliverytime')->from('vendors')->where('id',$vendortime)->get();
if($Restye->num_rows()>0)
{

foreach($Restye->result() as $Restye1);
$deliverytime=$Restye1->deliverytime;

$delivery=date('d-M-Y', strtotime($approcedon. ' + '.$deliverytime.' days'));

return $delivery;



}else
{
return '';
}


}
	
	
	
		function manualblockallitems($machineid,$jobcardid)
			{


			$resty=$this->db->select('partid,qty')->from('machine_bom')->where('mid',$machineid)->get();
			if($resty->num_rows()>0)
			{

			foreach($resty->result() as $resty1)
			{

			$pr=0;  $gpono=''; 

			$getcurrentstock=$this->getstock($resty1->partid);
			$data=array('itemid'=>$resty1->partid,'machineid'=>$machineid,'jobcardid'=>$jobcardid,'stock'=>$resty1->qty,'stockthattime'=>$getcurrentstock,'active'=>'1','issued'=>'0','pr'=>$pr,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'prno'=>$gpono);

			$this->db->insert('blockedstock',$data);

			}}
			return true;
			}


function getpendpocount()
{

	$rest=$this->db->select('a.masterid')->from('purchase_request a')->join('machine_parts_with_picture d','d.id=a.masterid')->where('a.approvalstatus','0')->where('a.closed','0')->order_by('d.part','ASC')->get();

	return $rest->num_rows();


}


function getfincodebyinstrument($machine)
{
	$finc='';
	$resyteyure=$this->db->select('fincode')->from('presto_instruments')->where('id',$machine)->where('fincode !=','')->get();
	if($resyteyure->num_rows()>0)
	{
		foreach($resyteyure->result() as $resyteyure);
		$finc=$resyteyure->fincode;
	}
	return $finc;
}




function getprstatusitemwise($prno,$itemid)
	{
	$prstatus='';
	$poraisedate='';
	$resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn,prno')->from('purchase_order')->where('prno',$prno)->where('itemid',$itemid)->get();
	if($resteyur->num_rows()>0)
	{
		foreach($resteyur->result() as $resteyur1);
		
		
		
		if($resteyur1->approved=='1')
		{
			$prstatus= "PO APPROVED";
			
			$estey=$this->db->select('qcstatus')->from('mrn')->where('pono',$resteyur1->pono)->where('itemid',$itemid)->get();
			if($estey->num_rows()>0)
			{
			foreach($estey->result() as $estey1);
			
			if($estey1->qcstatus=='1')
			{
			$prstatus='QC DONE';
			}else
			{
			$prstatus='QC PENDING';
			}
			
			
			}else
			{
			
			$prstatus= "PENDING DELIVERY";
			
			$purc=$this->db->select('closed')->from('purchase_request')->where('prno',$resteyur1->prno)->where('itemid',$itemid)->get();
			if($purc->num_rows()>0)
			{
			foreach($purc->result() as $purc1);

			if($purc1->closed=='1')
			{
			$prstatus= "PR CLOSED/PO TO BE RAISED"; 
			}

			}
			
			
			}
			 
		}else{
		
		$prstatus= "PO NOT APPROVED";
		$purc=$this->db->select('closed')->from('purchase_request')->where('prno',$resteyur1->prno)->get();
			if($purc->num_rows()>0)
			{
			foreach($purc->result() as $purc1);

			if($purc1->closed=='1')
			{
			$prstatus= "PR CLOSED/PO TO BE RAISED"; 
			}

			}
			
		}
		
		
		if($resteyur1->gateentrycomplete=='1')
		{
			$prstatus= "QC MRN"; 
		}
		
		
		if($resteyur1->completed=='1')
		{
			$prstatus= "COMPLETED"; 
		}
		
			

		
		
		
	
	}else{
		
		$prstatus= "PO TO BE RAISED";
	}



        return $prstatus;

	
	
}



function checkifanypreviousqtyisinwardedinmrn($itemid,$po,$poid)
{
	
	$resty=$this->db->select('sum(recqty) as inwardedqty')->from('mrn')->where('pono',$po)->where('itemid',$itemid)->where('poid',$poid)->get();
	if($resty->num_rows()>0)
	{
	foreach($resty->result() as $resty1);
	return  floatval($resty1->inwardedqty);
	}else{
		
		return 0;
		
	}
	
}


function getmrnhistorydata($recorddata)
{
    
    $data=array();
    $resty=$this->db->select('accept_qty,reject_qty,reject_reason,rejectedimage')->from('mrn_history')->where('record_id',$recorddata)->get();
    if($resty->num_rows()>0)
    {
    foreach($resty->result() as $resty1);
	$data['accept']=$resty1->accept_qty;
	$data['reject']=$resty1->reject_qty;
	$data['remarks']=$resty1->reject_reason;
	$data['image']=$resty1->rejectedimage;
    
    }
    
    return $data;

    
}

function jobcardunapprovedpo()
{
$jbcard[]=0;
$rsteye=$this->db->select('price,qty')->from('purchase_order')->where('approved','0')->where('source','1')->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{

$jbcard[]=$rsteye1->price*$rsteye1->qty;
}

}


return array_sum($jbcard);

}

function indentunapprovedpo()
{

$jbcard[]=0;
$rsteye=$this->db->select('price,qty')->from('purchase_order')->where('approved','0')->where('source','2')->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{

$jbcard[]=$rsteye1->price*$rsteye1->qty;
}

}


return array_sum($jbcard);

}
  



function imsunapprovedpo()
{

$jbcard[]=0;
$rsteye=$this->db->select('price,qty')->from('purchase_order')->where('approved','0')->where('source','3')->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{

$jbcard[]=$rsteye1->price*$rsteye1->qty;
}

}


return array_sum($jbcard);

}


function saveAction($data, $id) {
		$this->db->where('id', $id)
				 ->update('service_repair_request', $data);

		return $this->db->affected_rows();
	}
  

function getpendingitemcount($itemid)
{
$rest=$this->db->select('sum(a.qty) as sumqty')->from('purchase_order a')->where('a.completed','0')->where('a.itemid',$itemid)->get();
if($rest->num_rows()>0)
{
foreach($rest->result() as $restqq);
return $restqq->sumqty;
}
return '';



}
  
  
  function getclosedpocount()
{

	$rest=$this->db->select('a.masterid')->from('purchase_request a')->join('machine_parts_with_picture d','d.id=a.masterid')->where('a.approvalstatus','0')->where('a.closed','1')->order_by('d.part','ASC')->get();

	return $rest->num_rows();


}	


function getpartscount()
{

	$rest=$this->db->select('id')
				   ->from('machine_parts_with_picture')
				   ->where('flag','0')
				   ->get();

	return $rest->num_rows();


}


function updateQty($data, $id) {


$this->db->trans_begin();
		$this->db->where('id', $id)
				 ->update('machine_parts_with_picture', $data);

 if ($this->db->trans_status() === FALSE)
                {
                $this->db->trans_rollback();
                }
                else
                {
                $this->db->trans_commit();
                }
                
		return $this->db->affected_rows();
	}
  
  
  function getallpendingitemsfrompr($prno)
  {
  $as=array();
 
 $htmldata='';
	$rest=$this->db->select('d.part as machine_part,d.fincode,d.specification,d.size_in_mm,a.itemid,a.unit,a.qty,a.jobcardid')->from('purchase_request a')->join('machine_parts_with_picture d','d.id=a.masterid')->where('a.prno',$prno)->where('a.closed','0')->order_by('d.part','ASC')->get();
	if($rest->num_rows()>0)
	{
	
	
  
	$i=1;
	$a='';
	$j=1;
	
	$as[]=0;
	foreach($rest->result() as $rest1)
	{	
	
	$iscompleted=$this->checkifpoisnotcompleted($rest1->itemid,$prno,$rest1->jobcardid);
	if($iscompleted==0)
	{
	$currentstage=$this->getprstatusitemwise($prno,$rest1->itemid);
	if($i<6)
	{
	$a="";
	}else
	{
	$a="display:none;";
	} 
	
	if($currentstage<>'COMPLETED')
	{	
	if($i==1)
	{
	$htmldata.= "<table border='1' style='width:500px;'>
	<tr style=''>
	<th style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>Sno.</th>
	<th style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>ITEM</th>
	<th style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>SPECS & SIZE.</th>
	<th style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>STAGE</th>
	<th style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>EXPECTED DATE</th>
	</tr>
	<tbody>";
	}

	$as[]=1;
	$expecteddate=$this->getexpecteddatefordelivery($rest1->itemid,$rest1->jobcardid,$prno);
	$htmldata.="<tr style='".$a."' class='showmorejbcard".$rest1->jobcardid."'>
	<td style='padding:2px 2px 2px 2px; text-align:center;width:100px;'>".$i."</td>
	<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>".$rest1->machine_part."</td>
	<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'>".$rest1->specification."<br/>".$rest1->size_in_mm."</td>
	<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'><strong>".$currentstage."</strong></td>
	<td style='padding:2px 2px 2px 2px; text-align:center;width:200px;'><strong>".$expecteddate."</strong></td>
	</tr>";

	$i++;
	if($rest->num_rows()==$j)
	{

	$htmldata.="</tbody></table>";
	}
	}
	}
	
	$j++;
	}
	
	
	
	if(array_sum($as)>5)
	{
	$pend=array_sum($as)-5;
	$htmldata.="<a href='javascript:;' onclick='showalldata(".$rest1->jobcardid.");' class='closetoggle".$rest1->jobcardid."'>View ".$pend." More </a>";
	}
	}
	
	
	
	return $htmldata;
				
  
  }


function checkifpoisnotcompleted($itemid,$prno,$jobcardno)
{
$a=0;
$Resty=$this->db->select('completed')->from('purchase_order')->where('itemid',$itemid)->where('prno',$prno)->where('jobcard',$jobcardno)->get();
if($Resty->num_rows()>0)
{
foreach($Resty->result() as $Resty1);
if($Resty1->completed=='1')
{
$a=1;
}

}else
{
$a=0;
}

return $a;
}



function getexpecteddatefordelivery($itemid,$jobcardid,$prno)
{

$podetail=$this->getpoapprovaldate($itemid,$jobcardid,$prno);
if(count($podetail)>0)
{
    $vendor=$podetail['vendor'];
    $addedOn=$podetail['approveddate'];
    $enddate='';
    $Restrey=$this->db->select('deliverytime,otherdeliverytime')->from('vendors')->where('id',$vendor)->get();
    
    if($Restrey->num_rows()>0)
    {
        foreach($Restrey->result() as $Restrey1);
        if($Restrey1->deliverytime=='Other')
        {
        $deliday=$Restrey1->otherdeliverytime;
        }else
        {
        $deliday=$Restrey1->deliverytime;   
        }
    
   
        $addedOn=date('Y-m-d',strtotime($addedOn));
     

         $enddate =date('d-m-Y',strtotime($addedOn . ' +'.$deliday.' day'));
       
        
    }
    
   
        return $enddate;
  }else
  {
  
  return ''; 
  }
    
    
}

function getpoapprovaldate($itemid,$jobcardid,$prno)
{
$appdate=array();

$restyyu=$this->db->select('approvedOn,vendor')->from('purchase_order')->where('itemid',$itemid)->where('prno',$prno)->where('jobcard',$jobcardid)->where('approved','1')->get();
if($restyyu->num_rows()>0)
{
foreach($restyyu->result() as $restyyu1);
$appdate['approveddate']=$restyyu1->approvedOn;
$appdate['vendor']=$restyyu1->vendor;

}

return $appdate;

}




	function blockallstoreitems($machineid,$jobcardid,$selitem,$pr,$qty,$currstock)
	{
		
	
				$data=array('itemid'=>$selitem,'machineid'=>$machineid,'jobcardid'=>$jobcardid,'stock'=>$qty,'stockthattime'=>$currstock,'active'=>'1','issued'=>'0','pr'=>$pr,'addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id']);
				
				$this->db->insert('blockedstock',$data);
				$id=$this->db->insert_id();
				
		
		
		
		return $id;
		
	}
	
	
	
	function saveReturnStock($data) {
		$this->db->insert('return_stock_from_store', $data);
		return $this->db->affected_rows();
	}
  

	function updateFlag($data, $id) {
		$this->db->where('id', $id)
				 ->update('return_stock_from_store', $data);

		return $this->db->affected_rows();
	}
	
	
		function updateStock($data, $fincode) {
		$this->db->where('fincode', $fincode)
				 ->update('machine_parts_with_picture', $data);

		return $this->db->affected_rows();
	}
	
	
	function saveStock($data) {
		$this->db->insert('issuestocktousers', $data);
		return $this->db->affected_rows();
	}
	
	
	function getEditPoHistory($id) {
	$query = $this->db->select('a.qty, a.price, a.discount, b.part, a.itemid, a.vendor, c.name')
					  ->from('purchase_order a')
					  ->join('machine_parts_with_picture b','b.id=a.itemid')
					  ->join('vendors c','c.id=a.vendor')
					  ->where('a.id', $id)
					  ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			}
	}

	function getPriceDetails($vendorid) {
	$query = $this->db->select('listprice, discount')
					  ->from('vendors_price')
					  ->where('vendorid', $vendorid)
					  ->get();

			if($query->num_rows() > 0) {
				return $query->result();
			}
	}

	function update_po_history($data, $id) {
		$this->db->where('id', $id)
				 ->update('purchase_order', $data);

		return $this->db->affected_rows();
	}
	function totalapprovedpothismonth()
	{
		$ttotvalue[]=0;
		$start=date('Y-m-01')." 00:00:00";
		$end=date('Y-m-t')." 23:59:59";
		$resteye=$this->db->select('price,qty')->from('purchase_order')->where('approved','1')->where('approvedOn BETWEEN "'.$start. '" and "'.$end.'"')->get();
		if($resteye->num_rows()>0)
		{

			foreach($resteye->result() as $resteye1)
			{

				$ttotvalue[]=$resteye1->price*$resteye1->qty;

			}

		}
		

			return round(array_sum($ttotvalue));



	}




	function jobcardprvspo($type)
{

$this->db->select('a.id')
					  ->from('purchase_request a')
					  ->where('a.approvalstatus','0')
					  ->where('a.closed','0');
					if($type<>0)
					{
						$this->db->where('a.source',$type);
					} 

					$query=$this->db->get();

					return $query->num_rows();

	}

	function expcteddateofdelivery($poid,$prno)
	{
		$podelidate=$this->checkdeliveryday($prno);
		$restyeue=$this->db->select('followup_status,expected_date')->from('vendor_followup')->where('poid',$poid)->order_by('id','DESC')->limit(1)->get();
		if($restyeue->num_rows()>0)
		{
			foreach($restyeue->result() as $restyeue1);

			$expecteddate=$restyeue1->expected_date;
			if($expecteddate<>'0000-00-00')
			{
				return date('d-M-Y',strtotime($expecteddate));

			}else
			{
				$podelidate=$this->checkdeliveryday($prno);
				return date('d-M-Y',strtotime($podelidate));

			}



		}else
		{

			return date('d-M-Y',strtotime($podelidate));
		}



	}

	function vendorwisevalueandcountfordelivery($vend)
	{
		$idata=array();
		
		$idata12=array();
		$rest=$this->db->select('a.id,a.qty,a.price')->from('purchase_order a')->where('a.approved','1')->where('a.gateentrycomplete','0')->where('a.completed','0')->where('a.vendor',$vend)->get();
		if($rest->num_rows()>0)
		{
			foreach($rest->result() as $idata1)
			{
				$idata[]=$idata1->qty*$idata1->price;
			}

		}else
		{
			$idata[]=0;
		}

		$idata12[]=array_sum($idata);
		$idata12[]=$rest->num_rows();


		return $idata12;

			

	}
	

function gettatformis($mrndate)
	{
	

		$officestarttime="9:30";
$officeendtime="18:00";	

		/** Get TAT FROM AND SLAB **/			
			$totaldaysslave = 4;
			$daysslab=4;
			$day=0;
			$plannedon=$mrndate;
				

				$previouscomtime=$plannedon;
				$newtime=date('H:i:s',strtotime($previouscomtime));
				$TATDATE1=date('d-M-Y',strtotime($previouscomtime));
				if($day==1)
				{
				$TATDATE=date('Y-m-d',strtotime($TATDATE1."+".$daysslab." days"));
				}else{

				$todaysdate=date('Y-m-d');
				$officestarttime=date('Y-m-d',strtotime($todaysdate."+1 days"))." "."9:30";
				$officestime="9:30";
				$officeendtime=date('Y-m-d')." 18:00";
				$officeetime="18:00";

				$previoussteptime=date('H:i',strtotime($previouscomtime));
				$tattime = date('Y-m-d H:i', strtotime($previoussteptime.'+'.$daysslab.' hour'));
				
				if(strtotime($officeendtime)<strtotime($tattime)) 
				{
				/** TAT IS GREATER **/
				$timediff=round(abs(strtotime($tattime)-strtotime($officeendtime))/60,2);
				$TATDATE=date('Y-m-d',strtotime($TATDATE1."+1 days"));
				$newtime=date('g:i A',strtotime('+'.$timediff.' minutes',strtotime($officestarttime)));
				}else
				{
				/** NOT GREATER **/
				$TATDATE=$TATDATE1;
				$newtime=date('g:i A',strtotime($tattime));
				}


				}
					
							
	
			
			$TATDATEFORHOLIDAYCHECK=$TATDATE;
			$timestampforcheck=date('Y-m-d',strtotime($previouscomtime));
			$date_from=strtotime($timestampforcheck);
			$date_to=strtotime($TATDATEFORHOLIDAYCHECK);
			$betweendates=array();
			for ($g=$date_from; $g<=$date_to; $g+=86400) {  
			$betweendates[]= date("Y-m-d", $g);  
			} 

					
			if(count($betweendates)>0)
			{

			$alldates = "'" . implode ( "', '", $betweendates ) . "'";

			$ifholiday=$this->db->select('holiday_id')->from('prestogroup_holidays')->where_in('holiday_date',$alldates,false)->get();
			if($ifholiday->num_rows()>0)
			{
			$TATDATEFORHOLIDAYCHECK=date('Y-m-d', strtotime($TATDATEFORHOLIDAYCHECK."+".$ifholiday->num_rows()." days"));
			}else{  
			$TATDATEFORHOLIDAYCHECK=$TATDATEFORHOLIDAYCHECK;
			}
			}else
			{
			$TATDATEFORHOLIDAYCHECK=$TATDATEFORHOLIDAYCHECK;
			}
			
			$convertdate=date('Y-m-d',strtotime($TATDATEFORHOLIDAYCHECK));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$convertdate)->get();
				$chhutti=array();
				if($query->num_rows()>0){

				foreach($query->result() as $holidays);
				$nextdate= date('Y-m-d', strtotime($holidays->holiday_date."+1 days"));
				$query = $this->db->select('holiday_date')->from('prestogroup_holidays')->where('holiday_date',$nextdate)->get();
				if($query->num_rows()>0){

				foreach($query->result() as $afterholiday);
				$aftrholidaydate = $afterholiday->holiday_date;
				$TATDATE= date('d-M-Y', strtotime($aftrholidaydate."+1 days"));
				return $TATDATE." ".$newtime;

				}else {
				$TATDATE= date('d-M-Y', strtotime($nextdate));
				return $TATDATE." ".$newtime;
				}
				}else{
				$TATDATE=date('d-M-Y', strtotime($TATDATEFORHOLIDAYCHECK));
				return $TATDATE." ".$newtime;
				}
		
			
		}


			function storereciept()
			{

			$rest=$this->db->select('a.record_id')->from('mrn_history a')->where('a.storereciept','0')->where('a.accept_qty>','0')->group_by('a.id')->order_by('a.id','DESC')->get();
			return $rest->num_rows();
			}


			function checkforconversionapplicable($itemid)
			{
				$conweight=0;
				$resty=$this->db->select('conversion_unit,conversion_weight')->from('machine_parts_with_picture')->where('id',$itemid)->get();
				if($resty->num_rows()>0)
				{
					foreach($resty->result() as $resty1);
					if($resty1->conversion_unit>0)
					{
						$conweight=$resty1->conversion_weight;
					}
				}

				return $conweight;

			}

			function getissuedcount($item,$sdate,$edate)
			{
				$firstdate=date('Y-m-d',strtotime($sdate))." 00:00:00";
	$lastdate=date('Y-m-d',strtotime($edate))." 23:59:59";
			$Restey=$this->db->select('sum(stock) as issuedqty')->from('issuestocktousers a')->where('a.issuedOn BETWEEN "'.$firstdate. '" and "'.$lastdate.'"');
			
			$this->db->where('a.itemid',$item);
			
			$Restey=$this->db->get();

			if($Restey->num_rows()>0)
			{
				foreach($Restey->result() as $Restey1);
				
				return $Restey1->issuedqty;
			}else
			{
				return 0;
			}

			}


			function getpototalforunapproved($pono)
{
	$poval=0;
	$povalue[]=0;
	$resyteyur=$this->db->select('itemid,price,qty,vendor,potype')->from('purchase_order')->where('pono',$pono)->where('approved','0')->get();
	if($resyteyur->num_rows()>0)
	{
		foreach($resyteyur->result() as $resyteyur1123);
		$gst=$this->checkisupplierhasgst($resyteyur1123->vendor);
		foreach($resyteyur->result() as $resyteyur1)
		{
			/** ITEM MUL QTY **/
			
				$converted=$this->checkforconversionapplicable($resyteyur1->itemid);
				if($converted>0)
				{
					$finalqty=$converted*$resyteyur1->qty;

				}else
				{
					$finalqty=$resyteyur1->qty;	
				}
				$gstnva=$this->checkforitemgst($resyteyur1->itemid,$resyteyur1->potype);
				if($gstnva>0 && $gst>0)
				{
				$gww=$gstnva/100;
				$singleqtygst=$resyteyur1->price*$gww;
				$finalgst=$singleqtygst*$finalqty;
				$itemval=$resyteyur1->price*$finalqty+$finalgst;

				}else{

				$gstval=0;
				$itemval=$resyteyur1->price*$finalqty;
				}


				$povalue[]=$itemval;
			
			
		}
		
		
		
		
	}
	
	return array_sum($povalue);
	
	
}

     	function checkForDashboardModules($moduleid) {
	    //echo $moduleid; exit;
	    $query = $this->db->select('moduleid, userid, submoduleid')
	                      ->from('dashboard_access')
	                      ->where('userid', $_SESSION['logged_in']['user_id'])
	                      ->where('moduleid', $moduleid)
	                      ->get();
	 return $query->num_rows();
	}
	
	function checkForDashboardSubmodules($submoduleid) {
	    $query = $this->db->select('moduleid, userid, submoduleid')
	                      ->from('dashboard_access')
	                      ->where('userid', $_SESSION['logged_in']['user_id'])
	                      ->where('submoduleid', $submoduleid)
	                      ->get();
        return $query->num_rows();
        
	}
		
		
		
		
    function getEditModule($id) {
        $sql = $this->db->select('*')
                        ->from('system_reports')
                        ->where('id', $id)
                        ->get();
            if($sql->num_rows()>0) {
                return $sql->result();
                }
          }
  
    function update_module($id, $data) {
        $this->db->where('id', $id)
                 ->update('system_reports', $data);
        return $this->db->affected_rows();
    }

    function getMachines() {
    	$res = '';
    	$sql = $this->db->select('id, machine_name')
                        ->from('machine')
                        ->where('status', 1)
                        ->get();

        if ($sql->num_rows() > 0) {
        	$res = $sql->result();
        }

        return $res;
    }

    function getSelectedLocations($raw_mat_id) {
    	$location = array();
    	$sql = $this->db->select('location')
                        ->from('raw_material_locations')
                        ->where('raw_mat_id', $raw_mat_id)
                        ->get();

        if ($sql->num_rows() > 0) {
        	foreach($sql->result() as $row) {
        		$location[] = $row->location;
        	}
        }

        return $location;
    }

     function getMouldVariants($fincode) {
    	$res = '';
    	$sql = $this->db->select('id, name, mould, unit, per_pcs_weight, bag_weight, per_pcs_bag, per_shot_pcs, machine')
                        ->from('semi_fg_bom_mould_wise_variant')
                        ->where('code', $fincode)
                        ->get();

        if ($sql->num_rows() > 0) {
        	$res = $sql->result();
        }

        return $res;
    }
}

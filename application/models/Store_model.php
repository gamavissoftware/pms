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
	$suplo=$this->db->select('b.name,b.id')->from('vendors_price_view a')->join('vendors_view b','a.vendorid=b.id')->where('a.itemid',$itemid)->where('masterid',$masterid)->get();
	
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


function getmachineitemfincode($itemid)
{
	$iname='';
	$resty=$this->db->select('fincode')->from('machine_parts_with_picture')->where('id',$itemid)->get();
	if($resty->num_rows()>0)
	{
		foreach($resty->result() as $resty1);
		
		$iname=$resty1->fincode;
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
	$resty=$this->db->select('conversion_unit,conversion_weight,part,fincode,hsn,specification,size_in_mm,unit,material')->from('machine_parts_with_picture_view')->where('id',$itemid)->get();
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
				$restyui=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->order_by('first_name','ASC')->get();
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
		
		$resty=$this->db->select('first_name,last_name')->from('system_users_view')->where('user_id',$userid)->get();
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
    
     
		$this->db->select('a.id,a.part,a.unit,a.current_stock,a.min_stock, a.specification, a.makes, a.size_in_mm, a.material, a.raw_bop, a.fincode, a.qty, a.picture, d.rack_location, p.category')->from('machine_parts_with_picture  a')->join('store_rack_location d','a.location_id=d.id','left')->join('presto_machine_part_category p','a.category_id=p.id','left')->where('a.status','1');
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
                $restsys=$this->db->select('a.id')->from('machine_parts_with_picture_view  a')->where('a.current_stock<a.min_stock')->where('a.critical','1')->get();
                return $restsys->num_rows();
               
            } 


            function pendingmrn()
            {
                	$rest=$this->db->select('a.*')->from('mrn_view a')->where('a.mrndone','0')->group_by('a.gateentryno')->order_by('a.addedOn','DESC')->get();
                	return $rest->num_rows();
                
            }
            
            function rgpchallannotclosedcount()
            {
                 	$cha=$this->db->select('a.id')->from('outwardchallan_view a')->where('a.open','0')->group_by('a.id')->order_by('a.id','DESC')->get(); 
                 	
                 	return $cha->num_rows();
                
                
            }
            
            function bompr()
            {
                $rest=$this->db->select('a.jobcardid')->from('purchase_request_view a')->where('a.approvalstatus','0')->where('a.source','1')->group_by('a.prno')->get();
                
                return $rest->num_rows();
                
            }
            
            
             function imspr()
            {
                $rest=$this->db->select('a.jobcardid')->from('purchase_request_view a')->where('a.approvalstatus','0')->where('a.source','3')->group_by('a.prno')->get();
                
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
	$suplo=$this->db->select('a.id')->from('vendors_price_view a')->where('a.itemid',$itemid)->where('a.green_supplier','1')->get();
	
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
	$resyteyur=$this->db->select('itemid,price,qty,vendor,potype')->from('purchase_order_view')->where('pono',$pono)->get();
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
				$itemval=$resyteyur1->price*$finalqty;

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
	$Resteyre=$this->db->select('gst')->from('vendors_view')->where('id',$vendor)->get();
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
		$Restrur=$this->db->select('gst')->from('machine_parts_with_picture_view')->where('id',$itemid)->get();
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
    $resteur=$this->db->select('sourceid')->from('purchase_request_view')->where('prno',$prno)->get();
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
	
	
    $resteur=$this->db->select('script')->from('poinstructions_view')->get();
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
	$resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn')->from('purchase_order_view')->where('prno',$prno)->get();
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
    $restey=$this->db->select('job_card_no')->from('order_instruments_view')->where('id',$jobcard)->get();
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
	$resty=$this->db->select('a.fincode,a.size_in_mm,a.material,a.specification,b.rack_location,a.part,a.unit')->from('machine_parts_with_picture_view a')->join('store_rack_location_view b','a.location_id=b.id')->where('a.id',$itemid)->get();
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
    
    $restye=$this->db->select('instruments_name')->from('presto_instruments_view')->where('id',$id)->get();
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
	    $resteyue=$this->db->select('pono')->from('purchase_order_view')->where('prno',$prno)->get();
	    if($resteyue->num_rows()>0)
	    {
	        foreach($resteyue->result() as $resteyue1);
	        
	        $Restye=$this->db->select('expected_date')->from('vendor_followup_view')->where('itemid',$itemid)->where('po_no',$resteyue1->pono)->where('expected_date !=','0000-00-00')->order_by('id','ASC')->limit(1)->get();
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
	$restyey=$this->db->select('id')->from('machine_parts_with_picture_view')->where('gst','0.00')->get();
	return $restyey->num_rows();
	
} 


function withoutvendor()
{
		$restyey=$this->db->select('b.itemid')->from('machine_parts_with_picture_view a')->join('vendors_price b','a.id=b.itemid','left')->where('b.itemid',NULL)->get();
		return $restyey->num_rows();
		
		
		
	
}


function withoutfincode()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture_view')->where('fincode',NULL)->or_where('fincode','')->get();
		return $restyey->num_rows();
	

	
}



function withoutracklocation()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture_view')->where('location_id','0')->or_where('location_id',NULL)->get();
		return $restyey->num_rows();
	

	
}


function withoutimage()
{
		$restyey=$this->db->select('id')->from('machine_parts_with_picture_view')->where('picture',NULL)->or_where('picture','')->get();
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

	$rest=$this->db->select('a.masterid')->from('purchase_request_view a')->join('machine_parts_with_picture_view d','d.id=a.masterid')->where('a.approvalstatus','0')->where('a.closed','0')->order_by('d.part','ASC')->get();

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
	$resteyur=$this->db->select('id,approved,completed,gateentrycomplete,pono,vendor,addedOn,prno')->from('purchase_order_view')->where('prno',$prno)->where('itemid',$itemid)->get();
	if($resteyur->num_rows()>0)
	{
		foreach($resteyur->result() as $resteyur1);
		
		
		
		if($resteyur1->approved=='1')
		{
			$prstatus= "PO APPROVED";
			
			$estey=$this->db->select('qcstatus')->from('mrn_view')->where('pono',$resteyur1->pono)->where('itemid',$itemid)->get();
			if($estey->num_rows()>0)
			{
			foreach($estey->result() as $estey1);
			
			if($estey1->qcstatus=='1')
			{
			$prstatus='COMPLETED';
			}else
			{
			$prstatus='QC PENDING';
			}
			
			
			}else
			{
			
			$prstatus= "PENDING DELIVERY";
			
			$purc=$this->db->select('closed')->from('purchase_request_view')->where('prno',$resteyur1->prno)->where('itemid',$itemid)->get();
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
		$purc=$this->db->select('closed')->from('purchase_request_view')->where('prno',$resteyur1->prno)->get();
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
	
	$resty=$this->db->select('sum(recqty) as inwardedqty')->from('mrn_view')->where('pono',$po)->where('itemid',$itemid)->where('poid',$poid)->get();
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
$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";
$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approvedOn BETWEEN "'.$start. '" and "'.$end.'"')->where('approved',1)->where('source','1')->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{

$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
	if($conweight['conversion_unit']!=0)
	{
		//echo $rsteye1->itemid."|".$conweight['conversion_weight']; exit;
		$finalqty=$rsteye1->qty*$conweight['conversion_weight'];
		

		
	}

}

$jbcard[]=$rsteye1->price*$finalqty;
}

}



return array_sum($jbcard);

}

function indentunapprovedpo()
{
$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";

$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approvedOn BETWEEN "'.$start. '" and "'.$end.'"')->where('approved',1)->where('source',2)->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{

$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
	if($conweight['conversion_unit']!=0)
	{
		
		$finalqty=$rsteye1->qty*$conweight['conversion_weight'];
		

		
	}

}

$jbcard[]=$rsteye1->price*$finalqty;
}

}


//echo "<pre>"; print_r($jbcard); exit;
return array_sum($jbcard);

}
  



function imsunapprovedpo()
{
	$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";

$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approvedOn BETWEEN "'.$start. '" and "'.$end.'"')->where('approved',1)->where_in('source','3,4',false)->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{
	$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
	if($conweight['conversion_unit']!=0)
	{
		//echo $rsteye1->itemid."|".$conweight['conversion_weight']; exit;
		$finalqty=$rsteye1->qty*$conweight['conversion_weight'];
		

		
	}

}

$jbcard[]=$rsteye1->price*$finalqty;
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

	$rest=$this->db->select('a.masterid')->from('purchase_request_view a')->join('machine_parts_with_picture_view d','d.id=a.masterid')->where('a.approvalstatus','0')->where('a.closed','1')->order_by('d.part','ASC')->get();

	return $rest->num_rows();


}	


function getpartscount()
{

	$rest=$this->db->select('id')
				   ->from('machine_parts_with_picture_view')
				   ->where('flag','0')
				   ->get();

	return $rest->num_rows();


}


function updateQty($data, $id) {


		$this->db->where('id', $id)
				 ->update('machine_parts_with_picture', $data);
                
		return $this->db->affected_rows();
	}
  
  
  function getallpendingitemsfrompr($prno)
  {
  $as=array();
 
 $htmldata='';
	$rest=$this->db->select('d.part as machine_part,d.fincode,d.specification,d.size_in_mm,a.itemid,a.unit,a.qty,a.jobcardid')->from('purchase_request_view a')->join('machine_parts_with_picture_view d','d.id=a.masterid')->where('a.prno',$prno)->where('a.closed','0')->order_by('d.part','ASC')->get();
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
		$resteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approved','1')->where('approvedOn BETWEEN "'.$start. '" and "'.$end.'"')->where('negotiation',1)->get();
		if($resteye->num_rows()>0)
		{

			foreach($resteye->result() as $resteye1)
			{

				$finalqty=$resteye1->qty;
				$conweight=$this->checkforconvertedweight($resteye1->itemid);
				if(count($conweight)>0)
				{
				if($conweight['conversion_unit']!=0)
				{
				//echo $rsteye1->itemid."|".$conweight['conversion_weight']; exit;
				$finalqty=$resteye1->qty*$conweight['conversion_weight'];

				}

				}

				$ttotvalue[]=$resteye1->price*$finalqty;

			}

		}
		

			return round(array_sum($ttotvalue));



	}




	function jobcardprvspo($type)
{

$this->db->select('a.id')
					  ->from('purchase_request_view a')
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
		$restyeue=$this->db->select('followup_status,expected_date')->from('vendor_followup_view')->where('poid',$poid)->order_by('id','DESC')->limit(1)->get();
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
				$scheduler_data=array();
				$scheduler_data[]=0;
				$rest=$this->db->select('a.accept_qty,a.record_id')->from('mrn_history_view a')->join('mrn_view c','a.record_id=c.id')->where('a.storereciept','0')->group_by('a.id')->order_by('a.id','DESC')->get();
				if($rest->num_rows()>0)
				{
				$i=1;
				foreach($rest->result() as $restyui1)
				{
				if(floatval($restyui1->accept_qty)>0)
				{
				$scheduler_data[] = 1;
				$i++;
				}
				}
				}

				return array_sum($scheduler_data);

			}


			function checkforconversionapplicable($itemid)
			{
				$conweight=0;
				$resty=$this->db->select('conversion_unit,conversion_weight')->from('machine_parts_with_picture_view')->where('id',$itemid)->get();
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
				//$gww=$gstnva/100;
				//$singleqtygst=$resyteyur1->price*$gww;
				//$finalgst=$singleqtygst*$finalqty;
				$itemval=$resyteyur1->price*$finalqty;

				}else{

				$gstval=0;
				$itemval=$resyteyur1->price*$finalqty;
				}


				$povalue[]=$itemval;
			
			
		}
		
		
		
		
	}
	
	return array_sum($povalue);
	
	
}

function getjobworkitemdetail($itemid)
{
$data=array();
$resteyu=$this->db->select('item,fincode,hsname,hscode,igst,cgst,sgst')->from('jobworkitems')->where('id',$itemid)->get();
if($resteyu->num_rows()>0)
{
	foreach($resteyu->result() as $resteyu1);
	$data['name']=$resteyu1->item;
	$data['fincode']=$resteyu1->fincode;
	$data['hsname']=$resteyu1->hsname;
	$data['hscode']=$resteyu1->hscode;
	$data['igst']=$resteyu1->igst;
	$data['cgst']=$resteyu1->cgst;
	$data['sgst']=$resteyu1->sgst;
}

return $data;
}
		


		function getbomgst($id)
{
	$ins='';
	$query = $this->db->select('gst')->from('machine_parts_with_picture')->where('id',$id)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->gst;
			
		}
		
		return $ins;
 
}
function getbomfincode($id)
{
	$ins='';
	$query = $this->db->select('fincode')->from('machine_parts_with_picture')->where('id',$id)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $instruments);
		$ins=$instruments->fincode;
			
		}
		
		return $ins;

}

	function getautofincode()
	{

		$Resteure=$this->db->select('fincode')->from('machine_parts_with_picture')->where('mitrcodeflag','1')->order_by('id','DESC')->limit(1)->get();
		if($Resteure->num_rows()>0)
		{
		foreach($Resteure->result() as $Resteure1);
		$previouscode=$Resteure1->fincode;
		$num=$previouscode+1;
		}else
		{
		$num="1000";
		}

		return $num;


	}


	function getissuestockdata($items)
	{
			$a=array();

			$rest=$this->db->select('a.itemid,a.jobcardid,a.stock,b.part,b.specification,b.fincode,b.size_in_mm,b.unit,c.shortname')->from('issuestocktousers a')->join('machine_parts_with_picture b','a.itemid=b.id')->join('units c','b.unit=c.id')->where('a.id',$items)->get();
			if($rest->num_rows()>0)
			{
			foreach($rest->result() as $restyu);

			$a['name']=$restyu->part;
			$a['id']=$restyu->itemid;
			$a['jobcard']=$restyu->jobcardid;
			$a['stock']=$restyu->stock;
			$a['specification']=$restyu->specification;
			$a['fincode']=$restyu->fincode;
			$a['size_in_mm']=$restyu->size_in_mm;
			$a['unit']=$restyu->shortname;
			$a['rate']=$this->getchallanvalue($restyu->itemid);

			}

			return $a;

	}


function getchallanvalue($id)
{
		/** GET DATA FROM LAST PO **/
		$query = $this->db->select('price')->from('purchase_order')->where('itemid',$id)->where('source','1')->order_by('id','DESC')->limit(1)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $row);
		return  number_format((float)$row->price, 2, '.', '');
		}else{

		$query =$this->db->select('max(a.price) as maxprice')->from('vendors_price a')->where('itemid',$id)->get();
		if($query->num_rows()>0)
		{
		foreach($query->result() as $row);
		if($row->maxprice<>NULL)
		{
		return  number_format((float)$row->maxprice, 2, '.', '');
		}else{
		return 0;
		}

		}else{

		return 0;
		}


		}

}


function moneyFormatIndia($num) {
    // $explrestunits = "" ;
    // if(strlen($num)>3) {
    //     $lastthree = substr($num, strlen($num)-3, strlen($num));
    //     $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
    //     $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
    //     $expunit = str_split($restunits, 2);
    //     for($i=0; $i<sizeof($expunit); $i++) {
    //         // creates each of the 2's group and adds a comma to the end
    //         if($i==0) {
    //             $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
    //         } else {
    //             $explrestunits .= $expunit[$i].",";
    //         }
    //     }
    //     $thecash = $explrestunits.$lastthree;
    // } else {
    //     $thecash = $num;
    // }
    // return $thecash; // writes the final format where $currency is the currency symbol.

     $num = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);

    return $num;
}


function checkifmrndone($poid)
	{
		$qty1=0;
		$res=$this->db->select('SUM(recqty) as totalqty')->from('mrn')->where('poid',$poid)->get();
		if($res->num_rows() >0)
		{
			foreach($res->result() as $row);
			$tqty=$row->totalqty;
			if($tqty !='')
			{
				$qty1=$tqty;
			}
		}

		return $qty1;
	}

function getpendingitemcounaftermrn($itemid)
{
	$data=array();
	$data[]=0;
$rest=$this->db->select('a.qty,a.id')->from('purchase_order_view a')->where('a.completed','0')->where('a.itemid',$itemid)->get();
if($rest->num_rows()>0)
{
foreach($rest->result() as $restqq)
{
	$balqty=$this->checkifmrndone($restqq->id);

	$finalqty=$restqq->qty-$balqty;
	$data[]=$finalqty;

}
return array_sum($data);
}
return '';



}



	function calculateFiscalYearForDate($month)
{
if($month > 4)
{
$y = date('Y');
$pt = date('Y', strtotime('+1 year'));
$fy = $y."-04-01".":".$pt."-03-31";
}
else
{
$y = date('Y', strtotime('-1 year'));
$pt = date('Y');
$fy = $y."-04-01".":".$pt."-03-31";
}
return $fy;
}


function jobcardunapprovedpovalue()
{
$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";
$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approved',0)->where('source','1')->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{
$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
	if($conweight['conversion_unit']!=0)
	{
		//echo $rsteye1->itemid."|".$conweight['conversion_weight']; exit;
		$finalqty=$rsteye1->qty*$conweight['conversion_weight'];
		

		
	}

}

$jbcard[]=$rsteye1->price*$finalqty;
}

}


return array_sum($jbcard);

}



function indentunapprovedpovalue()
{
	$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";

$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where('approved',0)->where('source',2)->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{
	$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
	if($conweight['conversion_unit']!=0)
	{
	
		$finalqty=$rsteye1->qty*$conweight['conversion_weight'];



		
	}

}

$jbcard[]=$rsteye1->price*$finalqty;
}

}


//echo "<pre>"; print_r($jbcard);
return array_sum($jbcard);

}
  


  function imsunapprovedpovalue()
{
	$start=date('Y-m-d')." 00:00:00";
$end=date('Y-m-d')." 23:59:59";

$jbcard[]=0;
$rsteye=$this->db->select('itemid,price,qty')->from('purchase_order_view')->where_in('source','3,4',false)->where('approved',0)->where('negotiation',1)->get();
if($rsteye->num_rows()>0)
{
foreach($rsteye->result() as $rsteye1)
{
$finalqty=$rsteye1->qty;
$conweight=$this->checkforconvertedweight($rsteye1->itemid);
if(count($conweight)>0)
{
if($conweight['conversion_unit']!=0)
{
//echo $rsteye1->itemid."|".$conweight['conversion_weight']; exit;
$finalqty=$rsteye1->qty*$conweight['conversion_weight'];



}

}

$jbcard[]=$rsteye1->price*$finalqty;
}

}


return array_sum($jbcard);

}

function pendingprcount($source)
{

	$rest=$this->db->select('a.id')->from('purchase_request_view a')->where('a.approvalstatus','0')->where('a.closed','0')->where('a.source',$source)->limit(50)->get();

	return $rest->num_rows();


}

function pendingpofordelivery($source)
{
	$rest=$this->db->select('a.id')->from('purchase_order a')->where('a.approved','1')->where('a.close_by','0')->where('a.completed','0')->where('a.source',$source)->group_by('a.pono')->get();

	return $rest->num_rows();
}

function checkforconvertedweight($itemid)
{
	$data=array();
	$resteyuri=$this->db->select('conversion_unit,conversion_weight')->from('machine_parts_with_picture_view')->where('id',$itemid)->get();
	if($resteyuri->num_rows()>0)
	{
		foreach($resteyuri->result() as $frow);

		$data['conversion_unit']=$frow->conversion_unit;
		$data['conversion_weight']=$frow->conversion_weight;

	}

	return $data;

}

		public function pendingorderamountold28Jan2022()
		{
			$pendingamt=array();
			$pendingamt[]=0;
			$this->db->select('a.order_id,a.finalprice,b.export')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->where('a.complete','0')->where('b.closeorder',0)->where('b.selforder','0')->where('b.testronix',0);

			
				
				$order=$this->db->get();
				if($order->num_rows()>0)
				{
				$t=1;
				foreach($order->result() as $order1)
				{
					if($order1->export==0)
					{
					$pendingamt[]=$order1->finalprice;
					}else
					{
						$pendingamt[]=$order1->finalprice*75;
					}
				
				}
				
				}
				$totalpenamt=array_sum($pendingamt);

			

				$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',0)->where('a.movetodispatch','0')->where('a.selforder','0')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked',0)->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$read[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$readypendingamt[]=$completedjobcard11->finalprice;
											}else
											{
											$readypendingamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}  


				$totalreadyamt=array_sum($readypendingamt);

				$dispatchamt=array();
				$dispatchamt[]=0;
				// $this->db->select('a.order_id,a.finalprice,b.export')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->where('a.complete','1')->where('b.testronix',0)->where('a.packed','1')->where('b.closeorder',0)->where('b.selforder','0')->where('b.movetodispatch','1')->where('a.finalpacked','0');
				
				// $dispatchorder=$this->db->get();
				// if($dispatchorder->num_rows()>0)
				// {
				// $t=1;
				// foreach($dispatchorder->result() as $order3)
				// {
				// 	if($order3->export==0)
				// 	{
				// 	$dispatchamt[]=$order3->finalprice;
				// 	}else
				// 	{
				// 		$dispatchamt[]=$order3->finalprice*75;
				// 	}
				
				// }
				
				// }



				$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',0)->where('a.movetodispatch','1')->where('a.selforder','0')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$read[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$dispatchamt[]=$completedjobcard11->finalprice;
											}else
											{
											$dispatchamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}  



				$totaldispatchamt=array_sum($dispatchamt);

				$salesforceorderamt=array();
				$salesforceorderamt[]=0;
				$saleforce=$this->db->select('a.order_id,b.finalprice,a.export')->from('salesforce_orders a')->join('salesforce_order_instruments b','a.order_id=b.order_id')->where('order_status','1')->get();
				if($saleforce->num_rows() >0)
				{
				foreach($saleforce->result() as $ordersales)
				{
					$res=$this->db->select('id')->from('salesforceorders_history')->where('order_id',$ordersales->order_id)->where('result','APPROVED')->get();
				if($res->num_rows() > 0){

					if($ordersales->export==0)
					{
					$salesforceorderamt[]=$ordersales->finalprice;
					}else
					{
						$salesforceorderamt[]=$ordersales->finalprice*75;
					}
				}
				}
				}
				$totalsalesforceamt=array_sum($salesforceorderamt);

				return round($totalpenamt+$totalreadyamt+$totaldispatchamt+$totalsalesforceamt);

		}

		public function orderstrattoend(){
		$order_data = array();
		$order_data[]=0;			
		$restye=$this->db->select('a.order_id')->from('prestogroup_orders_view a')->where('a.closeorder',0)->where('a.selforder',0)->get();
		$tot=$restye->num_rows();
		return $tot;
		
		}

		public function pendingorderamount()
		{

			$pendingamt=array();
			$pendingamt[]=0;

			$orderdata=array();

			$pendcou=$this->db->select('a.order_id,b.sforder,a.item_id,b.export,b.internal_order_no')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->join('order_planning_view c','a.id=c.jobcard_id')->where('a.complete','0')->where('b.testronix',0)->where('b.closeorder',0)->where('b.selforder','0')->group_by('a.order_id')->get();
			 
$pendorder=$pendcou->num_rows();
		
			 $pendcou11=$this->db->select('a.order_id,b.sforder,a.item_id,b.export,a.job_card_no')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->join('order_planning_view c','a.id=c.jobcard_id')->where('a.complete','0')->where('b.testronix',0)->where('b.closeorder',0)->where('b.selforder','0')->get();
			

			


if($pendorder>0)
{
	foreach($pendcou11->result() as $pendcou1)
	{
			$this->db->select('a.order_id,a.finalprice')->from('salesforce_order_instruments a')->where('a.order_id',$pendcou1->sforder)->where('a.item_id',$pendcou1->item_id);
				
				$order=$this->db->get();
				
				if($order->num_rows()>0)
				{
				$t=1;
				foreach($order->result() as $order1)
				{
				
					if($pendcou1->export==0){
					$pendingamt[]=$order1->finalprice;
					}else
					{
					 $pendingamt[]=$order1->finalprice*75;
					}
				
				}
				
				}


	}

}


/** Unplanned Amount **/
$unp=array();
$unp[]=0;

$rest=$this->db->query("SELECT a.id,a.job_card_no,a.item_id,b.sforder,b.export from order_instruments_view a JOIN prestogroup_orders_view b ON a.order_id=b.order_id where a.id not in (select jobcard_id from order_planning_view) AND b.testronix=0 AND b.closeorder=0 AND b.selforder=0 AND b.order_status=1");
if($rest->num_rows()>0)
{
	foreach($rest->result() as $unpl)
	{
		$ressss=$this->db->select('finalprice')->from('salesforce_order_instruments_view')->where('order_id',$unpl->sforder)->where('item_id',$unpl->item_id)->get();
		if($ressss->num_rows()>0)
		{
			foreach($ressss->result() as $ressss1)
			{

				if($unpl->export==1)
				{
				$unp[]=$ressss1->finalprice*75;
				}else
				{
					$unp[]=$ressss1->finalprice;
				}


			}

		}


	}
}


$totalpenamt=round(array_sum($pendingamt)+array_sum($unp));

/** END PENDING **/


/** READY **/

$readypendingamt=array();
$readypendingamt[]=0;
$read=array();
$read[]=0;

			

		$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',0)->where('a.movetodispatch','0')->where('a.selforder','0')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$read[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$readypendingamt[]=$completedjobcard11->finalprice;
											}else
											{
											$readypendingamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}


				$totalreadyamt=round(array_sum($readypendingamt));
			


			/** DISPATCH **/

				$dispatchamt=array();
				$dispatchamt[]=0;
				$disorder1=array();
				$disorder1[]=0;

			$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',0)->where('a.movetodispatch','1')->where('a.selforder','0')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$disorder1[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$dispatchamt[]=$completedjobcard11->finalprice;
											}else
											{
											$dispatchamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}
				$totaldispatchamt=round(array_sum($dispatchamt));
				


			/** END **/


			/** SALES FORCE **/


				$salesforceorderamt=array();
				$salesforceorderamt[]=0;
				$salescount=0;



				$saleforce=$this->db->select('a.order_id,b.finalprice,a.export')->from('salesforce_orders a')->join('salesforce_order_instruments b','a.order_id=b.order_id')->where('order_status','1')->get();

				if($saleforce->num_rows() >0)
				{
					foreach($saleforce->result() as $ordersales)
					{
							$res=$this->db->select('id')->from('salesforceorders_history')->where('order_id',$ordersales->order_id)->where('result','APPROVED')->get();
						
						if($res->num_rows() > 0){

								if($ordersales->export==0){
								$salesforceorderamt[]=$ordersales->finalprice;
								}else{
								$salesforceorderamt[]=$ordersales->finalprice*75;
								}
						
						}
					}
				}
	
				$totalsalesforceamt=round(array_sum($salesforceorderamt));
		



			/** END **/


		//echo array_sum($pendingamt)."<br/>".array_sum($unp)."<br/>".array_sum($readypendingamt)."<br/>".array_sum($dispatchamt)."<br/>".array_sum($salesforceorderamt); exit;

			$finalamount=$totalpenamt+$totalreadyamt+$totaldispatchamt+$totalsalesforceamt;

			return $finalamount;


		}
		function getloginusers($userid)
			 {
				 $abc=array();
				$restyui=$this->db->select('user_id,first_name,last_name')->from('system_users')->where('user_status','1')->where('hide_profile','0')->where('user_id',$userid)->order_by('first_name','ASC')->get();
				if($restyui->num_rows()>0)
				{
					
					
					return $restyui->result();
					
				}else{
					 return $abc;
					
				}
				 
			 }

			 function get_finacial_year_range() {
    $year = date('Y');
    $month = date('m');
    if($month<4){
        $year = $year-1;
    }
    $start_date = date('Y-m-d',strtotime(($year).'-04-01'));
    $end_date = date('Y-m-d',strtotime(($year+1).'-03-31'));
    $response = array('start_date' => $start_date, 'end_date' => $end_date);
    return $response;
}
public function pendingorderamount_testronix()
		{

			$pendingamt=array();
			$pendingamt[]=0;

			$orderdata=array();

			$pendcou=$this->db->select('a.order_id,b.sforder,a.item_id,b.export,b.internal_order_no')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->join('order_planning_view c','a.id=c.jobcard_id')->where('a.complete','0')->where('b.testronix',1)->where('b.closeorder',0)->where('b.selforder','0')->where('b.order_type','SALE')->group_by('a.order_id')->get();
			 
$pendorder=$pendcou->num_rows();
		
			 $pendcou11=$this->db->select('a.order_id,b.sforder,a.item_id,b.export,a.job_card_no')->from('order_instruments_view a')->join('prestogroup_orders_view b','a.order_id=b.order_id')->join('order_planning_view c','a.id=c.jobcard_id')->where('a.complete','0')->where('b.testronix',1)->where('b.closeorder',0)->where('b.selforder','0')->where('b.order_type','SALE')->get();
			

			


if($pendorder>0)
{
	foreach($pendcou11->result() as $pendcou1)
	{
			$this->db->select('a.order_id,a.finalprice')->from('salesforce_order_instruments a')->where('a.order_id',$pendcou1->sforder)->where('a.item_id',$pendcou1->item_id);
				
				$order=$this->db->get();
				
				if($order->num_rows()>0)
				{
				$t=1;
				foreach($order->result() as $order1)
				{
				
					if($pendcou1->export==0){
					$pendingamt[]=$order1->finalprice;
					}else
					{
					 $pendingamt[]=$order1->finalprice*75;
					}
				
				}
				
				}


	}

}


/** Unplanned Amount **/
$unp=array();
$unp[]=0;

$rest=$this->db->query("SELECT a.id,a.job_card_no,a.item_id,b.sforder,b.export from order_instruments_view a JOIN prestogroup_orders_view b ON a.order_id=b.order_id where a.id not in (select jobcard_id from order_planning_view) AND b.testronix=1 AND b.closeorder=0 AND b.selforder=0 AND b.order_status=1 AND b.order_type='SALE'");
if($rest->num_rows()>0)
{
	foreach($rest->result() as $unpl)
	{
		$ressss=$this->db->select('finalprice')->from('salesforce_order_instruments_view')->where('order_id',$unpl->sforder)->where('item_id',$unpl->item_id)->get();
		if($ressss->num_rows()>0)
		{
			foreach($ressss->result() as $ressss1)
			{

				if($unpl->export==1)
				{
				$unp[]=$ressss1->finalprice*75;
				}else
				{
					$unp[]=$ressss1->finalprice;
				}


			}

		}


	}
}


$totalpenamt=round(array_sum($pendingamt)+array_sum($unp));

/** END PENDING **/


/** READY **/

$readypendingamt=array();
$readypendingamt[]=0;
$read=array();
$read[]=0;

			

		$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',1)->where('a.movetodispatch','0')->where('a.selforder','0')->where('a.order_type','SALE')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$read[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$readypendingamt[]=$completedjobcard11->finalprice;
											}else
											{
											$readypendingamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}


				$totalreadyamt=round(array_sum($readypendingamt));
			


			/** DISPATCH **/

				$dispatchamt=array();
				$dispatchamt[]=0;
				$disorder1=array();
				$disorder1[]=0;

			$this->db->select('a.order_id,a.export,a.sforder')->from('prestogroup_orders_view a')->where('a.order_status','1');
		$query = $this->db->where('a.closeorder','0')->where('a.testronix',1)->where('a.movetodispatch','1')->where('a.selforder','0')->order_by('a.order_id','desc')->get();
		$i=1;
		if($query->num_rows()>0)
		{
			foreach($query->result() as $row)
			{
				$completedjobcard=$this->db->select('id')->from('order_instruments')->where('order_id',$row->order_id)->where('complete','1')->where('packed','1')->where('finalpacked','0')->get();
				$completedjobcards=$completedjobcard->num_rows();

				$query1 = $this->db->select('a.order_id')->from('order_instruments a')->where('a.order_id',$row->order_id)->get();
				$alljobcard=$query1->num_rows();

				if($completedjobcards==$alljobcard)
					{
						if($alljobcard>0)
							{
								$disorder1[]=1;

								$completedjobcard1=$this->db->select('finalprice')->from('salesforce_order_instruments')->where('order_id',$row->sforder)->get();
								if($completedjobcard1->num_rows()>0)
								{
									foreach($completedjobcard1->result() as $completedjobcard11)
										{

											if($row->export==0){
											$dispatchamt[]=$completedjobcard11->finalprice;
											}else
											{
											$dispatchamt[]=$completedjobcard11->finalprice*75;
											}
										}

								}	


							}
					}


			}
		}
				$totaldispatchamt=round(array_sum($dispatchamt));
				


			/** END **/


			// /** SALES FORCE **/


			// 	$salesforceorderamt=array();
			// 	$salesforceorderamt[]=0;
			// 	$salescount=0;



			// 	$saleforce=$this->db->select('a.order_id,b.finalprice,a.export')->from('salesforce_orders a')->join('salesforce_order_instruments b','a.order_id=b.order_id')->where('order_status','1')->get();

			// 	if($saleforce->num_rows() >0)
			// 	{
			// 		foreach($saleforce->result() as $ordersales)
			// 		{
			// 				$res=$this->db->select('id')->from('salesforceorders_history')->where('order_id',$ordersales->order_id)->where('result','APPROVED')->get();
						
			// 			if($res->num_rows() > 0){

			// 					if($ordersales->export==0){
			// 					$salesforceorderamt[]=$ordersales->finalprice;
			// 					}else{
			// 					$salesforceorderamt[]=$ordersales->finalprice*75;
			// 					}
						
			// 			}
			// 		}
			// 	}
	
			// 	$totalsalesforceamt=round(array_sum($salesforceorderamt));
		



			// /** END **/


			$finalamount=$totalpenamt+$totalreadyamt+$totaldispatchamt;

			return $finalamount;


		}	

		function get_extra_detail($pon)
		{


			/** GET MRN NO. **/
											$mrnno=array();
											$mr='';
											$a=$this->db->select('gateentryno')->from('mrn')->where('pono',$pon)->get();
											
											if($a->num_rows()>0)
											{
												foreach($a->result() as $a1)
												{
												$mrnno[]=$a1->gateentryno;
												}

											}

											//echo "<pre>"; print_r($mrnno); exit;
											if(count($mrnno)>0)
											{
												$mr=implode(',',$mrnno);
											}
											/** END **/


											$mrnn_date='';
											$rt111=$this->db->select('max(mrndoneOn) as maxmrndate')->from('mrn_view')->where('pono',$pon)->where('mrndone',1)->get();
											if($rt111->num_rows()>0)
											{
												
												foreach($rt111->result() as $rt123);
										
											if($rt123->maxmrndate<>'')
											{
												$mrnn_date=date('d-M-Y',strtotime($rt123->maxmrndate));
											}else
											{
												$mrnn_date="NA";
											}

											}


											$store_date='';
											$rt=$this->db->select('max(storerecvon) as maxstoredate')->from('mrn_history_view')->where('po_no',$pon)->where('storereciept',1)->get();
											if($rt->num_rows()>0)
											{
												foreach($rt->result() as $rt1);
												if($rt1->maxstoredate<>'')
											{	
												$store_date=date('d-M-Y',strtotime($rt1->maxstoredate));
											}else
											{
												$store_date='NA';
											}

											}


											$vouvcher='';
											$rt=$this->db->select('max(account_accepted_on) as maxaccountdate')->from('mrn_view')->where('pono',$pon)->where('account_accepted',1)->get();
											if($rt->num_rows()>0)
											{
												foreach($rt->result() as $rt1);
												if($rt1->maxaccountdate<>'')
											{	
												$vouvcher=date('d-M-Y',strtotime($rt1->maxaccountdate));
											}else
											{
												$vouvcher="NA";
											}

											}

											return $mr."|".$mrnn_date."|".$store_date."|".$vouvcher;

		}	 

	function get_challan_count($type)
	{

    $this->db->select('a.id')->from('outwardchallan_view a')->join('system_users_view f','a.addedBy=f.user_id')->join('system_users_view g','a.supplier=g.user_id','left');
    if($type<>0)
   {
       $this->db->where('a.type',$type);
   
    }

	$this->db->where('a.open',0);
    $cha=$this->db->group_by('a.id')->order_by('a.id','DESC')->get();

    return $cha->num_rows();

	}


	function getpototal_new($pono,$stdate,$etdate)
{
	$poval=0;
	$povalue[]=0;
	$resyteyur=$this->db->select('itemid,price,qty,vendor,potype')->from('purchase_order_view')->where('pono',$pono)->where('approvedOn>=',$stdate)->where('approvedOn<=',$etdate)->get();
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
				$itemval=$resyteyur1->price*$finalqty;
				$itemval=$itemval+$finalgst;
				}else{

				$gstval=0;
				$itemval=$resyteyur1->price*$finalqty;
				}


				$povalue[]=$itemval;
			
			
		}
		
		
		
		
	}
	
	return array_sum($povalue);
	
	
}


function add_stock_store_reciept($potype,$itemid,$qty)
{

	if($potype==0)
			{
			$prevstockforitem=$this->getcurrentstock($itemid);
			}else
			{
			$prevstockforitem=$this->getcurrentstockforgeneralitem($itemid);
			}


	if($potype==0)
	{
	$currstock=$prevstockforitem;
	$newstock=$currstock+$qty;
	$datadb1=array('current_stock'=>$newstock);


	$this->db->where('id',$itemid);
	$this->db->update('machine_parts_with_picture',$datadb1);
	}else{
	$currstock=$prevstockforitem;
	$newstock=$currstock+$qty;
	$datadb1=array('qty'=>$newstock);
	$this->db->where('id',$itemid);
	$this->db->update('house_keeping_items',$datadb1);
	}



}


	function getcurrentstock($itemid)
{
		
	$qyer=$this->db->select('current_stock')->from('machine_parts_with_picture_view')->where('id',$itemid)->get();
	if($qyer->num_rows()>0)
	{
		
		foreach($qyer->result() as $qyer12);
		$currstock=$qyer12->current_stock;
		
		return $currstock;
		
	}else{
		
		echo "ITEM NOT FOUND";EXIT;
		
	}
	
	
		
		
}



		function getcurrentstockforgeneralitem($itemid)
{
		
		
	$qyer1234=$this->db->select('qty as current_stock')->from('house_keeping_items')->where('id',$itemid)->get();
	if($qyer1234->num_rows()>0)
	{
		
		foreach($qyer1234->result() as $qyer12);
		$currstock=$qyer12->current_stock;
		
		return $currstock;
		
	}else{
		
		echo "ITEM NOT FOUND";EXIT;
		
	}
	
	
		
		
}


function inward_rgp_stock($type,$iname,$qty,$oitemid)
{



	// if($oitemid==36353)
	// {
	// 	$rt="id1";

	// }else
	// {
	// 	$rt="id";
	// }
		if($type=='1' || $type=='2')
		{
	
		
			$curstock=$this->instrumentstock($iname);
			
			$news=$curstock+$qty;
			$upst=array('stock'=>$news);
			$this->db->where('id',$iname);
			$this->db->update('presto_instruments',$upst);
		}else{
			

			$curstock=$this->bomstock($iname);
			$news=$curstock+$qty;
			$upst=array('current_stock'=>$news);
			$this->db->where('id',$iname);
			$this->db->update('machine_parts_with_picture',$upst);
			
			// $upst=array('current_stock'=>$news);
			// $this->db->where('id',$iname);
			// $this->db->update('machine_parts_with_picture',$upst);
			
		}


}



function bomstock($id)
{
	
	$query = $this->db->select('a.current_stock')->from('machine_parts_with_picture a')->where('a.id',$id)->get();
	if($query->num_rows()>0)
			{
		    foreach($query->result() as $row);
			return $row->current_stock;
			}else{
			return 0;
			}
	
}


function instrumentstock($id)
{
	
	$query = $this->db->select('stock')->from('presto_instruments')->where('id',$id)->get();
			if($query->num_rows()>0)
			{
			foreach($query->result() as $instruments);
			$ins=$instruments->stock;
			return $ins;
			}else{
			return 0;
			}	
	
	
	
}

function insert_inward_items($newqty,$recv,$recvrmk,$debit,$id,$chalid)
{
	$data=array('recvqty'=>$newqty,'recvweight'=>$recv,'recvremarks'=>$recvrmk,'debit_note'=>$debit,'recvOn'=>date('Y-m-d H:i:s'),'recvBy'=>$_SESSION['logged_in']['user_id']);
		$this->db->where('id',$id);
		$this->db->where('challanid',$chalid);
		$this->db->update('outwardchallan_item',$data);
		return $this->db->affected_rows();
}

function insert_inward_itemsNew($newqty,$recv,$recvrmk,$debit,$id,$chalid,$addstock)
{
	$data=array('recvqty'=>$newqty,'recvweight'=>$recv,'recvremarks'=>$recvrmk,'debit_note'=>$debit,'recvOn'=>date('Y-m-d H:i:s'),'recvBy'=>$_SESSION['logged_in']['user_id'],'add_stock'=>$addstock);
		$this->db->where('id',$id);
		$this->db->where('challanid',$chalid);
		$this->db->update('outwardchallan_item',$data);
		return $this->db->affected_rows();
}

function getRGPNO($startdate,$enddate)
{

	$prnos=$this->db->select('challanno')->from('outwardchallan')->where('addedOn>=',$startdate)->where('addedOn<=',$enddate)->order_by('id','DESC')->limit(1)->get();

$num=$prnos->num_rows();

if($num==0)

{

$num1=1;

$num_padded = sprintf("%03d", $num1);

$code='PRERGP-'.$num_padded;

}else{
foreach($prnos->result() as $prnos1);

$resty=explode('-',$prnos1->challanno);

$lastnum=(int) $resty[1];


$num1=$lastnum+1;

$num_padded = sprintf("%03d", $num1);

$code='PRERGP-'.$num_padded;

}

return $code;

}


function all_rgp_transactions($type1,$supplier,$code,$challandate,$remarks,$dispatch,$gst,$email,$mobile,$address,$itemname,$unit,$qty,$rate,$type)
{

$data=array('type'=>$type1,'supplier'=>$supplier,'challanno'=>$code,'challandate'=>$challandate,'remarks'=>$remarks,'dispatchedby'=>$dispatch,'standalone'=>'1','addedOn'=>date('Y-m-d H:i:s'),'addedBy'=>$_SESSION['logged_in']['user_id'],'approved'=>'1','gst'=>$gst,'email'=>$email,'mobile_no'=>$mobile,'address'=>$address,'approvedOn'=>date('Y-m-d H:i:s'));

$this->db->insert('outwardchallan',$data);
$lid=$this->db->insert_id();

return $lid;
}


function update_rgp_stock($itemname,$unit,$qty,$rate,$type,$lid,$minustock)
{

	
		for($i=0;$i<count($itemname);$i++)
		{
		if($itemname[$i]==6)
		{
		$a="challanid";
		}else
		{
		$a="challanid";
		}

		if($minustock[$i]==1)
		{
			$m=1;
		}else
		{
			$m=0;
		}
		$udata=array($a=>$lid,'type'=>$type[$i],'itemid'=>$itemname[$i],'quantity'=>$qty[$i],'unit'=>$unit[$i],'price'=>$rate[$i],'returnable'=>'1','billable'=>'0','addedOn'=>date('Y-m-d H:i:s'),'minustock'=>$m);

		$this->db->insert('outwardchallan_item',$udata);

		if($m==1)
		{
			if($type[$i]==1 || $type[$i]==2)
			{
				$curstock=$this->instrumentstock($itemname[$i]);
				$news=$curstock-$qty[$i];
				$upst=array('stock'=>$news);
				$this->db->where('id',$itemname[$i]);
				$this->db->update('presto_instruments',$upst);
			}else{
				$curstock=$this->bomstock($itemname[$i]);
				$news=$curstock-$qty[$i];
				$upst=array('current_stock'=>$news);
				$this->db->where('id',$itemname[$i]);
				$this->db->update('machine_parts_with_picture',$upst);

				/** RECONSILE **/

				$this->reconcile_curl(date('Y-m-d'),$itemname[$i]);

			} 
		}


		}


}

function getstoreInwards($start,$end,$item)
{
	$acc=array();
	$acc[]=0;
	$restey=$this->db->select('accept_qty')->from('mrn_history')->where('itemid',$item)->where('storereciept',1)->where('storerecvon>=',$start." 00:00:00")->where('storerecvon<=',$end." 23:59:59")->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$acc[]=$row->accept_qty;
		}
	}


	return array_sum($acc);
}


function getRGPInwards($start,$end,$item)
{
	$acc=array();
	$acc[]=0;
	$restey=$this->db->select('recvqty')->from('outwardchallan_item')->where('itemid',$item)->where('type',3)->where('returnable',1)->where('recvOn>=',$start." 00:00:00")->where('recvOn<=',$end." 23:59:59")->where('add_stock',1)->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$acc[]=$row->recvqty;
		}
	}


	return array_sum($acc);
}


function getReturnStockInwards($start,$end,$item)
{
	$acc=array();
	$acc[]=0;
	$restey=$this->db->select('quantity')->from('return_stock_from_store')->where('item_id',$item)->where('flag',1)->where('rollback',0)->where('updated_store_on>=',$start." 00:00:00")->where('updated_store_on<=',$end." 23:59:59")->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$acc[]=$row->quantity;
		}
	}


	return array_sum($acc);
}


function getRGPOutwards($start,$end,$item)
{

	$acc=array();
	$acc[]=0;
	$restey=$this->db->select('quantity')->from('outwardchallan_item')->where('itemid',$item)->where('type',3)->where('returnable',1)->where('minustock',1)->where('addedOn>=',$start." 00:00:00")->where('addedOn<=',$end." 23:59:59")->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$acc[]=$row->quantity;
		}
	}


	return array_sum($acc);

}

function getRGPOutwards1($start,$end,$item)
{

if($item==234 && $start="2023-09-09")
{
$a="quantity";
}else
{
$a="quantity";
}
$acc=array();
$acc[]=0;
$restey=$this->db->select($a)->from('outwardchallan_item')->where('itemid',$item)->where('type',3)->where('returnable',1)->where('addedOn>=',$start." 00:00:00")->where('addedOn<=',$end." 23:59:59")->where('minustock',1)->get();
if($restey->num_rows()>0)
{
foreach($restey->result() as $row)
{
$acc[]=$row->quantity;
}
}

return array_sum($acc);

}



function getIssuedStock($start,$end,$item)
{

	$acc=array();
	$acc[]=0;
	$restey=$this->db->select('stock')->from('issuestocktousers')->where('itemid',$item)->where('storeaccept',1)->where('acceptedOn>=',$start." 00:00:00")->where('acceptedOn<=',$end." 23:59:59")->get();
	if($restey->num_rows()>0)
	{
		foreach($restey->result() as $row)
		{
			$acc[]=$row->stock;
		}
	}


	return array_sum($acc);

}

function getopendate($item)
{

$stock='2023-08-01';
$r=$this->db->select('stock_date')->from('ims_opening_stock')->where('itemid',$item)->get();
if($r->num_rows()>0)
{
foreach($r->result() as $row);
$stock=$row->stock_date;
}


return $stock;
	

}


function getfactory_employee_data($id)
	{
		$guser='';
		
		$resty=$this->db->select('employee_name')->from('prestogroup_employees')->where('id',$id)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $row);
			$guser=$row->employee_name;
			
			
				
		}

		return $guser;
	}


	function checkifstorehasnotaccepted($itemid,$blockedid)
	{
	    $restsyeee=array();
	    $restsyeee[]=0;
		$resty=$this->db->select('a.stock')->from('issuestocktousers a')->where('a.itemid',$itemid)->where('a.blockedid ',$blockedid)->where('a.storeaccept',0)->get();
		if($resty->num_rows()>0)
		{
			foreach($resty->result() as $resty1)
			{
				$restsyeee[]=$resty1->stock;
			}
                 
           
			
		}
		
		return array_sum($restsyeee);
		
	}


	function reconcile_curl($date,$itemid)
	{
		$rtr=$this->db->select('id')->from('ims_opening_stock')->where('itemid',$itemid)->get();
		if($rtr->num_rows()>0)
		{		
		$start=date('Y-m-d',strtotime($date.' -1 Days'));
		$this->getCurrentstockasapplicable($start,$date,$itemid);
		}


	}



	function getCurrentstockasapplicable($startdate,$enddate,$item)
	{
		
	$vendor_data=array();
	$startdate=$startdate;
	$enddate=$enddate;
	$item=$item;
	$basedate=$this->getopendate($item);
	$itemname=$this->getmachineitemname($item);
	$fincode=$this->getmachineitemfincode($item);

	// $period=$this->createDateRangeArray($startdate,$enddate);
	// echo "<pre>"; print_r($period); exit;
	$period=$this->createDateRangeArray($startdate,$enddate);
	//$period = new DatePeriod(new DateTime($startdate), new DateInterval('P1D'), new DateTime($enddate.' +1 day'));

	$i=1;

    foreach ($period as $date) {
     


        $curdayBefore=$date;
        $curdayBefore=date("Y-m-d",strtotime($curdayBefore.' -1 Days'));
        $curdate=$date;
        $opening_base=$this->get_opening_stock($item);
        if($curdate=='2023-08-01')
        {
        	$closing_prev=$opening_base;
        }else
        {
        
		/** For OPENING **/
	    $purchase=$this->getstoreInwards($basedate,$curdayBefore,$item);      
	    $rgpin=$this->getRGPInwards($basedate,$curdayBefore,$item);      
	    $retstockin=$this->getReturnStockInwards($basedate,$curdayBefore,$item); 
	    $rgout=$this->getRGPOutwards($basedate,$curdayBefore,$item);      
	    $issued=$this->getIssuedStock($basedate,$curdayBefore,$item); 
	    $Pinward=$purchase+$rgpin+$retstockin;
	    $POutward=$rgout+$issued;
	    $closing_prev=$opening_base+$Pinward-$POutward;
		/** For Opening **/  
		}

		/** CURR **/
	    $purchase=$this->getstoreInwards($curdate,$curdate,$item);      
	    $rgpin=$this->getRGPInwards($curdate,$curdate,$item);      
	    $retstockin=$this->getReturnStockInwards($curdate,$curdate,$item); 
	    $rgout=$this->getRGPOutwards1($curdate,$curdate,$item);      
	    $issued=$this->getIssuedStock($curdate,$curdate,$item); 
		/** OPENING + INWARD - OUT **/
		$Pinward=$purchase+$rgpin+$retstockin;
		$POutward=$rgout+$issued;
		$closing_curr_stock=$closing_prev+$Pinward-$POutward;
		/** CURR **/     
		$mitrstock=$this->getstock($item);
		if($closing_curr_stock>$mitrstock)
		{
			$diff=$closing_curr_stock-$mitrstock;
		}else if($closing_curr_stock<$mitrstock)
		{
			$diff=$closing_curr_stock-$mitrstock;
		}else
		{
			$diff=0;
		}

		$dataup=0;
		if($curdate==date('Y-m-d'))
		{
			
			if($closing_curr_stock>=0)
			{
				$closing_curr_stock=$closing_curr_stock;
			}else
			{
				$closing_curr_stock=0;
			}

			$arr=array('current_stock'=>$closing_curr_stock,'last_stock_updatedOn'=>date('Y-m-d H:i:s'));
			$this->db->where('id',$item);
			$this->db->update('machine_parts_with_picture',$arr);
			if($this->db->affected_rows()>0)
			{
				$dataup=1;
			}

		}

	$i++;
	}



	return $dataup;

}

		function get_opening_stock($item)
	{
		$stock=0;
		$r=$this->db->select('stock')->from('ims_opening_stock')->where('itemid',$item)->get();
		if($r->num_rows()>0)
		{
		foreach($r->result() as $row);
		$stock=$row->stock;
		}


	return $stock;


	}	


	function createDateRangeArray($strDateFrom,$strDateTo)
{
    // takes two dates formatted as YYYY-MM-DD and creates an
    // inclusive array of the dates between the from and to dates.

    // could test validity of dates here but I'm already doing
    // that in the main script

    $aryRange = [];

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}

function getpendpocount_filter()
{

	$this->db->select('a.masterid')->from('purchase_request_view a')->join('machine_parts_with_picture_view d','d.id=a.masterid')->join('presto_machine_part_category c','c.id=d.category_id')->where('a.approvalstatus','0')->where('a.closed','0');
	if($_SESSION['logged_in']['role']<>1)
	{
		$this->db->where('c.user_id',$_SESSION['logged_in']['user_id']);
	}
	$rest=$this->db->order_by('d.part','ASC')->get();

	return $rest->num_rows();


}


}

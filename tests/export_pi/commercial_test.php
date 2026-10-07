<?php
require __DIR__.'/../../application/helpers/export_pi_helper.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
check(export_pi_discount(1000, array('discount_type'=>1,'discountvalue'=>10)) === 100.0, 'Percentage discount');
check(export_pi_discount(1000, array('discount_type'=>2,'discountvalue'=>75)) === 75.0, 'Fixed discount');
check(export_pi_discount(100, array('discount_type'=>2,'discountvalue'=>200)) == 100, 'Discount capped at subtotal');
check(export_pi_discount(100, array('discount_type'=>1,'discountvalue'=>-10)) == 0, 'Negative discount ignored');
foreach (array('export_performa_invoice','edit_export_performa_invoice') as $view) {
    $source = file_get_contents(__DIR__.'/../../application/views/form/'.$view.'.php');
    foreach (array('bill_to_state','bill_to_state_code','bill_to_gst_no','ship_to_state','ship_to_state_code','ship_to_gst_no') as $field) {
        preg_match('/<input[^\n]*name="'.$field.'"[^\n]*/', $source, $match);
        check(!isset($match[0]) || strpos($match[0], 'required') === false, $view.': '.$field.' optional');
    }
}
echo "Export PI discount and optional field checks passed.\n";
class PiResult { private $data; function __construct($data) {$this->data=$data;} function row_array(){return $this->data;} }
class PiDb {
    public $saved=array(); private $table;
    function select($fields){return $this;} function from($table){$this->table=$table;return $this;}
    function where($field,$value){return $this;} function table_exists($table){return true;}
    function get($table=null){$table=$table ?: $this->table; return new PiResult($table==='quotation_discount_data' ? array('discount_type'=>1,'discountvalue'=>10) : $this->saved);}
    function replace($table,$row){$this->saved=$row;}
}
class PiSales {function getRecordID($lead){return 22;} function quotation_freight_packing_forwarding($id){return array('packing_charges'=>5,'freight'=>1);}}
class PiInput {public $values=array('pi_packing_charges'=>'0','pi_forwarding_charges'=>'2.5','pi_other_charges'=>'125','pi_freight'=>'3','pi_freight_charges'=>'50');function post($key){return isset($this->values[$key])?$this->values[$key]:null;}}
class PiUri {function segment($n){return 42;}}
$ci=(object)array('db'=>new PiDb(),'salescrm'=>new PiSales(),'input'=>new PiInput(),'uri'=>new PiUri());
export_pi_save_commercial($ci, 9);
$terms=export_pi_commercial($ci,9,22);
check($terms['packing_charges']==0,'Zero overrides quotation charge');
check($terms['forwarding_charges']==2.5 && $terms['other_charges']==125,'Custom charges survive reopen');
check($terms['freight']==3 && $terms['freight_charges']==50,'Freight mode and amount survive reopen');
check(export_pi_discount(1000,$terms)==100,'Quotation discount preserved');
$ci->input->values=array('pi_other_charges'=>'200');
export_pi_save_commercial($ci,9);
$terms=export_pi_commercial($ci,9,22);
check($terms['other_charges']==200 && $terms['packing_charges']==0,'Edit preserves previous overrides');
echo "Export PI save/reopen checks passed.\n";

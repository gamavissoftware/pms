<?php
require __DIR__.'/../../application/helpers/export_pi_helper.php';
class PaymentResult {
    private $row;
    function __construct($row) {$this->row=$row;}
    function row_array() {return $this->row;}
}
class PaymentDb {
    public $tables=array(); private $table; private $key;
    function select($s) {return $this;}
    function from($s) {$this->table=$s;return $this;}
    function where($k,$v) {$this->key=$v;return $this;}
    function order_by($k,$v) {return $this;}
    function limit($n) {return $this;}
    function get() {return new PaymentResult(isset($this->tables[$this->table][$this->key])?$this->tables[$this->table][$this->key]:array());}
}
function verify($actual,$expected,$label) {if($actual!==$expected) throw new RuntimeException($label);}
$db=new PaymentDb(); $ci=(object)array('db'=>$db);
$db->tables=array(
    'payment_terms'=>array(1=>array('id'=>1,'payment_terms'=>'30% advance, balance before dispatch'),2=>array('id'=>2,'payment_terms'=>'Payment against LC')),
    'quotation_other_information'=>array(22=>array('terms_value'=>1)),
    'payment_terms_milestone'=>array(1=>array('payment_percentage'=>'30'))
);
verify(export_pi_payment_terms($ci,0,22),array('text'=>'30% advance, balance before dispatch','advance_percentage'=>30.0),'Missing PO falls back to quotation');
verify(export_pi_payment_terms($ci,999,22)['advance_percentage'],30.0,'Stale PO ID falls back');
verify(export_pi_payment_terms($ci,2,22),array('text'=>'Payment against LC','advance_percentage'=>null),'PO terms retained without milestone');
unset($db->tables['payment_terms_milestone'][1]);
verify(export_pi_payment_terms($ci,0,22)['advance_percentage'],null,'Text-only quotation has no invented advance');
$db->tables['payment_terms_milestone'][1]=array('payment_percentage'=>'0');
verify(export_pi_payment_terms($ci,0,22)['advance_percentage'],0.0,'Explicit zero retained');
$db->tables['payment_terms_milestone'][1]=array('payment_percentage'=>'150');
verify(export_pi_payment_terms($ci,0,22)['advance_percentage'],null,'Invalid percentage rejected');
verify(export_pi_payment_terms($ci,0,999),array('text'=>'','advance_percentage'=>null),'Missing terms handled');
echo "Payment term fallback and milestone checks passed.\n";

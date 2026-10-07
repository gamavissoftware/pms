<?php
// Model loading inside a CI view attaches the model to the controller, not the Loader.
define('page_url', '/index.php/');
set_error_handler(function($severity, $message) { throw new ErrorException($message, 0, $severity); });
class ApprovalViewModel {
    public $rows = array();
    public function get_approval_queue($id) {
        if ($id !== 139) throw new Exception('Wrong approver');
        return $this->rows;
    }
}
class ApprovalViewLoader {
    public $session, $model;
    public function model($name, $alias) {
        $ci = &get_instance();
        $ci->$alias = $this->model;
    }
    public function render() {
        ob_start();
        include __DIR__ . '/../../application/views/df_change_control/_approval_queue.php';
        return ob_get_clean();
    }
}
$ci = new stdClass();
function &get_instance() { global $ci; return $ci; }
$loader = new ApprovalViewLoader();
$loader->session = (object)array('userdata'=>array('logged_in'=>array('user_id'=>139)));
$loader->model = new ApprovalViewModel();
$ci->load = $loader;
if (strpos($loader->render(), 'No change requests are awaiting approval.') === false) throw new Exception('Empty panel missing');
$loader->model->rows = array(array('id'=>7,'change_no'=>'ECN-7','df_id'=>0,'df_no'=>null,'title'=>'<script>unsafe</script>','priority'=>'HIGH','first_name'=>'Test','last_name'=>'User','created_on'=>'2026-09-11'));
$html = $loader->render();
if (strpos($html, '/Df_change_control/view/7') === false || strpos($html, '&lt;script&gt;') === false || strpos($html, '<script>') !== false) throw new Exception('Queue link or escaping failed');
$loader->session->userdata['logged_in']['user_id'] = 161;
if (trim($loader->render()) !== '') throw new Exception('Panel exposed to another admin');
echo "PASS: loader-context rendering, empty and populated queues, escaping, and user 139 visibility.\n";

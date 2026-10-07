<?php
require __DIR__ . '/bootstrap.php';
define('APPPATH', __DIR__ . '/../../application/');
define('assets_url', '/assets/');
define('sitetitle', 'PMS');
set_error_handler(function($severity,$message,$file,$line){throw new ErrorException($message,0,$severity,$file,$line);});
class OtViewSession {function flashdata($key){return '';}}
class OtViewConfig {function item($key){return false;}}
class OtViewRenderer {
    public $load,$session,$config;
    function __construct(){$this->load=$this;$this->session=new OtViewSession();$this->config=new OtViewConfig();}
    function view($view,$data=array()){
        // Shared PMS partials query the live session/database; fixture their boundary only.
        if ($view==='common/nav-menu') {
            echo '<div data-shared-view="common/nav-menu" style="background:white;padding:10px"><a role="button" data-toggle="collapse" href="#master">Master</a><div id="master" class="collapse"><a href="#limits">Task Master Dashboard</a></div><div class="dropdown"><a href="#" data-toggle="dropdown">Profile menu</a><ul class="dropdown-menu"><li><a href="#profile">My profile</a></li></ul></div></div><script>$(document).ready(function(){document.querySelector("[data-shared-view]").setAttribute("data-jquery-ready","yes");});</script>'; return;
        }
        if (strpos($view,'common/')===0) { echo '<div data-shared-view="'.ot_e($view).'">Shared PMS '.ot_e($view).'</div>'; return; }
        extract($data);include __DIR__.'/../../application/views/'.$view.'.php';}
    function page($view,$data){ob_start();$this->view('overtime/_header',$data);$this->view('overtime/'.$view,$data);$this->view('overtime/_footer',$data);return ob_get_clean();}
}
list($model,$db)=ot_fixture();
$input=ot_team_input('+1 day',array(1),'<script>alert("xss")</script> Labour');
$input['reason']='<script>alert("xss")</script> Finish dispatch packing.';
$id=$model->submit(2,$input,bin2hex(random_bytes(32)));
$model->decide($id,139,'APPROVE','Production line needs additional packing time.');
$renderer=new OtViewRenderer();
$base=array('permissions'=>ot_user_permissions($db,139),'section'=>'','title'=>'Overtime','viewer'=>$model->user(139),'csrf'=>'fixture-token','statuses'=>array('PENDING_LEADER','PENDING_ADMIN','APPROVED','REJECTED','CANCELLED'));
$filters=array('from'=>date('Y-m-01'),'to'=>date('Y-m-t'),'status'=>'','df_id'=>0,'person'=>'','employee_id'=>0,'department_id'=>0);
$shared=array('filters'=>$filters,'summary'=>$model->summary($base['viewer'],$filters),'rows'=>$model->listing($base['viewer'],$filters),'page'=>1,'options'=>$model->filter_options($base['viewer']));
$pages=array(
 'create'=>array('values'=>array(),'error'=>'','submission_key'=>str_repeat('a',64),'users'=>$model->users(2),'dfs'=>$model->df_options(),'policy'=>$model->policy(2),'viewer'=>$model->user(4)),
 'index'=>array_merge($shared,array('inbox'=>false,'notifications'=>$model->notifications($base['viewer']))),
 'view'=>array('request'=>$model->get_request($id),'history'=>$model->history($id),'can_decide'=>false,'can_cancel'=>false),
 'reports'=>array_merge($shared,array('group'=>'employee','groups'=>$model->grouped_report($base['viewer'],$filters,'employee'))),
 'settings'=>array_merge(array('policy'=>$model->policy(2),'users'=>$model->users(2)),$model->settings_data(2)),
 'setup'=>array()
);
$pendingId=$model->submit(2,ot_team_input('+2 days',array(5),'Manual helper'),bin2hex(random_bytes(32)));
$pages['pending']=array('request'=>$model->get_request($pendingId),'history'=>$model->history($pendingId),'can_decide'=>true,'can_cancel'=>false,'reassign_users'=>array());
$pages['requester']=array('viewer'=>$model->user(2),'permissions'=>ot_user_permissions($db,2),'request'=>$model->get_request($pendingId),'history'=>$model->history($pendingId),'can_decide'=>false,'can_cancel'=>true,'reassign_users'=>array());
// An administrator sees the masters; user 139 approves but holds no master grants.
$admin=array('viewer'=>$model->user(4),'permissions'=>ot_user_permissions($db,4));
$pages['settings_cost']=array_merge($admin,array('section'=>'cost','policy'=>$model->policy(2),'users'=>$model->users(2)),$model->settings_data(2));
$pages['reports_day']=array_merge($shared,array('group'=>'day','groups'=>$model->grouped_report($base['viewer'],$filters,'day')));
$aliases=array('pending'=>'view','requester'=>'view','settings_cost'=>'settings','reports_day'=>'reports');
$destination=isset($argv[1])?$argv[1]:sys_get_temp_dir().'/pms-overtime-preview';
if(!is_dir($destination))mkdir($destination,0700,true);
foreach($pages as $view=>$data){$html=$renderer->page(isset($aliases[$view])?$aliases[$view]:$view,array_merge($base,$data));check(strpos($html,'data-shared-view="common/nav-menu"')!==false,'Shared PMS navigation included');check(strpos($html,'data-shared-view="common/footer"')!==false,'Shared PMS footer included');check(strpos($html,'<script>alert("xss")</script>')===false,'Output escaped');file_put_contents($destination.'/'.$view.'.html',$html);}
echo "PASS: all screens and approval/cancellation variants render without PHP warnings; untrusted request content escaped. Preview: $destination\n";

<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class refresh extends admin {
	
	private $db;
	public $siteid,$categorys;
	public function __construct() {
		parent::__construct();
		$this->db = pc_base::load_model('content_model');
		$this->siteid = $this->get_siteid();
		$this->categorys = getcache('category_content_'.$this->siteid,'commons');
		//权限判断
		if(isset($_REQUEST['catid']) && $_SESSION['roleid'] != 1 && ROUTE_A !='pass' && strpos(ROUTE_A,'public_')===false) {
			
		}
	}

	public function init () {
		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$rolename = $roles[$_SESSION['roleid']];
		$site = pc_base::load_app_class('sites');
		$sitelist = $site->get_list($_SESSION['roleid']);
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		$site_model = param::get_cookie('site_model');
		include $this->admin_tpl('index');
	}
	
	// ##############################################
	/**
	 * @category 信息刷新
	 */
	public function info() {

		$catid = $_REQUEST['catid'];
		param::set_cookie('module', 'content');
		if (isset($_POST['ids']) && !empty($_POST['ids'])) {
			$ids = $_POST['ids'];
			foreach ($ids as $id) {
				param::set_cookie('catid', $catid);
				$category = $this->categorys[$catid];
				$modelid = $category['modelid'];
				$this->db->set_model($modelid);
				$info = $this->db->get_one(array('id'=>$id));
				
				$new = $this->timeref($info);
				if($new!=null && $new!=''){
					$this->db->update(array('updatetime'=>$new),array('id'=>$id));
				}
			}
			showmessage(L('update_success'));
			
		} else {
			$category = $this->categorys[$catid];
			$limittime = strtotime("-20 days 12 hours 30 seconds");
			$where = "updatetime < $limittime";
			$modelid = $category['modelid'];
			$this->db->set_model($modelid);
			$infos = $this->db->listinfo($where, 'updatetime ASC', 1, 20);
			foreach ($infos as $info) {
				$id = $info['id'];
				$new = $this->timeref($info);
				if($new!=null && $new!=''){
					$this->db->update(array('updatetime'=>$new),array('id'=>$id));
				}
			}
			showmessage(L('update_success'));
			
		}
	}
	
	/**
	 * @category 新闻刷新
	 */
	public function news() {

		$catid = $_REQUEST['catid'];
		param::set_cookie('module', 'content');
		if (isset($_POST['ids']) && !empty($_POST['ids'])) {
			$ids = $_POST['ids'];
			foreach ($ids as $id) {
				param::set_cookie('catid', $catid);
				$category = $this->categorys[$catid];
				$modelid = $category['modelid'];
				$this->db->set_model($modelid);
				$info = $this->db->get_one(array('id'=>$id));
				
				$new = $this->timeref($info, 3);
				if($new!=null && $new!=''){
					$this->db->update(array('updatetime'=>$new),array('id'=>$id));
				}
			}
			showmessage(L('update_success'));
			
		} else {
			$category = $this->categorys[$catid];
			$modelid = $category['modelid'];
			$this->db->set_model($modelid);
			$infos = $this->db->listinfo('', 'updatetime DESC', 1, 10);
			foreach ($infos as $info) {
				$id = $info['id'];
				$new = $this->timeref($info, 3);
				if($new!=null && $new!=''){
					$this->db->update(array('updatetime'=>$new),array('id'=>$id));
				}
			}
			showmessage(L('update_success'));
			
		}
	}
	
	/**
	 *@category 刷新时间算法    规则：1.更新时间至少比发布时间晚一天    2.发布时间距现在至少15天     3.更新时间为最近三天
	 *@return 时间戳整数
	 */
	private function timeref($info, $max=3, $min=1){
		$ADAY = 3600*24;
		$CURRTIME = time();
		if($info['updatetime']==null||$info['updatetime']=='') return null;
		if($info['inputtime']==null||$info['inputtime']=='') return null;
		
		if(($info['updatetime']-$info['inputtime']) < $ADAY) {
			if($CURRTIME - $info['inputtime'] > 15*$ADAY){
				//最近3天
				$day = rand($min, $max);
				$basetime = strtotime("-".$day." day");
				//一天内的随机时间
				$hour = rand(1,24);
				$second = rand(1,60);
				$rand = ($hour*3600 + $second);//strtotime("-$hour hours $second seconds");
				
				return $basetime + $rand;
			}
		}
		
		return null;
	}
	// ##############################################

}
?>
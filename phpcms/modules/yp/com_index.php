<?php
defined('IN_PHPCMS') or exit('No permission resources.');
class com_index {
	function __construct() {
		pc_base::load_app_func('global');
		$this->yp_company_db = pc_base::load_model('yp_company_model');
		$this->db = pc_base::load_model('yp_content_model');
		$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
     	define("SITEID",$siteid);
		$this->setting = getcache('yp_setting', 'yp');
		$this->models = getcache('yp_model', 'model');
		$this->setting_models = getcache('models', 'yp');

     	//获取企业详情
     	$this->userid = intval($_GET['userid']);
		$this->array = get_companyinfo($this->userid);
		if (empty($this->array)) showmessage(L('business_does_not_exist'));
		$this->default_tpl = 'yp/'.$this->array['tplname'];
		define('TEMPLATE_PATH', APP_PATH.'statics/'.$this->default_tpl.'/');
	}

	public function init() {
		$array = $this->array;
		$SEO = array();
 		$SEO['title'] = L('first').' - '.$array[companyname];
		$SEO['keywords'] = L('first').' - '.$array[companyname];
		$SEO['description'] = L('first').' - '.$array[companyname];

  		include template($this->default_tpl, 'index');
	}

	public function about() {
		$array = $this->array;
		$SEO['title'] = L('company_profile').' - '.$array[companyname];
		$SEO['keywords'] = L('company_profile').' - '.$array[companyname];
		$SEO['description'] = L('company_profile').' - '.$array[companyname];
  		include template($this->default_tpl, 'about');
	}

	public function contact() {
		$array = $this->array;
  		$map = $array['map'];
		list($lng, $lat, $ZoomLevel) = explode('|', $map);
		$SEO['title'] = L('contact_us').' - '.$array[companyname];
		$SEO['keywords'] = L('contact_us').' - '.$array[companyname];
		$SEO['description'] = L('contact_us').' - '.$array[companyname];
   		include template($this->default_tpl, 'contact');
	}

	public function certificate() {
		$array = $this->array;
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
 		$pages = $this->db->pages;
		$SEO['title'] = L('certificate').' - '.$array[companyname];
		$SEO['keywords'] = L('certificate').' - '.$array[companyname];
		$SEO['description'] = L('certificate').' - '.$array[companyname];
  		include template($this->default_tpl, 'certificate');
	}

	public function model() {
		$modelid = $_GET['modelid'] = intval($_GET['modelid']);
		if (!$modelid) showmessage(L('link_address_error'));
		$yp_models = getcache('yp_model', 'model');
		if(!$yp_models[$modelid]){
			showmessage(L('model_does_not_exist'),HTTP_REFERER);
		}
		$template = substr(trim($yp_models[$modelid]['tablename']), 3);
		$page = $_GET['page'] = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$catid = $_GET['catid'] = isset($_GET['catid']) && intval($_GET['catid']) ? intval($_GET['catid']) : '';
		$array = $this->array;
		$this->db->set_model($modelid);
		$catid_arr = get_compay_catid($modelid, $this->userid);
		$categorys = getcache('category_yp_'.$modelid, 'yp');
		//SEO设置
		$setting = string2array($yp_models[$modelid]['setting']);
		//PRINT_R($setting);
		$setting['title'] = $setting['meta_title'].' - '.$array['companyname'];
		$SEO = seo(SITEID, '', $setting['title'], $setting['meta_description'], $setting['meta_keywords']);
		include template($this->default_tpl, $template);
	}

	public function show() {
		$id = $_GET['id'] = intval($_GET['id']);
		$catid = $_GET['catid'] = intval($_GET['catid']);
		$modelid = $_GET['modelid'] = intval($_GET['modelid']);
		if (!$id) showmessage(L('link_address_error_content'));
		if (!$catid) showmessage(L('link_address_error_category'));
		$category_db = pc_base::load_model('category_model');
		if (!$category_db->get_one(array('catid'=>$catid))) showmessage(L('category_does_not_exist'));


		$content_yp = pc_base::load_model('yp_content_model');
		$data = $content_yp->get_content($catid, $id, $modelid);
		if (!$data) showmessage(L('information_deleted'));
		if ($data['addition_field']) {
			$addition_field = $data['addition_field'];
			unset($data['addition_field']);
		}
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		if (!$modelid) $modelid  = $content_yp->modelid;
		$yp_models = getcache('yp_model', 'model');
		if(!$yp_models[$modelid]){
			showmessage(L('model_not_exist'),HTTP_REFERER);
		}
		$CATEGORYS = getcache('category_yp_'.$modelid, 'yp');
		$CAT = $CATEGORYS[$catid];
		$CAT['setting'] = string2array($CAT['setting']);

		require_once CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR.'yp_output.class.php';
		$yp_output = new yp_output($modelid,$catid,$CATEGORYS);
		$userid = intval($data['userid']);
		$rs = $yp_output->get($data);
		if ($addition_field) {
			$addition_field = string2array($addition_field);
			$additional_fields = $yp_output->fields = get_additional_fields($catid, $CATEGORYS);
			$additional_data = $yp_output->get($addition_field);
			$additional_base = $additional_general = array();
			foreach ($additional_data as $k => $v) {
				if ($additional_fields[$k]['isbase ']) {
					$additional_base[$k] = $additional_data[$k];
				} else {
					$additional_general[$k] = $additional_data[$k];
				}
			}
			unset($additional_data, $addition_field);
		}
		extract($rs);
		if(isset($rs['paginationtype'])) {
			$paginationtype = $rs['paginationtype'];
			$maxcharperpage = $rs['maxcharperpage'];
		}
		$pages = $titles = '';
		if($data['paginationtype']==1) {
			//自动分页
			if($maxcharperpage < 10) $maxcharperpage = 500;
			$contentpage = pc_base::load_app_class('contentpage', 'content');
			$content = $contentpage->get_data($content,$maxcharperpage);
		}
		if($data['paginationtype']!=0) {
			//手动分页
			$CONTENT_POS = strpos($content, '[page]');
			if($CONTENT_POS !== false) {
				$contents = array_filter(explode('[page]', $content));
				$pagenumber = count($contents);
				if (strpos($content, '[/page]')!==false && ($CONTENT_POS<7)) {
					$pagenumber--;
				}
				for($i=1; $i<=$pagenumber; $i++) {
					$pageurls[$i][0] = compute_company_url('show', array('modelid'=>$modelid, 'catid'=>$catid, 'id'=>$id, 'page'=>$i));
				}
				$END_POS = strpos($content, '[/page]');
				if($END_POS !== false) {
					if($CONTENT_POS>7) {
						$content = '[page]'.$title.'[/page]'.$content;
					}
					if(preg_match_all("|\[page\](.*)\[/page\]|U", $content, $m, PREG_PATTERN_ORDER)) {
						foreach($m[1] as $k=>$v) {
							$p = $k+1;
							$titles[$p]['title'] = strip_tags($v);
							$titles[$p]['url'] = $pageurls[$p][0];
						}
					}
				}
				//当不存在 [/page]时，则使用下面分页
				pc_base::load_app_func('util', 'content');
				$pages = content_pages($pagenumber,$page, $pageurls);
				//判断[page]出现的位置是否在第一位
				if($CONTENT_POS<7) {
					$content = $contents[$page];
				} else {
					if ($page==1 && !empty($titles)) {
						$content = $title.'[/page]'.$contents[$page-1];
					} else {
						$content = $contents[$page-1];
					}
				}
				if($titles) {
					list($title, $content) = explode('[/page]', $content);
					$content = trim($content);
					if(strpos($content,'</p>')===0) {
						$content = '<p>'.$content;
					}
					if(stripos($content,'<p>')===0) {
						$content = $content.'</p>';
					}
				}
			}
		}

		$setting = $this->setting;
		//取得模板文件名
		$template = substr(trim($yp_models[$modelid]['tablename']), 3).'_show';
		//SEO
		$siteid = get_siteid();
		$model_setting = string2array($MODEL[$modelid]['setting']);
		$seo_keywords = $CAT['setting']['meta_keywords'] ? $CAT['setting']['meta_keywords'] : ($model_setting['meta_keywords'] ? $model_setting['meta_keywords'] : $this->setting['meta_keywords']);
		$seo_description = $CAT['setting']['meta_description'] ? $CAT['setting']['meta_description'] : ($model_setting['meta_description'] ? $model_setting['meta_description'] : $this->setting['meta_description']);
		$SEO = seo($siteid, '', $title, $seo_description, $seo_keywords);
		$array = $this->array;
		$catid_arr = get_compay_catid($modelid, $this->userid);
		$setting = getcache('yp_setting', 'yp');

		include template($this->default_tpl, $template);
	}

	public function guestbook(){
		$array =$this->array;
		$yp_guestbook = pc_base::load_model('yp_guestbook_model');

		//提交留言处理程序段
		if (isset($_POST['dosubmit'])) {
			if(!intval($_POST['info']['userid'])){
				showmessage(L('failure_to_submit'),HTTP_REFERER);
			}
			if(!$_POST['info']['content']){
				showmessage(L('fill_out_message'),HTTP_REFERER);
			}
			if($_POST['info']['title']){
				$info['title'] = $_POST['info']['title'];
			}
			if($_POST['info']['url']){
				$info['url'] = $_POST['info']['url'];
			}
			$info['userid'] = intval($_POST['info']['userid']);
			$info['telephone'] = $_POST['info']['telephone'];
			$info['username'] = $_POST['info']['username'];
			$info['qq'] = $_POST['info']['qq'];
			$info['content'] = new_html_special_chars($_POST['info']['content']);
 			$info['addtime'] = SYS_TIME;
			$info['status'] = '0';
 			$resute = $yp_guestbook->insert($info);
			if($resute){
				showmessage(L('operation_success'),HTTP_REFERER);
			}else{
				showmessage(L('failure_to_submit'),HTTP_REFERER);
			}
		}

		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
 		$pages = $yp_guestbook->pages;

		$SEO['title'] = L('guestbook').' - '.$array[companyname];
		$SEO['keywords'] = L('guestbook').' - '.$array[companyname];
		$SEO['description'] = L('guestbook').' - '.$array[companyname];
		pc_base::load_sys_class('form', '', 0);
		include template($this->default_tpl, 'guestbook');
	}
}
?>
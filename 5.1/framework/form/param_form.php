<?php
/**
 * 自定义参数
 * @package phpok\framework\form
 * @作者 qinggan <admin@phpok.com>
 * @版权 深圳市锟铻科技有限公司
 * @主页 http://www.phpok.com
 * @版本 4.x
 * @授权 http://www.phpok.com/lgpl.html 开源授权协议：GNU Lesser General Public License
 * @时间 2017年12月02日
**/

if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class param_form extends _init_auto
{
	public function __construct()
	{
		parent::__construct();
	}

	public function phpok_config()
	{
		$this->view($this->dir_phpok."form/html/param_admin.html","abs-file");
	}

	public function cssjs()
	{
		$this->addjs("js/form.param.js");
		$this->addjs('js/jscolor/jscolor.js');
		$this->addjs('js/laydate/laydate.js');
	}

	public function phpok_format($rs,$appid="admin")
	{
		if(!$rs){
			return false;
		}
		$this->cssjs();
		$pname = false;
		if($rs['p_name']){
			$pname = explode("\n",$rs['p_name']);
			$this->assign('_pname',$pname);
		}
		if($rs['content']){
			$list = unserialize($rs['content']);
		}else{
			$list = array();
			if($pname){
				foreach($pname as $key=>$value){
					$tmp = array($value);
					$list['title'][] = $value;
				}
			}else{
				$list = array('title'=>array(),'content'=>array());
			}
		}
		if($rs['p_type']){
			$rs['p_width'] = intval($rs['p_width']) ? intval($rs['p_width']) : '80';
		}else{
			$rs['p_width'] = intval($rs['p_width']) ? intval($rs['p_width']) : '300';
		}
		if(!$rs['p_add'] || (!$pname && $rs['p_add'] == 'no')){
			$rs['p_add'] = 'yes';
		}
		$this->assign('_rslist',$list);
		$this->assign('_rs',$rs);
		$this->assign('_ptype',$rs['p_type']);
		$this->assign('_param_edit',($rs['p_add'] == 'yes' ? true : false));
		$file = $appid == 'admin' ? $this->dir_phpok.'form/html/param_admin_tpl.html' : $this->dir_phpok.'form/html/param_www_tpl.html';
		if(!is_file($file)){
			$file = $this->dir_phpok.'form/html/param_admin_tpl.html';
		}
		return $this->fetch($file,'abs-file');
	}

	public function phpok_get($rs,$appid="admin")
	{
		if(!$rs){
			return false;
		}
		if($rs['ext']){
			$ext = is_string($rs['ext']) ? unserialize($rs['ext']) : $rs['ext'];
		}
		$list = array();
		if($ext && $ext['p_type']){
			$list['title'] = $this->get($rs['identifier'].'_title');
			if($list['title'] && count($list['title'])>0){
				$tmp = $GLOBALS['app']->get($rs['identifier'].'_body');
				if($tmp){
					$list['content'] = array_chunk($tmp,count($list['title']));
				}
				return serialize($list);
			}
			return false;
		}else{
			$list['title'] = $this->get($rs['identifier'].'_title');
			$list['content'] = $this->get($rs['identifier'].'_body');
			return serialize($list);
		}
	}

	public function phpok_show($rs,$appid="admin")
	{
		if(!$rs || !$rs['content']){
			return false;
		}
		$info = is_string($rs['content']) ? unserialize($rs['content']) : $rs['content'];
		if(!$info || !$info['title'] || !is_array($info['title'])){
			return false;
		}
		$ext = false;
		if($rs['ext']){
			if(is_string($rs['ext'])){
				$ext = unserialize($rs['ext']);
			}else{
				$ext = $rs['ext'];
			}
		}
		if($appid == 'admin'){
			$html = '';
			if($ext && $ext['p_type']){
				foreach(($info['content'] ? $info['content'] : array()) as $key=>$value){
					$tmp = '';
					foreach($info['title'] as $k=>$v){
						if($tmp){
							$tmp .= " / ";
						}
						$tmp .= $v.': '.$value[$k];
					}
					$html .= '<div>'.$tmp.'</div>';
				}
			}else{
				if(!$info['title']){
					$info['title'] = array();
				}
				foreach($info['title'] as $key=>$value){
					$html .= '<div>'.$value.'：'.$info['content'][$key].'</div>';
				}
			}
			if(!$html){
				$html = '无 <b>'.implode("/",$info['title']).'</b> 内容';
			}

			$html = '<input type="button" value="'.implode("/",$info['title']).'" class="layui-btn" onclick="$.dialog.alert(\''.$html.'\');" />';
			return $html;
		}else{
			
			if($ext && $ext['p_type']){
				return $info;
			}else{
				$list = array();
				if(!$info['title']){
					$info['title'] = array();
				}
				foreach($info['title'] as $key=>$value){
					$list[$value] = $info['content'][$key];
				}
				return $list;
			}
		}
	}
}
<?php
/**
 * 购物车
 * @package phpok\www
 * @作者 qinggan <admin@phpok.com>
 * @版权 2015-2016 深圳市锟铻科技有限公司
 * @主页 http://www.phpok.com
 * @版本 4.x
 * @授权 http://www.phpok.com/lgpl.html PHPOK开源授权协议：GNU Lesser General Public License
 * @时间 2016年08月17日
**/

if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class cart_control extends phpok_control
{
	/**
	 * 购物车ID，该ID将贯穿整个购物过程
	**/
	private $cart_id = 0;
	
	/**
	 * 构造函数
	**/
	public function __construct()
	{
		parent::control();
		$this->cart_id = $this->model('cart')->cart_id($this->session->sessid(),$this->session->val('user_id'));
	}

	/**
	 * 购物车内容，留空读取cart_tip模板信息提示
	**/
	public function index_f()
	{
		//取得购物车产品列表
		$rslist = $this->model('cart')->get_all($this->cart_id);
		if(!$rslist){
			$this->model('site')->site_id($this->site['id']);
			$tplfile = $this->model('site')->tpl_file($this->ctrl,'tip');
			if(!$tplfile){
				$tplfile = 'cart_tip';
			}
	    	$this->view($tplfile);
		}
		$this->assign("rslist",$rslist);
		$totalprice = 0;
		$_date = date("Ymd",$this->time);
		foreach($rslist as $key=>$value){
			$totalprice += price_format_val($value['price'] * $value['qty'],$this->site['currency_id']);
			$value['_checked'] = ($value['dateline'] && date("Ymd",$value['dateline']) == $_date) ? true : false;
			$rslist[$key] = $value;
		}
		$price = price_format($totalprice,$this->site['currency_id']);
		$this->assign('price',$price);
		$this->model('site')->site_id($this->site['id']);
		$tplfile = $this->model('site')->tpl_file($this->ctrl,$this->func);
		if(!$tplfile){
			$tplfile = 'cart_index';
		}
		$this->view($tplfile);
	}

	/**
	 * 购物车产品加入成功后，跳转的页面
	 * @参数 $id 加入成功后返回的 qinggan_cart_product 表里的主键ID
	 * @参数 $product_id 产品，即 qinggan_list 表中的ID，在购物车里，统一叫产品ID
	**/
	public function success_f()
	{
		$product_id = $this->get('product_id','int');
		$id = $this->get('id','int');
		$this->assign('product_id',$product_id);
		$this->assign('id',$id);
		$tplfile = $this->model('site')->tpl_file($this->ctrl,$this->func);
		if(!$tplfile){
			$tplfile = 'cart_success';
		}
		$this->view($tplfile);
	}

	/**
	 * 购物车结算页，生成订单并进行支付
	**/
	public function checkout_f()
	{
		$id = $this->get('id','int');
		if(!$id){
			$this->error(P_Lang('未指定要结算的产品ID'),$this->url('cart'));
		}
		if($id && !is_array($id)){
			$id = explode(",",$id);
		}
		foreach($id as $key=>$value){
			if(!$value || !trim($value) || !intval($value)){
				unset($id[$key]);
			}
		}
		//定义要结算的产品ID
		$this->assign('id',implode(",",$id));
		$rslist = $this->model('cart')->get_all($this->cart_id,$id);
		if(!$rslist){
			$this->error(P_Lang('您的购物车里没有任何产品'),$this->url);
		}
		if($this->session->val('user_id')){
			$user_rs = $this->model('user')->get_one($this->session->val('user_id'));
			$this->assign('user',$user_rs);
		}
		$totalprice = 0;
		foreach($rslist as $key=>$value){
			$totalprice += price_format_val($value['price'] * $value['qty']);
		}
		$this->assign('product_price',price_format($totalprice,$this->site['currency_id']));
		$this->assign("rslist",$rslist);
		//检测购物车是否需要使用地址
		$is_virtual = true;
		foreach($rslist as $key=>$value){
			if(!$value['is_virtual']){
				$is_virtual = false;
			}
		}
		$this->assign('is_virtual',$is_virtual);
		
		if($is_virtual && $user_rs){
			$address = array('mobile'=>$user_rs['mobile'],'email'=>$user_rs['email']);
			$this->assign('address',$address);
		}
		if(!$is_virtual){
			$this->_address();
		}
		$pricelist = $this->model('site')->price_status_all(true);
		if($pricelist){
			foreach($pricelist as $key=>$value){
				if(!$value['status']){
					unset($pricelist[$key]);
					continue;
				}
				if($value['identifier'] == 'product'){
					$value['price'] = price_format($totalprice,$this->site['currency_id']);
					$value['price_val'] = $totalprice;
					$pricelist[$key] = $value;
				}
				if($value['identifier'] == 'shipping'){
					if($is_virtual){
						unset($pricelist[$key]);
						continue;
					}
					if($this->tpl->val('address')){
						$freight_price = $this->_freight();
						if(!$freight_price){
							unset($pricelist[$key]);
							continue;
						}
						$value['price'] = price_format($freight_price,$this->site['currency_id']);
						$value['price_val'] = $freight_price;
						$pricelist[$key] = $value;
					}
				}
				if($value['identifier'] == 'discount'){
					unset($pricelist[$key]);
					continue;
				}
			}
		}
		$this->assign('pricelist',$pricelist);
		if($freight_price){
			$price = price_format(($totalprice+$freight_price),$this->site['currency_id']);
			$price_val = price_format_val(($totalprice+$freight_price),$this->site['currency_id']);
		}else{
			$price = price_format($totalprice,$this->site['currency_id']);
			$price_val = price_format_val($totalprice,$this->site['currency_id']);
		}
		$this->assign('price',$price);
		$this->assign('price_val',$price_val);
		//支付方式
		$paylist = $this->model('payment')->get_all($this->site['id'],1,($this->is_mobile ? 1 : 0));
		$this->assign("paylist",$paylist);
		if($this->session->val('user_id')){
			$wlist = $this->model('order')->balance($this->session->val('user_id'));
			if($wlist){
				if($wlist['balance']){
					$this->assign('balance',$wlist['balance']);
				}
				if($wlist['integral']){
					$this->assign('integral',$wlist['integral']);
				}
			}
		}
		$this->model('site')->site_id($this->site['id']);
		$tplfile = $this->model('site')->tpl_file($this->ctrl,$this->func);
		if(!$tplfile){
			$tplfile = 'cart_checkout';
		}
		$this->view($tplfile);
	}

	/**
	 * 会员购买商品最后填写的地址
	**/
	private function _address()
	{
		if(!$this->session->val('user_id')){
			$this->assign('pca_rs',form_edit('pca','','pca'));
			return true;
		}
		$condition = "a.user_id='".$this->session->val('user_id')."'";
		$addresslist = $this->model('address')->get_list($condition,0,30);
		if($addresslist){
			$first = $address_id = 0;
			foreach($addresslist as $key=>$value){
				if($key<1){
					$first = $value['id'];
				}
				if($value['is_default']){
					$address_id = $value['id'];
					break;
				}
			}
			if(!$address_id && $first){
				$address_id = $first;
			}
			$this->assign('address_id',$address_id);
			$this->assign('address_list',$addresslist);
		}
	}

	/**
	 * 计算运费
	 * @参数 $rslist 购物车里的产品列表
	 * @参数 $address 数组，地址
	 * @返回 false 或 运费，未格式化
	**/
	private function _freight($rslist='',$address='')
	{
		if(!$rslist){
			$rslist = $this->tpl->val('rslist');
			if(!$rslist){
				return false;
			}
		}
		if(!$rslist || !is_array($rslist)){
			return false;
		}
		if(!$address){
			$address = $this->tpl->val('address');
			if(!$address){
				return false;
			}
		}
		if(!$address || !is_array($address)){
			return false;
		}
		$weight = $volume = $total = 0;
		foreach($rslist as $key=>$value){
			$weight += floatval($value['weight'] * $value['qty']);
			$volume += floatval($value['volume'] * $value['qty']);
			$total += $value['qty'];
		}
		$data = array('weight'=>$weight,'number'=>$total,'volume'=>$volume);
		return $this->model('cart')->freight_price($data,$address['province'],$address['city']);
	}

	public function price_f()
	{
		$is_virtual = true;
		$rslist = $this->model('cart')->get_all($this->cart_id);
		if(!$rslist){
			$this->error(P_Lang('购物车是空的'));
		}
		$province = $this->get('province');
		$city = $this->get('city');
		$freight_price = $product_price = 0;
		foreach($rslist AS $key=>$value){
			if(!$value['is_virtual']){
				$is_virtual = false;
			}
			$product_price += price_format_val($value['price'] * $value['qty']);
		}
		if($province && $city && !$is_virtual){
			$address = array('province'=>$province,'city'=>$city);
			$freight_price = $this->_freight($rslist,$address);
		}
		$pricelist = $this->model('site')->price_status_all();
		$price = $product_price;
		if($pricelist){
			foreach($pricelist as $key=>$value){
				if(!$value['status'] || $key == 'discount'){
					unset($pricelist[$key]);
					continue;
				}
				if($key == 'product'){
					$value['price'] = price_format($product_price);
					$value['price_val'] = $product_price;
					$pricelist[$key] = $value;
				}
				if($key == 'shipping'){
					if($is_virtual){
						unset($pricelist[$key]);
						continue;
					}
					if($freight_price){
						$value['price'] = price_format($freight_price,$this->site['currency_id']);
						$value['price_val'] = $freight_price;
						$pricelist[$key] = $value;
						$price += $freight_price;
					}
				}
			}
		}
		$data = array('pricelist'=>$pricelist,'price'=>price_format($price));
		$this->success($data);
	}

	//计算运费
	public function freight_f()
	{
		if(!$_SESSION['cart']){
			$this->json(P_Lang('您的购物车里没有任何产品'));
		}
		unset($_SESSION['cart']['freight_price']);
		$price_zero = price_format('0.00',$this->site['currency_id']);
		if($_SESSION['cart']['address_id'] == 'email'){
			$this->json($price_zero,true);
		}
		$rslist = $this->model('cart')->get_all($this->cart_id);
		if(!$rslist){
			$this->json(P_Lang('您的购物车里没有任何产品'));
		}
		$weight = $volume = $pid = $total = 0;
		foreach($rslist as $key=>$value){
			$pid = $value['project_id'];
			$weight += floatval($value['weight'] * $value['qty']);
			$volume += floatval($value['volume'] * $value['qty']);
			$total += $value['qty'];
			if($value['ext'] && $value['attrlist']){
				$ext = explode(",",$value['ext']);
				foreach($value['attrlist'] as $k=>$v){
					foreach($v['rslist'] as $kk=>$vv){
						if(in_array($vv['id'],$ext)){
							$weight += floatval($vv['weight']);
							$volume += floatval($vv['volume']);
						}
					}
				}
			}
		}
		//读取项目信息
		$project = $this->model('project')->get_one($pid,false);
		if(!$project || !$project['freight']){
			$this->json($price_zero,true);
		}
		$freight = $this->model('freight')->get_one($project['freight']);
		if(!$freight){
			$this->json($price_zero,true);
		}
		$param_val = false;
		if($freight['type'] == 'weight'){
			$param_val = $weight;
		}elseif($freight['type'] == 'volume'){
			$param_val = $volume;
		}elseif($freight['type'] == 'number'){
			$param_val = $total;
		}elseif($freight['type'] == 'fixed'){
			$param_val = 'fixed';
		}
		$address = $this->model('user')->address_one($_SESSION['cart']['address_id']);
		if(!$address || $address['user_id'] != $_SESSION['user_id']){
			$this->json($price_zero,true);
		}
		$zone_id = $this->model('freight')->zone_id($freight['id'],$address['province'],$address['city']);
		if(!$zone_id){
			$this->json($price_zero,true);
		}
		$val = $this->model('freight')->price_one($zone_id,$param_val);
		if($val){
			if(strpos($val,'N') !== false){
				$val = str_replace("N",$param_val,$val);
				eval("\$val = $val;");
			}
			$_SESSION['cart']['freight_price'] = $val;
			$this->json(price_format($val,$this->site['currency_id']),true);
		}
		$this->json($price_zero,true);
	}

	public function all_price_f()
	{
		$price = $_SESSION['cart']['totalprice'];
		if($_SESSION['cart']['freight_price']){
			$price += $_SESSION['cart']['freight_price'];
		}
		$this->json(price_format($price,$this->site['currency_id']),true);
	}

}
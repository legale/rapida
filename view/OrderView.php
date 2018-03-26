<?PHP

/**
 * Корзина покупок
 * Этот класс использует шаблон cart.tpl
 *
 */
 
require_once('View.php');

class OrderView extends View
{
	public function __construct()
	{
		parent::__construct();
		$this->design->smarty->registerPlugin("function", "checkout_form", array($this, 'checkout_form'));
	}

	//////////////////////////////////////////
	// Основная функция
	//////////////////////////////////////////
	function fetch()
	{
		// Скачивание файла
		if($this->request->get('file'))
		{
			return $this->download();
		}
		else
		{
			return $this->fetch_order();
		}
	
	}
	
	function fetch_order()
	{
		if( isset($this->coMaster->uri_arr['path']['url']) )
			$order = $this->orders->get_order($this->coMaster->uri_arr['path']['url']);
		elseif(!empty($_SESSION['order_id']))
			$order = $this->orders->get_order(intval($_SESSION['order_id']));
		else
			return false;
			
		if(!$order)
			return false;
						
		$purchases = $this->orders->get_purchases(array('order_id'=>intval($order['id'])));
		if(!$purchases)
			return false;
			
		if($this->request->method('post'))
		{
			if($payment_method_id = $this->request->post('payment_method_id', 'integer'))
			{
				$this->orders->update_order($order['id'], array('payment_method_id'=>$payment_method_id));
				$order = $this->orders->get_order((integer)$order['id']);
			}
			elseif($this->request->post('reset_payment_method'))
			{
				$this->orders->update_order($order['id'], array('payment_method_id'=>null));
				$order = $this->orders->get_order((integer)$order['id']);
			}
		}
		
		$products_ids = array();
		$variants_ids = array();
		foreach($purchases as $purchase)
		{
			$products_ids[] = $purchase['product_id'];
			$variants_ids[] = $purchase['variant_id'];
		}
		if($products = $this->products->get_products(array('id'=>$products_ids))){
			$variants = $this->variants->get_variants(array('grouped'=>'product_id', 'id'=>$variants_ids));
		}
		
		foreach($purchases as $k=>$pr){
			if(!empty($products[$pr['product_id']])){
				$purchases[$k]['product'] = $products[$pr['product_id']];
			}
			if(!empty($variants[$pr['product_id']])){
				$purchases[$k]['variants'] = $variants[$pr['product_id']];
			}
		}
		
		// Способ доставки
		$delivery = $this->delivery->get_delivery($order['delivery_id']);
		$this->design->assign('delivery', $delivery);
			
		$this->design->assign('order', $order);
		$this->design->assign('purchases', $purchases);

		// Способ оплаты
		if($order['payment_method_id'])
		{
			$payment_method = $this->payment->get_payment_method($order['payment_method_id']);
		} else {
			$payment_method = '';
		}
		$this->design->assign('payment_method', $payment_method);
			
		// Варианты оплаты
		$payment_methods = $this->payment->get_payment_methods(array('delivery_id'=>$order['delivery_id'], 'enabled'=>1));
		$this->design->assign('payment_methods', $payment_methods);

		// Все валюты
		$this->design->assign('all_currencies', $this->money->get_currencies());

		
		
		// Выводим заказ
		return $this->body = $this->design->fetch('order.tpl');
	}
	
	private function download()
	{
		$file = $this->request->get('file');
		
		if(!$url = $this->request->get('url', 'string'))
			return false;
			
		$order = $this->orders->get_order((string)$url);
		if(!$order)
			return false;
			
		if(!$order['paid'])
			return false;
						
		// Проверяем, есть ли такой файл в покупках	
		$query = $this->db->placehold("SELECT p.id FROM __purchases p, __variants v WHERE p.variant_id=v.id AND p.order_id=? AND v.attachment=?", $order['id'], $file);		$this->db->query($query);
		if($this->db->num_rows()==0)
			return false;
		
		header("Content-type: application/force-download");
		header("Content-Disposition: attachment; filename=\"$file\"");
		header("Content-Length: ".filesize($this->config->root_dir.$this->config->downloads_dir.$file));
		readfile($this->config->root_dir.$this->config->downloads_dir.$file);
		
		exit();
	}
	
	public function checkout_form($params, $smarty)
	{
		if(!isset($params['module'])){
			$params['module'] = '';
		}
		if(!isset($params['order_id'])){
			$params['order_id'] = '';
		}
		if(!isset($params['button_text'])){
			$params['button_text'] = '';
		}
		$module_name = preg_replace("/[^A-Za-z0-9]+/", "", $params['module']);

		$form = '';
		if(!empty($module_name) && is_file("payment/$module_name/$module_name.php"))
		{
			include_once("payment/$module_name/$module_name.php");
			$module = new $module_name();
			$form = $module->checkout_form($params['order_id'], $params['button_text']);
		}
		return $form;
	}

}

<?php
 
require_once('View.php');

class WishlistView extends View
{

	public function fetch()
	{
		print_r($_COOKIE);
		if(!empty($_COOKIE['wishlist_products'])){
			$pids = explode(',', $_COOKIE['wishlist_products']);
			// Выбираем товары из базы
			$products = $this->products->get_products(array('product_id' => $ids));
		}
		
		if( !empty($products) )
		{
			$pids = array_keys($products);

			$variants = $this->variants->get_variants(array('grouped' => 'product_id', 
			'product_id'=>$pids, 'in_stock'=>true));
			

			if(is_array($products)){
				foreach($products as $pid=>&$product){
					$product['variants'] = is_array($variants[$pid]) ? $variants[$pid] : array();
				}
			}

			//~ print "<PRE>";
			//~ print var_export($products, true);
			//~ print "</PRE>";
			
			
			$this->design->assign('products', $products);
			$this->design->assign('wishlist', true);
 		}
	
		// Скидка
		$discount = 0;
		if(isset($_SESSION['user_id']) && $user = $this->users->get_user(intval($_SESSION['user_id']))){
			$discount = $user['discount'];
		}

		
		if($this->page)
		{
			$this->design->assign('meta_title', $this->page['meta_title']);
			$this->design->assign('meta_keywords', $this->page['meta_keywords']);
			$this->design->assign('meta_description', $this->page['meta_description']);
		}	

		return $this->design->fetch('products.tpl');
	}
	
}

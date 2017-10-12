{* Информера корзины (отдаётся аяксом) *}

                  <h3>Корзина:</h3>    
            <div class="block-content">
				<div class="summary">
                            <p><strong title="cart">{$cart->total_products} шт.</strong> -
				<span class="price">{$cart->total_price|convert} {$currency->sign|escape}</span></p>
            		</div>                        
           
              <div class="cart-content">                
                <div class="cart-indent">                    
                  <div class="cart-content-header">                        
                    <p class="subtotal">                                                            
                      <span class="label">Итого:
                      </span> 
                      <span class="price">{$cart->total_price|convert} {$currency->sign|escape}
                      </span>                                                                                    
                    </p>                                                                                                                                                
                   
                  </div>                    
                  <ol id="cart-sidebar" class="mini-products-list">
                  {foreach from=$cart->purchases item=purchase}                                             
                    <li class="item">
                    {*	
                      <div class="product-control-buttons">		
                        <a href="cart/remove/{$purchase->variant->id}" title="Удалить из корзины" class="btn-remove">Удалить из корзины</a>									
                      </div>
                    *}   
		{$image = $purchase->product->images|first}
		{if $image}
		<a href="products/{$purchase->product->url}" class="product-image" title="{$product->name|escape}"><img src="{$image->filename|resize:50:50}" alt="{$product->name|escape}"></a>
		{/if}                                 
   	
                      <p class="product-name">
		<a href="products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
		{$purchase->variant->name|escape}
                      </p>    
                      <div class="product-details">        <strong>{$purchase->amount}</strong> x                                      
                        <span class="price">{($purchase->variant->price)|convert} {$currency->sign}
                        </span>                                           
                      </div>
                    </li> 
                    {/foreach}                                                                                   
                  </ol>                       
                  <div class="actions">                        
                    <button type="button" title="Оформить заказ" class="button" onclick="setLocation('cart')">
                      <span>
                        <span>Оформить заказ
                        </span>
                      </span>
                    </button>                                           
                  </div>                    
<script type="text/javascript">decorateList('cart-sidebar', 'none-recursive')</script>                
                </div>            
              </div>            
              <p class="mini-cart">
                <strong title="cart">{$cart->total_products}</strong> 
              </p>        
            </div>
            
          
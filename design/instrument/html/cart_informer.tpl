{* Информера корзины (отдаётся аяксом) *}

  <div class="heading">
  
  	<a>	
        <b>Корзина:</b>
    	<span class="sc-button"></span>   	
       
    	<span id="cart-total">{$cart->total_products} {$cart->total_products|plural:'товар':'товаров':'товара'} - <strong>{$cart->total_price|convert} {$currency->sign|escape}</strong></span>
       
		<span class="clear"></span>
        </a>
    </div>
    
  <div class="content">
  {if $cart->total_products>0}
  	
         <span class="latest-added">Товары в вашей корзине:</span>
    <div class="mini-cart-info">
      <table class="cart">
                <tbody>
                {foreach from=$cart->purchases item=purchase}
                <tr>
          <td class="image">
		{$image = $purchase->product->images|first}
		{if $image}
		<a href="products/{$purchase->product->url}"><img src="{$image->filename|resize:50:50}" alt="{$product->name|escape}"></a>
		{/if}
            </td>
          <td class="name">
          <a href="products/{$purchase->product->url}">{$purchase->product->name|escape}</a>
		{$purchase->variant->name|escape}
		            <div>
                          </div>
              <span class="total">{$purchase->variant->price|convert}&nbsp;{$currency->sign}</span>            
              <span class="quantity">x&nbsp;{$purchase->amount}</span>
              
              </td>

        </tr>
        {/foreach}
                      </tbody></table>
    </div>
    <div>
      <table class="total">

          <td align="right" class="total-right"><b>Итого:</b></td>
          <td align="left" class="total-left"><span class="t-price">{$cart->total_price|convert} {$currency->sign|escape}</span></td>
        </tr>
              </tbody></table>
    </div>
    <div class="checkout">
    <a class="button" href="cart"><span>Оформить</span></a>
    </div>
	
	
{else}  	
        <div class="empty">Корзина пуста!</div>
{/if}          
      </div>
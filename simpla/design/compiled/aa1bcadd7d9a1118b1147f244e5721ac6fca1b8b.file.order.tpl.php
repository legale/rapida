<?php /* Smarty version Smarty-3.1.18, created on 2017-10-12 00:42:28
         compiled from "simpla\design\html\order.tpl" */ ?>
<?php /*%%SmartyHeaderCode:119891649159de904470d602-21610286%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'aa1bcadd7d9a1118b1147f244e5721ac6fca1b8b' => 
    array (
      0 => 'simpla\\design\\html\\order.tpl',
      1 => 1492708202,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '119891649159de904470d602-21610286',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'manager' => 0,
    'order' => 0,
    'keyword' => 0,
    'prev_order' => 0,
    'next_order' => 0,
    'message_error' => 0,
    'message_success' => 0,
    'labels' => 0,
    'l' => 0,
    'order_labels' => 0,
    'user' => 0,
    'purchases' => 0,
    'purchase' => 0,
    'image' => 0,
    'v' => 0,
    'currency' => 0,
    'settings' => 0,
    'loop' => 0,
    'subtotal' => 0,
    'deliveries' => 0,
    'd' => 0,
    'delivery' => 0,
    'payment_methods' => 0,
    'pm' => 0,
    'payment_method' => 0,
    'payment_currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.18',
  'unifunc' => 'content_59de9044ae9b64_59635014',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59de9044ae9b64_59635014')) {function content_59de9044ae9b64_59635014($_smarty_tpl) {?><?php if (!is_callable('smarty_function_math')) include 'D:\\openserver5.2.7\\OSPanel\\domains\\startup.my\\Smarty\\libs\\plugins\\function.math.php';
?>
<?php $_smarty_tpl->_capture_stack[0][] = array('tabs', null, null); ob_start(); ?>
	<?php if (in_array('orders',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
		<li <?php if ($_smarty_tpl->tpl_vars['order']->value->status==0) {?>class="active"<?php }?>><a href="index.php?module=OrdersAdmin&status=0">Новые</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['order']->value->status==1) {?>class="active"<?php }?>><a href="index.php?module=OrdersAdmin&status=1">Приняты</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['order']->value->status==2) {?>class="active"<?php }?>><a href="index.php?module=OrdersAdmin&status=2">Выполнены</a></li>
		<li <?php if ($_smarty_tpl->tpl_vars['order']->value->status==3) {?>class="active"<?php }?>><a href="index.php?module=OrdersAdmin&status=3">Удалены</a></li>
	<?php if ($_smarty_tpl->tpl_vars['keyword']->value) {?>
	<li class="active"><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersAdmin','keyword'=>$_smarty_tpl->tpl_vars['keyword']->value,'id'=>null,'label'=>null),$_smarty_tpl);?>
">Поиск</a></li>
	<?php }?>
	<?php }?>
	<?php if (in_array('labels',$_smarty_tpl->tpl_vars['manager']->value->permissions)) {?>
	<li><a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('module'=>'OrdersLabelsAdmin','keyword'=>null,'id'=>null,'page'=>null,'label'=>null),$_smarty_tpl);?>
">Метки</a></li>
	<?php }?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>


<?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable("Заказ №".((string)$_smarty_tpl->tpl_vars['order']->value->id), null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php } else { ?>
<?php $_smarty_tpl->tpl_vars['meta_title'] = new Smarty_variable('Новый заказ', null, 1);
if ($_smarty_tpl->parent != null) $_smarty_tpl->parent->tpl_vars['meta_title'] = clone $_smarty_tpl->tpl_vars['meta_title'];?>
<?php }?>

<!-- Основная форма -->
<form method=post id=order enctype="multipart/form-data">
<input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">

<div id="name">
	<input name=id type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"/> 
	<h1><?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>Заказ №<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>Новый заказ<?php }?>
	<select class=status name="status">
		<option value='0' <?php if ($_smarty_tpl->tpl_vars['order']->value->status==0) {?>selected<?php }?>>Новый</option>
		<option value='1' <?php if ($_smarty_tpl->tpl_vars['order']->value->status==1) {?>selected<?php }?>>Принят</option>
		<option value='2' <?php if ($_smarty_tpl->tpl_vars['order']->value->status==2) {?>selected<?php }?>>Выполнен</option>
		<option value='3' <?php if ($_smarty_tpl->tpl_vars['order']->value->status==3) {?>selected<?php }?>>Удален</option>
	</select>
	</h1>
	<a href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('view'=>'print','id'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>
" target="_blank"><img src="./design/images/printer.png" name="export" title="Печать заказа"></a>


	<div id=next_order>
		<?php if ($_smarty_tpl->tpl_vars['prev_order']->value) {?>
		<a class=prev_order href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('id'=>$_smarty_tpl->tpl_vars['prev_order']->value->id),$_smarty_tpl);?>
">←</a>
		<?php }?>
		<?php if ($_smarty_tpl->tpl_vars['next_order']->value) {?>
		<a class=next_order href="<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0][0]->url_modifier(array('id'=>$_smarty_tpl->tpl_vars['next_order']->value->id),$_smarty_tpl);?>
">→</a>
		<?php }?>
	</div>
		
</div> 


<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
<!-- Системное сообщение -->
<div class="message message_error">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_error']->value=='error_closing') {?>Нехватка товара на складе<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?></span>
	<?php if ($_GET['return']) {?>
	<a class="button" href="<?php echo $_GET['return'];?>
">Вернуться</a>
	<?php }?>
</div>
<!-- Системное сообщение (The End)-->
<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value) {?>
<!-- Системное сообщение -->
<div class="message message_success">
	<span class="text"><?php if ($_smarty_tpl->tpl_vars['message_success']->value=='updated') {?>Заказ обновлен<?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value=='added') {?>Заказ добавлен<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['message_success']->value;?>
<?php }?></span>
	<?php if ($_GET['return']) {?>
	<a class="button" href="<?php echo $_GET['return'];?>
">Вернуться</a>
	<?php }?>
</div>
<!-- Системное сообщение (The End)-->
<?php }?>



<div id="order_details">
	<h2>Детали заказа <a href='#' class="edit_order_details"><img src='design/images/pencil.png' alt='Редактировать' title='Редактировать'></a></h2>
	
	<div id="user">
	<ul class="order_details">
		<li>
			<label class=property>Дата</label>
			<div class="edit_order_detail view_order_detail">
			<?php echo $_smarty_tpl->tpl_vars['order']->value->date;?>
 <?php echo $_smarty_tpl->tpl_vars['order']->value->time;?>

			</div>
		</li>
		<li>
			<label class=property>Имя</label> 
			<div class="edit_order_detail" style='display:none;'>
				<input name="name" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
			</div>
			<div class="view_order_detail">
				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->name, ENT_QUOTES, 'UTF-8', true);?>

			</div>
		</li>
		<li>
			<label class=property>Email</label>
			<div class="edit_order_detail" style='display:none;'>
				<input name="email" class="simpla_inp" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->email, ENT_QUOTES, 'UTF-8', true);?>
" />
			</div>
			<div class="view_order_detail">
				<a href="mailto:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->email, ENT_QUOTES, 'UTF-8', true);?>
?subject=Заказ%20№<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->email, ENT_QUOTES, 'UTF-8', true);?>
</a>
			</div>
		</li>
		<li>
			<label class=property>Телефон</label>
			<div class="edit_order_detail" style='display:none;'>
				<input name="phone" class="simpla_inp " type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->phone, ENT_QUOTES, 'UTF-8', true);?>
" />
			</div>
			<div class="view_order_detail">
				<?php if ($_smarty_tpl->tpl_vars['order']->value->phone) {?>
				<span class="ip_call" data-phone="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->phone, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->phone, ENT_QUOTES, 'UTF-8', true);?>
</span><?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->phone, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>
			</div>
		</li>
		<li>
			<label class=property>Адрес <a href='http://maps.yandex.ru/' id=address_link target=_blank><img align=absmiddle src='design/images/map.png' alt='Карта в новом окне' title='Карта в новом окне'></a></label>
			<div class="edit_order_detail" style='display:none;'>
				<textarea name="address"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->address, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
			</div>
			<div class="view_order_detail">
				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->address, ENT_QUOTES, 'UTF-8', true);?>

			</div>
		</li>
		<li>
			<label class=property>Комментарий пользователя</label>
			<div class="edit_order_detail" style='display:none;'>
			<textarea name="comment"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->comment, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
			</div>
			<div class="view_order_detail">
				<?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->comment, ENT_QUOTES, 'UTF-8', true));?>

			</div>
		</li>
	</ul>
	</div>

	
	<?php if ($_smarty_tpl->tpl_vars['labels']->value) {?>
	<div class='layer'>
	<h2>Метка</h2>
	<!-- Метки -->
	<ul>
		<?php  $_smarty_tpl->tpl_vars['l'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['l']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['labels']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['l']->key => $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->_loop = true;
?>
		<li>
		<label for="label_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
">
		<input id="label_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" type="checkbox" name="order_labels[]" value="<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['l']->value->id,$_smarty_tpl->tpl_vars['order_labels']->value)) {?>checked<?php }?>>
		<span style="background-color:#<?php echo $_smarty_tpl->tpl_vars['l']->value->color;?>
;" class="order_label"></span>
		<?php echo $_smarty_tpl->tpl_vars['l']->value->name;?>

		</label>
		</li>
		<?php } ?>
	</ul>
	<!-- Метки -->
	</div>
	<?php }?>

	
	<div class='layer'>
	<h2>Покупатель <a href='#' class="edit_user"><img src='design/images/pencil.png' alt='Редактировать' title='Редактировать'></a> <?php if ($_smarty_tpl->tpl_vars['user']->value) {?><a href="#" class='delete_user'><img src='design/images/delete.png' alt='Удалить' title='Удалить'></a><?php }?></h2>
		<div class='view_user'>
		<?php if (!$_smarty_tpl->tpl_vars['user']->value) {?>
			Не зарегистрирован
		<?php } else { ?>
			<a href='index.php?module=UserAdmin&id=<?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
' target=_blank><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a> (<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->email, ENT_QUOTES, 'UTF-8', true);?>
)
		<?php }?>
		</div>
		<div class='edit_user' style='display:none;'>
		<input type=hidden name=user_id value='<?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
'>
		<input type=text id='user' class="input_autocomplete" placeholder="Выберите пользователя">
		</div>
	</div>
	

	
	<div class='layer'>
	<h2>Примечание <a href='#' class="edit_note"><img src='design/images/pencil.png' alt='Редактировать' title='Редактировать'></a></h2>
	<ul class="order_details">
		<li>
			<div class="edit_note" style='display:none;'>
				<label class=property>Ваше примечание (не видно пользователю)</label>
				<textarea name="note"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
			</div>
			<div class="view_note" <?php if (!$_smarty_tpl->tpl_vars['order']->value->note) {?>style='display:none;'<?php }?>>
				<label class=property>Ваше примечание (не видно пользователю)</label>
				<div class="note_text"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</div>
			</div>
		</li>
	</ul>
	</div>
		
</div>


<div id="purchases">
 
	<div id="list" class="purchases">
		<?php  $_smarty_tpl->tpl_vars['purchase'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['purchase']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['purchases']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['purchase']->key => $_smarty_tpl->tpl_vars['purchase']->value) {
$_smarty_tpl->tpl_vars['purchase']->_loop = true;
?>
		<div class="row">
			<div class="image cell">
				<input type=hidden name=purchases[id][<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
] value='<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
'>
				<?php $_smarty_tpl->tpl_vars['image'] = new Smarty_variable($_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['first'][0][0]->first_modifier($_smarty_tpl->tpl_vars['purchase']->value->product->images), null, 0);?>
				<?php if ($_smarty_tpl->tpl_vars['image']->value) {?>
				<img class=product_icon src='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['resize'][0][0]->resize_modifier($_smarty_tpl->tpl_vars['image']->value->filename,35,35);?>
'>
				<?php }?>
			</div>
			<div class="purchase_name cell">
			
				<div class='purchase_variant'>				
				<span class=edit_purchase style='display:none;'>
				<select name=purchases[variant_id][<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
] <?php if (count($_smarty_tpl->tpl_vars['purchase']->value->product->variants)==1&&$_smarty_tpl->tpl_vars['purchase']->value->variant_name==''&&$_smarty_tpl->tpl_vars['purchase']->value->variant->sku=='') {?>style='display:none;'<?php }?>>					
		    	<?php if (!$_smarty_tpl->tpl_vars['purchase']->value->variant) {?><option price='<?php echo $_smarty_tpl->tpl_vars['purchase']->value->price;?>
' amount='<?php echo $_smarty_tpl->tpl_vars['purchase']->value->amount;?>
' value=''><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->variant_name, ENT_QUOTES, 'UTF-8', true);?>
 <?php if ($_smarty_tpl->tpl_vars['purchase']->value->sku) {?>(арт. <?php echo $_smarty_tpl->tpl_vars['purchase']->value->sku;?>
)<?php }?></option><?php }?>
				<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['purchase']->value->product->variants; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['v']->value->stock>0||$_smarty_tpl->tpl_vars['v']->value->id==$_smarty_tpl->tpl_vars['purchase']->value->variant->id) {?>
					<option price='<?php echo $_smarty_tpl->tpl_vars['v']->value->price;?>
' amount='<?php echo $_smarty_tpl->tpl_vars['v']->value->stock;?>
' value='<?php echo $_smarty_tpl->tpl_vars['v']->value->id;?>
' <?php if ($_smarty_tpl->tpl_vars['v']->value->id==$_smarty_tpl->tpl_vars['purchase']->value->variant_id) {?>selected<?php }?> >
					<?php echo $_smarty_tpl->tpl_vars['v']->value->name;?>

					<?php if ($_smarty_tpl->tpl_vars['v']->value->sku) {?>(арт. <?php echo $_smarty_tpl->tpl_vars['v']->value->sku;?>
)<?php }?>
					</option>
					<?php }?>
				<?php } ?>
				</select>
				</span>
				<span class=view_purchase>
					<?php echo $_smarty_tpl->tpl_vars['purchase']->value->variant_name;?>
 <?php if ($_smarty_tpl->tpl_vars['purchase']->value->sku) {?>(арт. <?php echo $_smarty_tpl->tpl_vars['purchase']->value->sku;?>
)<?php }?>			
				</span>
				</div>
		
				<?php if ($_smarty_tpl->tpl_vars['purchase']->value->product) {?>
				<a class="related_product_name" href="index.php?module=ProductAdmin&id=<?php echo $_smarty_tpl->tpl_vars['purchase']->value->product->id;?>
&return=<?php echo urlencode($_SERVER['REQUEST_URI']);?>
"><?php echo $_smarty_tpl->tpl_vars['purchase']->value->product_name;?>
</a>
				<?php } else { ?>
				<?php echo $_smarty_tpl->tpl_vars['purchase']->value->product_name;?>
				
				<?php }?>
			</div>
			<div class="price cell">
				<span class=view_purchase><?php echo $_smarty_tpl->tpl_vars['purchase']->value->price;?>
</span>
				<span class=edit_purchase style='display:none;'>
				<input type=text name=purchases[price][<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
] value='<?php echo $_smarty_tpl->tpl_vars['purchase']->value->price;?>
' size=5>
				</span>
				<?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>

			</div>
			<div class="amount cell">			
				<span class=view_purchase>
					<?php echo $_smarty_tpl->tpl_vars['purchase']->value->amount;?>
 <?php echo $_smarty_tpl->tpl_vars['settings']->value->units;?>

				</span>
				<span class=edit_purchase style='display:none;'>
					<?php if ($_smarty_tpl->tpl_vars['purchase']->value->variant) {?>
					<?php echo smarty_function_math(array('equation'=>"min(max(x,y),z)",'x'=>$_smarty_tpl->tpl_vars['purchase']->value->variant->stock+$_smarty_tpl->tpl_vars['purchase']->value->amount*($_smarty_tpl->tpl_vars['order']->value->closed),'y'=>$_smarty_tpl->tpl_vars['purchase']->value->amount,'z'=>$_smarty_tpl->tpl_vars['settings']->value->max_order_amount,'assign'=>"loop"),$_smarty_tpl);?>

					<?php } else { ?>
					<?php echo smarty_function_math(array('equation'=>"x",'x'=>$_smarty_tpl->tpl_vars['purchase']->value->amount,'assign'=>"loop"),$_smarty_tpl);?>

					<?php }?>
			        <select name=purchases[amount][<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
]>
						<?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['name'] = 'amounts';
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'] = (int) 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['loop']->value+1) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'] = ((int) 1) == 0 ? 1 : (int) 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop'];
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'] < 0)
    $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'] = max($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'] > 0 ? 0 : -1, $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start']);
else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'] = min($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop']-1);
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['total'] = min(ceil(($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'] > 0 ? $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['loop'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'] : $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start']+1)/abs($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'])), $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['max']);
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['amounts']['total']);
?>
							<option value="<?php echo $_smarty_tpl->getVariable('smarty')->value['section']['amounts']['index'];?>
" <?php if ($_smarty_tpl->tpl_vars['purchase']->value->amount==$_smarty_tpl->getVariable('smarty')->value['section']['amounts']['index']) {?>selected<?php }?>><?php echo $_smarty_tpl->getVariable('smarty')->value['section']['amounts']['index'];?>
 <?php echo $_smarty_tpl->tpl_vars['settings']->value->units;?>
</option>
						<?php endfor; endif; ?>
			        </select>
				</span>			
			</div>
			<div class="icons cell">		
				<?php if (!$_smarty_tpl->tpl_vars['order']->value->closed) {?>
					<?php if (!$_smarty_tpl->tpl_vars['purchase']->value->product) {?>
					<img src='design/images/error.png' alt='Товар был удалён' title='Товар был удалён' >
					<?php } elseif (!$_smarty_tpl->tpl_vars['purchase']->value->variant) {?>
					<img src='design/images/error.png' alt='Вариант товара был удалён' title='Вариант товара был удалён' >
					<?php } elseif ($_smarty_tpl->tpl_vars['purchase']->value->variant->stock<$_smarty_tpl->tpl_vars['purchase']->value->amount) {?>
					<img src='design/images/error.png' alt='На складе остал<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['purchase']->value->variant->stock,'ся','ось');?>
 <?php echo $_smarty_tpl->tpl_vars['purchase']->value->variant->stock;?>
 товар<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['purchase']->value->variant->stock,'','ов','а');?>
' title='На складе остал<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['purchase']->value->variant->stock,'ся','ось');?>
 <?php echo $_smarty_tpl->tpl_vars['purchase']->value->variant->stock;?>
 товар<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['plural'][0][0]->plural_modifier($_smarty_tpl->tpl_vars['purchase']->value->variant->stock,'','ов','а');?>
'  >
					<?php }?>
				<?php }?>
				<a href='#' class="delete" title="Удалить"></a>		
			</div>
			<div class="clear"></div>
		</div>
		<?php } ?>
		<div id="new_purchase" class="row" style='display:none;'>
			<div class="image cell">
				<input type=hidden name=purchases[id][] value=''>
				<img class=product_icon src=''>
			</div>
			<div class="purchase_name cell">
				<div class='purchase_variant'>				
					<select name=purchases[variant_id][] style='display:none;'></select>
				</div>
				<a class="purchase_name" href=""></a>
			</div>
			<div class="price cell">
				<input type=text name=purchases[price][] value='' size=5> <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>

			</div>
			<div class="amount cell">
	        	<select name=purchases[amount][]></select>
			</div>
			<div class="icons cell">
				<a href='#' class="delete" title="Удалить"></a>	
			</div>
			<div class="clear"></div>
		</div>
	</div>

 	<div id="add_purchase" <?php if ($_smarty_tpl->tpl_vars['purchases']->value) {?>style='display:none;'<?php }?>>
 		<input type=text name=related id='add_purchase' class="input_autocomplete" placeholder='Выберите товар чтобы добавить его'>
 	</div>
	<?php if ($_smarty_tpl->tpl_vars['purchases']->value) {?>
	<a href='#' class="dash_link edit_purchases">редактировать покупки</a>
	<?php }?>


	<?php if ($_smarty_tpl->tpl_vars['purchases']->value) {?>
	<div class="subtotal">
	Всего<b> <?php echo $_smarty_tpl->tpl_vars['subtotal']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</b>
	</div>
	<?php }?>

	<div class="block discount layer">
		<h2>Скидка</h2>
		<input type=text name=discount value='<?php echo $_smarty_tpl->tpl_vars['order']->value->discount;?>
'> <span class=currency>%</span>		
	</div>

	<div class="subtotal layer">
	С учетом скидки<b> <?php echo round(($_smarty_tpl->tpl_vars['subtotal']->value-$_smarty_tpl->tpl_vars['subtotal']->value*$_smarty_tpl->tpl_vars['order']->value->discount/100),2);?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</b>
	</div> 
	
	<div class="block discount layer">
		<h2>Купон<?php if ($_smarty_tpl->tpl_vars['order']->value->coupon_code) {?> (<?php echo $_smarty_tpl->tpl_vars['order']->value->coupon_code;?>
)<?php }?></h2>
		<input type=text name=coupon_discount value='<?php echo $_smarty_tpl->tpl_vars['order']->value->coupon_discount;?>
'> <span class=currency><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>		
	</div>

	<div class="subtotal layer">
	С учетом купона<b> <?php echo round(($_smarty_tpl->tpl_vars['subtotal']->value-$_smarty_tpl->tpl_vars['subtotal']->value*$_smarty_tpl->tpl_vars['order']->value->discount/100-$_smarty_tpl->tpl_vars['order']->value->coupon_discount),2);?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</b>
	</div> 
	
	<div class="block delivery">
		<h2>Доставка</h2>
				<select name="delivery_id">
				<option value="0">Не выбрана</option>
				<?php  $_smarty_tpl->tpl_vars['d'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['d']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['deliveries']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['d']->key => $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['d']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value->id==$_smarty_tpl->tpl_vars['delivery']->value->id) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['d']->value->name;?>
</option>
				<?php } ?>
				</select>	
				<input type=text name=delivery_price value='<?php echo $_smarty_tpl->tpl_vars['order']->value->delivery_price;?>
'> <span class=currency><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>
				<div class="separate_delivery">
					<input type=checkbox id="separate_delivery" name=separate_delivery value='1' <?php if ($_smarty_tpl->tpl_vars['order']->value->separate_delivery) {?>checked<?php }?>> <label  for="separate_delivery">оплачивается отдельно</label>
				</div>
	</div>

	<div class="total layer">
	Итого<b> <?php echo $_smarty_tpl->tpl_vars['order']->value->total_price;?>
 <?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</b>
	</div>
		
		
	<div class="block payment">
		<h2>Оплата</h2>
				<select name="payment_method_id">
				<option value="0">Не выбрана</option>
				<?php  $_smarty_tpl->tpl_vars['pm'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['pm']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_methods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['pm']->key => $_smarty_tpl->tpl_vars['pm']->value) {
$_smarty_tpl->tpl_vars['pm']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['pm']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['pm']->value->id==$_smarty_tpl->tpl_vars['payment_method']->value->id) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['pm']->value->name;?>
</option>
				<?php } ?>
				</select>
		
		<input type=checkbox name="paid" id="paid" value="1" <?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>checked<?php }?>> <label for="paid" <?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>class="green"<?php }?>>Заказ оплачен</label>		
	</div>

 
	<?php if ($_smarty_tpl->tpl_vars['payment_method']->value) {?>
	<div class="subtotal layer">
	К оплате<b> <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['convert'][0][0]->convert($_smarty_tpl->tpl_vars['order']->value->total_price,$_smarty_tpl->tpl_vars['payment_currency']->value->id);?>
 <?php echo $_smarty_tpl->tpl_vars['payment_currency']->value->sign;?>
</b>
	</div>
	<?php }?>


	<div class="block_save">
	<input type="checkbox" value="1" id="notify_user" name="notify_user">
	<label for="notify_user">Уведомить покупателя о состоянии заказа</label>

	<input class="button_green button_save" type="submit" name="" value="Сохранить" />
	</div>


</div>


</form>
<!-- Основная форма (The End) -->




<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

<script>
$(function() {

	// Раскраска строк
	function colorize()
	{
		$("#list div.row:even").addClass('even');
		$("#list div.row:odd").removeClass('even');
	}
	// Раскрасить строки сразу
	colorize();
	
	// Удаление товара
	$(".purchases a.delete").live('click', function() {
		 $(this).closest(".row").fadeOut(200, function() { $(this).remove(); });
		 return false;
	});
 

	// Добавление товара 
	var new_purchase = $('.purchases #new_purchase').clone(true);
	$('.purchases #new_purchase').remove().removeAttr('id');

	$("input#add_purchase").autocomplete({
  	serviceUrl:'ajax/add_order_product.php',
  	minChars:0,
  	noCache: false, 
  	onSelect:
  		function(suggestion){
  			new_item = new_purchase.clone().appendTo('.purchases');
  			new_item.removeAttr('id');
  			new_item.find('a.purchase_name').html(suggestion.data.name);
  			new_item.find('a.purchase_name').attr('href', 'index.php?module=ProductAdmin&id='+suggestion.data.id);
  			
  			// Добавляем варианты нового товара
  			var variants_select = new_item.find('select[name*=purchases][name*=variant_id]');
			for(var i in suggestion.data.variants)
			{
				sku = suggestion.data.variants[i].sku == ''?'':' (арт. '+suggestion.data.variants[i].sku+')';
  				variants_select.append("<option value='"+suggestion.data.variants[i].id+"' price='"+suggestion.data.variants[i].price+"' amount='"+suggestion.data.variants[i].stock+"'>"+suggestion.data.variants[i].name+sku+"</option>");
  			}
  			
  			if(suggestion.data.variants.length>1 || suggestion.data.variants[0].name != '')
  				variants_select.show();
  				  				
			variants_select.bind('change', function(){change_variant(variants_select);});
				change_variant(variants_select);
  			
  			if(suggestion.data.image)
  				new_item.find('img.product_icon').attr("src", suggestion.data.image);
  			else
  				new_item.find('img.product_icon').remove();

			$("input#add_purchase").val('').focus().blur(); 
  			new_item.show();
  		},
		formatResult:
			function(suggestion, currentValue){
				var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
				var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
  				return (suggestion.data.image?"<img align=absmiddle src='"+suggestion.data.image+"'> ":'') + suggestion.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>');
			}
  		
  });
  
  // Изменение цены и макс количества при изменении варианта
  function change_variant(element)
  {
		price = element.find('option:selected').attr('price');
		amount = element.find('option:selected').attr('amount');
		element.closest('.row').find('input[name*=purchases][name*=price]').val(price);
		
		// 
		amount_select = element.closest('.row').find('select[name*=purchases][name*=amount]');
		selected_amount = amount_select.val();
		amount_select.html('');
		for(i=1; i<=amount; i++)
			amount_select.append("<option value='"+i+"'>"+i+" <?php echo $_smarty_tpl->tpl_vars['settings']->value->units;?>
</option>");
		amount_select.val(Math.min(selected_amount, amount));


		return false;
  }
  
  
	// Редактировать покупки
	$("a.edit_purchases").click( function() {
		 $(".purchases span.view_purchase").hide();
		 $(".purchases span.edit_purchase").show();
		 $(".edit_purchases").hide();
		 $("div#add_purchase").show();
		 return false;
	});
  
	// Редактировать получателя
	$("div#order_details a.edit_order_details").click(function() {
		 $("ul.order_details .view_order_detail").hide();
		 $("ul.order_details .edit_order_detail").show();
		 return false;
	});
  
	// Редактировать примечание
	$("div#order_details a.edit_note").click(function() {
		 $("div.view_note").hide();
		 $("div.edit_note").show();
		 return false;
	});
  
	// Редактировать пользователя
	$("div#order_details a.edit_user").click(function() {
		 $("div.view_user").hide();
		 $("div.edit_user").show();
		 return false;
	});
	$("input#user").autocomplete({
		serviceUrl:'ajax/search_users.php',
		minChars:0,
		noCache: false, 
		onSelect:
			function(suggestion){
				$('input[name="user_id"]').val(suggestion.data.id);
			}
	});
  
	// Удалить пользователя
	$("div#order_details a.delete_user").click(function() {
		$('input[name="user_id"]').val(0);
		$('div.view_user').hide();
		$('div.edit_user').hide();
		return false;
	});

	// Посмотреть адрес на карте
	$("a#address_link").attr('href', 'http://maps.yandex.ru/?text='+$('#order_details textarea[name="address"]').val());
  
	// Подтверждение удаления
	$('select[name*=purchases][name*=variant_id]').bind('change', function(){change_variant($(this));});
	$("input[name='status_deleted']").click(function() {
		if(!confirm('Подтвердите удаление'))
			return false;	
	});

});

</script>

<style>
.autocomplete-suggestions{
background-color: #ffffff;
overflow: hidden;
border: 1px solid #e0e0e0;
overflow-y: auto;
}
.autocomplete-suggestions .autocomplete-suggestion{cursor: default;}
.autocomplete-suggestions .selected { background:#F0F0F0; }
.autocomplete-suggestions div { padding:2px 5px; white-space:nowrap; }
.autocomplete-suggestions strong { font-weight:normal; color:#3399FF; }
</style>


<?php }} ?>

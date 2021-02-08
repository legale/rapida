<?PHP

/**
 * Корзина покупок
 * Этот класс использует шаблон cart.tpl
 *
 */

require_once('View.php');

class CartView extends View
{
    //////////////////////////////////////////
    // Изменения товаров в корзине
    //////////////////////////////////////////
    public function __construct()
    {
        parent::__construct();


        //выводим пустой coupon_error, чтобы в шаблоне не было ошибки
        $this->design->assign('coupon_error', '');
        $this->design->assign('error', '');

        //~ print_r($_POST);

        // Если нажали оформить заказ
        if (isset($_POST['checkout'])) {
            $order = array();
            $order['delivery_id'] = $this->request->post('delivery_id', 'integer');
            $order['name'] = $this->request->post('name');
            $order['email'] = $this->request->post('email');
            $order['address'] = $this->request->post('address');
            $order['phone'] = $this->request->post('phone');
            $order['comment'] = $this->request->post('comment');
            $order['ip'] = $_SERVER['REMOTE_ADDR'];

            $captcha_code = $this->request->post('captcha_code', 'string');

            // Скидка
            $cart = $this->cart->get();
            $order['discount'] = $cart['discount'];

            if (isset($cart['coupon'])) {
                $order['coupon_discount'] = $cart['coupon_discount'];
                $order['coupon_code'] = $cart['coupon']['code'];
            }


            if (!empty($this->user->id)) {
                $order['user_id'] = $this->user->id;
            }

            if (empty($order['name'])) {
                $this->design->assign('error', 'empty_name');
            } elseif (empty($order['email'])) {
                $this->design->assign('error', 'empty_email');
            } if ($this->config->captcha_order && ($_SESSION['captcha_code'] != $captcha_code || empty($captcha_code))) {
                $this->design->assign('error', 'captcha');
            } else {

                // Добавляем заказ в базу
                $order_id = $this->orders->add_order($order);

                //что-то пошло не так на этапе создания заказа
                if ($order_id === false) {
                    dtimer::log(__METHOD__ . " add_order return false", 1);
                }
                $_SESSION['order_id'] = $order_id;

                // Если использовали купон, увеличим количество его использований
                if (isset($cart['coupon'])) {
                    $this->coupons->update_coupon($cart['coupon']->id, array('usages' => 'usages + 1'));
                }
                // Добавляем товары к заказу
                foreach ($_SESSION['shopping_cart'] as $variant_id => $amount) {
                    $this->orders->add_purchase(array('order_id' => $order_id, 'variant_id' => intval($variant_id), 'amount' => intval($amount)));
                }

                if ($order = $this->orders->get_order($order_id)) {
                    // Стоимость доставки
                    $delivery = $this->delivery->get_delivery($order['delivery_id']);
                    if (!empty($delivery) && $delivery->free_from > $order['total_price']) {
                        $this->orders->update_order($order['id'], array('delivery_price' => $delivery->price, 'separate_delivery' => $delivery->separate_payment));
                    }

                    // Отправляем письмо пользователю
                    $this->notify->email_order_user($order['id']);

                    // Отправляем письмо администратору
                    $this->notify->email_order_admin($order['id']);


                    // Очищаем корзину (сессию)
                    $this->cart->empty_cart();

                    // Перенаправляем на страницу заказа
                    header('Location: ' . $this->config->root_url . '/order/' . $order->trans);
                } else {
                    dtimer::log(__METHOD__ . " unable to get_order ");
                }
            }
        } else {

            // Если нам запостили amounts, обновляем их
            if ($amounts = $this->request->post('amounts')) {
                foreach ($amounts as $variant_id => $amount) {
                    $this->cart->add($variant_id, $amount);
                }

                $coupon_code = trim($this->request->post('coupon_code', 'string'));
                if (empty($coupon_code)) {
                    $this->cart->apply_coupon('');
                    header('location: ' . $this->config->root_url . '/cart/');
                } else {
                    $coupon = $this->coupons->get_coupon((string)$coupon_code);

                    if (empty($coupon) || !$coupon['valid']) {
                        $this->cart->apply_coupon($coupon_code);
                        $this->design->assign('coupon_error', 'invalid');
                    } else {
                        $this->cart->apply_coupon($coupon_code);
                        header('location: ' . $this->config->root_url . '/cart/');
                    }
                }
            }

        }


        //добавляем переменные в шаблон
        $this->design->assign('delivery_id', isset($order['delivery_id']) ? $order['delivery_id'] : '');
        $this->design->assign('name', isset($order['name']) ? $order['name'] : '');
        $this->design->assign('email', isset($order['email']) ? $order['email'] : '');
        $this->design->assign('phone', isset($order['phone']) ? $order['phone'] : '');
        $this->design->assign('address', isset($order['address']) ? $order['address'] : '');
        $this->design->assign('comment', isset($order['comment']) ? $order['comment'] : '');


    }


    //////////////////////////////////////////
    // Основная функция
    //////////////////////////////////////////
    function fetch()
    {

        // Способы доставки
        $deliveries = $this->delivery->get_deliveries(array('enabled' => 1));
        $this->design->assign('deliveries', $deliveries);

        // Данные пользователя
        if (isset($this->user->id)) {
            $last_order = false;
            if ($last_order = $this->orders->get_orders(array('user_id' => $this->user->id, 'limit' => 1))) {
                $last_order = reset($last_order);
            }
            if ($last_order) {
                $this->design->assign('name', $last_order['name']);
                $this->design->assign('email', $last_order['email']);
                $this->design->assign('phone', $last_order['phone']);
                $this->design->assign('address', $last_order['address']);
            } else {
                $this->design->assign('name', $this->user->name);
                $this->design->assign('email', $this->user->email);
            }
        }

        // Если существуют валидные купоны, нужно вывести инпут для купона
        if ($this->coupons->count_coupons(array('valid' => 1)) > 0) {
            $this->design->assign('coupon_request', true);
        } else {
            $this->design->assign('coupon_request', false);
        }

        //передаем текст для мета тега robots
        $this->design->assign('robots', "noindex, nofollow");

        //передаем в шаблон canonical со ссылкой на главную страницу
        $this->design->assign('canonical', "/");

        // Выводим корзину
        return $this->design->fetch('cart.tpl');
    }

}

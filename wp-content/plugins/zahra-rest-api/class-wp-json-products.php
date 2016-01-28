<?php

require 'class-wp-json-zahra-users.php';

class Product extends Zahra_API {

    public function register_routes($routes) {
        $routes['/zahra/products'] = array(
            array(array($this, 'get_posts'), WP_JSON_Server::READABLE),
            array(array($this, 'order'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
        );
        $routes['/zahra/products/coupons'] = array(
            array(array($this, 'check_coupon'), WP_JSON_Server::READABLE)
        );

        return $routes;
    }

    public function get_posts($filter = array(), $context = 'view', $type = 'wpsc-product', $page = 1, $tax) {

        //get all pages?	
        //the post is in the form of ?tax[wpsc_product_category]=lamsa

        $posts = parent::get_posts($filter, $context, 'wpsc-product', $page, $tax);
        return $posts;
    }

    /**
     * Prepares post data for return in an XML-RPC object.
     *
     * @access protected
     *
     * @param array $post The unprepared post data
     * @param string $context The context for the prepared post. (view|view-revision|edit|embed)
     * @return array The prepared post data
     */
    protected function prepare_post($post, $context = 'view') {


        $data = parent::prepare_post($post, $context);

        //remove content because we don't need
        unset($data['content']);



        $exception = array("_wpsc_price",
            "_wpsc_special_price",
            "_wpsc_sku",
            "_wpsc_stock");


        //filter the meta
        $data['custom_meta'] = parent::filterMeta($data['custom_meta'], $exception);
        
        //get the price and stock
        $data['price'] = $data['custom_meta']['_wpsc_price'];
        $data['stock'] = $data['custom_meta']['_wpsc_stock'];
        
        //get the product gallery
        //mixed unserialize ( string $str )
        
        //get the gallery
        $gallery = get_post_gallery($post['ID'],false);
        
        $data['media'] = $gallery["src"];
        
        //filter through the custom meta and add only the ones you want ..
        return apply_filters('json_prepare_product', $data, $post, $context);
    }

    public function check_coupon($code) {

        global $wpdb;

        $coupon = $wpdb->get_row('SELECT * from wp_wpsc_coupon_codes WHERE coupon_code=' . $code);

        if ($coupon != NULL) {

            return $coupon;
        } else {

            $response = new WP_JSON_Response();

            $response->set_status(404);

            return $response;
        }
    }

    public function order($data) {

        //use wpdb to do generic queries in this case ...
        global $wpdb;

        /*
         * how to place a new order : 
         * 
         * 1) add new user or find a user using email?
         *
         * 2) you add to wp_order using the information that the user gives
         * you in the information and address screens
         * 
         * 3) you take the id of wp_order that's generated and you add all
         * the products in the cart to wp_order_product. (in this table you only have
         * order_id, product_id, quantity, price) and decrement the quantity by the order quantity ...
         */

        $user = new Zahra_Users();

        $user_id = $user->insertCustomer($data);

        $wpdb->insert(
            'wp_order', array(
            'user_id' => $user_id,
            'total' => $data['total']
                )
        );

        $order_id = $wpdb->insert_id;
        
        
        $arr = $data['products'];

        foreach ($arr as $pj):

            $rows = $wpdb->insert(
                'wp_order_product', array(
                'order_id' => $order_id,
                'product_id' => $pj['ID'],
                'quantity' => $pj['quantity'],
                'price' => $pj['price']
                    )
            );

            $product_id = $wpdb->insert_id;

            if ($rows == 1) {
                //update the prodcut quantity in post_meta
                //todo: get the current stock and use wordpress update post meta   
                $current_stock = $wpdb->get_var("SELECT meta_value FROM wp_postmeta WHERE post_id = " . $pj['ID'] . " AND meta_key ='_wpsc_stock'");

                $left = $current_stock - $pj['quantity'];
                $sleft = (string) $left;

                $wpdb->update(
                        'wp_postmeta', array(
                    'meta_value' => $sleft
                        ), array('post_id' => $pj['ID'], 'meta_key' => '_wpsc_stock')
                );
            }

        endforeach;
        
       
        $date = date('l jS \of F Y h:i:s A');

        $message = "
مرحبا, 
يسرنا أنه تم بنجاح استلام طلبيتك رقم    :   ". $order_id .  "والتي تم إجراؤها بتاريخ : ". $date ."
ملاحظة :
يرجى انتظار التواصل معك لإجراءات الدفع وتوصيل الطلب إليك !
ان كان لديك أي استفسارات يرجى مراسلتنا عبر البريد الإلكتروني sales@zahra.org.sa
شكراً لتسوقك من زهرة !";
        
        $headers = 'From: Zahra Store <sales@zahra.org.sa>' . "\r\n";
        
        wp_mail( $data['email'], "متجر زهرة ", $message, $headers);

        
        $response = new WP_JSON_Response();

        $response->set_status(201);

        return $response;
    }

}

?>
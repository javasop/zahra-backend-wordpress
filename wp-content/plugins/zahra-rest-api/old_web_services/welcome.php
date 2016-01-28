<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function index() {
        
    }

    public function products_list_json() {
        /*
          Get All The Products id, put them in an unique array.
          make query for images foreach product in the array.
          generate a json response.
         */
        $our_list = array();
        $array_ids = array();
        $unique_id_array = null;
        $query_get_ids = $this->db->query("SELECT product_id FROM products_list2");
        if ($query_get_ids->num_rows() > 0) {
            foreach ($query_get_ids->result() as $row) {
                $array_ids[] = $row->product_id;
            }
            $unique_id_array = array_unique($array_ids);
        }


        if ($unique_id_array != null) {

            foreach ($unique_id_array as $id) {


                $q = $this->db->query("SELECT * FROM products_list2 WHERE product_id ='$id'");

                if ($q->num_rows() > 0) {

                    $results = $q->result();
                    // columns. 
                    $product = array(
                        'product_id' => null,
                        'product_name' => null,
                        'product_images' => null,
                        'product_description' => null,
                        'featured_image' => null,
                        'product_price' => null,
                        'product_stock' => null,
                        'category_name' => null
                    );


                    // init
                    $product_images = array();

                    $x = 0;
                    foreach ($results as $result) {
                        $x = $result->product_id;

                        if ($product['product_id'] == null) { // first time.
                            $product['product_id'] = $x;
                            $product['product_name'] = $result->product_name;
                            $product_images[] = $result->product_image;
                            $product['product_description'] = $result->product_description;
                            $product['featured_image'] = $result->featured_image;
                            $product['product_price'] = $result->product_price;
                            $product['product_stock'] = $result->product_stock;
                            $product['category_name'] = $result->category_name;
                        } else {

                            if ($product['product_id'] == $x) {

                                $product_images[] = $result->product_image;
                            }
                        }
                    }
                }// end loop
		
                $product['product_images'] = $product_images;
// here do not add the product that has a stock amount of zero.
               if($product['product_stock'] > 0){
 $our_list[] = $product;
}
            }
        }
//get the default response headers 
    header("Access-Control-Allow-Origin: *");

        echo json_encode($our_list);
    }

    public function products_list_lz_json() {
        /*
          Get All The Products id, put them in an unique array.
          make query for images foreach product in the array.
          generate a json response.
         */
        $our_list = array();
        $array_ids = array();
        $unique_id_array = null;
        $query_get_ids = $this->db->query("SELECT product_id FROM products_list2 WHERE category_name = 'لمسة زهرة'");
        if ($query_get_ids->num_rows() > 0) {
            foreach ($query_get_ids->result() as $row) {
                $array_ids[] = $row->product_id;
            }
            $unique_id_array = array_unique($array_ids);
        }


        if ($unique_id_array != null) {

            foreach ($unique_id_array as $id) {


                $q = $this->db->query("SELECT * FROM products_list2 WHERE product_id ='$id'");

                if ($q->num_rows() > 0) {

                    $results = $q->result();
                    // columns. 
                    $product = array(
                        'product_id' => null,
                        'product_name' => null,
                        'product_images' => null,
                        'product_description' => null,
                        'featured_image' => null,
                        'product_price' => null,
                        'product_stock' => null,
                        'category_name' => null
                    );


                    // init
                    $product_images = array();

                    $x = 0;
                    foreach ($results as $result) {
                        $x = $result->product_id;

                        if ($product['product_id'] == null) { // first time.
                            $product['product_id'] = $x;
                            $product['product_name'] = $result->product_name;
                            $product_images[] = $result->product_image;
                            $product['product_description'] = $result->product_description;
                            $product['featured_image'] = $result->featured_image;
                            $product['product_price'] = $result->product_price;
                            $product['product_stock'] = $result->product_stock;
                            $product['category_name'] = $result->category_name;
                        } else {

                            if ($product['product_id'] == $x) {

                                $product_images[] = $result->product_image;
                            }
                        }
                    }
                }// end loop

                $product['product_images'] = $product_images;
               
// here do not add the product that has a stock amount of zero.
               if($product['product_stock'] > 0){
 $our_list[] = $product;
}
            }
        }
//get the default response headers 
    header("Access-Control-Allow-Origin: *");

        echo json_encode($our_list);
    }

    public function verify_coupon() {

//get the default response headers 
    header("Access-Control-Allow-Origin: *");

        $string = $_SERVER['REQUEST_URI'];
$ur = parse_url($string);
        parse_str($ur['query']);
        $query = $this->db->get_where('wp_wpsc_coupon_codes', array('coupon_code' => $cc));

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $is_used = $row['is-used'];
                $is_active = $row['active'];
                $expiry = $row['expiry'];
                $value = $row['value'];
                $is_percentage = $row['is-percentage'];
            }


            $now_date = null;
            if ($is_used == 1 || $is_active == 0) {
                // expired
                $result = array('expired' => 1);
            } else {

                $result = array('value' => $value,
                    'is_precentage' => $is_percentage, 'expired' => "0");
            }

            echo json_encode($result);
        } else {
            echo "No code";
        }
    }

    public function place_new_order() {

        $order = $_GET['order'];
        $o = json_decode($order);


        $product = $_GET['product'];
        $pj = json_decode($product);
        var_dump($product);
        var_dump($pj);
        /*
         * how to place a new order : 
         * 
         * 1) you add to wp_order using the information that the user gives
         * you in the information and address screens
         * 
         * 2) you take the id of wp_order that's generated and you add all
         * the products in the cart to wp_order_product. (in this table you only have
         * order_id, product_id, quantity, Price)
         */




            $data = array(
                'first_name' => $o->first_name,
                'last_name' => $o->last_name,
                'address' => $o->address,
                'city' => $o->city,
                'region' => $o->region,
                'district' => $o->district,
                'status' => 'incomplete',
                'email' => $o->email,
                'phone' => $o->phone
            );
            $this->db->set('date', 'NOW()', false);
            $query = $this->db->insert('wp_order', $data);
            $order_id = 0;
            if ($this->db->affected_rows() > 0) {
                $order_id = $this->db->insert_id();
            }


        foreach ($pj as $p):
            $data = array(
                'order_id' => $order_id,
                'product_id' => $p->product_id,
                'price' => $p->price,
                'quantity' => $p->quantity,
            );

            $query = $this->db->insert('wp_order_product', $data);

        endforeach;
        // now, insert the products.
    }
    public function events_list_new_json() {

//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $our_list = array();
        $array_ids = array();
        $unique_id_array = null;
        $query_get_ids = $this->db->query("SELECT event_id FROM events_list_new");
        if ($query_get_ids->num_rows() > 0) {
            foreach ($query_get_ids->result() as $row) {
                $array_ids[] = $row->event_id;
            }
            $unique_id_array = array_unique($array_ids);
        }


        if ($unique_id_array != null) {

            foreach ($unique_id_array as $id) {


                $q = $this->db->query("SELECT * FROM events_list_new WHERE event_id ='$id'");
                $q2 = $this->db->query("SELECT * FROM events_tickets WHERE event_id ='$id'");


                if ($q->num_rows() > 0) {

                    $results = $q->result();
                    $results2 = $q2->result();
                    // columns. 
                    $event = array(
                        'event_id' => null,
                        'event_name' => null,
                        'event_start_time' => null,
                        'event_end_time' => null,
                        'featured_image' => null,
                        'event_start_date' => null,
                        'event_end_date' => null,
                        'event_description' => null,
                        'event_images' => null,
                        'location_name' => null,
                        'loaction_latitude' => null,
                        'location_longitude' => null
                    );


                    // init
                    $event_images = array();

                    $x = 0;
                    foreach ($results as $result) {
                        $x = $result->event_id;

                        if ($event['event_id'] == null) { // first time.
                            $event['event_id'] = $x;
                            $event['event_name'] = $result->event_name;
                            $event['event_start_time'] = $result->event_start_time;
                            $event['event_end_time'] = $result->event_end_time;
                            $event['featured_image'] = $result->featured_image;
                            $event['event_start_date'] = $result->event_start_date;
                            $event['event_end_date'] = $result->event_end_date;
                            $event['event_description'] = $result->event_description;
                            $event['event_images'] = $result->image;
                            $event['location_name'] = $result->location_name;
                            $event['location_latitude'] = $result->location_latitude;
                            $event['location_longitude'] = $result->location_longitude;
                        } else {

                            if ($event['event_id'] == $x) {

                                $event_images[] = $result->image;
                            }
                        }
                    } // end loop q1

                    foreach ($results2 as $r) {

                        $tickets_types = array();
                    }
                }// end loop big



                $event['event_images'] = $event_images;
                $our_list[] = $event;
            }
        }

        echo json_encode($our_list);
    }

    public function events_list_json() {


        $our_list = array();
        $array_ids = array();
        $unique_id_array = null;
        $query_get_ids = $this->db->query("SELECT event_id FROM events_list_new");
        if ($query_get_ids->num_rows() > 0) {
            foreach ($query_get_ids->result() as $row) {
                $array_ids[] = $row->event_id;
            }
            $unique_id_array = array_unique($array_ids);
        }


        if ($unique_id_array != null) {

            foreach ($unique_id_array as $id) {


                $q = $this->db->query("SELECT * FROM events_list_new WHERE event_id ='$id'");
                $q2 = $this->db->query("SELECT * FROM events_tickets WHERE event_id ='$id'");


                if ($q->num_rows() > 0) {

                    $results = $q->result();
                    $results2 = $q2->result();
                    // columns. 
                    $event = array(
                        'event_id' => null,
                        'event_name' => null,
                        'event_start_time' => null,
                        'event_end_time' => null,
                        'featured_image' => null,
                        'event_start_date' => null,
                        'event_end_date' => null,
                        'event_description' => null,
                        'event_images' => null,
                        'is_free' => null,
                        'location_name' => null,
                        'loaction_latitude' => null,
                        'location_longitude' => null,
                        'event_category' => null,
                        'event_color' => null,
                    );


                    // init
                    $event_images = array();

                    $x = 0;
                    foreach ($results as $result) {
                        $x = $result->event_id;

                        if ($event['event_id'] == null) { // first time.
                            $event['event_id'] = $x;
                            $event['event_name'] = $result->event_name;
                            $event['event_start_time'] = $result->event_start_time;
                            $event['event_end_time'] = $result->event_end_time;
                            $event['featured_image'] = $result->featured_image;
                            $event['event_start_date'] = $result->event_start_date;
                            $event['event_end_date'] = $result->event_end_date;
                            $event['event_description'] = $result->event_description;
                            $event['event_images'] = $result->image;
                            $event['is_free'] = 1;
                            $event['location_name'] = $result->location_name;
                            $event['location_latitude'] = $result->location_latitude;
                            $event['location_longitude'] = $result->location_longitude;
                            $event['event_category'] = "category";
                            $event['event_color'] = "color";
                        } else {

                            if ($event['event_id'] == $x) {

                                $event_images[] = $result->image;
                            }
                        }
                    } // end loop q1

                    foreach ($results2 as $r) {

                        $tickets_types = array();
                    }
                }// end loop big



                $event['event_images'] = $event_images;
                $our_list[] = $event;
            }
        }

        echo json_encode($our_list);
    }

    public function tickets_list_json($event_id) {
        $ticket = array();
        $sql = "SELECT * FROM events_tickets WHERE event_id = '$event_id'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $tickets_info = array();
            foreach ($query->result() as $i) :
                $tickets_info['ticket_id'] = strval($i->ticket_id);
                $tickets_info['ticket_name'] = strval($i->ticket_name);
                $tickets_info['ticket_spaces'] = strval($i->ticket_spaces);
                $tickets_info['booked_spaces'] = strval($i->booked_spaces);
                $tickets_info['remaining_spaces'] = strval($i->remaining_spaces);
                $tickets_info['ticket_price'] = strval($i->ticket_price);
                $ticket[] = $tickets_info;
            endforeach;
        }
        echo json_encode($ticket);
    }

    public function booking() {
        $get_parameters = array();
        if (isset($_SERVER['QUERY_STRING'])) {
            $pairs = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($pairs as $pair) {
                $part = explode('=', $pair);
                if ($pair != '' || $pair != null) {
                    $get_parameters[$part[0]] = $part[1];
                }
            }
        }

        //var_dump($get_parameters);



        /* How to make a booking: 
         * 
         * 1) you take the email the user provided and add a new row to wp_users
         */
        
        $email = $get_parameters['email'];
        $query = $this->db->query("INSERT INTO wp_users (user_email) VALUES (?)", array($email));
        // super vars. 
        $user_id;
        $event_id = $get_parameters['event_id'];
        $booking_id;
        
        if($this->db->affected_rows() > 0){
          $user_id = $this->db->insert_id();
          
          //get the default response headers 
    header("Access-Control-Allow-Origin: *");
          echo $user_id;
       }
        
        /* 
         * 
         * 2) take the id (user_id)  previously added and add the following to table wp_usermeta:
         *    a) meta_key = "first_name" , meta_value= you put the first name the user provided
         * 
         *    b) meta_key = "last_name" , meta_value= you put the last name the user provided 
         * 
         *    c) meta_key = "dbem_phone" , meta_value= you put the phone number the user provided
         * 
         *    d) meta_key = "city" , meta_value = the city
         * 
         *    e) meta_key = "region" , meta_value = the region
         * 
         *    f) meta_key = "district" , meta_value = the district
         */
        
        $data = array();

        
        foreach ($get_parameters as $key => $value) {
            if($key == 'phone'){
                $data['dbem_phone'] = $value;
            }
            else if($key == 'phone' || $key == 'ticket_id' || $key == 'ticket_spaces' || $key == 'ticket_price' ){
            	//don't do anything here
            }
            else{
            	
            $data[$key] = $value;
            
            }
        } 
        
         
         $query = null;
         
         foreach ($data as $key => $value) {
         	
         	  $insert_data = array(
                'user_id' => $user_id,
                'meta_key' => $key,
                'meta_value' => $value,
            );
            
            $query = $this->db->insert('wp_usermeta', $insert_data);
            
        }              
        
        
        //var_dump($data);
        
 
     

        
        if($this->db->affected_rows() > 0){
            
        }
        
        
        
         /* 
         * 3) after taking the event_id you add a new row to wp_em_bookings having :
         *    event_id, person_id(user_id), booking_spaces,booking_status,booking_price
         */
        
        $event_id = $get_parameters['event_id']; 
        $booking_data = array(
            'event_id' => $event_id, 
            'person_id' => $user_id,
            'booking_spaces' => $get_parameters['ticket_spaces'], 
            'booking_status' => 1, 
            'booking_price' => $get_parameters['ticket_price']
        );
        
        $query = $this->db->insert('wp_em_bookings', $booking_data);
        
        if($this->db->affected_rows() > 0){
            $booking_id = $this->db->insert_id();
        }
        
        
         /* 
         * 4) you take the booking_id previously added and you add a new row to wp_em_tickets_bookings:
         *    booking_id(previous), ticket_id,ticket_booking_spaces,ticket_booking_price
         * 
         */
        
        $middle_table_data = array(
            'booking_id' => $booking_id, 
            'ticket_id' => $get_parameters['ticket_id'], 
            'ticket_booking_spaces' => $get_parameters['ticket_spaces']
        );
        
        $query = $this->db->insert('wp_em_tickets_bookings', $middle_table_data);
        if($this->db->affected_rows() > 0){
            
        }
    }

    function ask_question() {

        $string = $_SERVER['REQUEST_URI'];

        parse_str($string);

        if (isset($q)) {

            // echo $q;
            //$sql = "INSERT INTO wp_posts (post_author, post_date, post_title, post_type) VALUES('4','NOW()', $q, 'questions')";

            $data = array(
                'post_author' => '4',
                'post_date' => 'NOW()',
                'post_title' => $q,
                'post_type' => 'questions',
                'post_status' => 'draft'
            );

            //$this->db->set('NOW()',false);
            $query = $this->db->insert('wp_posts', $data);


            if ($query) {
                $result = array('inserted' => 1);
            } else {
                $result = array('inserted' => 0);
            }

//get the default response headers 
    header("Access-Control-Allow-Origin: *");

            echo json_encode($result);
        }
    }

    // get the most recent questions first.
    function get_all_questions() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $this->db->select('ID, post_date, post_content, post_title');
        $query = $this->db->get_where('wp_posts', array('post_status' => 'publish',
            'post_type' => 'questions'));
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    function get_all_articles() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $sql = "SELECT t1.ID article_id,
            t1.post_date, t1.post_content, 
            t1.post_title, t1.post_excerpt,
            t2.ID , t3.meta_value first_name , 
            t4.meta_value last_name 
            FROM wp_posts t1 JOIN wp_users t2 
            ON t1.post_author = t2.ID 
            JOIN wp_usermeta t3 ON t2.ID = t3.user_id
            JOIN wp_usermeta t4 ON t2.ID = t4.user_id
            WHERE t3.meta_key = 'first_name' AND
            t4.meta_key='last_name' AND 
            t1.post_type='post'
            AND t1.post_status='publish'
            ORDER BY t1.post_date DESC
            ";
            


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(array('results' => 0));
        }
    }

    function get_all_stories() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $sql = "SELECT t1.ID article_id,
            t1.post_date, t1.post_content, 
            t1.post_title, t1.post_excerpt,
            t2.ID , t3.meta_value first_name , 
            t4.meta_value last_name 
            FROM wp_posts t1 JOIN wp_users t2 
            ON t1.post_author = t2.ID 
            JOIN wp_usermeta t3 ON t2.ID = t3.user_id
            JOIN wp_usermeta t4 ON t2.ID = t4.user_id
            WHERE t3.meta_key = 'first_name' AND
            t4.meta_key='last_name' AND 
            t1.post_type='stories'
            AND t1.post_status='publish'
            ORDER BY t1.post_date DESC
            ";


        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(array('results' => 0));
        }
    }

    function get_all_booklets() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $query = $this->db->get('booklets');
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(array('results' => 0));
        }
    }

    function get_all_amal_brochures() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");
        $query = $this->db->get('amal_brochures');
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(array('results' => 0));
        }
    }

    public function hospitals_list_json() {
//get the default response headers 
    header("Access-Control-Allow-Origin: *");

        $info = array();
        $hospital_list = array();
        $id_array = array();

        $id_sql = "SELECT DISTINCT ID FROM hospitals_list";
        $id_query = $this->db->query($id_sql);

        if ($id_query->num_rows() > 0) {

            foreach ($id_query->result() as $i) {
                //echo $i->ID." ";
                $id_array[] = $i->ID;
            }

            // foreach element in the id array we should query the database to get all
            // info and populated in an array. 


            $size = count($id_array);

            //echo 'count : '.$size;

            for ($i = 0; $i < $size; $i++) {


                $this->db->where('ID', $id_array[$i]);
                $query = $this->db->get('hospitals_list');

                if ($query->num_rows() > 0) {
                    //echo "OK";
                    $current_name = "";
                    $current_id = "";
                    foreach ($query->result() as $t) {
                        $current_id = $t->ID;
                        $current_name = $t->post_title;
                    }

                    foreach ($query->result() as $row) {

                        //echo $row->post_title." ".$row->meta_key." ".$row->meta_value;

                        $info[$row->meta_key] = $row->meta_value;
                    }
                    // final modifications. 

                    $info['hospital_id'] = $current_id;
                    $info['hospital_name'] = $current_name;

                    $hospital_list[] = $info;
                } else {
                    echo "NO";
                }
            }
        } else {
            // nim_rows < 0
            $hospital_list['result'] = false;
            echo 'f';
        }
        echo json_encode($hospital_list);

//**//
    }
    
    
    
    
    public function register_a_member(){
    
    $get_parameters = array();
        if (isset($_SERVER['QUERY_STRING'])) {
            $pairs = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($pairs as $pair) {
                $part = explode('=', $pair);
                if ($pair != '' || $pair != null) {
                    $get_parameters[$part[0]] = $part[1];
                }
            }
        }
    
    //var_dump($get_parameters);
    
        //registering zahra members instructions :
    $user_id = 0;
        $data = array('user_login' => '',
    'user_pass' => '',
    'user_nicename' =>'',
    'user_email' => '',	
    'user_url' =>'',
    'user_registered' => '',	
    'user_activation_key' => '',
    'user_status' => '',
    'display_name' => ''
    );
    $query = $this->db->insert('wp_users', $data);
    //
    

    //
    if($this->db->affected_rows() > 0){
    $user_id = $this->db->insert_id();
    }
    
    $data = array(
    'meta_key' => 'wp_capabilities',
    'meta_value' => 'a:1:{s:12:"zahra_member";s:1:"1";}',
    'user_id' => $user_id
    );
    $this->db->insert('wp_usermeta', $data);
    
    $i = 6;
    foreach($get_parameters as $key => $value){
	    $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => $i, 'VALUE' => $get_parameters[$key]));
	    $i++;
    }
    
    	$this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 3, 'VALUE' => $get_parameters['mobile']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 4, 'VALUE' => $get_parameters['account']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 6, 'VALUE' => $get_parameters['fn']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 7, 'VALUE' => $get_parameters['sn']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 8, 'VALUE' => $get_parameters['tn']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 9, 'VALUE' => $get_parameters['ln']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 10, 'VALUE' => $get_parameters['email']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 11, 'VALUE' => $get_parameters['type']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 12, 'VALUE' => $get_parameters['dob']));
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 13, 'VALUE' => $get_parameters['national_id_city']));                                             
        $this->db->insert('wp_cimy_uef_data', array('USER_ID' => $user_id, 'FIELD_ID' => 14, 'VALUE' => $get_parameters['national_id_date']));                                                                                    


                             
    
    /**    
1) add to table wp_users : just add to the table and obtain the ID.

2) add to table wp_usermeta for the same user_id  :

 a) meta_key =  'wp_capabilities' ,,,,,  meta_value = 'a:1:{s:12:"zahra_member";s:1:"1";}' 

 b) meta_key = 'wp_user_level' ,,,,,,, meta_value = '0'

3) add to table wp_cimy_uef_data USER_ID = the user_id previously obtained, insert a row for each field that the user enters in the iPhone app (first_name , middle_name , third_name ?. etc) as follows :
   
   a) for the first name insert the following : FIELD_ID = 6 , VALUE = the first name

   b) for the middle name insert the following : FIELD_ID = 7 , VALUE = //

   c) for the third name insert the following : FIELD_ID = 8 , VALUE = //

   d) for the last name insert the following : FIELD_ID = 9 , VALUE = //

   e) for the email insert the following : FIELD_ID = 10 , VALUE = //

   f) for the type name insert the following : FIELD_ID = 11 , VALUE = //

   g) for the national ID insert the following : FIELD_ID = 2 , VALUE = //

   h) for the account number insert the following : FIELD_ID = 3 , VALUE = //

   i) for the mobile number insert the following : FIELD_ID = 4 , VALUE = // 
   
   j) for the Date of Birth insert the following : FIELD_ID = 12 , VALUE
   
   k) for the ID Date insert the following : FIELD_ID = 13 , VALUE = //
   
   l) for the ID City insert the following : FIELD_ID = 14 , VALUE = //
   
   
    **/
    }
    
}

/* End of file welcome.php */
    /* Location: ./application/controllers/welcome.php */
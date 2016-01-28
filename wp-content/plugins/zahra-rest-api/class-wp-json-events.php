<?php

class Event extends Zahra_API {

    public function register_routes($routes) {
        $routes['/zahra/events'] = array(
            array(array($this, 'get_posts'), WP_JSON_Server::READABLE),
            array(array($this, 'booking'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON),
        );
        $routes['/zahra/events/tickets'] = array(
            array(array($this, 'get_tickets'), WP_JSON_Server::READABLE),
        );

        return $routes;
    }

    public function get_posts($filter = array(), $context = 'view', $type = 'stories', $page = 1) {

        $posts = parent::get_posts($filter, $context, 'event', $page);

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

        global $wpdb;

        $data = parent::prepare_post($post, $context);

        //remove content because we don't need
        unset($data['content']);

        $exception = array("_event_id",
"_event_start_time",
            "_event_end_time",
            "_event_all_day",
            "_event_start_date",
            "_event_end_date",
            "_event_rsvp",
            "_event_rsvp_date",
            "_event_rsvp_time",
            "_event_rsvp_spaces",
            "_event_spaces",
            "_event_status",
            "_start_ts",
            "_location_id");


        //filter the meta
        $data['custom_meta'] = $this->filterMeta($data['custom_meta'], $exception);


        //add tickets to the events
        //TODO: add remaining spaces
        $event_id = $data["custom_meta"]["_event_id"];
        $tickets = $wpdb->get_results("SELECT * FROM wp_em_tickets WHERE event_id=". $event_id, "ARRAY_A");


        //add location
        $location_id = $data["custom_meta"]["_location_id"];
        $location = $wpdb->get_results("SELECT * FROM wp_em_locations WHERE location_id = " . $location_id, "ARRAY_A");


        $gallery = get_post_gallery($post['ID'], false);


        $data['location'] = $location;
        $data['tickets'] = $tickets;
        $data['media'] = $gallery["src"];


        return apply_filters('json_prepare_events', $data, $post, $context);
    }

    public function booking($data) {

        global $wpdb;

        $user = new Zahra_Users();

        $user_id = $user->insertCustomer($data);


        $event_id = $data['event_id'];


        /*
         * 3) after taking the event_id you add a new row to wp_em_bookings having :
         *    event_id, person_id(user_id), booking_spaces,booking_status,booking_price
         */

        $booking_data = array(
            'event_id' => $event_id,
            'person_id' => $user_id,
            'booking_spaces' => $data['ticket_spaces'],
            'booking_status' => 1,
            'booking_price' => $data['ticket_price']
        );

        $wpdb->insert(
                'wp_em_bookings', $booking_data
        );

        $booking_id = $wpdb->insert_id;


        /*
         * 4) you take the booking_id previously added and you add a new row to wp_em_tickets_bookings:
         *    booking_id(previous), ticket_id,ticket_booking_spaces,ticket_booking_price
         * 
         */

        $middle_table_data = array(
            'booking_id' => $booking_id,
            'ticket_id' => $data['ticket_id'],
            'ticket_booking_spaces' => $data['ticket_spaces']
        );

        $wpdb->insert(
                'wp_em_tickets_bookings', $middle_table_data
        );

        $middle_id = $wpdb->insert_id;

        $response = new WP_JSON_Response();

        $response->set_status(201);

        return $response;
    }

}

?>
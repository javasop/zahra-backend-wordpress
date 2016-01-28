<?php

class Hospital extends Zahra_API {
  
    public function register_routes( $routes ) {
        $routes['/zahra/hospitals'] = array(
            array( array( $this, 'get_posts'), WP_JSON_Server::READABLE )
            //array( array( $this, 'new_post'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
/**
        $routes['/zahra/hospitals/(?P<id>\d+)'] = array(
            array( array( $this, 'get_post'), WP_JSON_Server::READABLE ),
            array( array( $this, 'edit_post'), WP_JSON_Server::EDITABLE | WP_JSON_Server::ACCEPT_JSON ),
            array( array( $this, 'delete_post'), WP_JSON_Server::DELETABLE ),
        );
**/
        return $routes;
    }
    
  public function get_posts( $filter = array(), $context = 'view', $type = 'hospital', $page = 1 ) {
    
    			$posts = parent::get_posts( $filter, $context, 'hospital', $page );
    			
    			return $posts;
   	}
	

}

?>